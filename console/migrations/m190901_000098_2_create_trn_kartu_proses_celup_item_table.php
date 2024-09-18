<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000098_2_create_trn_kartu_proses_celup_item_table extends Migration
{
    const TABLE_NAME = "trn_kartu_proses_celup_item";

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
            'kartu_process_id' => $this->integer()->unsigned()->notNull(),
            'stock_id' => $this->integer()->unsigned()->notNull(),
            'panjang_m' => $this->integer()->unsigned()->notNull(),
            'mesin' => $this->string(),
            'tube' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('1=kiri, 2=kanan'),
            'note' => $this->text(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=Pending, 2=Valid'),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige_group', self::TABLE_NAME, 'greige_group_id', 'mst_greige_group', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME, 'greige_id', 'mst_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_order_celup', self::TABLE_NAME, 'order_celup_id', 'trn_order_celup', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_kartu_proses', self::TABLE_NAME, 'kartu_process_id', 'trn_kartu_proses_celup', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_stock', self::TABLE_NAME, 'stock_id', 'trn_stock_greige', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
