<?php

use yii\db\Migration;

/**
 * Class m241114_075346_add_column_tunggu_marketing_and_toping_matching_to_trn_kartu_proses_dyeing_table
 */
class m241114_075346_add_column_tunggu_marketing_and_toping_matching_to_trn_kartu_proses_dyeing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_kartu_proses_dyeing', 'tunggu_marketing', $this->boolean()->defaultValue(false));
        $this->addColumn('trn_kartu_proses_dyeing', 'toping_matching', $this->boolean()->defaultValue(false));
        $this->addColumn('trn_kartu_proses_dyeing', 'date_toping_matching', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {   
        $this->dropColumn('trn_kartu_proses_dyeing', 'tunggu_marketing');
        $this->dropColumn('trn_kartu_proses_dyeing', 'toping_matching');
        $this->dropColumn('trn_kartu_proses_dyeing', 'date_toping_matching');
        echo "m241114_075346_add_column_tunggu_marketing_and_toping_matching_to_trn_kartu_proses_dyeing_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241114_075346_add_column_tunggu_marketing_and_toping_matching_to_trn_kartu_proses_dyeing_table cannot be reverted.\n";

        return false;
    }
    */
}
