<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000046_1_create_kartu_proses_printing_item_table extends Migration
{
    const TABLE_NAME = "trn_kartu_proses_printing_item";

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
            'stock_id' => $this->integer()->unsigned()->notNull(),
            'panjang_m' => $this->integer()->unsigned()->notNull(),
            'mesin' => $this->string(),
            'note' => $this->text(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=Pending, 2=Valid'),
            'date' => $this->date()->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME, 'sc_id', 'trn_sc', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME, 'sc_greige_id', 'trn_sc_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME, 'mo_id', 'trn_mo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_wo', self::TABLE_NAME, 'wo_id', 'trn_wo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_kartu_proses', self::TABLE_NAME, 'kartu_process_id', 'trn_kartu_proses_printing', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_stock', self::TABLE_NAME, 'stock_id', 'trn_stock_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_created', self::TABLE_NAME, 'created_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_updated', self::TABLE_NAME, 'updated_by', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_updated', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_created', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_stock', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_kartu_proses', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_wo', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME);

        $this->dropTable(self::TABLE_NAME);
    }
}
