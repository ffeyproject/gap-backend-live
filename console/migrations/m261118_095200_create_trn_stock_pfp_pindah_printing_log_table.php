<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_stock_pfp_pindah_printing_log}}`.
 */
class m261118_095200_create_trn_stock_pfp_pindah_printing_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%trn_stock_pfp_pindah_printing_log}}', [
            'id' => $this->primaryKey(),
            'stock_id' => $this->integer()->notNull(),
            'greige_id' => $this->integer()->notNull(),
            'panjang_m' => $this->double()->notNull()->defaultValue(0),
            'jenis_printing_asal' => $this->smallInteger()->null(),
            'jenis_printing_tujuan' => $this->smallInteger()->notNull(),
            'note' => $this->text()->null(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex(
            'idx_sppl_stock_id',
            '{{%trn_stock_pfp_pindah_printing_log}}',
            'stock_id'
        );

        $this->createIndex(
            'idx_sppl_greige_id',
            '{{%trn_stock_pfp_pindah_printing_log}}',
            'greige_id'
        );

        $this->createIndex(
            'idx_sppl_created_at',
            '{{%trn_stock_pfp_pindah_printing_log}}',
            'created_at'
        );

        $this->addForeignKey(
            'fk_sppl_stock',
            '{{%trn_stock_pfp_pindah_printing_log}}',
            'stock_id',
            '{{%trn_stock_greige}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_sppl_greige',
            '{{%trn_stock_pfp_pindah_printing_log}}',
            'greige_id',
            '{{%mst_greige}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_sppl_created_by',
            '{{%trn_stock_pfp_pindah_printing_log}}',
            'created_by',
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
        $this->dropForeignKey('fk_sppl_created_by', '{{%trn_stock_pfp_pindah_printing_log}}');
        $this->dropForeignKey('fk_sppl_greige', '{{%trn_stock_pfp_pindah_printing_log}}');
        $this->dropForeignKey('fk_sppl_stock', '{{%trn_stock_pfp_pindah_printing_log}}');
        $this->dropIndex('idx_sppl_created_at', '{{%trn_stock_pfp_pindah_printing_log}}');
        $this->dropIndex('idx_sppl_greige_id', '{{%trn_stock_pfp_pindah_printing_log}}');
        $this->dropIndex('idx_sppl_stock_id', '{{%trn_stock_pfp_pindah_printing_log}}');
        $this->dropTable('{{%trn_stock_pfp_pindah_printing_log}}');
    }
}
