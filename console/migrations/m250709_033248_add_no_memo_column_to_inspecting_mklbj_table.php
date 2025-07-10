<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%inspecting_mkl_bj}}`.
 */
class m250709_033248_add_no_memo_column_to_inspecting_mklbj_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%inspecting_mkl_bj}}', 'no_memo', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%inspecting_mkl_bj}}', 'no_memo');
    }
}