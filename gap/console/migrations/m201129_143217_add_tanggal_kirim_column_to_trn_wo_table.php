<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%trn_wo}}`.
 */
class m201129_143217_add_tanggal_kirim_column_to_trn_wo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%trn_wo}}', 'tgl_kirim', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%trn_wo}}', 'tgl_kirim');
    }
}
