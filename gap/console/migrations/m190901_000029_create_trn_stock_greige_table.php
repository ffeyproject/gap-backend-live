<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000029_create_trn_stock_greige_table extends Migration
{
    const TABLE_NAME = "trn_stock_greige";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'greige_group_id' => $this->integer()->unsigned()->notNull(),
            'greige_id' => $this->integer()->unsigned()->notNull(),
            'nomor_wo' => $this->string()->comment('Hanya berlaku untuk jenis gudang ex finish'),
            'asal_greige' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('1=Water Jet Loom, 2=Beli Lokal, 3=Rapier, 4=Beli Import, 5=Lain-lain'),
            'no_lapak' => $this->string()->notNull(),
            'grade' => $this->tinyInteger(2)->notNull()->comment('1=A,2=B,3=C,4=D,5=E'),
            'lot_lusi' => $this->string()->notNull(),
            'lot_pakan' => $this->string()->notNull(),
            'no_set_lusi' => $this->string()->notNull(),
            'panjang_m' => $this->float()->notNull()->defaultValue(0)->comment('kuantiti sesuai degan satuan pada greige group (meter, yard, kg, pcs, dll..)'),
            'status_tsd' => $this->tinyInteger(2)->notNull()->comment('1=sm(salur muda),2=st(salur tua),3=sa(salur abnormal'),
            'no_document' => $this->string()->notNull(),
            'pengirim' => $this->string()->notNull(),
            'mengetahui' => $this->string()->notNull(),
            'note' => $this->text(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=Pending, 2=Valid, 3=On Process Card, 4=Dipotong, 5=Dikeluarkan Dari Gudang'),
            'date' => $this->date()->notNull(),
            'jenis_gudang' => $this->tinyInteger(1)->notNull()->defaultValue(1)->unsigned()->comment('1=Gudang Fresh, 2=Gudang WIP, 3=Gudang PFP, 4=Gudang Ex Finish'),
            'keputusan_qc' => $this->smallInteger()->comment('mereferensi ke TrnReturBuyer::keputusanQcOptions() khusus ntuk jenis gudang ex finish'),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige_group', self::TABLE_NAME, 'greige_group_id', 'mst_greige_group', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME, 'greige_id', 'mst_greige', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_greige_group', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
