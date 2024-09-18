<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kartu_process_pfp_process}}`.
 */
class m190901_000112_0_add_berat_column_to_trn_kartu_proses_printing_table extends Migration
{
    const TABLE_NAME = "trn_kartu_proses_printing";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_NAME, 'berat', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {

    }
}
