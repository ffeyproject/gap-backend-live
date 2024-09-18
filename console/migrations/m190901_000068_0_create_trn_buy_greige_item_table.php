<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000068_0_create_trn_buy_greige_item_table extends Migration
{
    const TABLE_NAME = "trn_buy_greige_item";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'greige_group_id' => $this->integer()->unsigned()->notNull(),
            'greige_id' => $this->integer()->unsigned()->notNull(),
            'buy_greige_id' => $this->integer()->unsigned()->notNull(),
            'qty' => $this->integer()->unsigned()->notNull()->comment('kuantiti sesuai degan satuan pada greige group (meter, yard, kg, pcs, dll..)'),
            'note' => $this->text(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige_group', self::TABLE_NAME, 'greige_group_id', 'mst_greige_group', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME, 'greige_id', 'mst_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_buy_greige', self::TABLE_NAME, 'buy_greige_id', 'trn_buy_greige', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
