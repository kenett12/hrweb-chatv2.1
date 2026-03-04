<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * This runs BEFORE the controller method.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // 1. Check if user is logged in via session
        if (!$session->get('isLoggedIn')) {
            helper('cookie');
            $token = get_cookie('remember_token');
            $loggedInViaCookie = false;

            if ($token) {
                $model = new \App\Models\UserModel();
                $user = $model->where('remember_token', $token)->first();

                if ($user && $user['status'] === 'active') {
                    $session->set([
                        'id'         => $user['id'],
                        'email'      => $user['email'],
                        'role'       => $user['role'],
                        'isLoggedIn' => true
                    ]);
                    $loggedInViaCookie = true;
                }
            }

            if (!$loggedInViaCookie) {
                return redirect()->to(base_url('login'))->with('msg', 'Please login first.');
            }
        }

        // 2. Check Role-Based Access (if arguments like 'superadmin' are passed in routes)
        if (!empty($arguments)) {
            $userRole = $session->get('role');
            if (!in_array($userRole, $arguments)) {
                // Redirect to their own dashboard if they try to peek at others
                $staffRoles = ['admin', 'superadmin', 'tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it', 'tsr'];
                $prefix = in_array($userRole, $staffRoles) ? (in_array($userRole, ['admin', 'superadmin']) ? 'superadmin' : 'tsr') : $userRole;
                return redirect()->to(base_url($prefix . '/dashboard'))->with('error', 'Unauthorized access to that area.');
            }
        }
    }

    /**
     * This runs AFTER the controller method.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Not needed for basic auth
    }
}