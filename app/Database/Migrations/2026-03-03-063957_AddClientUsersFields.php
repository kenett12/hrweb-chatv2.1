<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddClientUsersFields extends Migration
{
    public function up()
    {
        $fields = [
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'client_role' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'client_id');
        $this->forge->dropColumn('users', 'client_role');
    }
}
