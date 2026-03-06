<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToTicketCategories extends Migration
{
    public function up()
    {
        $fields = [
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at'
            ],
        ];
        $this->forge->addColumn('ticket_categories', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('ticket_categories', 'updated_at');
    }
}
