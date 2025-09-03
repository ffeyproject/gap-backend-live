<?php

use yii\db\Migration;

/**
 * Class m250903_020139_add_note_to_inspecting_mkl_bj
 */
class m250903_020139_add_note_to_inspecting_mkl_bj extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('{{%inspecting_mkl_bj}}', 'note', $this->text()->null()->comment('Catatan tambahan'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('{{%inspecting_mkl_bj}}', 'note');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250903_020139_add_note_to_inspecting_mkl_bj cannot be reverted.\n";

        return false;
    }
    */
}