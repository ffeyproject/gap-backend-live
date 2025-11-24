<?php

use yii\db\Migration;

/**
 * Class m251124_024036_change_panjang_m_to_double
 */
class m251124_024036_change_panjang_m_to_double extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE trn_kartu_proses_dyeing_item ALTER COLUMN panjang_m TYPE double precision');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
         $this->execute('ALTER TABLE trn_kartu_proses_dyeing_item ALTER COLUMN panjang_m TYPE integer');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251124_024036_change_panjang_m_to_double cannot be reverted.\n";

        return false;
    }
    */
}