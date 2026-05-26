<?php

use yii\db\Migration;

/**
 * Class m260526_075212_add_keterangan_to_mst_process_dyeing_table
 */
class m260526_075212_add_keterangan_to_mst_process_dyeing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mst_process_dyeing', 'keterangan', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mst_process_dyeing', 'keterangan');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260526_075212_add_keterangan_to_mst_process_dyeing_table cannot be reverted.\n";

        return false;
    }
    */
}
