<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest; // Added for specific web request methods
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     * Use IncomingRequest to enable getVar() and getPost() support.
     *
     * @var IncomingRequest|CLIRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically.
     */
    protected $helpers = ['form', 'url', 'html', 'cookie', 'socket'];

    /**
     * Shared data array for views to maintain consistency across roles.
     */
    protected $viewData = [];

    /**
     * Session instance accessible to all child controllers.
     */
    protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // 1. Initialize Session globally
        $this->session = \Config\Services::session();

        // 2. Pre-populate shared view data for consistency (Header/Sidebar)
        $role = $this->session->get('role');
        $staffRoles = ['admin', 'superadmin', 'tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it', 'tsr'];
        $portalPrefix = in_array($role, $staffRoles) ? (in_array($role, ['admin', 'superadmin']) ? 'superadmin' : 'tsr') : $role;

        $userId = $this->session->get('id');
        $userAvailability = 'offline';
        if ($userId) {
            $db = \Config\Database::connect();
            $userRecord = $db->table('users')->select('availability_status')->where('id', $userId)->get()->getRowArray();
            if ($userRecord && isset($userRecord['availability_status'])) {
                $userAvailability = $userRecord['availability_status'];
            }
        }

        $this->viewData = [
            'session'          => $this->session,
            'userRole'         => $role,
            'portalPrefix'     => $portalPrefix,
            'userEmail'        => $this->session->get('email'),
            'isLoggedIn'       => $this->session->get('isLoggedIn'),
            'userAvailability' => $userAvailability
        ];

        // 3. Automated Analytics Tracking for Clients
        if ($this->session->get('role') === 'client') {
            $this->recordAnalytics();
        }
    }

    /**
     * recordAnalytics
     * Background tracking for visitors and page views.
     */
    protected function recordAnalytics()
    {
        $db = \Config\Database::connect();
        $userId = $this->session->get('id');
        $sessionId = session_id();
        $ip = $this->request->getIPAddress();
        $today = date('Y-m-d');

        // 1. Always track as Page View
        $db->table('analytics')->insert([
            'client_id'  => $userId,
            'type'       => 'page_view',
            'ip_address' => $ip,
            'session_id' => $sessionId,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // 2. Track as Visitor if first time for this session today
        $exists = $db->table('analytics')
                    ->where('client_id', $userId)
                    ->where('session_id', $sessionId)
                    ->where('type', 'visitor')
                    ->where('created_at >=', $today . ' 00:00:00')
                    ->countAllResults();

        if ($exists === 0) {
            $db->table('analytics')->insert([
                'client_id'  => $userId,
                'type'       => 'visitor',
                'ip_address' => $ip,
                'session_id' => $sessionId,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}