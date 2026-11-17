<?php

use yii\db\Migration;

/**
 * Class m261117_090500_change_panjang_m_printing_to_double
 */
class m261117_090500_change_panjang_m_printing_to_double extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE trn_kartu_proses_printing_item ALTER COLUMN panjang_m TYPE double precision');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('ALTER TABLE trn_kartu_proses_printing_item ALTER COLUMN panjang_m TYPE integer');
    }
}
