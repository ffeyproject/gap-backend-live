<?php

use yii\db\Migration;

/**
 * Class m250627_064439_add_ket_defect_to_trn_gudang_inspect_item
 */
class m250627_064439_add_ket_defect_to_trn_gudang_inspect_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_gudang_inspect_item', 'ket_defect', $this->text()->null());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trn_gudang_inspect_item', 'ket_defect');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250627_064439_add_ket_defect_to_trn_gudang_inspect_item cannot be reverted.\n";

        return false;
    }
    */
}