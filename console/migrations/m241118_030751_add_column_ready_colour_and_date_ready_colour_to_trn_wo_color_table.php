<?php

use yii\db\Migration;

/**
 * Class m241118_030751_add_column_ready_colour_and_date_ready_colour_to_trn_wo_color_table
 */
class m241118_030751_add_column_ready_colour_and_date_ready_colour_to_trn_wo_color_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_wo_color', 'ready_colour', $this->boolean()->defaultValue(false));
        $this->addColumn('trn_wo_color', 'date_ready_colour', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {   
        $this->dropColumn('trn_wo_color', 'ready_colour');
        $this->dropColumn('trn_wo_color', 'date_ready_colour');
        echo "m241118_030751_add_column_ready_colour_and_date_ready_colour_to_trn_wo_color_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241118_030751_add_column_ready_colour_and_date_ready_colour_to_trn_wo_color_table cannot be reverted.\n";

        return false;
    }
    */
}
