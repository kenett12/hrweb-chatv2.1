<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TicketModel;

/**
 * TicketController for Superadmin
 * Read-only global oversight of all tickets in the system.
 */
class TicketController extends BaseController
{
    protected $ticketModel;
    protected $viewData = [];

    public function __construct()
    {
        $this->ticketModel = new TicketModel();
    }

    /**
     * Display a list of all tickets across all statuses.
     */
    public function index()
    {
        // Ensure only superadmins can access
        if (!in_array($this->session->get('role'), ['admin', 'superadmin'])) {
            return redirect()->to(base_url('login'))->with('msg', 'Unauthorized access.');
        }

        $this->viewData['title'] = 'Global Ticket Directory';
        
        $clientId = $this->request->getGet('client_id');

        // Use the existing method but requesting ALL common status types
        $tickets = $this->ticketModel->getTicketsInQueue(['Open', 'In Progress', 'Resolved', 'Closed']);
        
        // Filter by client if client_id is provided in query string (for View Logs feature)
        if ($clientId) {
            $tickets = array_filter($tickets, function($t) use ($clientId) {
                return $t['client_id'] == $clientId;
            });
            $this->viewData['title'] = 'Ticket Logs (Filtered)';
        }

        $this->viewData['tickets'] = $tickets;

        return view('admin/tickets/index', $this->viewData);
    }

    /**
     * View the full read-only thread of a specific ticket.
     */
    public function view($id)
    {
        if (!in_array($this->session->get('role'), ['admin', 'superadmin'])) {
            return redirect()->to(base_url('login'))->with('msg', 'Unauthorized access.');
        }

        $ticket = $this->ticketModel->getTicketWithUsers($id);

        if (!$ticket) {
            return redirect()->to('superadmin/tickets')->with('error', 'Ticket not found.');
        }

        $this->viewData['title']   = "Ticket #" . $ticket['id'];
        $this->viewData['ticket']  = $ticket;
        $this->viewData['replies'] = $this->ticketModel->getReplies($id);

        return view('admin/tickets/view', $this->viewData);
    }
    
    /**
     * Adds a superadmin message to the unified conversation thread.
     */
    public function reply($id)
    {
        if (!in_array($this->session->get('role'), ['admin', 'superadmin'])) {
            return redirect()->to(base_url('login'))->with('msg', 'Unauthorized access.');
        }

        $message = $this->request->getPost('message');
        $adminId = session()->get('id') ?? session()->get('user_id');

        if (!empty(trim($message))) {
            $this->ticketModel->addReply([
                'ticket_id'  => $id,
                'user_id'    => $adminId,
                'message'    => esc($message),
                'is_bot'     => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $this->ticketModel->update($id, [
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // ── NOTIFICATIONS ──
            $notifModel = new \App\Models\NotificationModel();
            $ticketInfo = $this->ticketModel->find($id);

            // Notify Client
            $notifModel->sendNotification(
                $ticketInfo['client_id'], 
                "Superadmin Reply", 
                "A Superadmin has left a message on your ticket (#{$id}).", 
                base_url("client/chat/{$id}")
            );

            // Notify TSR (if assigned)
            if (!empty($ticketInfo['assigned_to'])) {
                $notifModel->sendNotification(
                    $ticketInfo['assigned_to'], 
                    "Superadmin Reply", 
                    "A Superadmin has left a message on ticket #{$id}.", 
                    base_url("tsr/tickets/view/{$id}")
                );
            }

            // ── ACTUAL REAL-TIME WEBSOCKET PUSH ──
            if (function_exists('emit_socket_event')) {
                emit_socket_event('new_ticket_message', [
                    'ticket_id'   => $id,
                    'message'     => esc($message),
                    'is_bot'      => 0,
                    'sender_id'   => $adminId,
                    'sender_name' => session()->get('username') ?? session()->get('email') ?? 'Superadmin',
                    'sender_role' => session()->get('role'),
                    'time'        => date('h:i A')
                ]);
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success']);
        }
        return redirect()->back()->with('success', 'Reply posted.');
    }
}
