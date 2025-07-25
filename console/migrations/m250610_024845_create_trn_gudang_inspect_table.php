<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_gudang_inspect}}`.
 */
class m250610_024845_create_trn_gudang_inspect_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%trn_gudang_inspect}}', [
            'id' => $this->primaryKey(),
            'greige_group_id' => $this->integer(),
            'greige_id' => $this->integer()->notNull(),
            'asal_greige' => $this->integer()->notNull(),
            'no_lapak' => $this->string(255)->notNull(),
            'lot_lusi' => $this->string(255)->notNull(),
            'lot_pakan' => $this->string(255)->notNull(),
            'status_tsd' => $this->integer()->notNull(),
            'no_document' => $this->string(255)->notNull(),
            'pengirim' => $this->string(255)->notNull(),
            'note' => $this->text()->null(),
            'status' => $this->integer()->notNull()->defaultValue(1),
            'date' => $this->date()->notNull(),

            'jenis_gudang' => $this->integer()->notNull()->defaultValue(1),
            'nomor_wo' => $this->string(255)->null(),
            'keputusan_qc' => $this->integer()->null(),
            'color' => $this->string(255)->null(),
            'pfp_jenis_gudang' => $this->integer()->null(),

            'is_pemotongan' => $this->boolean()->defaultValue(false),
            'is_hasil_mix' => $this->boolean()->defaultValue(false),

            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        // Foreign key contoh (optional)
        $this->addForeignKey(
            'fk-trn_gudang_inspect-greige_id',
            '{{%trn_gudang_inspect}}',
            'greige_id',
            '{{%mst_greige}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-trn_gudang_inspect-greige_group_id',
            '{{%trn_gudang_inspect}}',
            'greige_group_id',
            '{{%mst_greige_group}}',
            'id',
            'SET NULL'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-trn_gudang_inspect-greige_id', '{{%trn_gudang_inspect}}');
        $this->dropForeignKey('fk-trn_gudang_inspect-greige_group_id', '{{%trn_gudang_inspect}}');
        $this->dropTable('{{%trn_gudang_inspect}}');
    }
}
