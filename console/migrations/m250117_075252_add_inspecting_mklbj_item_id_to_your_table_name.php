<?php

use yii\db\Migration;

/**
 * Class m230117_123456_add_inspecting_mklbj_item_id_to_your_table_name
 */
class m230117_123456_add_inspecting_mklbj_item_id_to_your_table_name extends Migration
{
    public function safeUp()
    {
        $this->addColumn('defect_inspecting_items', 'inspecting_mklbj_item_id', $this->integer()->null()->after('column_name')); // Ganti column_name dengan kolom sebelumnya
        $this->addForeignKey(
            'fk-defect_inspecting_items-inspecting_mklbj_item_id', 
            'defect_inspecting_items',                            
            'inspecting_mklbj_item_id',                   
            'inspecting_mkl_bj_items',                    
            'id',                                         
            'SET NULL',                                   
            'CASCADE'                                     
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-defect_inspecting_items-inspecting_mklbj_item_id', 'defect_inspecting_items');
        $this->dropColumn('defect_inspecting_items', 'inspecting_mklbj_item_id');
    }
}