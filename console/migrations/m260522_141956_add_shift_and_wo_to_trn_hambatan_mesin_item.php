<?php

use yii\db\Migration;

/**
 * Class m260522_141956_add_shift_and_wo_to_trn_hambatan_mesin_item
 */
class m260522_141956_add_shift_and_wo_to_trn_hambatan_mesin_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%trn_hambatan_mesin_item}}', 'shift', $this->string(50)->null());
        $this->addColumn('{{%trn_hambatan_mesin_item}}', 'no_wo', $this->string(100)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%trn_hambatan_mesin_item}}', 'no_wo');
        $this->dropColumn('{{%trn_hambatan_mesin_item}}', 'shift');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260522_141956_add_shift_and_wo_to_trn_hambatan_mesin_item cannot be reverted.\n";

        return false;
    }
    */
}
