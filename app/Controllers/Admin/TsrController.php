<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\TsrModel;

/**
 * TsrController handles the management of Technical Support staff.
 * Organized under the Admin namespace for Superadmin access only.
 */
class TsrController extends BaseController
{
    /**
     * Display the list of all TSR accounts.
     */
    public function index()
    {
        // 1. Security Check (Session inherited from BaseController)
        if ($this->session->get('role') !== 'superadmin') {
            return redirect()->to(base_url('login'))->with('msg', 'Unauthorized access.');
        }

        $db = \Config\Database::connect();

        // 2. Fetch data by joining Parent (users) and Sub-table (tsrs)
        $builder = $db->table('users');
        $builder->select('users.id, users.email, users.status, tsrs.full_name, tsrs.employee_id');
        $builder->join('tsrs', 'tsrs.user_id = users.id');
        $builder->where('users.role', 'tsr');
        $query = $builder->get();

        // 3. Populate shared viewData for the Sidebar and Header
        $this->viewData['title'] = 'TSR Management';
        $this->viewData['page_title'] = 'Technical Support Accounts';
        $this->viewData['tsr_list'] = $query->getResultArray();

        // 4. Load the view from the organized sub-folder
        return view('admin/pages/tsr_management', $this->viewData);
    }

    /**
     * Create a new TSR account (Parent + Sub-table)
     */
    public function store()
    {
        $userModel = new UserModel();
        $tsrModel = new TsrModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $fullName = $this->request->getPost('full_name');
        $empId = $this->request->getPost('employee_id');

        // Step A: Insert into the Parent 'users' table
        $userData = [
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'tsr',
            'status' => 'active'
        ];

        if ($userModel->insert($userData)) {
            $newUserId = $userModel->insertID();

            // Step B: Insert into the 'tsrs' sub-table
            $tsrModel->insert([
                'user_id' => $newUserId,
                'full_name' => $fullName,
                'employee_id' => $empId
            ]);

            return redirect()->back()->with('success', 'TSR Account created successfully.');
        }

        return redirect()->back()->with('error', 'Failed to create account.');
    }
}