<?php

use yii\db\Migration;

/**
 * Class m241128_080329_add_column_is_redyeing_to_trn_kartu_proses_dyeing_table
 */
class m241128_080329_add_column_is_redyeing_to_trn_kartu_proses_dyeing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_kartu_proses_dyeing', 'is_redyeing', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {   
        $this->dropColumn('trn_kartu_proses_dyeing', 'is_redyeing');
        echo "m241128_080329_add_column_is_redyeing_to_trn_kartu_proses_dyeing_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241128_080329_add_column_is_redyeing_to_trn_kartu_proses_dyeing_table cannot be reverted.\n";

        return false;
    }
    */
}
