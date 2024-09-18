<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mutasi_ex_finish_alt}}`.
 */
class m210210_040733_create_mutasi_ex_finish_alt_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mutasi_ex_finish_alt}}', [
            'id' => $this->primaryKey(),
            'no_referensi' => $this->string()->notNull(),
            'pemohon' => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_at' => $this->integer(),
            'updated_by' => $this->integer(),
            'no_urut' => $this->integer(),
            'no' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mutasi_ex_finish_alt}}');
    }
}
