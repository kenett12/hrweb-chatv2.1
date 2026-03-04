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
     * chats
     * Displays a chat-like directory interface for the TSR.
     */
    public function chats()
    {
        $this->viewData['title'] = 'Active Chats';
        
        $staffId = session()->get('id') ?? session()->get('user_id');

        // Fetch all tickets that can be considered chats and are assigned to this TSR
        $this->viewData['chats'] = $this->ticketModel->getTicketsInQueue(['Open', 'In Progress', 'Resolved', 'Closed'], $staffId);

        return view('tsr/chat_directory', $this->viewData);
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

        // --- TICKETING ESCALATION MATRIX ---
        // Determine who the current staff can forward tickets to based on flowchart
        $currentRole = session()->get('role');
        $allowedTargetRoles = [];

        switch ($currentRole) {
            case 'tsr_level_1':
                $allowedTargetRoles = ['tl', 'dev'];
                break;
            case 'tl':
                $allowedTargetRoles = ['supervisor'];
                break;
            case 'supervisor':
                $allowedTargetRoles = ['manager', 'superadmin'];
                break;
            case 'manager':
                $allowedTargetRoles = ['superadmin'];
                break;
            case 'dev':
                $allowedTargetRoles = ['tsr_level_2'];
                break;
            case 'tsr_level_2':
                $allowedTargetRoles = ['it'];
                break;
            case 'it':
                $allowedTargetRoles = []; // Top of technical chain
                break;
            case 'superadmin':
                // Superadmins can route anywhere
                $allowedTargetRoles = ['tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'];
                break;
        }

        $forwardableStaff = [];
        if (!empty($allowedTargetRoles)) {
            $db = \Config\Database::connect();
            $builder = $db->table('users');
            $builder->select('users.id, users.role, tsrs.full_name');
            $builder->join('tsrs', 'tsrs.user_id = users.id', 'left'); // Left join because superadmins might not be in the tsrs table
            $builder->whereIn('users.role', $allowedTargetRoles);
            $builder->where('users.status', 'active');
            
            $results = $builder->get()->getResultArray();
            
            foreach ($results as &$staff) {
                // If the target is a superadmin, they exist in the superadmins table, not tsrs
                if ($staff['role'] === 'superadmin') {
                    $superadminData = $db->table('superadmins')->where('user_id', $staff['id'])->first();
                    $staff['full_name'] = $superadminData ? $superadminData->admin_name : 'Superadmin';
                }
            }
            $forwardableStaff = $results;
        }
        
        $this->viewData['forwardable_staff'] = $forwardableStaff;

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
        
        $db = \Config\Database::connect();
        $db->table('users')->where('id', $staffId)->update(['availability_status' => 'busy']);
        
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
                    'sender_name' => session()->get('username') ?? session()->get('email') ?? 'TSR',
                    'sender_role' => session()->get('role'),
                    'time'        => date('h:i A')
                ]);
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'time' => date('h:i A')
            ]);
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
                $db = \Config\Database::connect();
                $db->table('users')->where('id', $staffId)->update(['availability_status' => 'busy']);
            }

            $this->ticketModel->update($id, $updateData);

            // ── CHECK STATUS FOR TSR AVAILABILITY ──
            $ticketInfo = $this->ticketModel->find($id);
            if (in_array($status, ['Resolved', 'Closed', 'Open'])) {
                $db = \Config\Database::connect();
                if (!empty($ticketInfo['assigned_to'])) {
                    $activeCount = $this->ticketModel->where('assigned_to', $ticketInfo['assigned_to'])
                                       ->where('status', 'In Progress')
                                       ->countAllResults();
                    if ($activeCount == 0) {
                        $db->table('users')->where('id', $ticketInfo['assigned_to'])->update(['availability_status' => 'active']);
                    }
                }
            }

            // ── NOTIFICATIONS ──
            $notifModel = new \App\Models\NotificationModel();
            
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

    /**
     * forwardTicket
     * Escalates or routes a ticket to another staff member based on the flowchart hierarchy.
     */
    public function forwardTicket($id)
    {
        $staffId = session()->get('id');
        $newAssignedUserId = $this->request->getPost('forward_to_user_id');

        if (!$newAssignedUserId) {
            return redirect()->back()->with('error', 'Please select a staff member to forward this ticket to.');
        }

        // Verify the target user exists and is a staff member
        $userModel = new \App\Models\UserModel();
        $targetUser = $userModel->select('id, role')->find($newAssignedUserId);
        $tsrModel = new \App\Models\TsrModel();
        $targetStaff = $tsrModel->where('user_id', $newAssignedUserId)->first();

        if (!$targetUser || !$targetStaff) {
            return redirect()->back()->with('error', 'Invalid staff member selected.');
        }

        // Update Ticket Assignment
        $this->ticketModel->update($id, [
            'assigned_to' => $newAssignedUserId,
            'status'      => 'In Progress', // Automatically make sure it stays active
            'updated_at'  => date('Y-m-d H:i:s')
        ]);
        
        $db = \Config\Database::connect();
        $db->table('users')->where('id', $newAssignedUserId)->update(['availability_status' => 'busy']);

        // Check if the original staff member is now free
        $activeCount = $this->ticketModel->where('assigned_to', $staffId)
                           ->where('status', 'In Progress')
                           ->countAllResults();
        if ($activeCount == 0) {
            $db->table('users')->where('id', $staffId)->update(['availability_status' => 'active']);
        }

        // Inject System Audit Message into the Chat Thread
        $roleLabels = [
            'tsr_level_1' => 'TSR Level 1',
            'tl'          => 'Team Leader',
            'supervisor'  => 'Supervisor',
            'manager'     => 'Manager',
            'dev'         => 'Developer',
            'tsr_level_2' => 'TSR Level 2',
            'it'          => 'IT Support'
        ];
        $targetRoleName = $roleLabels[$targetUser['role']] ?? 'Staff';
        $systemMessage = "System: Ticket was escalated/forwarded to {$targetStaff['full_name']} ({$targetRoleName}).";

        $this->ticketModel->addReply([
            'ticket_id'  => $id,
            'user_id'    => $staffId, // The person who forwarded it
            'message'    => $systemMessage,
            'is_bot'     => 1, // Treat as system notification in UI
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Emit Socket Event so everyone in chat sees the update
        if (function_exists('emit_socket_event')) {
            emit_socket_event('new_ticket_message', [
                'ticket_id'   => $id,
                'message'     => $systemMessage,
                'is_bot'      => 1,
                'sender_id'   => $staffId,
                'sender_name' => 'System',
                'sender_role' => 'bot',
                'time'        => date('h:i A')
            ]);
        }

        return redirect()->to('tsr/tickets')->with('success', "Ticket successfully forwarded to {$targetStaff['full_name']}.");
    }
}