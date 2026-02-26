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
        $this->viewData = [
            'session'    => $this->session,
            'userRole'   => $this->session->get('role'),
            'userEmail'  => $this->session->get('email'),
            'isLoggedIn' => $this->session->get('isLoggedIn')
        ];
    }
}