<?php

use yii\db\Migration;

/**
 * Class m260612_030504_add_qo_keterangan_to_trn_kartu_proses_dyeing
 */
class m260612_030504_add_qo_keterangan_to_trn_kartu_proses_dyeing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%trn_kartu_proses_dyeing}}', 'qo_keterangan', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%trn_kartu_proses_dyeing}}', 'qo_keterangan');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260612_030504_add_qo_keterangan_to_trn_kartu_proses_dyeing cannot be reverted.\n";

        return false;
    }
    */
}
