<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000063_0_create_trn_greige_keluar_table extends Migration
{
    const TABLE_NAME = "trn_greige_keluar";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id'=>$this->primaryKey()->unsigned(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(255),
            'date' => $this->date()->notNull(),
            'note' => $this->text(),
            'posted_at' => $this->integer()->unsigned(),
            'approved_at' => $this->integer()->unsigned(),
            'approved_by' => $this->integer()->unsigned()->comment('yang memerintahkan pengeluaran greige jika ada'),
            'jenis' => $this->smallInteger()->defaultValue(1)->notNull()->comment('1=sample, 2=jual, 3=makloon, 4=lain-lain'),
            'destinasi' => $this->string()->comment('nama orang/divisi/instansi yang mengambil barang'),
            'no_referensi' => $this->string(),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('1=draft, 2=posted, 3=approved, 4=rejected'),
            'created_at' => $this->integer()->unsigned(),
            'created_by' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
