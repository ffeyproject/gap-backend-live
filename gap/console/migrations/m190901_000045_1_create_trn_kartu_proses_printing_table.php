<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000045_1_create_trn_kartu_proses_printing_table extends Migration
{
    const TABLE_NAME = "trn_kartu_proses_printing";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'sc_id' => $this->integer()->unsigned()->notNull(),
            'sc_greige_id' => $this->integer()->unsigned()->notNull(),
            'mo_id' => $this->integer()->unsigned()->notNull(),
            'wo_id' => $this->integer()->unsigned()->notNull(),
            'wo_color_id' => $this->integer()->unsigned()->notNull(),
            'kartu_proses_id' => $this->integer()->unsigned(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(),
            'no_proses' => $this->string(),
            'asal_greige' => $this->tinyInteger(1)->notNull()->comment('1=Water Jet Loom, 2=Beli, 3=Rapier'),
            'dikerjakan_oleh' => $this->string(),
            'lusi' => $this->string(),
            'pakan' => $this->string(),
            'note' => $this->text(),
            'date' => $this->date()->notNull(),
            'posted_at' => $this->integer()->unsigned(),
            'approved_at' => $this->integer()->unsigned(),
            'approved_by' => $this->integer()->unsigned(),
            'status' => $this->tinyInteger(1)->notNull()->comment('1=draft, 2=posted, 3=delivered (diterima produksi / bisa diinput progress proses), 4=approved (disetujui untuk diinspect), 5=inspected, 6=Ganti Greige (proses gagal dan dibuat memo pengantian greige), 7=Ganti Greige Linked (sudah dibuat kartu proses turunan nya), 8=Batal'),
            'kombinasi' => $this->string(),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
            'memo_pg' => $this->text()->comment('memo penggantian greige'),
            'memo_pg_at' => $this->integer()->unsigned(),
            'memo_pg_by' => $this->integer()->unsigned(),
            'memo_pg_no' => $this->string(255),
            'delivered_at' => $this->integer()->unsigned(),
            'delivered_by' => $this->integer()->unsigned(),
            'reject_notes' => $this->text(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME, 'sc_id', 'trn_sc', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME, 'sc_greige_id', 'trn_sc_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME, 'mo_id', 'trn_mo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_wo', self::TABLE_NAME, 'wo_id', 'trn_wo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_created', self::TABLE_NAME, 'created_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_updated', self::TABLE_NAME, 'updated_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_wo_color', self::TABLE_NAME, 'wo_color_id', 'trn_wo_color', 'id');

        //tambahkan field kartu_process_printing_id berserta relasinya ke tabel trn_inspecting
        $this->addColumn('trn_inspecting', 'kartu_process_printing_id', $this->integer()->unsigned());
        $this->addForeignKey('fk_trn_inspecting_kartu_process_printing', 'trn_inspecting', 'kartu_process_printing_id', self::TABLE_NAME, 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_wo', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_created', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_updated', self::TABLE_NAME);

        $this->dropForeignKey('fk_trn_inspecting_kartu_process_printing', 'trn_inspecting');
        $this->dropColumn('trn_inspecting', 'kartu_process_printing_id');

        $this->dropTable(self::TABLE_NAME);
    }
}
