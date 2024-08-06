<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTypeAndTradeForToBidsTable extends Migration
{
    public function up()
    {
        // Adiciona a coluna 'type' para distinguir entre compra (1) e troca (2)
        $this->forge->addColumn('bids', [
            'type' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 1,
                'comment' => '1=Compra, 2=Troca'
            ]
        ]);

        // Adiciona a coluna 'trade_for' para descrever o que é oferecido em troca
        $this->forge->addColumn('bids', [
            'trade_for' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'comment' => 'Descrição do que é oferecido em troca'
            ]
        ]);

        // Modifica a coluna 'value' para permitir NULL (já que trocas não têm valor)
        $this->forge->modifyColumn('bids', [
            'value' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
                'comment' => 'Valor da oferta (NULL para trocas)'
            ]
        ]);
    }

    public function down()
    {
        // Remove as colunas adicionadas
        $this->forge->dropColumn('bids', 'type');
        $this->forge->dropColumn('bids', 'trade_for');
        
        // Restaura a coluna 'value' para NOT NULL
        $this->forge->modifyColumn('bids', [
            'value' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => false
            ]
        ]);
    }
}
