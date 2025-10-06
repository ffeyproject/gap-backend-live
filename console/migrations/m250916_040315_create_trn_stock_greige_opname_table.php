<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_stock_greige_opname}}`.
 */
class m250916_040315_create_trn_stock_greige_opname_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%trn_stock_greige_opname}}', [
            'id' => $this->primaryKey(),
            'greige_group_id' => $this->integer()->notNull(),
            'greige_id' => $this->integer()->notNull(),
            'asal_greige' => $this->smallInteger()->notNull()->defaultValue(1),
            'no_lapak' => $this->string()->notNull(),
            'grade' => $this->smallInteger()->notNull(),
            'lot_lusi' => $this->string()->notNull(),
            'lot_pakan' => $this->string()->notNull(),
            'no_set_lusi' => $this->string()->notNull(),
            'panjang_m' => $this->double()->notNull()->defaultValue(0),
            'status_tsd' => $this->smallInteger()->notNull(),
            'no_document' => $this->string()->notNull(),
            'pengirim' => $this->string()->notNull(),
            'mengetahui' => $this->string()->notNull(),
            'note' => $this->text(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'date' => $this->date()->notNull(),
            'jenis_gudang' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_at' => $this->integer(),
            'updated_by' => $this->integer(),
            'nomor_wo' => $this->string(),
            'keputusan_qc' => $this->smallInteger(),
            'color' => $this->string(),
            'pfp_jenis_gudang' => $this->smallInteger(),
            'is_pemotongan' => $this->boolean()->notNull()->defaultValue(false),
            'is_hasil_mix' => $this->boolean()->notNull()->defaultValue(false),
            'trans_from' => $this->string(45),
            'id_from' => $this->integer(),
            'qr_code' => $this->string(45),
        ]);

        // creates index for column `greige_id`
        $this->createIndex(
            '{{%idx-trn_stock_greige_opname-greige_id}}',
            '{{%trn_stock_greige_opname}}',
            'greige_id'
        );

        // add foreign key for table `trn_stock_greige`
        $this->addForeignKey(
            '{{%fk-trn_stock_greige_opname-greige_id}}',
            '{{%trn_stock_greige_opname}}',
            'greige_id',
            '{{%trn_stock_greige}}',
            'id',
            'CASCADE'
        );
    }
    
    /**
     * {@inheritdoc}
     */
     public function safeDown()
    {
        // drops foreign key for table `trn_stock_greige`
        $this->dropForeignKey(
            '{{%fk-trn_stock_greige_opname-greige_id}}',
            '{{%trn_stock_greige_opname}}'
        );

        // drops index for column `greige_id`
        $this->dropIndex(
            '{{%idx-trn_stock_greige_opname-greige_id}}',
            '{{%trn_stock_greige_opname}}'
        );

        $this->dropTable('{{%trn_stock_greige_opname}}');
    }
    
}