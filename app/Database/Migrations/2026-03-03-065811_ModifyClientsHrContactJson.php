<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyClientsHrContactJson extends Migration
{
    public function up()
    {
        $fields = [
            'hr_contact' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('clients', $fields);
    }

    public function down()
    {
        $fields = [
            'hr_contact' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ];
        $this->forge->modifyColumn('clients', $fields);
    }
}
