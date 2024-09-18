<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%inspecting_mkl_bj}}`.
 */
class m210707_044920_add_extra_column_to_inspecting_mkl_bj_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%inspecting_mkl_bj}}', 'no_urut', $this->integer());
        $this->addColumn('{{%inspecting_mkl_bj}}', 'no', $this->string());
        $this->addColumn('{{%inspecting_mkl_bj}}', 'delivered_at', $this->integer());
        $this->addColumn('{{%inspecting_mkl_bj}}', 'delivered_by', $this->integer());
        $this->addColumn('{{%inspecting_mkl_bj}}', 'delivery_reject_note', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
