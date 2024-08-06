<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyInvitesTableMakeUserInvitedNullable extends Migration
{
    public function up()
    {
        // Make user_invited field nullable
        $this->forge->modifyColumn('invites', [
            'user_invited' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true, // Make it nullable
            ],
        ]);
    }

    public function down()
    {
        // Revert user_invited field to NOT NULL
        $this->forge->modifyColumn('invites', [
            'user_invited' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false, // Make it NOT NULL again
            ],
        ]);
    }
}
