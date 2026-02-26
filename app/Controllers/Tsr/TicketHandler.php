<?php

namespace App\Controllers\Tsr;

use App\Controllers\BaseController;
use App\Models\TicketModel;

/**
 * TicketHandler
 * Logic for Technical Support Representatives (TSR) to manage and reply to tickets.
 */
class TicketHandler extends BaseController
{
    protected $ticketModel;
    protected $viewData = []; // Store data for views

    /**
     * Constructor to initialize the Ticket Model.
     */
    public function __construct()
    {
        $this->ticketModel = new TicketModel();
    }

    /**
     * index
     * Lists all open or active tickets in the system for staff review.
     */
    public function index()
    {
        $this->viewData['title'] = 'Ticket Queue';
        
        // Calling the method correctly from the model
        $this->viewData['tickets'] = $this->ticketModel->getTicketsInQueue(['Open', 'In Progress']);

        return view('tsr/tickets/index', $this->viewData);
    }

    /**
     * liveQueue
     * Returns JSON data containing the live ticket queue for dynamic UI updates.
     */
    public function liveQueue()
    {
        $tickets = $this->ticketModel->getTicketsInQueue(['Open', 'In Progress']);
        return $this->response->setJSON($tickets);
    }

    /**
     * view
     * Displays the full ticket details and conversation thread.
     * * @param int $id
     */
    public function view($id)
    {
        // Fetch detailed ticket info including joined user names
        $ticket = $this->ticketModel->getTicketWithUsers($id);

        if (!$ticket) {
            return redirect()->to('tsr/tickets')->with('error', 'Ticket not found.');
        }

        $this->viewData['title']   = "Ticket #" . $ticket['id'];
        $this->viewData['ticket']  = $ticket;
        
        // Fetch the conversation history (Bot, Client, and Staff messages)
        $this->viewData['replies'] = $this->ticketModel->getReplies($id);

        return view('tsr/tickets/view', $this->viewData);
    }

    /**
     * claim
     * Allows a TSR to assign themselves to a ticket.
     */
    public function claim($id)
    {
        $staffId = session()->get('id') ?? session()->get('user_id');

        $superadmin = (new \App\Models\UserModel())->where('role', 'superadmin')->first();
        $adminId = $superadmin ? $superadmin['id'] : null;

        $updateData = [
            'assigned_to' => $staffId,
            'status'      => 'In Progress',
            'updated_at'  => date('Y-m-d H:i:s')
        ];
        
        if ($adminId) {
            $updateData['superadmin_id'] = $adminId;
        }

        $this->ticketModel->update($id, $updateData);

        if ($adminId) {
            $this->ticketModel->addReply([
                'ticket_id'  => $id,
                'user_id'    => $adminId, 
                'message'    => "This ticket has been claimed. A Superadmin has been added to this thread to assist and monitor progress. This is now a 3-way Group Chat.",
                'is_bot'     => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // ── NOTIFICATIONS ──
        $notifModel = new \App\Models\NotificationModel();
        $ticketInfo = $this->ticketModel->find($id);
        
        // Notify Client
        $notifModel->sendNotification(
            $ticketInfo['client_id'], 
            "Ticket Claimed", 
            "Your ticket (#{$id}) is now being handled by a support agent.", 
            base_url("client/chat/{$id}")
        );
        
        // Notify Superadmin
        if ($adminId) {
            $notifModel->sendNotification(
                $adminId, 
                "TSR Claimed Ticket", 
                "A TSR has claimed ticket #{$id}. You have been added to the chat.", 
                base_url("superadmin/tickets/view/{$id}")
            );
        }

        // ── ACTUAL REAL-TIME WEBSOCKET PUSH ──
        if (function_exists('emit_socket_event')) {
            emit_socket_event('global_ticket_change', [
                'type' => 'ticket_claimed',
                'ticket_id' => $id
            ]);
        }

        return redirect()->back()->with('success', 'You have successfully claimed this ticket.');
    }

    /**
     * reply
     * Adds a staff message to the unified conversation thread.
     */
    public function reply($id)
    {
        $message = $this->request->getPost('message');
        $staffId = session()->get('id') ?? session()->get('user_id');

        if (!empty(trim($message))) {
            // Use the model's addReply method for consistency
            $this->ticketModel->addReply([
                'ticket_id'  => $id,
                'user_id'    => $staffId,
                'message'    => esc($message),
                'is_bot'     => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Refresh ticket timestamp and ensure it is marked "In Progress"
            $this->ticketModel->update($id, [
                'status'     => 'In Progress',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // ── NOTIFICATIONS ──
            $notifModel = new \App\Models\NotificationModel();
            $ticketInfo = $this->ticketModel->find($id);

            // Notify Client
            $notifModel->sendNotification(
                $ticketInfo['client_id'], 
                "New Support Reply", 
                "A support agent has replied to your ticket (#{$id}).", 
                base_url("client/chat/{$id}")
            );

            // Notify Superadmin (if monitoring)
            if (!empty($ticketInfo['superadmin_id'])) {
                $notifModel->sendNotification(
                    $ticketInfo['superadmin_id'], 
                    "New TSR Reply", 
                    "A TSR has replied to ticket #{$id}.", 
                    base_url("superadmin/tickets/view/{$id}")
                );
            }

            // ── ACTUAL REAL-TIME WEBSOCKET PUSH ──
            if (function_exists('emit_socket_event')) {
                emit_socket_event('new_ticket_message', [
                    'ticket_id'   => $id,
                    'message'     => esc($message),
                    'is_bot'      => 0,
                    'sender_id'   => $staffId,
                    'sender_name' => session()->get('username') ?? session()->get('email') ?? 'TSR'
                ]);
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success']);
        }
        return redirect()->back()->with('success', 'Reply posted.');
    }

    /**
     * updateStatus
     * Direct endpoint to change ticket status (e.g., Resolved, Closed).
     */
    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');
        $staffId = session()->get('id') ?? session()->get('user_id');

        if (in_array($status, ['Open', 'In Progress', 'Resolved', 'Closed'])) {
            $updateData = [
                'status'     => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // If a TSR moves it to In Progress, auto-assign it to them
            if ($status === 'In Progress') {
                $updateData['assigned_to'] = $staffId;
            }

            $this->ticketModel->update($id, $updateData);

            // ── NOTIFICATIONS ──
            $notifModel = new \App\Models\NotificationModel();
            $ticketInfo = $this->ticketModel->find($id);
            
            // Notify Client
            $notifModel->sendNotification(
                $ticketInfo['client_id'], 
                "Ticket Status Updated", 
                "Your ticket (#{$id}) status has been updated to: {$status}.", 
                base_url("client/chat/{$id}")
            );

            // ── ACTUAL REAL-TIME WEBSOCKET PUSH ──
            if (function_exists('emit_socket_event')) {
                emit_socket_event('global_ticket_change', [
                    'type' => 'status_updated',
                    'ticket_id' => $id,
                    'status' => $status
                ]);
            }

            return redirect()->back()->with('success', "Ticket status updated to {$status}.");
        }

        return redirect()->back()->with('error', 'Invalid status provided.');
    }
}