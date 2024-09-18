<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%gudang_jadi_mutasi}}`.
 */
class m220805_014621_create_gudang_jadi_mutasi_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%gudang_jadi_mutasi}}', [
            'id' => $this->bigPrimaryKey(),
            'no_urut' => $this->integer(),
            'nomor' => $this->string(),
            'date' => $this->date()->notNull(),
            'pengirim' => $this->string()->notNull(),
            'penerima' => $this->string()->notNull(),
            'kepala_gudang' => $this->string()->notNull(),
            'dept_tujuan' => $this->string()->notNull(),
            'note' => $this->text()->notNull()->defaultValue(''),
            'status' => $this->smallInteger()->notNull()->defaultValue('1')->comment('1=Draft, 2=Posted'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ]);
        $this->createIndex('gudang_jadi_mutasi_idx_date', '{{%gudang_jadi_mutasi}}', 'date');
        $this->createIndex('gudang_jadi_mutasi_idx_status', '{{%gudang_jadi_mutasi}}', 'status');

        $this->createTable('{{%gudang_jadi_mutasi_item}}', [
            'id' => $this->bigPrimaryKey(),
            'mutasi_id' => $this->bigInteger()->notNull(),
            'stock_id' => $this->bigInteger()->notNull()->comment('id stock gudang jadi.'),
            'note' => $this->text()->notNull()->defaultValue(''),
        ]);
        $this->addForeignKey('fk_gudang_jadi_mutasi_item_mutasi', '{{%gudang_jadi_mutasi_item}}', 'mutasi_id', '{{%gudang_jadi_mutasi}}', 'id', 'restrict');
        $this->addForeignKey('fk_gudang_jadi_mutasi_item_stock', '{{%gudang_jadi_mutasi_item}}', 'stock_id', '{{%trn_gudang_jadi}}', 'id', 'restrict');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%gudang_jadi_mutasi}}');
    }
}
