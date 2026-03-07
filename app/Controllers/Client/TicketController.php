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

        $filters = [
            'search'    => $this->request->getGet('search'),
            'status'    => $this->request->getGet('status') ?? 'Open',
            'date_from' => $this->request->getGet('date_from'),
            'date_to'   => $this->request->getGet('date_to')
        ];

        if ($filters['status'] === 'Resolved') {
            $filters['status'] = 'Closed';
        }
        
        $builder = $this->ticketModel->whereIn('client_id', $companyUsers);

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                    ->like('ticket_number', $s)
                    ->orLike('subject', $s)
                    ->orLike('category', $s)
                    ->groupEnd();
        }
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $builder->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }
        if (!empty($filters['date_to'])) {
            $builder->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        $this->viewData['tickets'] = $builder->orderBy('created_at', 'DESC')->findAll();

        return view('client/tickets/index', $this->viewData);
    }

    /**
     * create
     * Loads the form to submit a new support ticket.
     */
    public function create()
    {
        $this->viewData['title'] = 'Submit New Ticket';
        
        $db = \Config\Database::connect();
        $allCategories = $db->table('ticket_categories')->orderBy('name', 'ASC')->get()->getResultArray();
        
        $parents = [];
        $subcategories = [];
        
        foreach ($allCategories as $cat) {
            if (empty($cat['parent_id'])) {
                $parents[] = $cat;
            } else {
                $subcategories[] = $cat;
            }
        }

        $this->viewData['categories'] = $parents;
        $this->viewData['subcategories'] = $subcategories;

        return view('client/tickets/create', $this->viewData);
    }

    /**
     * store
     * Processes the submission of a new ticket and saves any uploaded evidence.
     */
    public function store()
    {
        $clientId = session()->get('id') ?? session()->get('user_id');

        // Handle MULTIPLE file uploads
        $files = $this->request->getFileMultiple('attachments');
        $uploadedFiles = [];
        $primaryAttachment = null;

        if ($files) {
            foreach ($files as $file) {
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $fileName = $file->getRandomName();
                    $file->move(WRITEPATH . 'uploads/tickets', $fileName);
                    $uploadedFiles[] = $fileName;
                    
                    // Set the first valid file as the primary attachment for backward compatibility
                    if ($primaryAttachment === null) {
                        $primaryAttachment = $fileName;
                    }
                }
            }
        }

        // Handle external links
        $links = $this->request->getPost('external_links');
        $validLinks = [];
        if (is_array($links)) {
            foreach ($links as $link) {
                if (!empty(trim($link))) {
                    $validLinks[] = trim($link);
                }
            }
        }

        $db = \Config\Database::connect();
        
        $assignedTo = null; // ALWAYS START UNASSIGNED SO BOT TAKES THE LEAD
        $status = 'Open';

        // Save the main ticket record and get the ID
        $ticketId = $this->ticketModel->insert([
            'ticket_number'  => $this->ticketModel->generateNumber(),
            'client_id'      => $clientId,
            'assigned_to'    => $assignedTo,
            'subject'        => $this->request->getPost('subject'),
            'category'       => $this->request->getPost('category'),
            'subcategory'    => $this->request->getPost('subcategory'),
            'priority'       => $this->request->getPost('priority'),
            'description'    => $this->request->getPost('description'),
            'attachment'     => $primaryAttachment, // Legacy support
            'attachments'    => !empty($uploadedFiles) ? json_encode($uploadedFiles) : null,
            'external_links' => !empty($validLinks) ? json_encode($validLinks) : null,
            'status'         => $status
        ]);

        // ── BOT GREETING ──
        $greetingMessage = "Hi there! I'm the HRWeb Bot. Thank you for reaching out. We have received your ticket and our support team will attend to it shortly. In the meantime, if you have any additional details or screenshots, feel free to share them here!";
        $db->table('ticket_replies')->insert([
            'ticket_id'  => $ticketId,
            'user_id'    => null,
            'message'    => $greetingMessage,
            'is_bot'     => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

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