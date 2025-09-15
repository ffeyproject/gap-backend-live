<?php

use yii\db\Migration;

/**
 * Class m250912_033910_add_column_ket_defect_to_trn_gudang_stock_opname_item
 */
class m250912_033910_add_column_ket_defect_to_trn_gudang_stock_opname_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_gudang_stock_opname_item', 'ket_defect', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {   
        $this->dropColumn('trn_gudang_stock_opname_item', 'ket_defect');
        echo "m250912_033910_add_column_ket_defect_to_trn_gudang_stock_opname_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250912_033910_add_column_ket_defect_to_trn_gudang_stock_opname_item cannot be reverted.\n";

        return false;
    }
    */
}
