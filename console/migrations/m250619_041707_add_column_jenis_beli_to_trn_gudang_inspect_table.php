<?php

use yii\db\Migration;

/**
 * Class m250619_041707_add_column_jenis_beli_to_trn_gudang_inspect_table
 */
class m250619_041707_add_column_jenis_beli_to_trn_gudang_inspect_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_gudang_inspect', 'jenis_beli', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trn_gudang_inspect', 'jenis_beli');

        echo "m250619_041707_add_column_jenis_beli_to_trn_gudang_inspect_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250619_041707_add_column_jenis_beli_to_trn_gudang_inspect_table cannot be reverted.\n";

        return false;
    }
    */
}
