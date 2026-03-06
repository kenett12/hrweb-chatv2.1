<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhotoToChatRooms extends Migration
{
    public function up()
    {
        $fields = [
            'room_image' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'approval_status'
            ],
        ];
        $this->forge->addColumn('chat_rooms', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('chat_rooms', 'room_image');
    }
}
