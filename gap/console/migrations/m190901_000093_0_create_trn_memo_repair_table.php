<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000093_0_create_trn_memo_repair_table extends Migration
{
    const TABLE_NAME = "trn_memo_repair";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'retur_buyer_id' => $this->integer()->unsigned()->notNull(),
            'sc_id' => $this->integer()->unsigned(),
            'sc_greige_id' => $this->integer(),
            'mo_id' => $this->integer()->unsigned(),
            'wo_id' => $this->integer()->unsigned(),
            'date' => $this->date()->notNull(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(255),
            'note' => $this->text(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=Draft, 2=Sedang Repair, 3=Selesai Repair, 4=Mutasi Ke Gudang Jadi'),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
            'mutasi_at' => $this->integer()->unsigned()->comment('dimutasi ke gudang jadi pada'),
            'mutasi_by' => $this->integer()->unsigned(),
            'mutasi_note' => $this->text()->comment('catatan mutasi ke gudang jadi'),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_retur_buyer', self::TABLE_NAME, 'retur_buyer_id', 'trn_retur_buyer', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME, 'sc_id', 'trn_sc', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME, 'sc_greige_id', 'trn_sc_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME, 'mo_id', 'trn_mo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_wo', self::TABLE_NAME, 'wo_id', 'trn_wo', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
