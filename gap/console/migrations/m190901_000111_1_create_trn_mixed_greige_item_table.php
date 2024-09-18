<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kartu_process_pfp_process}}`.
 */
class m190901_000111_1_create_trn_mixed_greige_item_table extends Migration
{
    const TABLE_NAME = "trn_mixed_greige_item";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'mix_id' => $this->integer()->unsigned()->notNull(),
            'stock_greige_id' => $this->integer()->unsigned()->notNull()
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_mix', self::TABLE_NAME, 'mix_id', 'trn_mixed_greige', 'id');
        $this->addPrimaryKey('trn_mixed_greige_item_pkey', self::TABLE_NAME, ['mix_id', 'stock_greige_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {

    }
}
