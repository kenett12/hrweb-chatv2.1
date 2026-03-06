<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceTicketsAttachmentsAndLinks extends Migration
{
    public function up()
    {
        $fields = [
            'attachments'    => ['type' => 'TEXT', 'null' => true, 'after' => 'attachment'],
            'external_links' => ['type' => 'TEXT', 'null' => true, 'after' => 'attachments'],
        ];

        $this->forge->addColumn('tickets', $fields);
        $this->forge->addColumn('ticket_replies', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tickets', ['attachments', 'external_links']);
        $this->forge->dropColumn('ticket_replies', ['attachments', 'external_links']);
    }
}
