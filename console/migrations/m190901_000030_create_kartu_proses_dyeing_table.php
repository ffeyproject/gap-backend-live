<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000030_create_kartu_proses_dyeing_table extends Migration
{
    const TABLE_NAME = "trn_kartu_proses_dyeing";

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
            'kartu_proses_id' => $this->integer()->unsigned(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(),
            'asal_greige' => $this->tinyInteger(1)->notNull()->comment('1=Water Jet Loom, 2=Beli, 3=Rapier'),
            'dikerjakan_oleh' => $this->string(),
            'lusi' => $this->string(),
            'pakan' => $this->string(),
            'note' => $this->text(),
            'date' => $this->date()->notNull(),
            'posted_at' => $this->integer()->unsigned(),
            'approved_at' => $this->integer()->unsigned(),
            'approved_by' => $this->integer()->unsigned(),
            'delivered_at' => $this->integer()->unsigned(),
            'delivered_by' => $this->integer()->unsigned(),
            'reject_notes' => $this->text(),

            'berat' => $this->string(),
            'lebar' => $this->string(),
            'k_density_lusi' => $this->string()->comment('density_lusi konstruksi greige'),
            'k_density_pakan' => $this->string()->comment('density_pakan konstruksi greige'),
            'lebar_preset' => $this->string(),
            'lebar_finish' => $this->string(),
            'berat_finish' => $this->string(),
            't_density_lusi' =>$this->string()->comment('density_lusi target hasil jadi'),
            't_density_pakan' => $this->string()->comment('density_pakan target hasil jadi'),
            'handling' => $this->string(),
            'hasil_tes_gosok' => $this->string(),
            'wo_color_id' => $this->integer()->notNull(),

            'status' => $this->tinyInteger(1)->notNull()->comment('1=draft, 2=posted, 3=delivered (diterima produksi / bisa diinput progress proses), 4=approved (disetujui untuk diinspect), 5=inspected, 6=Ganti Greige (proses gagal dan dibuat memo pengantian greige), 7=Ganti Greige Linked (sudah dibuat kartu proses turunan nya), 8=Batal'),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),

            'memo_pg' => $this->text()->comment('memo penggantian greige'),
            'memo_pg_at' => $this->integer()->unsigned(),
            'memo_pg_by' => $this->integer()->unsigned(),
            'memo_pg_no' => $this->string(255),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME, 'sc_id', 'trn_sc', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME, 'sc_greige_id', 'trn_sc_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME, 'mo_id', 'trn_mo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_wo', self::TABLE_NAME, 'wo_id', 'trn_wo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_created', self::TABLE_NAME, 'created_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_updated', self::TABLE_NAME, 'updated_by', 'user', 'id');
        $this->addForeignKey('fk_trn_kartu_proses_dyeing_wo_color', self::TABLE_NAME, 'wo_color_id', 'trn_wo_color', 'id');
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
        $this->dropTable(self::TABLE_NAME);
    }
}
