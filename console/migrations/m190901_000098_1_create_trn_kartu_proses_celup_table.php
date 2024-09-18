<?php

use yii\db\Migration;

/**
 * ---
 */
class m190901_000098_1_create_trn_kartu_proses_celup_table extends Migration
{
    const TABLE_NAME = "trn_kartu_proses_celup";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'greige_group_id' => $this->integer()->unsigned()->notNull(),
            'greige_id' => $this->integer()->unsigned()->notNull(),
            'order_celup_id' => $this->integer()->unsigned()->notNull(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(),
            'no_proses' => $this->string(),
            'asal_greige' => $this->tinyInteger(1)->notNull()->comment('mereferensi ke model TrnStockGreige::asalGreigeOptions()'),
            'dikerjakan_oleh' => $this->string(),
            'lusi' => $this->string(),
            'pakan' => $this->string(),
            'note' => $this->text(),
            'date' => $this->date()->notNull(),
            'posted_at' => $this->integer()->unsigned(),
            'approved_at' => $this->integer()->unsigned(),
            'approved_by' => $this->integer()->unsigned(),
            'status' => $this->tinyInteger(1)->notNull()->comment('1=draft, 2=posted, 3=delivered (diterima produksi / bisa diinput progress proses), 4=approved (disetujui untuk diinspect), 5=inspected (Masuk gudang PFP), 6=Gagal Proses'),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
            'delivered_at' => $this->integer()->unsigned(),
            'delivered_by' => $this->integer()->unsigned(),
            'reject_notes' => $this->text(),
            'berat' => $this->string(),
            'lebar' => $this->string(),
            'k_density_lusi' => $this->string()->comment('density_lusi konstruksi greige'),
            'k_density_pakan' => $this->string()->comment('density_pakan konstruksi greige'),
            'gramasi' => $this->string(),
            'lebar_preset' => $this->string(),
            'lebar_finish' => $this->string(),
            'berat_finish' => $this->string(),
            't_density_lusi' => $this->string()->comment('density_lusi target hasil jadi'),
            't_density_pakan' => $this->string()->comment('density_pakan target hasil jadi'),
            'handling' => $this->string(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige_group', self::TABLE_NAME, 'greige_group_id', 'mst_greige_group', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME, 'greige_id', 'mst_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_order_celup', self::TABLE_NAME, 'order_celup_id', 'trn_order_celup', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_approved', self::TABLE_NAME, 'approved_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_delivered', self::TABLE_NAME, 'delivered_by', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
