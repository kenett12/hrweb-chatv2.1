<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\TicketModel;

/**
 * TicketController
 * Manages ticket listing, creation, and the detailed view with chat preview.
 */
class TicketController extends BaseController
{
    protected $ticketModel;
    protected $viewData = []; // Initialize to prevent property access warnings

    /**
     * Constructor to initialize the Ticket Model.
     */
    public function __construct()
    {
        $this->ticketModel = new TicketModel();
    }

    /**
     * index
     * Lists all tickets belonging to the current client.
     */
    public function index()
    {
        $clientId = session()->get('id') ?? session()->get('user_id');
        
        $this->viewData['title'] = 'My Support Tickets';

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($clientId);
        $companyId = $user['client_id'];
        
        $companyUsers = $companyId ? $userModel->where('client_id', $companyId)->findColumn('id') : [$clientId];

        // Using whereIn() to get all tickets for the company
        $this->viewData['tickets'] = $this->ticketModel->whereIn('client_id', $companyUsers)->findAll();

        return view('client/tickets/index', $this->viewData);
    }

    /**
     * create
     * Loads the form to submit a new support ticket.
     */
    public function create()
    {
        $this->viewData['title'] = 'Submit New Ticket';
        $this->viewData['categories'] = $this->ticketModel->getCategories();

        return view('client/tickets/create', $this->viewData);
    }

    /**
     * store
     * Processes the submission of a new ticket and saves any uploaded evidence.
     */
    public function store()
    {
        $clientId = session()->get('id') ?? session()->get('user_id');

        // Handle file upload security and logic
        $file = $this->request->getFile('attachment');
        $fileName = ($file && $file->isValid() && !$file->hasMoved()) ? $file->getRandomName() : null;

        if ($fileName) {
            $file->move(FCPATH . 'uploads/tickets', $fileName);
        }

        $db = \Config\Database::connect();
        
        // Find company lead TSR
        $userRecord = $db->table('users')->where('id', $clientId)->get()->getRowArray();
        $companyId = $userRecord['client_id'] ?? null;
        
        $assignedTo = null;
        $status = 'Open';

        if ($companyId) {
            $clientRecord = $db->table('clients')->where('id', $companyId)->get()->getRowArray();
            if ($clientRecord && !empty($clientRecord['hr_contact'])) {
                $hrContact = json_decode($clientRecord['hr_contact'], true);
                $leadTsrId = $hrContact['lead'] ?? null;
                
                if ($leadTsrId) {
                    $leadUser = $db->table('users')->where('id', $leadTsrId)->get()->getRowArray();
                    if ($leadUser && $leadUser['availability_status'] === 'active') {
                        $assignedTo = $leadTsrId;
                        $status = 'In Progress';
                        $db->table('users')->where('id', $leadTsrId)->update(['availability_status' => 'busy']);
                    } else {
                        // Escalate if Lead is busy/offline
                        $escalationHierarchy = ['tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'];
                        foreach ($escalationHierarchy as $role) {
                            $availableStaff = $db->table('users')
                                ->where('role', $role)
                                ->where('status', 'active')
                                ->where('availability_status', 'active')
                                ->orderBy('id', 'ASC') // Assign to the first available for simplicity
                                ->get()->getRowArray();
                                
                            if ($availableStaff) {
                                $assignedTo = $availableStaff['id'];
                                $status = 'In Progress';
                                $db->table('users')->where('id', $availableStaff['id'])->update(['availability_status' => 'busy']);
                                break;
                            }
                        }
                    }
                }
            }
        }

        // Save the main ticket record
        $this->ticketModel->save([
            'ticket_number' => $this->ticketModel->generateNumber(),
            'client_id'     => $clientId,
            'assigned_to'   => $assignedTo,
            'subject'       => $this->request->getPost('subject'),
            'category'      => $this->request->getPost('category'),
            'priority'      => $this->request->getPost('priority'),
            'description'   => $this->request->getPost('description'),
            'attachment'    => $fileName,
            'status'        => $status
        ]);

        $ticketId = $this->ticketModel->insertID();

        // ── NOTIFICATIONS ──
        $notifModel = new \App\Models\NotificationModel();
        $title = "New Ticket Created";
        $msg   = "A new ticket (#{$ticketId}) has been submitted.";
        
        // Notify TSRs (broadcasting to role view)
        $notifModel->broadcastToRole('tsr', $title, $msg, base_url("tsr/tickets/view/{$ticketId}"));
        // Notify Superadmins
        $notifModel->broadcastToRole('superadmin', $title, $msg, base_url("superadmin/tickets/view/{$ticketId}"));

        // ── ACTUAL REAL-TIME WEBSOCKET PUSH ──
        if (function_exists('emit_socket_event')) {
            emit_socket_event('global_ticket_change', [
                'type' => 'new_ticket',
                'ticket_id' => $ticketId
            ]);
        }

        return redirect()->to('client/tickets')->with('success', 'Ticket Submitted successfully.');
    }

    /**
     * view
     * Loads ticket metadata and the unified chat history for the preview panel.
     */
    public function view($id)
    {
        $clientId = session()->get('id') ?? session()->get('user_id');

        // Fetch detailed ticket info including the assigned staff name (JOINED data)
        $ticket = $this->ticketModel->getTicketWithUsers($id);

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($clientId);
        $companyId = $user['client_id'];
        
        $companyUsers = $companyId ? $userModel->where('client_id', $companyId)->findColumn('id') : [$clientId];

        // Security check: Ensure the ticket exists and belongs to the active company
        if (!$ticket || !in_array($ticket['client_id'], $companyUsers)) {
            return redirect()->to('client/tickets')->with('error', 'Unauthorized access to ticket.');
        }

        $this->viewData['title'] = 'Ticket Details';
        $this->viewData['ticket'] = $ticket;

        // Fetch MERGED chat history (Bot messages + Human replies)
        // Fixed: The model now explicitly returns an array, resolving the 'void' warning
        $this->viewData['replies'] = $this->ticketModel->getReplies($id);

        return view('client/tickets/view', $this->viewData);
    }
}