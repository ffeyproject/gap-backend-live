<?php

use yii\db\Migration;

/**
 * Class m250212_032029_add_column_inspection_table_to_table_trn_inspecting
 */
class m250212_032029_add_column_inspection_table_to_table_trn_inspecting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_inspecting', 'inspection_table', $this->integer()->defaultValue(null)->null());
    }

    public function safeDown()
    {
        $this->dropColumn('trn_inspecting', 'inspection_table');
    }
}