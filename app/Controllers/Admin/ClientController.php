<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ClientModel;

/**
 * ClientController handles corporate client accounts and sub-accounts.
 */
class ClientController extends BaseController
{
    public function index()
    {
        if ($this->session->get('role') !== 'superadmin') {
            return redirect()->to(base_url('login'))->with('msg', 'Unauthorized access.');
        }

        $db = \Config\Database::connect();

        $builder = $db->table('clients');
        $builder->select('clients.id as client_id, clients.company_name, clients.hr_contact, (SELECT COUNT(id) FROM users WHERE users.client_id = clients.id) as account_count');
        $builder->orderBy('clients.id', 'DESC');
        $query = $builder->get();

        $this->viewData['title'] = 'Client Management';
        $this->viewData['page_title'] = 'Corporate Accounts';
        $this->viewData['client_list'] = $query->getResultArray();

        // Fetch all TSR Level 1 for the dropdown (Initial Client Contact)
        $tsrQuery = $db->table('users')
                       ->select('id, email')
                       ->where('role', 'tsr_level_1')
                       ->where('status', 'active')
                       ->get();
        $this->viewData['tsr_list'] = $tsrQuery->getResultArray();

        return view('admin/pages/client_management', $this->viewData);
    }

    public function store()
    {
        $clientModel = new ClientModel();

        $companyName = $this->request->getPost('company_name');
        
        $leadTsr = $this->request->getPost('lead_tsr');
        $coTsr1 = $this->request->getPost('co_tsr_1');
        $coTsr2 = $this->request->getPost('co_tsr_2');

        // Create structured JSON object
        $hrContactData = [
            'lead' => $leadTsr,
            'co1'  => $coTsr1,
            'co2'  => $coTsr2
        ];
        $hrContactJson = json_encode($hrContactData);

        if ($clientModel->insert([
            'user_id' => null, // No main user ID anymore
            'company_name' => $companyName,
            'hr_contact' => $hrContactJson
        ])) {
            return redirect()->back()->with('success', 'Corporate Company created successfully.');
        }

        return redirect()->back()->with('error', 'Failed to create corporate company.');
    }

    public function update($id)
    {
        $clientModel = new ClientModel();

        $companyName = $this->request->getPost('company_name');
        
        $leadTsr = $this->request->getPost('lead_tsr');
        $coTsr1 = $this->request->getPost('co_tsr_1');
        $coTsr2 = $this->request->getPost('co_tsr_2');

        // Create structured JSON object
        $hrContactData = [
            'lead' => $leadTsr,
            'co1'  => $coTsr1,
            'co2'  => $coTsr2
        ];
        $hrContactJson = json_encode($hrContactData);

        $clientRecord = $clientModel->find($id);
        if ($clientRecord) {
            $clientModel->update($id, [
                'company_name' => $companyName,
                'hr_contact'   => $hrContactJson
            ]);
            return redirect()->back()->with('success', 'Company updated successfully.');
        }

        return redirect()->back()->with('error', 'Company record not found.');
    }

    public function delete($id)
    {
        $clientModel = new ClientModel();
        $userModel = new UserModel();

        $clientRecord = $clientModel->find($id);
        if ($clientRecord) {
            // Delete all sub-accounts under this company first
            $userModel->where('client_id', $id)->delete();
            
            // Delete the company
            if ($clientModel->delete($id)) {
                return redirect()->back()->with('success', 'Company and all associated accounts deleted successfully.');
            }
        }

        return redirect()->back()->with('error', 'Failed to delete company.');
    }

    // --- SUB-ACCOUNT METHODS ---

    public function getAccounts($clientId)
    {
        if ($this->session->get('role') !== 'superadmin') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $userModel = new UserModel();
        $accounts = $userModel->select('id, email, full_name, status, client_role')
                              ->where('client_id', $clientId)
                              ->where('role', 'client')
                              ->findAll();

        return $this->response->setJSON($accounts);
    }

    public function storeAccount()
    {
        $rules = [
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'client_role' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()['email'] ?? 'Validation failed. Ensure the email is unique and valid.');
        }

        $userModel = new UserModel();

        $clientId = $this->request->getPost('client_id');
        $email = $this->request->getPost('email');
        $fullName = $this->request->getPost('full_name');
        $password = $this->request->getPost('password');
        $clientRole = $this->request->getPost('client_role');

        $userData = [
            'email' => $email,
            'full_name' => $fullName,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'client',
            'client_id' => $clientId,
            'client_role' => $clientRole,
            'status' => 'active'
        ];

        if ($userModel->insert($userData)) {
            return redirect()->back()->with('success', 'Sub-Account created successfully.');
        }

        return redirect()->back()->with('error', 'Failed to create sub-account.');
    }

    public function updateAccount($id)
    {
        $rules = [
            'email' => "permit_empty|valid_email|is_unique[users.email,id,{$id}]",
            'password' => 'permit_empty|min_length[8]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()['email'] ?? 'Validation failed. Ensure the email is unique.');
        }

        $userModel = new UserModel();

        $email = $this->request->getPost('email');
        $fullName = $this->request->getPost('full_name');
        $password = $this->request->getPost('password');
        $clientRole = $this->request->getPost('client_role');

        $updateData = [];
        if (!empty($email)) $updateData['email'] = $email;
        if (!empty($fullName)) $updateData['full_name'] = $fullName;
        if (!empty($password)) $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        if (!empty($clientRole)) $updateData['client_role'] = $clientRole;

        if (!empty($updateData)) {
            if ($userModel->update($id, $updateData)) {
                return redirect()->back()->with('success', 'Sub-Account updated successfully.');
            }
        }

        return redirect()->back()->with('error', 'Failed to update sub-account.');
    }

    public function deleteAccount($id)
    {
        $userModel = new UserModel();
        if ($userModel->delete($id)) {
            return redirect()->back()->with('success', 'Sub-Account deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete sub-account.');
    }
}
