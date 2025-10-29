<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_kartu_proses_dyeing_item_log}}`.
 */
class m251027_073555_create_trn_kartu_proses_dyeing_item_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%trn_kartu_proses_dyeing_item_log}}', [
            'id' => $this->primaryKey(),

            // Relasi ke kartu proses utama
            'kartu_process_id' => $this->integer()->notNull(),

            // Relasi ke item roll
            'item_id' => $this->integer()->null(),

            // Relasi ke stock greige
            'stock_id' => $this->integer()->null(),

            // Jenis aksi: 1 = tambah, 2 = hapus, 3 = ubah qty
            'action_type' => $this->smallInteger()->notNull(),

            // Qty sebelum dan sesudah perubahan
            'qty_before' => $this->decimal(10, 2)->null(),
            'qty_after'  => $this->decimal(10, 2)->null(),

            // Alasan kenapa dilakukan perubahan
            'alasan' => $this->text()->null(),

            // Waktu dan pengguna
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        // ==== Index dan Foreign Key ====

        // ke tabel utama kartu proses dyeing
        $this->addForeignKey(
            'fk_itemlog_kartu_proses',
            '{{%trn_kartu_proses_dyeing_item_log}}',
            'kartu_process_id',
            '{{%trn_kartu_proses_dyeing}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // ke tabel item dyeing
        $this->addForeignKey(
            'fk_itemlog_item',
            '{{%trn_kartu_proses_dyeing_item_log}}',
            'item_id',
            '{{%trn_kartu_proses_dyeing_item}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // ke tabel stock greige
        $this->addForeignKey(
            'fk_itemlog_stock',
            '{{%trn_kartu_proses_dyeing_item_log}}',
            'stock_id',
            '{{%trn_stock_greige}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // ke tabel user (created_by & updated_by)
        $this->addForeignKey(
            'fk_itemlog_created_by',
            '{{%trn_kartu_proses_dyeing_item_log}}',
            'created_by',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_itemlog_updated_by',
            '{{%trn_kartu_proses_dyeing_item_log}}',
            'updated_by',
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
        $this->dropForeignKey('fk_itemlog_updated_by', '{{%trn_kartu_proses_dyeing_item_log}}');
        $this->dropForeignKey('fk_itemlog_created_by', '{{%trn_kartu_proses_dyeing_item_log}}');
        $this->dropForeignKey('fk_itemlog_stock', '{{%trn_kartu_proses_dyeing_item_log}}');
        $this->dropForeignKey('fk_itemlog_item', '{{%trn_kartu_proses_dyeing_item_log}}');
        $this->dropForeignKey('fk_itemlog_kartu_proses', '{{%trn_kartu_proses_dyeing_item_log}}');

        $this->dropTable('{{%trn_kartu_proses_dyeing_item_log}}');
    }
}