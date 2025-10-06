<?php

use yii\db\Migration;

/**
 * Class m250916_080512_add_stock_opname_to_mst_greige_table
 */
class m250916_080512_add_stock_opname_to_mst_greige_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%mst_greige}}', 'stock_opname', $this->double()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%mst_greige}}', 'stock_opname');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250916_080512_add_stock_opname_to_mst_greige_table cannot be reverted.\n";

        return false;
    }
    */
}