<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\ClientModel;

/**
 * Dashboard Controller for Corporate Clients.
 * Organized under the Client namespace for role-specific access.
 */
class Dashboard extends BaseController
{
    /**
     * Display the Client Portal Overview.
     */
    public function index()
    {
        // Safety check: ensure only clients can access this logic
        if ($this->session->get('role') !== 'client') {
            return redirect()->to(base_url('login'))->with('msg', 'Unauthorized access.');
        }

        $clientModel = new ClientModel();
        $ticketModel = new \App\Models\TicketModel();
        $clientId = $this->session->get('id');

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($clientId);
        $companyId = $user['client_id'];

        // Fetch specific company profile data using the company ID
        $clientProfile = $companyId ? $clientModel->find($companyId) : null;

        // Get all user IDs for this company so we can show company-wide stats
        $companyUsers = $companyId ? $userModel->where('client_id', $companyId)->findColumn('id') : [$clientId];

        // Populate viewData with role-specific information
        $this->viewData['title'] = 'Client Portal';
        $this->viewData['page_title'] = 'Welcome, ' . ($clientProfile['company_name'] ?? 'Client');
        $this->viewData['company_name'] = $clientProfile['company_name'] ?? 'Company Profile Not Found';
        $this->viewData['hr_contact'] = $clientProfile['hr_contact'] ?? 'N/A';
        $this->viewData['active_chats'] = 0; // Placeholder for chat app development

        // Fetch Statistics using the new Analytics engine
        $analyticsModel = new \App\Models\AnalyticsModel();
        $analyticsData = $analyticsModel->getAnalyticsStats($companyUsers);
        foreach ($analyticsData as $key => $val) { $this->viewData[$key] = $val; }

        // Fetch Chat/Ticket Metrics (Today vs Last 7 Days Avg)
        $chatsToday = $ticketModel->whereIn('client_id', $companyUsers)->where('created_at >=', date('Y-m-d') . ' 00:00:00')->countAllResults();
        $chatsLast7Avg = $ticketModel->whereIn('client_id', $companyUsers)
                                     ->where('created_at >=', date('Y-m-d', strtotime('-8 days')) . ' 00:00:00')
                                     ->where('created_at <=', date('Y-m-d', strtotime('-1 days')) . ' 23:59:59')
                                     ->countAllResults() / 7;
        
        $this->viewData['chats_today'] = $chatsToday;
        $this->viewData['chats_trend'] = $chatsLast7Avg > 0 ? (($chatsToday - $chatsLast7Avg) / $chatsLast7Avg) * 100 : 0;
        $this->viewData['chats_last_7'] = round($chatsLast7Avg, 1);

        // Fetch Reporting (Sentiment) from KB Feedback
        $db = \Config\Database::connect();
        $feedbackTotal = $db->table('kb_feedback')->whereIn('user_id', $companyUsers)->countAllResults();
        $feedbackHelpful = $db->table('kb_feedback')->whereIn('user_id', $companyUsers)->where('is_helpful', 1)->countAllResults();
        
        $this->viewData['sentiment'] = $feedbackTotal > 0 ? round(($feedbackHelpful / $feedbackTotal) * 100, 1) : 0;
        $this->viewData['feedback_total'] = $feedbackTotal;

        // Fetch recent tickets
        $this->viewData['recent_tickets'] = $ticketModel->whereIn('client_id', $companyUsers)
                                                        ->orderBy('updated_at', 'DESC')
                                                        ->limit(5)
                                                        ->findAll();

        // Path: app/Views/client/dashboard.php
        return view('client/dashboard', $this->viewData);
    }

    public function settings()
    {
        if ($this->session->get('role') !== 'client') {
            return redirect()->to(base_url('login'))->with('msg', 'Unauthorized access.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($this->session->get('id'));
        $companyId = $user['client_id'];
        
        $clientModel = new \App\Models\ClientModel();
        $clientProfile = $companyId ? $clientModel->find($companyId) : null;

        // Get user for email
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($this->session->get('id'));

        $this->viewData['title'] = 'Account Settings';
        $this->viewData['page_title'] = 'Account Settings';
        $this->viewData['clientProfile'] = $clientProfile;
        $this->viewData['user'] = $user;

        // Fetch Lead TSR Name for visual clarity
        $leadName = 'Unassigned';
        if ($clientProfile && !empty($clientProfile['hr_contact'])) {
            $contacts = json_decode($clientProfile['hr_contact'], true);
            $leadEmail = $contacts['lead'] ?? null;
            if ($leadEmail) {
                $db = \Config\Database::connect();
                $tsrRecord = $db->table('users')
                    ->select('tsrs.full_name')
                    ->join('tsrs', 'tsrs.user_id = users.id')
                    ->where('users.email', $leadEmail)
                    ->get()
                    ->getRowArray();
                if ($tsrRecord) {
                    $leadName = $tsrRecord['full_name'];
                }
            }
        }
        $this->viewData['hr_contact_name'] = $leadName;

        return view('client/settings', $this->viewData);
    }

    public function updateSettings()
    {
        if ($this->session->get('role') !== 'client') {
            return redirect()->to(base_url('login'))->with('msg', 'Unauthorized access.');
        }

        $userModel = new \App\Models\UserModel();
        $userId = $this->session->get('id');
        $user = $userModel->find($userId);

        // Inputs for profile update
        $fullName = $this->request->getPost('full_name');

        // Inputs for password change
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Update Full Name regardless of password change if provided
        if ($fullName !== null) {
            $userModel->update($userId, ['full_name' => $fullName]);
        }

        if (empty($currentPassword) && empty($newPassword)) {
            return redirect()->to(base_url('client/settings'))->with('msg', 'Profile updated successfully.');
        }

        if (empty($currentPassword)) {
            return redirect()->to(base_url('client/settings'))->with('error', 'Please enter your current password.');
        }

        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->to(base_url('client/settings'))->with('error', 'Your current password is incorrect.');
        }

        if (empty($newPassword) || strlen($newPassword) < 6) {
            return redirect()->to(base_url('client/settings'))->with('error', 'New password must be at least 6 characters.');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->to(base_url('client/settings'))->with('error', 'New passwords do not match.');
        }

        $userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT)
        ]);

        return redirect()->to(base_url('client/settings'))->with('msg', 'Security settings and password updated successfully.');
    }

    public function submitFeedback($id)
    {
        if ($this->session->get('role') !== 'client') {
            return redirect()->to(base_url('login'))->with('msg', 'Unauthorized access.');
        }

        $ticketModel = new \App\Models\TicketModel();
        $ticket = $ticketModel->find($id);

        if (!$ticket || $ticket['client_id'] != $this->session->get('id')) {
            return redirect()->back()->with('error', 'Ticket not found or unauthorized.');
        }

        $data = [
            'feedback_rating'  => $this->request->getPost('rating'),
            'feedback_comment' => esc($this->request->getPost('comment')),
            'updated_at'       => date('Y-m-d H:i:s')
        ];

        $ticketModel->update($id, $data);

        return redirect()->to(base_url('client/dashboard'))->with('msg', 'Thank you for your feedback!');
    }
}