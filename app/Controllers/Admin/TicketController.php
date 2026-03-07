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
        
        // Filtering parameters
        $filters = [
            'search'          => $this->request->getGet('search'),
            'status'          => $this->request->getGet('tab') === 'closing_requests' ? '' : ($this->request->getGet('status') ?? 'Open'),
            'category'        => $this->request->getGet('category'),
            'date_from'       => $this->request->getGet('date_from'),
            'date_to'         => $this->request->getGet('date_to'),
            'client_id'       => $this->request->getGet('client_id'),
            'close_requested' => $this->request->getGet('tab') === 'closing_requests' ? 1 : $this->request->getGet('close_requested')
        ];
        
        if ($filters['status'] === 'Resolved') {
            $filters['status'] = 'Closed';
        }

        // Fetch filtered tickets
        $tickets = $this->ticketModel->getFilteredTickets($filters);

        // Fetch all pending closure requests for the floating overlay (ignore current table filters)
        $this->viewData['pending_closures'] = $this->ticketModel->where('close_requested', 1)
                                                              ->where('status !=', 'Closed')
                                                              ->orderBy('created_at', 'DESC')
                                                              ->findAll();

        if ($this->request->getGet('tab') === 'closing_requests') {
            $this->viewData['title'] = 'Tickets Awaiting Closure';
            
            // Exclude already-closed tickets from the Closing Requests table view
            $tickets = array_filter($tickets, function($t) {
                return $t['status'] !== 'Closed';
            });
        }
        
        // Filter by client if client_id is provided in query string (for View Logs feature)
        if ($filters['client_id']) {
            $tickets = array_filter($tickets, function($t) use ($filters) {
                return $t['client_id'] == $filters['client_id'];
            });
            $this->viewData['title'] = 'Ticket Logs (Filtered)';
        }

        $this->viewData['tickets'] = $tickets;

        // Fetch Categories for the Manager Tab
        $db = \Config\Database::connect();
        $categories = $db->table('ticket_categories')
                         ->where('parent_id', null)
                         ->orWhere('parent_id', 0)
                         ->orderBy('name', 'ASC')
                         ->get()
                         ->getResultArray();

        foreach ($categories as &$cat) {
            $cat['subcategories'] = $db->table('ticket_categories')
                                       ->where('parent_id', $cat['id'])
                                       ->orderBy('name', 'ASC')
                                       ->get()
                                       ->getResultArray();
        }

        $this->viewData['categories'] = $categories;

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

        // --- HANDLE MULTIPLE ATTACHMENTS ---
        $uploadedFiles = [];
        $files = $this->request->getFiles();
        if (isset($files['attachments'])) {
            foreach ($files['attachments'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(WRITABLEPATH . 'uploads/tickets', $newName);
                    $uploadedFiles[] = $newName;
                }
            }
        }

        // --- HANDLE EXTERNAL LINKS ---
        $links = $this->request->getPost('external_links');
        $validLinks = [];
        if (is_array($links)) {
            foreach ($links as $link) {
                if (!empty(trim($link))) $validLinks[] = trim($link);
            }
        }

        if (!empty(trim($message)) || !empty($uploadedFiles) || !empty($validLinks)) {
            $this->ticketModel->addReply([
                'ticket_id'      => $id,
                'user_id'        => $adminId,
                'message'        => esc($message),
                'attachments'    => !empty($uploadedFiles) ? json_encode($uploadedFiles) : null,
                'external_links' => !empty($validLinks) ? json_encode($validLinks) : null,
                'is_bot'         => 0,
                'created_at'     => date('Y-m-d H:i:s')
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

    /**
     * storeCategory
     * Handles creating and updating ticket categories/subcategories.
     */
    public function storeCategory()
    {
        if (!in_array($this->session->get('role'), ['admin', 'superadmin'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $id       = $this->request->getPost('id');
        $parentId = $this->request->getPost('parent_id');
        $name     = $this->request->getPost('name');
        $desc     = $this->request->getPost('description');

        $db = \Config\Database::connect();
        $data = [
            'name'        => esc($name),
            'parent_id'   => !empty($parentId) ? $parentId : null,
            'description' => esc($desc),
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        if ($id) {
            $db->table('ticket_categories')->where('id', $id)->update($data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $db->table('ticket_categories')->insert($data);
        }

        return redirect()->to(base_url('superadmin/tickets?tab=manager'))->with('success', 'Category saved.');
    }

    /**
     * updateTicket
     * Quick edit for remarks and due dates from the directory view.
     */
    public function updateTicket()
    {
        if (!in_array($this->session->get('role'), ['admin', 'superadmin'])) {
            return redirect()->to(base_url('login'))->with('msg', 'Unauthorized access.');
        }

        $id = $this->request->getPost('id');
        $data = [
            'due_date'             => $this->request->getPost('due_date') ?: null,
            'dev_remarks_1'        => esc($this->request->getPost('dev_remarks_1')),
            'support_remarks'      => esc($this->request->getPost('support_remarks')),
            'dev_remarks_2'        => esc($this->request->getPost('dev_remarks_2')),
            'reoccurrence_remarks' => esc($this->request->getPost('reoccurrence_remarks')),
            'updated_at'           => date('Y-m-d H:i:s')
        ];

        $this->ticketModel->update($id, $data);

        return redirect()->back()->with('success', 'Ticket updated successfully.');
    }

    /**
     * resolveTicket
     * Finalizes a ticket, sets fixed_at timestamp, and notifies client for feedback.
     */
    public function resolveTicket($id)
    {
        if (!in_array($this->session->get('role'), ['admin', 'superadmin'])) {
            return redirect()->to(base_url('login'))->with('msg', 'Unauthorized access.');
        }

        $status = $this->request->getPost('status') ?: 'Closed';
        
        $data = [
            'status'          => $status,
            'fixed_at'        => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
            'close_requested' => 0
        ];

        $this->ticketModel->update($id, $data);

        // Notify Client for feedback if closed
        if ($status === 'Closed') {
            $notifModel = new \App\Models\NotificationModel();
            $ticketInfo = $this->ticketModel->find($id);
            $notifModel->sendNotification(
                $ticketInfo['client_id'], 
                "Ticket Closed", 
                "Your ticket (#{$id}) has been marked as Closed. Please let us know your feedback.", 
                base_url("client/dashboard") // Client will see feedback modal here
            );
        }

        // Socket Emit
        if (function_exists('emit_socket_event')) {
            emit_socket_event('global_ticket_change', [
                'type' => 'ticket_resolved',
                'ticket_id' => $id,
                'status' => $status
            ]);
        }

        // Reset TSR availability if closing
        if ($status === 'Closed') {
            // Re-fetch ticket info to be absolutely sure we have latest assigned_to
            $finalTicket = $this->ticketModel->find($id);
            if (!empty($finalTicket['assigned_to'])) {
                $staffId = $finalTicket['assigned_to'];
                $db = \Config\Database::connect();
                
                // Count remaining In Progress tickets for this TSR
                $activeCount = $this->ticketModel->where('assigned_to', $staffId)
                                               ->where('status', 'In Progress')
                                               ->countAllResults();
                
                if ($activeCount == 0) {
                    $db->table('users')->where('id', $staffId)->update(['availability_status' => 'active']);
                    
                    // Emit separate event for TSR status update to trigger UI refresh if needed
                    if (function_exists('emit_socket_event')) {
                        emit_socket_event('staff_status_update', [
                            'staff_id' => $staffId,
                            'status'   => 'active'
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', "Ticket marked as {$status}.");
    }

    /**
     * deleteCategory
     * Removes a category or subcategory.
     */
    public function deleteCategory($id)
    {
        if (!in_array($this->session->get('role'), ['admin', 'superadmin'])) {
            return redirect()->to(base_url('login'))->with('msg', 'Unauthorized access.');
        }

        $db = \Config\Database::connect();
        
        // If it's a parent, we might want to prevent deletion if it has children, 
        // or just nullify them. Here we'll just delete the children too for simplicity in management.
        $db->table('ticket_categories')->where('parent_id', $id)->delete();
        $db->table('ticket_categories')->where('id', $id)->delete();

        return redirect()->to(base_url('superadmin/tickets?tab=manager'))->with('success', 'Category removed.');
    }
}
