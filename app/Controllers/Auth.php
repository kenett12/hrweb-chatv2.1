<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    /**
     * Display the organized login page.
     */
    public function login()
    {
        // Auto-redirect already logged-in users
        if ($this->session->get('isLoggedIn')) {
            $role = $this->session->get('role') ?? 'superadmin';
            $staffRoles = ['tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it', 'tsr'];
            $prefix = in_array($role, $staffRoles) ? 'tsr' : $role;
            return redirect()->to(base_url($prefix . '/dashboard'));
        }

        // Path: app/Views/auth/login.php
        return view('auth/login', $this->viewData);
    }

    /**
     * Display the Privacy & Terms page
     */
    public function privacyTerms()
    {
        return view('auth/privacy_terms');
    }

    /**
     * Display the Support page
     */
    public function support()
    {
        return view('auth/support');
    }

    /**
     * Display the Forgot Password page
     */
    public function forgotPassword()
    {
        return view('auth/forgot_password');
    }

    /**
     * Seed a fresh admin account using local PHP encryption.
     * Use this after resetting your database.
     */
    public function seed()
    {
        $db = \Config\Database::connect();
        
        // Wipe existing data for a clean fresh start
        $db->table('superadmins')->emptyTable();
        $db->query("DELETE FROM users");

        // 1. Insert into Parent Table
        $userData = [
            'email'    => 'admin@hrweb.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role'     => 'superadmin',
            'status'   => 'active'
        ];
        $db->table('users')->insert($userData);
        $newUserId = $db->insertID();

        // 2. Insert into Superadmin Sub-table
        $db->table('superadmins')->insert([
            'user_id'    => $newUserId,
            'admin_name' => 'Main Admin',
            'is_master'  => 1
        ]);

        return "Fresh Database Seeded! <br> Login: admin@hrweb.com <br> Pass: admin123";
    }

    /**
     * Authenticate and route users by their divided roles.
     */
    public function authenticate()
    {
        $model = new UserModel();
        
        // Use getPost for better security and IDE compatibility
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            // Check if account is active
            if ($user['status'] !== 'active') {
                return redirect()->back()->with('msg', 'Account is currently ' . $user['status']);
            }

            // Set session data for global consistency
            $this->session->set([
                'id'         => $user['id'],
                'email'      => $user['email'],
                'role'       => $user['role'],
                'isLoggedIn' => true
            ]);

            // Handle "Remember Me"
            if ($this->request->getPost('remember')) {
                $token = bin2hex(random_bytes(32));
                // Save hashed token or raw token to DB. Raw token is fine for this implementation scope.
                $model->update($user['id'], ['remember_token' => $token]);
                set_cookie('remember_token', $token, 30 * 24 * 60 * 60); // 30 days
            }

            // Set user online
            $model->update($user['id'], ['availability_status' => 'active']);

            // Dynamic redirect: /superadmin/dashboard, /tsr/dashboard, etc.
            $staffRoles = ['tsr', 'tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'];
            if (in_array($user['role'], $staffRoles)) {
                return redirect()->to(base_url('tsr/dashboard'));
            }

            return redirect()->to(base_url($user['role'] . '/dashboard'));
        }

        // Return with error if authentication fails
        return redirect()->back()->with('msg', 'Invalid Email or Password');
    }

    /**
     * Handle user logout and clear session.
     */
    public function logout()
    {
        $userId = $this->session->get('id');
        if ($userId) {
            $model = new UserModel();
            $model->update($userId, [
                'remember_token' => null,
                'availability_status' => 'offline'
            ]);
        }
        delete_cookie('remember_token');

        $this->session->destroy();
        return redirect()->to(base_url('login'));
    }
}