<?php

use yii\db\Migration;

/**
 * Handles adding column `jenis_inspek` to table `trn_inspecting`.
 */
class m250422_123456_add_column_jenis_inspek_to_trn_inspecting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_inspecting', 'jenis_inspek', $this->smallInteger()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trn_inspecting', 'jenis_inspek');
    }
}