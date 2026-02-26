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

        // Fetch specific company profile data using the session user_id
        $clientProfile = $clientModel->where('user_id', $this->session->get('id'))->first();

        // Populate viewData with role-specific information
        $this->viewData['title'] = 'Client Portal';
        $this->viewData['page_title'] = 'Welcome, ' . ($clientProfile['company_name'] ?? 'Client');
        $this->viewData['company_name'] = $clientProfile['company_name'] ?? 'Company Profile Not Found';
        $this->viewData['hr_contact'] = $clientProfile['hr_contact'] ?? 'N/A';
        $this->viewData['active_chats'] = 0; // Placeholder for chat app development

        // Path: app/Views/client/dashboard.php
        return view('client/dashboard', $this->viewData);
    }
}