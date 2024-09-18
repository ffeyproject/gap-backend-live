<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000032_1_create_kartu_proses_maklon_item_table extends Migration
{
    const TABLE_NAME = "trn_kartu_proses_maklon_item";

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
            'wo_id' => $this->integer()->unsigned()->notNull(),
            'kartu_process_id' => $this->integer()->unsigned()->notNull(),
            'qty' => $this->smallInteger()->notNull()->comment('Bukan panjang dalam meter, melainkan panjang/berat dalam unit greige group'),
            'note' => $this->text(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME, 'sc_id', 'trn_sc', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME, 'sc_greige_id', 'trn_sc_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME, 'mo_id', 'trn_mo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_wo', self::TABLE_NAME, 'wo_id', 'trn_wo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_kartu_proses', self::TABLE_NAME, 'kartu_process_id', 'trn_kartu_proses_maklon', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_kartu_proses', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_wo', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
