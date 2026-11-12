<?php

use yii\db\Migration;

/**
 * Adds indexes on delivered_at for trn_inspecting and inspecting_mkl_bj to optimize Tanggal Penerimaan filtering.
 */
class m261111_140500_add_delivered_at_indexes_for_inspecting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE INDEX IF NOT EXISTS idx_trn_inspecting_status_delivered_at ON {{%trn_inspecting}} (status, delivered_at DESC)');
        $this->execute('CREATE INDEX IF NOT EXISTS idx_inspecting_mkl_bj_status_delivered_at ON {{%inspecting_mkl_bj}} (status, delivered_at DESC)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DROP INDEX IF EXISTS idx_trn_inspecting_status_delivered_at');
        $this->execute('DROP INDEX IF EXISTS idx_inspecting_mkl_bj_status_delivered_at');
    }
}

