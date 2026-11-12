<?php

use yii\db\Migration;

/**
 * Adds indexes on trn_gudang_jadi for fast lookup by (trans_from, id_from) and (trans_from, date).
 */
class m261111_142000_add_indexes_for_trn_gudang_jadi_receipt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE INDEX IF NOT EXISTS idx_trn_gudang_jadi_trans_from_id_from ON {{%trn_gudang_jadi}} (trans_from, id_from)');
        $this->execute('CREATE INDEX IF NOT EXISTS idx_trn_gudang_jadi_trans_from_date ON {{%trn_gudang_jadi}} (trans_from, date DESC)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DROP INDEX IF EXISTS idx_trn_gudang_jadi_trans_from_id_from');
        $this->execute('DROP INDEX IF EXISTS idx_trn_gudang_jadi_trans_from_date');
    }
}

