<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingFieldsToDealsTable extends Migration
{
    public function up()
    {
        // Check if user_id column exists before adding it
        if (!$this->db->fieldExists('user_id', 'deals')) {
            $this->forge->addColumn('deals', [
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => false,
                    'after' => 'id'
                ]
            ]);
            
            $this->forge->addKey('user_id');
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        }

        if (!$this->db->fieldExists('status', 'deals')) {
            $this->forge->addColumn('deals', [
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['active', 'pending', 'completed', 'cancelled'],
                    'default' => 'active',
                    'null' => false,
                    'after' => 'urgency_limit_date'
                ]
            ]);
        }
    }

    public function down()
    {
        // Drop foreign key first if it exists
        try {
            $this->forge->dropForeignKey('deals', 'deals_user_id_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist, ignore error
        }
        
        // Drop columns if they exist
        if ($this->db->fieldExists('user_id', 'deals')) {
            $this->forge->dropColumn('deals', 'user_id');
        }
        
        if ($this->db->fieldExists('status', 'deals')) {
            $this->forge->dropColumn('deals', 'status');
        }
    }
}
