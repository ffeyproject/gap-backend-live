<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000044_1_create_trn_buy_pfp_table extends Migration
{
    const TABLE_NAME = "trn_buy_pfp";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'greige_group_id' => $this->integer()->unsigned()->notNull(),
            'greige_id' => $this->integer()->unsigned()->notNull(),
            'no_document' => $this->string()->notNull(),
            'vendor' => $this->string()->notNull(),
            'note' => $this->text(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=Draft, 2=Posted, 3=Approved'),
            'date' => $this->date()->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
            'approval_id' => $this->integer()->unsigned(),
            'approval_time' => $this->integer()->unsigned(),
            'reject_note' => $this->text(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige_group', self::TABLE_NAME, 'greige_group_id', 'mst_greige_group', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME, 'greige_id', 'mst_greige', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_greige_group', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
