<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000078_0_create_trn_terima_makloon_process_item_table extends Migration
{
    const TABLE_NAME = "trn_terima_makloon_process_item";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'terima_makloon_id' => $this->integer()->unsigned()->notNull(),
            'qty' => $this->float()->notNull(),
            'note' => $this->text(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_terima_makloon', self::TABLE_NAME, 'terima_makloon_id', 'trn_terima_makloon_process', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
