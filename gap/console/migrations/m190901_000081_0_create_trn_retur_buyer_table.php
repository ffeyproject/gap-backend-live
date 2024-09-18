<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000081_0_create_trn_retur_buyer_table extends Migration
{
    const TABLE_NAME = "trn_retur_buyer";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'jenis_gudang' => $this->tinyInteger(1)->notNull()->comment('Mereferensi ke TrnGudangJadi::jenisGudangOptions()'),
            'customer_id' => $this->integer()->unsigned()->notNull(),
            'sc_id' => $this->integer()->unsigned(),
            'sc_greige_id' => $this->integer()->unsigned(),
            'mo_id' => $this->integer()->unsigned(),
            'wo_id' => $this->integer()->unsigned()->notNull(),
            'date' => $this->date()->notNull(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(255),
            'unit' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('Mengacu pada MstGreigeGroup::unitOptions()'),

            'no_document' => $this->string()->comment('nomor surat jalan retur dari customer'),
            'penanggungjawab' => $this->string()->comment('nama penanggungjawab'),
            'nama_qc' => $this->string()->comment('nama petugas qc'),
            'date_document' => $this->date()->comment('tanggal surat jalan retur dari customer'),
            'keputusan_qc' => $this->smallInteger()->comment('1=Retur belum diperiksa 2=Retur tidak diterima karena barang bagus 3=Retur diterima, tapi barang dapat diperbaiki 4=Retur diterima, tapi barang tidak dapat diperbaiki'),

            'note' => $this->text(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=Draft, 2=Posted'),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
            'pengirim' => $this->string(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME, 'sc_id', 'trn_sc', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME, 'sc_greige_id', 'trn_sc_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME, 'mo_id', 'trn_mo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_wo', self::TABLE_NAME, 'wo_id', 'trn_wo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_customer', self::TABLE_NAME, 'customer_id', 'mst_customer', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
