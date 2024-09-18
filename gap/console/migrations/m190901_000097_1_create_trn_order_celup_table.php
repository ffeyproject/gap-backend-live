<?php

use yii\db\Migration;

/**
 * ---
 */
class m190901_000097_1_create_trn_order_celup_table extends Migration
{
    const TABLE_NAME = "trn_order_celup";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'sc_id' => $this->integer()->unsigned()->notNull(),
            'greige_group_id' => $this->integer()->unsigned()->notNull(),
            'greige_id' => $this->integer()->unsigned()->notNull(),
            'handling_id' => $this->integer()->unsigned()->notNull(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(),
            'qty' => $this->decimal(17,2)->unsigned()->notNull(),
            'note' => $this->text(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=Draft, 2=Posted, 3=Processed'),
            'date' => $this->date()->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige_group', self::TABLE_NAME, 'greige_group_id', 'mst_greige_group', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME, 'greige_id', 'mst_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_created', self::TABLE_NAME, 'created_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_updated', self::TABLE_NAME, 'updated_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME, 'sc_id', 'trn_sc', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_handling', self::TABLE_NAME, 'handling_id', 'mst_handling', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
