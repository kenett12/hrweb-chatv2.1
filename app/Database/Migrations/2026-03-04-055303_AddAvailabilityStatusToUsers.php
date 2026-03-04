<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAvailabilityStatusToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'availability_status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'busy', 'offline'],
                'default'    => 'offline',
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'availability_status');
    }
}
