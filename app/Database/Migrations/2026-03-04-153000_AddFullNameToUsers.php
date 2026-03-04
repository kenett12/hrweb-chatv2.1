<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFullNameToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'full_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'email'
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'full_name');
    }
}
