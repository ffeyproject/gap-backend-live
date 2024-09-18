<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kartu_process_pfp_process}}`.
 */
class m190901_000113_0_add_grade_column_to_mutasi_ex_finish_item_table extends Migration
{
    const TABLE_NAME = "mutasi_ex_finish_item";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn(self::TABLE_NAME, 'grade', $this->smallInteger()->notNull()->defaultValue(6)->comment('mengacu kepada TrnStockGreige::gradeOptions()'));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {

    }
}
