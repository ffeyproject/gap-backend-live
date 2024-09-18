<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000041_1_create_trn_order_pfp_table extends Migration
{
    const TABLE_NAME = "trn_order_pfp";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'greige_group_id' => $this->integer()->unsigned()->notNull(),
            'greige_id' => $this->integer()->unsigned()->notNull(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(),
            'qty' => $this->decimal(17,2)->unsigned()->notNull(),
            'note' => $this->text(),
            'handling_id' => $this->integer()->unsigned()->notNull(),
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
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_handling', self::TABLE_NAME, 'handling_id', 'mst_handling', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_updated', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_created', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_greige_group', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
