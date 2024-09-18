<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000075_0_create_trn_kirim_makloon_item_table extends Migration
{
    const TABLE_NAME = "trn_kirim_makloon_item";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'kirim_makloon_id' => $this->integer()->unsigned()->notNull(),
            'stock_id' => $this->integer()->unsigned()->notNull(),
            'qty' => $this->integer()->unsigned()->notNull(),
            'note' => $this->text(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_kirim_makloon', self::TABLE_NAME, 'kirim_makloon_id', 'trn_kirim_makloon', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_stock', self::TABLE_NAME, 'stock_id', 'trn_gudang_jadi', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
