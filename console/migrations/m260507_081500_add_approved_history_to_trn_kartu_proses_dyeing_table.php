<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%trn_kartu_proses_dyeing}}`.
 */
class m260507_081500_add_approved_history_to_trn_kartu_proses_dyeing_table extends Migration
{
    const TABLE_NAME = 'trn_kartu_proses_dyeing';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_NAME, 'approved_history', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(self::TABLE_NAME, 'approved_history');
    }
}
