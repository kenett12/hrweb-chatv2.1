<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ClientModel;

/**
 * ClientController handles corporate client accounts.
 */
class ClientController extends BaseController
{
    public function index()
    {
        if ($this->session->get('role') !== 'superadmin') {
            return redirect()->to(base_url('login'))->with('msg', 'Unauthorized access.');
        }

        $db = \Config\Database::connect();

        $builder = $db->table('users');
        $builder->select('users.id, users.email, users.status, clients.company_name, clients.hr_contact');
        $builder->join('clients', 'clients.user_id = users.id');
        $builder->where('users.role', 'client');
        $query = $builder->get();

        $this->viewData['title'] = 'Client Management';
        $this->viewData['page_title'] = 'Corporate Accounts';
        $this->viewData['client_list'] = $query->getResultArray();

        // Fetch all TSRs for the dropdown
        $tsrQuery = $db->table('users')
                       ->select('id, email')
                       ->where('role', 'tsr')
                       ->where('status', 'active')
                       ->get();
        $this->viewData['tsr_list'] = $tsrQuery->getResultArray();

        return view('admin/pages/client_management', $this->viewData);
    }

    public function store()
    {
        $userModel = new UserModel();
        $clientModel = new ClientModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $companyName = $this->request->getPost('company_name');
        $hrContact = $this->request->getPost('hr_contact');

        $userData = [
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'client',
            'status' => 'active'
        ];

        if ($userModel->insert($userData)) {
            $newUserId = $userModel->insertID();

            $clientModel->insert([
                'user_id' => $newUserId,
                'company_name' => $companyName,
                'hr_contact' => $hrContact
            ]);

            return redirect()->back()->with('success', 'Client Account created successfully.');
        }

        return redirect()->back()->with('error', 'Failed to create account.');
    }

    public function update($id)
    {
        $userModel = new UserModel();
        $clientModel = new ClientModel();

        $companyName = $this->request->getPost('company_name');
        $hrContact = $this->request->getPost('hr_contact');
        
        // Update user's email if provided
        $email = $this->request->getPost('email');
        if (!empty($email)) {
            $userModel->update($id, ['email' => $email]);
        }

        // Handle optional password update
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $userModel->update($id, ['password' => password_hash($password, PASSWORD_DEFAULT)]);
        }

        // Update client specific data using user_id as the foreign key reference
        // Note: The param $id is the user.id, so we must find the client record by user_id
        $clientRecord = $clientModel->where('user_id', $id)->first();
        if ($clientRecord) {
            $clientModel->update($clientRecord['id'], [
                'company_name' => $companyName,
                'hr_contact'   => $hrContact
            ]);
            return redirect()->back()->with('success', 'Client updated successfully.');
        }

        return redirect()->back()->with('error', 'Client record not found.');
    }

    public function delete($id)
    {
        $userModel = new UserModel();
        $clientModel = new ClientModel();

        // The user ID is sent. Delete the client first, then the user.
        $clientRecord = $clientModel->where('user_id', $id)->first();
        if ($clientRecord) {
            $clientModel->delete($clientRecord['id']);
        }
        
        if ($userModel->delete($id)) {
            return redirect()->back()->with('success', 'Client account deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete client account.');
    }
}