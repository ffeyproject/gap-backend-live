<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_kirim_makloon_item_v2}}`.
 */
class m210614_010948_create_trn_kirim_makloon_item_v2_table extends Migration
{
    const TABLE_NAME = "trn_kirim_makloon_v2_item";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'kirim_makloon_id' => $this->integer()->unsigned()->notNull(),
            'qty' => $this->integer()->unsigned()->notNull(),
            'note' => $this->text(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_kirim_makloon', self::TABLE_NAME, 'kirim_makloon_id', 'trn_kirim_makloon_v2', 'id', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
