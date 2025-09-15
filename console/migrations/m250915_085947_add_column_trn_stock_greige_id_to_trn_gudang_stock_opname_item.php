<?php

use yii\db\Migration;

/**
 * Class m250915_085947_add_column_trn_stock_greige_id_to_trn_gudang_stock_opname_item
 */
class m250915_085947_add_column_trn_stock_greige_id_to_trn_gudang_stock_opname_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_gudang_stock_opname_item', 'trn_stock_greige_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {   
        $this->dropColumn('trn_gudang_stock_opname_item', 'trn_stock_greige_id');
        echo "m250915_085947_add_column_trn_stock_greige_id_to_trn_gudang_stock_opname_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250915_085947_add_column_trn_stock_greige_id_to_trn_gudang_stock_opname_item cannot be reverted.\n";

        return false;
    }
    */
}
