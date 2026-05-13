<?php

use yii\db\Migration;

/**
 * Class m260513_062839_add_use_jetblack_to_mst_process_dyeing
 */
class m260513_062839_add_use_jetblack_to_mst_process_dyeing extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%mst_process_dyeing}}', 'use_jetblack', $this->boolean()->notNull()->defaultValue(false));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%mst_process_dyeing}}', 'use_jetblack');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260513_062839_add_use_jetblack_to_mst_process_dyeing cannot be reverted.\n";

        return false;
    }
    */
}
