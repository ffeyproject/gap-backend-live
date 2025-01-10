<?php

use yii\db\Migration;

/**
 * Class m250212_032924_add_column_gsm_item_to_table_inspecting_item
 */
class m250212_032924_add_column_gsm_item_to_table_inspecting_item extends Migration
{
    /**
     * {@inheritdoc}
     */
     public function safeUp()
    {
        $this->addColumn('inspecting_item', 'gsm_item', $this->float()->null());
    }

    public function safeDown()
    {
        $this->dropColumn('inspecting_item', 'gsm_item');
    }
    
}