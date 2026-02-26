<?php

namespace App\Controllers\Tsr;

use App\Controllers\BaseController;

/**
 * Dashboard Controller for Technical Support Representatives (TSR).
 * Organized under the Tsr namespace for role-specific logic.
 */
class Dashboard extends BaseController
{
    /**
     * Display the TSR Service Desk Overview.
     */
    public function index()
    {
        // Safety check to ensure only TSRs can access this folder
        if ($this->session->get('role') !== 'tsr') {
            return redirect()->to(base_url('login'))->with('msg', 'Unauthorized access.');
        }

        // Fetch live stats and active sessions
        $ticketModel = new \App\Models\TicketModel();
        $tsrId = $this->session->get('id') ?? $this->session->get('user_id');

        $stats = $ticketModel->getTsrStats($tsrId);

        $this->viewData['title']          = 'TSR Dashboard';
        $this->viewData['page_title']     = 'Service Desk Overview';
        $this->viewData['active_chats']   = $stats['active_chats'];
        $this->viewData['open_tickets']   = $stats['open_tickets'];
        $this->viewData['resolved_today'] = $stats['resolved_today'];
        $this->viewData['current_sessions'] = $ticketModel->getCurrentSessions($tsrId);

        // Path: app/Views/tsr/dashboard.php
        return view('tsr/dashboard', $this->viewData);
    }
}