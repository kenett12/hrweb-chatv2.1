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
        $staffRoles = ['tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'];
        
        $builder = $db->table('users');
        $builder->select('users.id, users.email, users.status, users.role, users.availability_status, tsrs.full_name, tsrs.employee_id');
        $builder->join('tsrs', 'tsrs.user_id = users.id');
        $builder->whereIn('users.role', $staffRoles);
        $query = $builder->get();

        $this->viewData['title'] = 'TSR Management';
        $this->viewData['page_title'] = 'Technical Support Accounts';
        $tsrList = $query->getResultArray();
        
        // --- KPI Calculations ---
        // 1. Fetch system settings for min target
        $minTargetQuery = $db->table('system_settings')->where('setting_key', 'min_tsr_leads')->get()->getRow();
        $minTarget = $minTargetQuery ? (int)$minTargetQuery->setting_value : 10;
        
        // 2. Fetch all clients to tally assignments
        $clientsQuery = $db->table('clients')->select('company_name, hr_contact')->get()->getResultArray();
        
        // Detail Assignments Array: [ ['company' => 'A', 'lead' => '', 'co1' => '', 'co2' => ''], ... ]
        $detailedAssignments = [];
        
        // Initialize KPI array per TSR
        $kpiData = [];
        foreach ($tsrList as $tsr) {
            $kpiData[$tsr['email']] = [
                'name' => $tsr['full_name'],
                'leads' => 0,
                'coleads' => 0,
                'utilization' => 0
            ];
        }

        foreach ($clientsQuery as $client) {
            $contacts = json_decode($client['hr_contact'], true);
            if (is_array($contacts)) {
                $lead = $contacts['lead'] ?? null;
                $co1 = $contacts['co1'] ?? null;
                $co2 = $contacts['co2'] ?? null;
                
                // Add to detailed assignments list regardless of whether they have assignments or not
                $detailedAssignments[] = [
                    'company' => $client['company_name'],
                    'lead' => $lead,
                    'co1' => $co1,
                    'co2' => $co2
                ];

                // Tally Lead
                if ($lead && isset($kpiData[$lead])) {
                    $kpiData[$lead]['leads']++;
                }
                
                // Tally Co-Lead (Only count unique distinct co-leads for the client if they are the same person assigned twice accidentally)
                $uniqueCoLeads = array_unique(array_filter([$co1, $co2]));
                foreach ($uniqueCoLeads as $coTsr) {
                    if (isset($kpiData[$coTsr])) {
                        // Optional: if a TSR is already a lead, do not count as a co-lead for the SAME company, even if selected.
                        if ($coTsr !== $lead) {
                            $kpiData[$coTsr]['coleads']++;
                        }
                    }
                }
            } else {
                // If the contacts array is empty, invalid, or legacy format without lead/co1 targets, still add empty state to map
                $detailedAssignments[] = [
                    'company' => $client['company_name'],
                    'lead' => null,
                    'co1' => null,
                    'co2' => null
                ];
            }
        }

        // Compute % Utilization based on Leads
        foreach ($kpiData as $email => &$data) {
            if ($minTarget > 0) {
                $data['utilization'] = round(($data['leads'] / $minTarget) * 100);
            }
        }
        unset($data);

        $this->viewData['tsr_list'] = $tsrList;
        $this->viewData['kpi_data'] = $kpiData;
        $this->viewData['detailed_assignments'] = $detailedAssignments;
        $this->viewData['min_target'] = $minTarget;

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

        $emailPrefix = $this->request->getPost('email_prefix');
        $email = $emailPrefix ? strtolower($emailPrefix . '@hrweb.ph') : '';
        
        $password = $this->request->getPost('password');
        $fullName = $this->request->getPost('full_name');
        $empId = $this->request->getPost('employee_id');
        $role = $this->request->getPost('role') ?? 'tsr_level_1'; // Use dynamic role select

        // Validation Rules
        $validationRules = [
            'full_name' => [
                'rules' => 'required|min_length[3]|max_length[100]|alpha_space',
                'errors' => [
                    'alpha_space' => 'Full name can only contain alphabetical characters and spaces.'
                ]
            ],
            'employee_id' => [
                'rules' => 'required|regex_match[/^[A-Za-z]{3}-\d{4}$/]|is_unique[tsrs.employee_id]',
                'errors' => [
                    'regex_match' => 'Employee ID must be exactly 3 letters followed by 4 numbers (e.g., AAA-0000).',
                    'is_unique' => 'This Employee ID is already registered.'
                ]
            ],
            'email_prefix' => [
                'rules' => 'required|regex_match[/^[a-zA-Z0-9_\.]+$/]',
                'errors' => [
                    'regex_match' => 'Email prefix can only contain letters, numbers, dots, and underscores.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/]',
                'errors' => [
                    'regex_match' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',
                    'min_length' => 'Password must be at least 8 characters long.'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            $errors = $this->validator->getErrors();
            $firstError = reset($errors);
            return redirect()->back()->withInput()->with('error', $firstError);
        }

        // Manual uniqueness check since we combined the email manually
        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->withInput()->with('error', 'This Email Address is already registered.');
        }

        // Step A: Insert into the Parent 'users' table
        $userData = [
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
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

    /**
     * Update a Staff account
     */
    public function update($id)
    {
        $userModel = new UserModel();
        $tsrModel = new TsrModel();

        // 1. Verify existence and permission
        $user = $userModel->find($id);
        $staffRoles = ['tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'];
        
        if (!$user || !in_array($user['role'], $staffRoles)) {
            return redirect()->back()->with('error', 'Staff account not found or invalid.');
        }

        $emailPrefix = $this->request->getPost('email_prefix');
        $email = $emailPrefix ? strtolower($emailPrefix . '@hrweb.ph') : '';
        
        $password = $this->request->getPost('password');
        $fullName = $this->request->getPost('full_name');
        $empId = $this->request->getPost('employee_id');
        $role = $this->request->getPost('role');
        $status = $this->request->getPost('status');

        // Validation Rules
        $validationRules = [
            'full_name' => [
                'rules' => 'required|min_length[3]|max_length[100]|alpha_space',
                'errors' => [
                    'alpha_space' => 'Full name can only contain alphabetical characters and spaces.'
                ]
            ],
            'employee_id' => [
                'rules' => 'required|regex_match[/^[A-Za-z]{3}-\d{4}$/]',
                'errors' => [
                    'regex_match' => 'Employee ID must be exactly 3 letters followed by 4 numbers (e.g., AAA-0000).'
                ]
            ],
            'email_prefix' => [
                'rules' => 'required|regex_match[/^[a-zA-Z0-9_\.]+$/]',
                'errors' => [
                    'regex_match' => 'Email prefix can only contain letters, numbers, dots, and underscores.'
                ]
            ]
        ];

        if (!empty($password)) {
            $validationRules['password'] = [
                'rules' => 'min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/]',
                'errors' => [
                    'regex_match' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',
                    'min_length' => 'Password must be at least 8 characters long.'
                ]
            ];
        }

        if (!$this->validate($validationRules)) {
            $errors = $this->validator->getErrors();
            $firstError = reset($errors);
            return redirect()->back()->withInput()->with('error', $firstError);
        }

        // Manual uniqueness check for email (excluding current user)
        $existingUserEmail = $userModel->where('email', $email)->where('id !=', $id)->first();
        if ($existingUserEmail) {
            return redirect()->back()->withInput()->with('error', 'This Email Address is already registered.');
        }

        // Manual uniqueness check for Employee ID (excluding current staff record)
        $staffRecord = $tsrModel->where('user_id', $id)->first();
        if ($staffRecord) {
            $existingEmp = $tsrModel->where('employee_id', $empId)->where('id !=', $staffRecord['id'])->first();
            if ($existingEmp) {
                return redirect()->back()->withInput()->with('error', 'This Employee ID is already registered.');
            }
        }

        // Step A: Update Parent 'users' table
        $userData = [
            'email' => $email,
            'role' => $role,
            'status' => $status
        ];
        
        if (!empty($password)) {
            $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $userModel->update($id, $userData);

        // Step B: Update 'tsrs' sub-table
        if ($staffRecord) {
            $tsrModel->update($staffRecord['id'], [
                'full_name' => $fullName,
                'employee_id' => $empId
            ]);
        }

        return redirect()->back()->with('success', 'Staff Account updated successfully.');
    }

    /**
     * Delete a TSR account and its related user record
     */
    public function delete($id)
    {
        $userModel = new UserModel();
        $tsrModel = new TsrModel();

        // Verify the user is part of the internal staff hierarchy before deleting
        $user = $userModel->find($id);
        $staffRoles = ['tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'];
        
        if ($user && in_array($user['role'], $staffRoles)) {
            // Delete the related tsrs sub-record first
            $tsrModel->where('user_id', $id)->delete();

            if ($userModel->delete($id)) {
                return redirect()->back()->with('success', 'Staff account deleted successfully.');
            }
        }

        return redirect()->back()->with('error', 'Failed to delete Staff account.');
    }
}