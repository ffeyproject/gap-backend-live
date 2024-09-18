<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000012_create_mst_handling_table extends Migration
{
    const TABLE_NAME = "mst_handling";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'greige_id' => $this->integer()->unsigned()->notNull(),
            'name' => $this->string()->notNull(),
            'lebar_preset' => $this->string()->notNull(),
            'lebar_finish' => $this->string()->notNull(),
            'berat_finish' => $this->string()->notNull(),
            'densiti_lusi' => $this->string()->notNull(),
            'densiti_pakan' => $this->string()->notNull(),
            'buyer_ids' => $this->text(),
            'ket_washing' => $this->boolean()->notNull()->defaultValue(false),
            'ket_wr' => $this->boolean()->notNull()->defaultValue(false),
            'berat_persiapan' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->unsigned(),
            'created_by' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);

        $this->addForeignKey('fk_mst_handling_greige', self::TABLE_NAME, 'greige_id', 'mst_greige', 'id');

        //penyesuaian tabel trn_wo
        $this->addColumn('trn_wo', 'handling_id', $this->integer()->unsigned()->notNull());
        $this->addForeignKey('fk_trn_wo_handling', 'trn_wo', 'handling_id', self::TABLE_NAME, 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_mst_handling_greige', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
