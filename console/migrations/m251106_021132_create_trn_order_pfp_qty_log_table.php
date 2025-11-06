<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_order_pfp_qty_log}}`.
 */
class m251106_021132_create_trn_order_pfp_qty_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%trn_order_pfp_qty_log}}', [
            'id' => $this->primaryKey(),
            'order_pfp_id' => $this->integer()->notNull(),
            'user_id' => $this->integer(),
            'qty_tambah' => $this->decimal(12, 2)->notNull(),
            'total_meter' => $this->decimal(14, 2)->notNull(),
            'keterangan' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
        ]);

        // Index untuk mempercepat pencarian
        $this->createIndex(
            'idx-trn_order_pfp_qty_log-order_pfp_id',
            '{{%trn_order_pfp_qty_log}}',
            'order_pfp_id'
        );

        $this->createIndex(
            'idx-trn_order_pfp_qty_log-user_id',
            '{{%trn_order_pfp_qty_log}}',
            'user_id'
        );

        // Foreign key ke trn_order_pfp
        $this->addForeignKey(
            'fk-trn_order_pfp_qty_log-order_pfp_id',
            '{{%trn_order_pfp_qty_log}}',
            'order_pfp_id',
            '{{%trn_order_pfp}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Foreign key ke tabel user
        $this->addForeignKey(
            'fk-trn_order_pfp_qty_log-user_id',
            '{{%trn_order_pfp_qty_log}}',
            'user_id',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-trn_order_pfp_qty_log-order_pfp_id', '{{%trn_order_pfp_qty_log}}');
        $this->dropForeignKey('fk-trn_order_pfp_qty_log-user_id', '{{%trn_order_pfp_qty_log}}');
        $this->dropTable('{{%trn_order_pfp_qty_log}}');
    }
}