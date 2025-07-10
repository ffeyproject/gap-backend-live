<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%inspecting_mkl_bj_items}}`.
 */
class m250710_031556_add_qty_bit_column_to_inspecting_mklbj_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%inspecting_mkl_bj_items}}', 'qty_bit', $this->double()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%inspecting_mkl_bj_items}}', 'qty_bit');
    }
}