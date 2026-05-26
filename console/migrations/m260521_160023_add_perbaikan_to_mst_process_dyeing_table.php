<?php

use yii\db\Migration;

/**
 * Class m260521_160023_add_perbaikan_to_mst_process_dyeing_table
 */
class m260521_160023_add_perbaikan_to_mst_process_dyeing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mst_process_dyeing', 'perbaikan', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mst_process_dyeing', 'perbaikan');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260521_160023_add_perbaikan_to_mst_process_dyeing_table cannot be reverted.\n";

        return false;
    }
    */
}
