<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubcategoriesToTickets extends Migration
{
    public function up()
    {
        // Add parent_id to ticket_categories for hierarchy
        $this->forge->addColumn('ticket_categories', [
            'parent_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'id']
        ]);

        // Add subcategory to tickets table
        $this->forge->addColumn('tickets', [
            'subcategory' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'category']
        ]);

        // Seed some subcategories
        $db = \Config\Database::connect();
        
        // Find main category IDs
        $techSupport = $db->table('ticket_categories')->where('name', 'Technical Support')->get()->getRowArray();
        $billing = $db->table('ticket_categories')->where('name', 'Billing Inquiry')->get()->getRowArray();
        $account = $db->table('ticket_categories')->where('name', 'Account Access')->get()->getRowArray();

        $subcats = [];
        if ($techSupport) {
            $subcats[] = ['parent_id' => $techSupport['id'], 'name' => 'System Fix', 'created_at' => date('Y-m-d H:i:s')];
            $subcats[] = ['parent_id' => $techSupport['id'], 'name' => 'Network Issue', 'created_at' => date('Y-m-d H:i:s')];
            $subcats[] = ['parent_id' => $techSupport['id'], 'name' => 'Hardware Issue', 'created_at' => date('Y-m-d H:i:s')];
        }
        if ($billing) {
            $subcats[] = ['parent_id' => $billing['id'], 'name' => 'Invoice Issue', 'created_at' => date('Y-m-d H:i:s')];
            $subcats[] = ['parent_id' => $billing['id'], 'name' => 'Refund Request', 'created_at' => date('Y-m-d H:i:s')];
        }
        if ($account) {
            $subcats[] = ['parent_id' => $account['id'], 'name' => 'Password Reset', 'created_at' => date('Y-m-d H:i:s')];
            $subcats[] = ['parent_id' => $account['id'], 'name' => 'Locked Account', 'created_at' => date('Y-m-d H:i:s')];
        }

        if (!empty($subcats)) {
            $db->table('ticket_categories')->insertBatch($subcats);
        }
    }

    public function down()
    {
        // Remove subcategories first
        $db = \Config\Database::connect();
        $db->table('ticket_categories')->where('parent_id >', 0)->delete();

        $this->forge->dropColumn('ticket_categories', 'parent_id');
        $this->forge->dropColumn('tickets', 'subcategory');
    }
}
