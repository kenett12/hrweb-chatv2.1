<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class SystemController extends BaseController
{
    /**
     * Display the System Management page
     */
    public function index()
    {
        // Security Check
        if ($this->session->get('role') !== 'superadmin') {
            return redirect()->to(base_url('login'))->with('error', 'Unauthorized access.');
        }

        $db = \Config\Database::connect();
        
        // Fetch current system settings
        $settingsQuery = $db->table('system_settings')->get()->getResultArray();
        
        // Format to key-value array for view
        $settings = [];
        foreach ($settingsQuery as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        $this->viewData['title'] = 'System Settings';
        $this->viewData['page_title'] = 'Global Configuration';
        $this->viewData['settings'] = $settings;

        return view('admin/pages/system_management', $this->viewData);
    }

    /**
     * Update a specific system setting
     */
    public function update()
    {
        if ($this->session->get('role') !== 'superadmin') {
            return redirect()->to(base_url('login'))->with('error', 'Unauthorized access.');
        }

        $db = \Config\Database::connect();
        $key = $this->request->getPost('setting_key');
        $val = $this->request->getPost('setting_value');

        if (empty($key) || $val === null) {
            return redirect()->back()->with('error', 'Invalid input provided.');
        }

        // Check if exists
        $exists = $db->table('system_settings')->where('setting_key', $key)->countAllResults() > 0;

        if ($exists) {
            $db->table('system_settings')
               ->where('setting_key', $key)
               ->update([
                   'setting_value' => $val,
                   'updated_at'    => date('Y-m-d H:i:s')
               ]);
        } else {
            $db->table('system_settings')->insert([
                'setting_key'   => $key,
                'setting_value' => $val,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->back()->with('success', 'System settings updated successfully.');
    }
}
