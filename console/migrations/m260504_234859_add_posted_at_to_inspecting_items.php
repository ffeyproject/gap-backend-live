<?php

use yii\db\Migration;

/**
 * Class m260504_234859_add_posted_at_to_inspecting_items
 */
class m260504_234859_add_posted_at_to_inspecting_items extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('inspecting_item', 'posted_at', $this->date());
        $this->addColumn('inspecting_mkl_bj_items', 'posted_at', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('inspecting_item', 'posted_at');
        $this->dropColumn('inspecting_mkl_bj_items', 'posted_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260504_234859_add_posted_at_to_inspecting_items cannot be reverted.\n";

        return false;
    }
    */
}
