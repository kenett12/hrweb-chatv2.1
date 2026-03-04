<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyClientsUserIdNullable extends Migration
{
    public function up()
    {
        $fields = [
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ];

        $this->forge->modifyColumn('clients', $fields);
    }

    public function down()
    {
        $fields = [
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ];

        $this->forge->modifyColumn('clients', $fields);
    }
}
