<?php

use yii\db\Migration;

/**
 * Class m250618_035529_add_column_is_out_to_trn_gudang_inspect_item
 */
class m250618_035529_add_column_is_out_to_trn_gudang_inspect_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_gudang_inspect_item', 'is_out', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {   
        $this->dropColumn('trn_gudang_inspect_item', 'is_out');
        echo "m250618_035529_add_column_is_out_to_trn_gudang_inspect_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250618_035529_add_column_is_out_to_trn_gudang_inspect_item cannot be reverted.\n";

        return false;
    }
    */
}
