<?php

use yii\db\Migration;

/**
 * Class m250912_034603_add_column_operator_to_trn_gudang_stock_opname
 */
class m250912_034603_add_column_operator_to_trn_gudang_stock_opname extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('trn_gudang_stock_opname', 'pengirim', 'operator');    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('trn_gudang_stock_opname', 'operator', 'pengirim');
        echo "m250912_034603_add_column_operator_to_trn_gudang_stock_opname cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250912_034603_add_column_operator_to_trn_gudang_stock_opname cannot be reverted.\n";

        return false;
    }
    */
}
