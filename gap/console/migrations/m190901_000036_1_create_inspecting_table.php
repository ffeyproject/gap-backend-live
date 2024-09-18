<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000036_1_create_inspecting_table extends Migration
{
    const TABLE_NAME = "trn_inspecting";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'sc_id' => $this->integer()->unsigned(),
            'sc_greige_id' => $this->integer()->unsigned(),
            'mo_id' => $this->integer()->unsigned(),
            'wo_id' => $this->integer()->unsigned(),
            'kartu_process_dyeing_id' => $this->integer()->unsigned(),
            'kartu_process_maklon_id' => $this->integer()->unsigned(),
            'jenis_process' => $this->tinyInteger(1)->notNull()->comment('mereferensi ke TrnScGreige::jenisProsesOptions()'),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(255),
            'date' => $this->date()->notNull(),
            'tanggal_inspeksi' => $this->date()->notNull(),
            'no_lot' => $this->string(255),
            'kombinasi' => $this->string(255),
            'note' => $this->text(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=draft, 2=posted, 3=approved, 4=delivered'),
            'unit' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('Mengacu pada MstGreigeGroup::unitOptions()'),

            'jenis_gudang' => $this->tinyInteger(1)->comment('Mereferensi ke TrnGudangJadi::jenisGudangOptions()'),

            /*'send_to_buyer_nomor_urut' => $this->integer(),
            'send_to_buyer_nomor' => $this->string(),
            'send_to_buyer_at' => $this->integer(),
            'send_to_buyer_by' => $this->integer(),
            'send_to_buyer_penerima' => $this->string(),
            'send_to_buyer_note' => $this->string(),
            'send_to_buyer_nama_kain' => $this->string(),
            'is_send_to_vendor' => $this->boolean()->notNull()->defaultValue(false),
            'vendor_id' => $this->integer(),
            'send_to_vendor_nomor_urut' => $this->integer(),
            'send_to_vendor_nomor' => $this->string(),
            'send_to_vendor_at' => $this->integer(),
            'send_to_vendor_by' => $this->integer(),
            'send_to_vendor_penerima' => $this->string(),
            'send_to_vendor_note' => $this->string(),
            'get_from_vendor_at' => $this->integer(),
            'get_from_vendor_by' => $this->integer(),
            'get_from_vendor_pengirim' => $this->string(),
            'get_from_vendor_note' => $this->string(),*/

            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
            'approved_at' => $this->integer()->unsigned(),
            'approved_by' => $this->integer()->unsigned(),
            'approval_reject_note' => $this->text(),
            'delivered_at' => $this->integer()->unsigned(),
            'delivered_by' => $this->integer()->unsigned(),
            'delivery_reject_note' => $this->text(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME, 'sc_id', 'trn_sc', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME, 'sc_greige_id', 'trn_sc_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME, 'mo_id', 'trn_mo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_wo', self::TABLE_NAME, 'wo_id', 'trn_wo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_kartu_process_dyeing', self::TABLE_NAME, 'kartu_process_dyeing_id', 'trn_kartu_proses_dyeing', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_kartu_process_maklon', self::TABLE_NAME, 'kartu_process_maklon_id', 'trn_kartu_proses_maklon', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_created', self::TABLE_NAME, 'created_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_updated', self::TABLE_NAME, 'updated_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_approved', self::TABLE_NAME, 'approved_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_delivered', self::TABLE_NAME, 'delivered_by', 'user', 'id');
        //$this->addForeignKey('fk_'.self::TABLE_NAME.'_vendor', self::TABLE_NAME, 'vendor_id', 'mst_vendor', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_delivered', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_approved', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_updated', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_created', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_kartu_process_maklon', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_kartu_process_dyeing', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_wo', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
