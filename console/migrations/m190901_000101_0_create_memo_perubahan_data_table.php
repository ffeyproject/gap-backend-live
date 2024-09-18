<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kartu_process_pfp_process}}`.
 */
class m190901_000101_0_create_memo_perubahan_data_table extends Migration
{
    const TABLE_NAME = "trn_memo_perubahan_data";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(),
            'description' => $this->text()->notNull(),
            'date' => $this->date()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('1=Draft 2=Posted'),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_creator', self::TABLE_NAME, 'created_by', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
