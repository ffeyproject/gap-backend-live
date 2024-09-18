<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kartu_process_pfp_process}}`.
 */
class m190901_000099_0_modify_mst_handling_table extends Migration
{
    const TABLE_NAME = "mst_handling";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_NAME, 'keterangan', $this->text());
        $this->addColumn(self::TABLE_NAME, 'no_hanger', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
