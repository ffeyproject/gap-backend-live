<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000063_1_create_trn_greige_keluar_item_table extends Migration
{
    const TABLE_NAME = "trn_greige_keluar_item";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'greige_keluar_id' => $this->integer()->unsigned()->notNull(),
            'stock_greige_id' => $this->integer()->unsigned()->notNull(),
            'note' => $this->text()
        ]);

        $this->addPrimaryKey('trn_greige_keluar_item_pkey', 'trn_greige_keluar_item', ['greige_keluar_id', 'stock_greige_id']);
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_stock_greige', self::TABLE_NAME, 'stock_greige_id', 'trn_stock_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige_keluar', self::TABLE_NAME, 'greige_keluar_id', 'trn_greige_keluar', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
