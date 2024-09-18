<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kartu_process_pfp_process}}`.
 */
class m190901_000115_0_modify_trn_kartu_proses_printing_table extends Migration
{
    const TABLE_NAME = "trn_kartu_proses_printing";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('trn_kartu_proses_printing', 'no_limit_item', $this->boolean()->defaultValue(false)->notNull()->comment('Jumlah item roll pada kartu proses tidak dibatasi oleh qty per batch greige terkait.'));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {

    }
}
