<?php

use yii\db\Migration;

/**
 * Class m250212_041508_add_column_inpsection_table_to_inspecting_mkl_bj
 */
class m250212_041508_add_column_inpsection_table_to_inspecting_mkl_bj extends Migration
{
    /**
     * {@inheritdoc}
     */
     public function safeUp()
    {
        $this->addColumn('inspecting_mkl_bj', 'inpsection_table', $this->integer()->null()->defaultValue(null));
    }

    public function safeDown()
    {
        $this->dropColumn('inspecting_mkl_bj', 'inpsection_table');
    }
}