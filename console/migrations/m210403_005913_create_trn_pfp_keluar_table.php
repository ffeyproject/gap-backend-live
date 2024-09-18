<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_pfp_keluar}}`.
 */
class m210403_005913_create_trn_pfp_keluar_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%trn_pfp_keluar}}', [
            'id'=>$this->primaryKey(),
            'no_urut' => $this->integer(),
            'no' => $this->string(255),
            'jenis' => $this->smallInteger()->defaultValue(1)->notNull()->comment('1=Pergantian, 2=Makloon, 3=Jual, 4=Sample, 5=lain-lain'),
            'destinasi' => $this->string()->comment('nama orang/divisi/instansi yang mengambil barang'),
            'no_referensi' => $this->string(),
            'date' => $this->date()->notNull(),
            'note' => $this->text(),
            'posted_at' => $this->integer(),
            'approved_at' => $this->integer(),
            'approved_by' => $this->integer()->comment('yang memerintahkan pengeluaran greige jika ada'),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('1=draft, 2=posted, 3=approved, 4=rejected'),
            'created_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%trn_pfp_keluar}}');
    }
}
