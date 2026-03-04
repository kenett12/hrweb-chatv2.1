<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUserRolesForFlowchart extends Migration
{
    public function up()
    {
        // 1. Modify the role column to include all the new staff tiers
        // The previous ENUM was ('superadmin','tsr','client')
        $fields = [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['superadmin', 'tsr', 'client', 'tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'],
                'default'    => 'client',
                'null'       => false,
            ],
        ];

        $this->forge->modifyColumn('users', $fields);

        // 2. Migrate existing 'tsr' accounts to 'tsr_level_1'
        $this->db->table('users')
                 ->where('role', 'tsr')
                 ->update(['role' => 'tsr_level_1']);

        // 3. Remove the old 'tsr' enum value completely
        $fieldsCleanup = [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['superadmin', 'client', 'tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'],
                'default'    => 'client',
                'null'       => false,
            ],
        ];
        
        $this->forge->modifyColumn('users', $fieldsCleanup);
    }

    public function down()
    {
        // In a rollback, map the new roles back to 'tsr'
        $this->db->table('users')
                 ->whereIn('role', ['tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'])
                 ->update(['role' => 'tsr']);
                 
        // Revert the ENUM column back to its original state
        $fields = [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['superadmin', 'tsr', 'client'],
                'default'    => 'client',
                'null'       => false,
            ],
        ];

        $this->forge->modifyColumn('users', $fields);
    }
}
