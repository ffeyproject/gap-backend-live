<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000084_0_create_trn_beli_kain_jadi_item_table extends Migration
{
    const TABLE_NAME = "trn_beli_kain_jadi_item";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'beli_kain_jadi_id' => $this->integer()->unsigned()->notNull(),
            'qty' => $this->integer()->unsigned()->notNull(),
            'note' => $this->text(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_beli_kain_jadi', self::TABLE_NAME, 'beli_kain_jadi_id', 'trn_beli_kain_jadi', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
