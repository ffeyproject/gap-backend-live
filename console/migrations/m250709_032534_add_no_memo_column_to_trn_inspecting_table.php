<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%trn_inspecting}}`.
 */
class m250709_032534_add_no_memo_column_to_trn_inspecting_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%trn_inspecting}}', 'no_memo', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%trn_inspecting}}', 'no_memo');
    }
}