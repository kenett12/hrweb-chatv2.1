<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TsrModel;
use App\Models\ClientModel;

/**
 * Dashboard Controller for Superadmin
 * Handled under the 'Admin' namespace for organization.
 */
class Dashboard extends BaseController
{
    /**
     * Display the main Superadmin overview.
     * All shared data (userRole, userEmail) is automatically loaded from BaseController.
     */
    public function index()
    {
        // Safety check: ensure only superadmins can access this logic
        if ($this->session->get('role') !== 'superadmin') {
            return redirect()->to(base_url('login'))->with('msg', 'Unauthorized access.');
        }

        $tsrModel = new TsrModel();
        $clientModel = new ClientModel();
        $db = \Config\Database::connect();

        // Check for active filter
        $filterType = $this->request->getGet('type') ?? 'all';
        $limit = ($filterType === 'all') ? 10 : 15;
        
        $activityLog = [];

        // 1. Fetch recent tickets
        if ($filterType === 'all' || $filterType === 'ticket') {
            $recentTickets = $db->table('tickets')
                ->select("id, ticket_number, subject as message, created_at, 'ticket' as type")
                ->orderBy('created_at', 'DESC')
                ->limit($limit)
                ->get()
                ->getResultArray();
            $activityLog = array_merge($activityLog, $recentTickets);
        }

        // 2. Fetch recent replies
        if ($filterType === 'all' || $filterType === 'reply') {
            $recentReplies = $db->table('ticket_replies')
                ->select("ticket_replies.ticket_id as id, t.ticket_number, ticket_replies.message, ticket_replies.created_at, 'reply' as type, u.email as user_email")
                ->join('tickets t', 't.id = ticket_replies.ticket_id', 'left')
                ->join('users u', 'u.id = ticket_replies.user_id', 'left')
                ->where('ticket_replies.is_bot', 0)
                ->orderBy('ticket_replies.created_at', 'DESC')
                ->limit($limit)
                ->get()
                ->getResultArray();
            $activityLog = array_merge($activityLog, $recentReplies);
        }

        // 3. Fetch recent users (registrations)
        if ($filterType === 'all' || $filterType === 'user') {
            $recentUsers = $db->table('users')
                ->select("id, role as ticket_number, email as message, created_at, 'user' as type")
                ->orderBy('created_at', 'DESC')
                ->limit($limit)
                ->get()
                ->getResultArray();
            $activityLog = array_merge($activityLog, $recentUsers);
        }

        // 4. Fetch recent KB Articles
        if ($filterType === 'all' || $filterType === 'kb') {
            $recentKb = $db->table('kb_articles')
                ->select("id, category as ticket_number, question as message, created_at, 'kb' as type")
                ->orderBy('created_at', 'DESC')
                ->limit($limit)
                ->get()
                ->getResultArray();
            $activityLog = array_merge($activityLog, $recentKb);
        }

        // 5. Fetch recent KB feedback
        if ($filterType === 'all' || $filterType === 'feedback') {
            $recentFeedback = $db->table('kb_feedback')
                ->select("kb_feedback.id, 'feedback' as ticket_number, IF(kb_feedback.is_helpful=1, 'Helpful', 'Not Helpful') as message, kb_feedback.created_at, 'feedback' as type, u.email as user_email, a.question as article_name")
                ->join('users u', 'u.id = kb_feedback.user_id', 'left')
                ->join('kb_articles a', 'a.id = kb_feedback.article_id', 'left')
                ->orderBy('kb_feedback.created_at', 'DESC')
                ->limit($limit)
                ->get()
                ->getResultArray();
            $activityLog = array_merge($activityLog, $recentFeedback);
        }

        // Sort whatever was merged
        usort($activityLog, function($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });

        // Always take top 15 regardless of filter
        $activityLog = array_slice($activityLog, 0, 15);

        // Populate local viewData with specific dashboard metrics
        $this->viewData['title'] = 'Superadmin Dashboard';
        $this->viewData['page_title'] = 'System Overview';
        $this->viewData['total_tsrs'] = $tsrModel->countAllResults();
        $this->viewData['total_clients'] = $clientModel->countAllResults();
        $this->viewData['recent_activity'] = $activityLog;
        $this->viewData['current_filter'] = $filterType;

        // Path: app/Views/admin/dashboard.php
        return view('admin/dashboard', $this->viewData);
    }
}