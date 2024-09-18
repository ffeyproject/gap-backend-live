<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000082_0_create_trn_retur_buyer_item_table extends Migration
{
    const TABLE_NAME = "trn_retur_buyer_item";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'retur_buyer_id' => $this->integer()->unsigned()->notNull(),
            'qty' => $this->integer()->unsigned()->notNull(),
            'grade' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('mereferensi ke TrnStockGreige::gradeOptions()'),
            'note' => $this->text(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_retur_buyer', self::TABLE_NAME, 'retur_buyer_id', 'trn_retur_buyer', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
