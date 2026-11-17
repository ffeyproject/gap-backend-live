<?php

use common\models\ar\TrnKartuProsesPrinting;
use yii\db\Migration;

/**
 * Class m261117_094000_set_default_no_limit_item_printing
 */
class m261117_094000_set_default_no_limit_item_printing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Ubah default column di database menjadi true
        $this->execute('ALTER TABLE trn_kartu_proses_printing ALTER COLUMN no_limit_item SET DEFAULT true');

        // Update semua kartu proses printing yang berstatus DRAFT agar otomatis no_limit_item = true
        $this->update('trn_kartu_proses_printing', ['no_limit_item' => true], ['status' => TrnKartuProsesPrinting::STATUS_DRAFT]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('ALTER TABLE trn_kartu_proses_printing ALTER COLUMN no_limit_item SET DEFAULT false');
    }
}
