<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%trn_gudang_jadi}}`.
 */
class m210210_042734_add_grade_column_to_trn_gudang_jadi_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%trn_gudang_jadi}}', 'grade', $this->smallInteger()->notNull()->defaultValue(6)->comment('mengacu kepada TrnStockGreige::gradeOptions()'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
