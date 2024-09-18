<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%mutasi_ex_finish_alt_item}}`.
 */
class m210216_034007_add_status_column_to_mutasi_ex_finish_alt_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%mutasi_ex_finish_alt_item}}', 'status', $this->smallInteger(1)->notNull()->defaultValue(1)->comment('1=Stock, 2=Dijual'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
