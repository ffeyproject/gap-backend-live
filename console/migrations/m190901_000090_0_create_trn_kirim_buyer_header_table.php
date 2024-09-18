<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000090_0_create_trn_kirim_buyer_header_table extends Migration
{
    const TABLE_NAME = "trn_kirim_buyer_header";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'customer_id' => $this->integer()->unsigned()->notNull(),
            'date' => $this->date()->notNull(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(255),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=Draft, 2=Posted'),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
            'pengirim' => $this->string(),
            'penerima' => $this->string(),
            'kepala_gudang' => $this->string(),
            'note' => $this->text(),
        ]);
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_customer', self::TABLE_NAME, 'customer_id', 'mst_customer', 'id');

        $this->execute('truncate table trn_kirim_buyer restart identity cascade');
        $this->dropColumn('trn_kirim_buyer', 'date');
        $this->dropColumn('trn_kirim_buyer', 'no_urut');
        $this->dropColumn('trn_kirim_buyer', 'no');
        $this->dropColumn('trn_kirim_buyer', 'status');
        $this->dropColumn('trn_kirim_buyer', 'created_at');
        $this->dropColumn('trn_kirim_buyer', 'created_by');
        $this->dropColumn('trn_kirim_buyer', 'updated_at');
        $this->dropColumn('trn_kirim_buyer', 'updated_by');
        $this->dropColumn('trn_kirim_buyer', 'penerima');
        $this->addColumn('trn_kirim_buyer', 'header_id', $this->integer()->unsigned()->notNull());
        $this->addForeignKey('fk_trn_kirim_buyer_header', 'trn_kirim_buyer', 'header_id', 'trn_kirim_buyer_header', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
