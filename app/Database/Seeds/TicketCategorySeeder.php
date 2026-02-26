<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder for Ticket Categories
 * Populate the database with default support categories
 */
class TicketCategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Technical Support',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Billing Inquiry',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Account Access',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Feature Request',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'General Inquiry',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Using Query Builder to insert the data
        $this->db->table('ticket_categories')->insertBatch($data);
    }
}

//php spark db:seed TicketCategorySeeder