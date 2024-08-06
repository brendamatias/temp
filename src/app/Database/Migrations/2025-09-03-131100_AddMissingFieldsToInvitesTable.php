<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingFieldsToInvitesTable extends Migration
{
    public function up()
    {
        // Add missing fields that the view expects
        $this->forge->addColumn('invites', [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'accepted', 'rejected', 'expired'],
                'default' => 'pending',
                'after' => 'user_invited'
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status'
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'message'
            ],
            'max_uses' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 1,
                'after' => 'expires_at'
            ],
            'used_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
                'after' => 'max_uses'
            ]
        ]);
    }

    public function down()
    {
        // Remove the added fields
        $this->forge->dropColumn('invites', ['status', 'message', 'expires_at', 'max_uses', 'used_count']);
    }
}
