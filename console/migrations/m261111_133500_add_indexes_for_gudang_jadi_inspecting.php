<?php

use yii\db\Migration;

/**
 * Adds indexes on inspecting_item and trn_inspecting to optimize /gudang-jadi/index loading.
 */
class m261111_133500_add_indexes_for_gudang_jadi_inspecting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE INDEX IF NOT EXISTS idx_inspecting_item_inspecting_id_grade ON {{%inspecting_item}} (inspecting_id, grade)');
        $this->execute('CREATE INDEX IF NOT EXISTS idx_trn_inspecting_status_date ON {{%trn_inspecting}} (status, date DESC)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DROP INDEX IF EXISTS idx_inspecting_item_inspecting_id_grade');
        $this->execute('DROP INDEX IF EXISTS idx_trn_inspecting_status_date');
    }
}

