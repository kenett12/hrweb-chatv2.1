<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Unified Ticket Model
 * Handles main tickets, dynamic categories, and the merged conversation thread.
 */
class TicketModel extends Model
{
    protected $table            = 'tickets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    protected $allowedFields    = [
        'ticket_number', 'client_id', 'assigned_to', 'superadmin_id',
        'subject', 'description', 'attachment', 
        'category', 'priority', 'status', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * getTicketsInQueue
     * Fetches specific tickets by status and includes client email as 'client_name'.
     *
     * @param array $statuses List of statuses to filter by.
     * @return array
     */
    public function getTicketsInQueue(array $statuses = ['Open', 'In Progress']): array
    {
        // We use COALESCE to fallback to the email if the company name isn't set.
        return $this->select('tickets.*, COALESCE(c.company_name, u.email) as client_name, COALESCE(t.full_name, s.email) as staff_name')
                    ->join('users u', 'u.id = tickets.client_id', 'left')
                    ->join('clients c', 'c.user_id = u.id', 'left')
                    ->join('users s', 's.id = tickets.assigned_to', 'left')
                    ->join('tsrs t', 't.user_id = s.id', 'left')
                    ->whereIn('tickets.status', $statuses)
                    ->orderBy('tickets.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * getTsrStats
     * Fetches dashboard metric counts for the technical support dashboard.
     */
    public function getTsrStats($tsrId): array
    {
        $today = date('Y-m-d');
        
        $activeChats = $this->where('status', 'In Progress')
                            ->where('assigned_to', $tsrId)
                            ->countAllResults();

        $openTickets = $this->where('status', 'Open')->countAllResults();

        $resolvedToday = $this->where('status', 'Resolved')
                              ->where('assigned_to', $tsrId)
                              ->like('updated_at', $today, 'after')
                              ->countAllResults();

        return [
            'active_chats'   => $activeChats,
            'open_tickets'   => $openTickets,
            'resolved_today' => $resolvedToday
        ];
    }

    /**
     * getCurrentSessions
     * Fetches a fast list of tickets assigned to the TSR or sitting in the open queue.
     */
    public function getCurrentSessions($tsrId): array
    {
        return $this->select('tickets.*, COALESCE(c.company_name, u.email) as client_name')
                    ->join('users u', 'u.id = tickets.client_id', 'left')
                    ->join('clients c', 'c.user_id = u.id', 'left')
                    ->groupStart()
                        ->where('tickets.status', 'Open')
                        ->orGroupStart()
                            ->where('tickets.status', 'In Progress')
                            ->where('tickets.assigned_to', $tsrId)
                        ->groupEnd()
                    ->groupEnd()
                    ->orderBy('tickets.updated_at', 'DESC')
                    ->limit(10)
                    ->findAll();
    }

    /**
     * getTicketWithUsers
     * Fetches ticket details with the email addresses of the client and assigned staff.
     */
    public function getTicketWithUsers($id)
    {
        return $this->select('tickets.*, COALESCE(c.company_name, u.email) as client_name, COALESCE(t.full_name, s.email) as staff_name, sa.email as superadmin_name')
                    ->join('users u', 'u.id = tickets.client_id', 'left')
                    ->join('clients c', 'c.user_id = u.id', 'left')
                    ->join('users s', 's.id = tickets.assigned_to', 'left')
                    ->join('tsrs t', 't.user_id = s.id', 'left')
                    ->join('users sa', 'sa.id = tickets.superadmin_id', 'left')
                    ->where('tickets.id', $id)
                    ->first();
    }

    /**
     * getReplies
     * Fetches the full conversation history for a ticket.
     */
    public function getReplies($ticketId): array
    {
        $replies = $this->db->table('ticket_replies')
                  ->select('ticket_replies.*, users.email as username, users.role')
                  ->join('users', 'users.id = ticket_replies.user_id', 'left') 
                  ->where('ticket_id', $ticketId)
                  ->orderBy('created_at', 'ASC')
                  ->get()
                  ->getResultArray();

        // Globally parse bot formatting tags so Admins and TSRs see rendered HTML
        foreach ($replies as &$msg) {
            if ($msg['is_bot'] == 1) {
                // Decode entities and normalize breaks
                $text = html_entity_decode($msg['message'] ?? '');
                $text = str_replace(['<br>', '<br />', '<br/>'], "\n", $text);

                // Strip out hidden ARTICLE_ID tags
                if (preg_match('/\[ARTICLE_ID:(\d+)\]/i', $text, $matches)) {
                    $text = str_replace($matches[0], '', $text);
                }

                // Render image tags natively
                $rendered = preg_replace_callback('/\[(?:IMAGE|IMG_FILE):([^\]]+)\]/i', function($matches) {
                    $fileName = trim($matches[1]);
                    if (is_numeric($fileName)) return ''; 

                    $url = base_url('assets/img/kb/' . $fileName);
                    return '<span class="block my-3"><img src="'.$url.'" class="rounded-2xl border border-slate-200 shadow-sm" style="max-width: 100%; height: auto;"></span>';
                }, $text);
                
                $msg['message'] = trim($rendered);
            }
        }

        return $replies;
    }

    /**
     * addReply
     * Centralized method to save a new message into the thread.
     */
    public function addReply($data)
    {
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->db->table('ticket_replies')->insert($data);
    }

    /**
     * getCategories
     * Fetches all available support categories.
     */
    public function getCategories()
    {
        return $this->db->table('ticket_categories')
                  ->orderBy('name', 'ASC')
                  ->get()
                  ->getResultArray();
    }

    /**
     * generateNumber
     * Generates a unique ticket number.
     */
    public function generateNumber()
    {
        $last = $this->orderBy('id', 'DESC')->first();
        $num  = $last ? ((int) substr($last['ticket_number'], -4)) + 1 : 1;
        return 'TIC-' . date('Ymd') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}