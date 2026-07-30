<?php

use yii\db\Migration;

/**
 * Class m260730_084500_add_is_inspector_column_to_user_table
 */
class m260730_084500_add_is_inspector_column_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'is_inspector', $this->boolean()->defaultValue(false)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'is_inspector');
    }
}
