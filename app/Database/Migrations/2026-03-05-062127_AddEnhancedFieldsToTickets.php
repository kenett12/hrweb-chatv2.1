<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnhancedFieldsToTickets extends Migration
{
    public function up()
    {
        $fields = [
            'due_date' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'status'
            ],
            'fixed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'due_date'
            ],
            'dev_remarks_1' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'fixed_at'
            ],
            'support_remarks' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'dev_remarks_1'
            ],
            'dev_remarks_2' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'support_remarks'
            ],
            'reoccurrence_remarks' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'dev_remarks_2'
            ],
            'close_requested' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'reoccurrence_remarks'
            ],
            'feedback_rating' => [
                'type' => 'INT',
                'constraint' => 1,
                'null' => true,
                'after' => 'close_requested'
            ],
            'feedback_comment' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'feedback_rating'
            ],
        ];
        $this->forge->addColumn('tickets', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tickets', [
            'due_date', 'fixed_at', 'dev_remarks_1', 'support_remarks',
            'dev_remarks_2', 'reoccurrence_remarks', 'close_requested',
            'feedback_rating', 'feedback_comment'
        ]);
    }
}
