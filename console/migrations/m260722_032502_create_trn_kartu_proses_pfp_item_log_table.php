<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_kartu_proses_pfp_item_log}}`.
 */
class m260722_032502_create_trn_kartu_proses_pfp_item_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%trn_kartu_proses_pfp_item_log}}', [
            'id' => $this->primaryKey(),
            'kartu_process_id' => $this->integer()->notNull(),
            'item_id'          => $this->integer()->null(),
            'stock_id'         => $this->integer()->null(),
            'action_type'      => $this->smallInteger()->notNull(),
            'qty_before'       => $this->decimal(10, 2)->null(),
            'qty_after'        => $this->decimal(10, 2)->null(),
            'alasan'           => $this->text()->null(),
            'created_at'       => $this->integer()->notNull(),
            'created_by'       => $this->integer()->null(),
            'updated_at'       => $this->integer()->null(),
            'updated_by'       => $this->integer()->null(),
        ]);

        $this->addForeignKey(
            'fk_pfp_itemlog_kartu_proses',
            '{{%trn_kartu_proses_pfp_item_log}}',
            'kartu_process_id',
            '{{%trn_kartu_proses_pfp}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_pfp_itemlog_stock',
            '{{%trn_kartu_proses_pfp_item_log}}',
            'stock_id',
            '{{%trn_stock_greige}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_pfp_itemlog_created_by',
            '{{%trn_kartu_proses_pfp_item_log}}',
            'created_by',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_pfp_itemlog_updated_by',
            '{{%trn_kartu_proses_pfp_item_log}}',
            'updated_by',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex(
            'idx_pfp_itemlog_item_id',
            '{{%trn_kartu_proses_pfp_item_log}}',
            'item_id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_pfp_itemlog_updated_by', '{{%trn_kartu_proses_pfp_item_log}}');
        $this->dropForeignKey('fk_pfp_itemlog_created_by', '{{%trn_kartu_proses_pfp_item_log}}');
        $this->dropForeignKey('fk_pfp_itemlog_stock', '{{%trn_kartu_proses_pfp_item_log}}');
        $this->dropForeignKey('fk_pfp_itemlog_kartu_proses', '{{%trn_kartu_proses_pfp_item_log}}');
        $this->dropIndex('idx_pfp_itemlog_item_id', '{{%trn_kartu_proses_pfp_item_log}}');
        $this->dropTable('{{%trn_kartu_proses_pfp_item_log}}');
    }
}
