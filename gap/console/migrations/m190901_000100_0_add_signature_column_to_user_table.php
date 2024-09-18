<?php

use yii\db\Migration;

/**
 *
 */
class m190901_000100_0_add_signature_column_to_user_table extends Migration
{
    const TABLE_NAME = "user";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_NAME, 'signature', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
