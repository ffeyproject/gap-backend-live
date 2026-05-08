<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%inspecting_dyeing_reject}}`.
 */
class m260507_080500_add_total_qty_to_inspecting_dyeing_reject_table extends Migration
{
    const TABLE_NAME = 'inspecting_dyeing_reject';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_NAME, 'total_qty', $this->decimal(12, 2)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(self::TABLE_NAME, 'total_qty');
    }
}
