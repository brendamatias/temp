<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationChannelsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'application_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'channel_type' => [
                'type' => 'ENUM',
                'constraint' => ['webpush', 'email', 'sms'],
            ],
            'config' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'is_enabled' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('application_id');
        $this->forge->addKey(['application_id', 'channel_type'], false, true); // unique composite key
        $this->forge->addForeignKey('application_id', 'applications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notification_channels');
    }

    public function down()
    {
        $this->forge->dropTable('notification_channels');
    }
}
