<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000021_create_mo_color_table extends Migration
{
    const TABLE_NAME = "trn_mo_color";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'sc_id' => $this->integer()->unsigned()->notNull(),
            'sc_greige_id' => $this->integer()->unsigned()->notNull(),
            'mo_id' => $this->integer()->unsigned()->notNull(),
            'color' => $this->string(255)->notNull(),
            'qty' => $this->float()->unsigned()->notNull(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME, 'mo_id', 'trn_mo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME, 'sc_id', 'trn_sc', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME, 'sc_greige_id', 'trn_sc_greige', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME);

        $this->dropTable(self::TABLE_NAME);
    }
}
