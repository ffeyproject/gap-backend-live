<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000061_0_create_trn_potong_greige_item_table extends Migration
{
    const TABLE_NAME = "trn_potong_greige_item";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'potong_greige_id' => $this->integer()->unsigned()->notNull(),
            'stock_greige_id' => $this->integer()->unsigned(),
            'panjang_m' => $this->float()->notNull(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_stock_greige', self::TABLE_NAME, 'stock_greige_id', 'trn_stock_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_potong_greige', self::TABLE_NAME, 'potong_greige_id', 'trn_potong_greige', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
