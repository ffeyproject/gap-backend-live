<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%mst_mesin_proses}}`.
 */
class m260520_033903_add_model_mesin_column_to_mst_mesin_proses_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%mst_mesin_proses}}', 'model_mesin', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%mst_mesin_proses}}', 'model_mesin');
    }
}
