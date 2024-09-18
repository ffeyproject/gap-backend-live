<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000044_2_create_trn_buy_pfp_item_table extends Migration
{
    const TABLE_NAME = "trn_buy_pfp_item";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'greige_group_id' => $this->integer()->unsigned()->notNull(),
            'greige_id' => $this->integer()->unsigned()->notNull(),
            'buy_pfp_id' => $this->integer()->unsigned()->notNull(),
            'panjang_m' => $this->integer()->unsigned()->notNull()->comment('kuantiti sesuai degan satuan pada greige group (meter, yard, kg, pcs, dll..)'),
            'note' => $this->text(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige_group', self::TABLE_NAME, 'greige_group_id', 'mst_greige_group', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME, 'greige_id', 'mst_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_buy_pfp', self::TABLE_NAME, 'buy_pfp_id', 'trn_buy_pfp', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_buy_pfp', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_greige_group', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
