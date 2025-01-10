<?php

use yii\db\Migration;

/**
 * Class m250212_042116_add_column_gsm_item_to_inspecting_mkl_bj_items
 */
class m250212_042116_add_column_gsm_item_to_inspecting_mkl_bj_items extends Migration
{
    /**
     * {@inheritdoc}
     */
     public function safeUp()
    {
        $this->addColumn('inspecting_mkl_bj_items', 'gsm_item', $this->double()->null());
    }

    public function safeDown()
    {
        $this->dropColumn('inspecting_mkl_bj_items', 'gsm_item');
    }
}