<?php

use yii\db\Migration;

/**
 * Class m250110_030958_add_columns_stock_id_qty_bit_to_inspecting_item_table
 */
class m250110_030958_add_columns_stock_id_qty_bit_to_inspecting_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->addColumn('{{%inspecting_item}}', 'stock_id', $this->integer()->null());

       
        $this->addColumn('{{%inspecting_item}}', 'qty_bit', $this->double()->null());

        
        $this->createIndex(
            '{{%idx-inspecting_item-stock_id}}',
            '{{%inspecting_item}}',
            'stock_id'
        );

        
        $this->addForeignKey(
            '{{%fk-inspecting_item-stock_id}}',
            '{{%inspecting_item}}',
            'stock_id',
            '{{%trn_stock_greige}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        $this->dropForeignKey(
            '{{%fk-inspecting_item-stock_id}}',
            '{{%inspecting_item}}'
        );

        
        $this->dropIndex(
            '{{%idx-inspecting_item-stock_id}}',
            '{{%inspecting_item}}'
        );

        
        $this->dropColumn('{{%inspecting_item}}', 'stock_id');

        
        $this->dropColumn('{{%inspecting_item}}', 'qty_bit');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250110_030958_add_columns_stock_id_qty_bit_to_inspecting_item_table cannot be reverted.\n";

        return false;
    }
    */
}