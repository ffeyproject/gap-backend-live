<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kartu_process_pfp_process}}`.
 */
class m190901_000106_0_add_no_limit_item_column_to_kartu_proses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_kartu_proses_celup', 'no_limit_item', $this->boolean()->defaultValue(false)->notNull()->comment('Jumlah item roll pada kartu proses tidak dibatasi oleh qty per batch greige terkait.'));
        $this->addColumn('trn_kartu_proses_dyeing', 'no_limit_item', $this->boolean()->defaultValue(false)->notNull()->comment('Jumlah item roll pada kartu proses tidak dibatasi oleh qty per batch greige terkait.'));
        $this->addColumn('trn_kartu_proses_pfp', 'no_limit_item', $this->boolean()->defaultValue(false)->notNull()->comment('Jumlah item roll pada kartu proses tidak dibatasi oleh qty per batch greige terkait.'));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {

    }
}
