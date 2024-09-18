<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%jual_ex_finish}}`.
 */
class m210224_032735_create_jual_ex_finish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%jual_ex_finish}}', [
            'id' => $this->bigPrimaryKey(),
            'no_urut' => $this->integer(),
            'no' => $this->string(255),
            'jenis_gudang' => $this->smallInteger()->notNull()->comment("1=Ex Retur Buyer, 2=Ex Gudang Jadi"),
            'customer_id' => $this->integer()->notNull(),
            'grade' => $this->smallInteger()->notNull()->comment("Mengacu pada TrnStockGreige::gradeOptions()"),
            'harga' => $this->decimal()->notNull(),
            'ongkir' => $this->smallInteger()->notNull()->comment('Yang dibebani ongkir. Mengacu pada TrnSc::ongkosAngkutOptions()'),
            'pembayaran' => $this->string()->notNull()->comment('Metode pembayaran'),
            'tanggal_pengiriman' => $this->date()->notNull(),
            'komisi' => $this->string()->notNull(),
            'jenis_order' => $this->smallInteger()->notNull()->comment("Mengacu pada TrnScGreige::processOptions()"),
            'is_resmi' => $this->boolean()->notNull()->defaultValue(false),
            'keterangan' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_jual_ex_finish_cust', '{{%jual_ex_finish}}', 'customer_id', 'mst_customer', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%jual_ex_finish}}');
    }
}
