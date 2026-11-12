<?php

use yii\db\Migration;

/**
 * Adds indexes on inspecting_mkl_bj_items and inspecting_mkl_bj to optimize /penerimaan-inspecting-mkl-bj/history.
 */
class m261111_135200_add_indexes_for_inspecting_mkl_bj extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE INDEX IF NOT EXISTS idx_inspecting_mkl_bj_items_inspecting_id_grade ON {{%inspecting_mkl_bj_items}} (inspecting_id, grade)');
        $this->execute('CREATE INDEX IF NOT EXISTS idx_inspecting_mkl_bj_status_tgl_kirim ON {{%inspecting_mkl_bj}} (status, tgl_kirim DESC)');
        $this->execute('CREATE INDEX IF NOT EXISTS idx_inspecting_mkl_bj_status_tgl_inspeksi ON {{%inspecting_mkl_bj}} (status, tgl_inspeksi DESC)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DROP INDEX IF EXISTS idx_inspecting_mkl_bj_items_inspecting_id_grade');
        $this->execute('DROP INDEX IF EXISTS idx_inspecting_mkl_bj_status_tgl_kirim');
        $this->execute('DROP INDEX IF EXISTS idx_inspecting_mkl_bj_status_tgl_inspeksi');
    }
}

