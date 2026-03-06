<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCategoryToChatRooms extends Migration
{
    public function up()
    {
        $fields = [
            'category' => [
                'type'       => 'ENUM',
                'constraint' => ['general', 'confidential'],
                'default'    => 'general',
                'after'      => 'name'
            ],
            'approval_status' => [
                'type'       => 'ENUM',
                'constraint' => ['approved', 'pending', 'rejected'],
                'default'    => 'approved',
                'after'      => 'category'
            ],
        ];
        $this->forge->addColumn('chat_rooms', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('chat_rooms', 'category');
        $this->forge->dropColumn('chat_rooms', 'approval_status');
    }
}
