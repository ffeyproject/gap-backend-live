<?php

use yii\db\Migration;

/**
 * Class m250912_033649_add_column_is_out_to_trn_gudang_stock_opname_item
 */
class m250912_033649_add_column_is_out_to_trn_gudang_stock_opname_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_gudang_stock_opname_item', 'is_out', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {   
        $this->dropColumn('trn_gudang_stock_opname_item', 'is_out');
        echo "m250912_033649_add_column_is_out_to_trn_gudang_stock_opname_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250912_033649_add_column_is_out_to_trn_gudang_stock_opname_item cannot be reverted.\n";

        return false;
    }
    */
}
