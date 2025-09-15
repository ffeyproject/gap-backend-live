<?php

use yii\db\Migration;

/**
 * Class m250912_035555_add_some_column_to_trn_gudang_stock_opname
 */
class m250912_035555_add_some_column_to_trn_gudang_stock_opname extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_gudang_stock_opname', 'jenis_beli', $this->integer()->defaultValue(0));


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {   
        $this->dropColumn('trn_gudang_stock_opname', 'jenis_beli');
        echo "m250912_035555_add_some_column_to_trn_gudang_stock_opname cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250912_035555_add_some_column_to_trn_gudang_stock_opname cannot be reverted.\n";

        return false;
    }
    */
}
