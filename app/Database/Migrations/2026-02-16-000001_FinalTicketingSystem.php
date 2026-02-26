<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FinalTicketingSystem extends Migration
{
    public function up()
    {
        // 1. Ticket Categories Table
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ticket_categories', true);

        // 2. Ticket Replies Table (Conversation System)
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ticket_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'message'    => ['type' => 'TEXT'],
            'attachment' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ticket_replies', true);

        // Seed default categories
        $db = \Config\Database::connect();
        $db->table('ticket_categories')->insertBatch([
            ['name' => 'Technical Support', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Billing Inquiry',   'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Feature Request',   'created_at' => date('Y-m-d H:i:s')],
        ]);
    }

    public function down() { /* Drop logic */ }
}