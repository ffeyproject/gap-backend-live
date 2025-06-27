<?php

use yii\db\Migration;

/**
 * Class m250627_063354_rename_pengirim_to_operator_in_trn_gudang_inspect
 */
class m250627_063354_rename_pengirim_to_operator_in_trn_gudang_inspect extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('trn_gudang_inspect', 'pengirim', 'operator');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('trn_gudang_inspect', 'operator', 'pengirim');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250627_063354_rename_pengirim_to_operator_in_trn_gudang_inspect cannot be reverted.\n";

        return false;
    }
    */
}