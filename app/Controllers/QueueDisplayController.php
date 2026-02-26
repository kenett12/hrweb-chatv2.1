<?php

namespace App\Controllers;

use App\Models\TicketModel;

/**
 * QueueDisplayController
 * Serves the standalone, full-screen TV-style ticket queue.
 */
class QueueDisplayController extends BaseController
{
    protected $ticketModel;

    public function __construct()
    {
        $this->ticketModel = new TicketModel();
    }

    /**
     * index
     * Serves the TV dashboard view.
     */
    public function index()
    {
        // We can pass initial data if we want, but AJAX will handle the live updates
        return view('queue_display');
    }

    /**
     * data
     * Returns JSON of the current queue (Open and In Progress).
     */
    public function data()
    {
        $tickets = $this->ticketModel->getTicketsInQueue(['Open', 'In Progress']);
        
        $response = [
            'now_serving' => [],
            'waiting'     => []
        ];

        foreach ($tickets as $ticket) {
            if ($ticket['status'] === 'In Progress') {
                $response['now_serving'][] = [
                    'ticket_number' => '#' . $ticket['id'],
                    'client'        => $ticket['client_name'] ?? 'Guest',
                    'counter'       => $ticket['staff_name'] ?? 'Counter 1',
                    'subject'       => $ticket['subject'] ?? 'No Subject',
                    'priority'      => $ticket['priority'] ?? 'Normal',
                    'category'      => $ticket['category'] ?? 'General'
                ];
            } elseif ($ticket['status'] === 'Open') {
                $response['waiting'][] = [
                    'ticket_number' => '#' . $ticket['id'],
                    'client'        => $ticket['client_name'] ?? 'Guest',
                    'subject'       => $ticket['subject'] ?? 'No Subject',
                    'priority'      => $ticket['priority'] ?? 'Normal',
                    'category'      => $ticket['category'] ?? 'General'
                ];
            }
        }

        return $this->response->setJSON($response);
    }
}
