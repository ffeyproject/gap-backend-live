<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kartu_process_pfp_process}}`.
 */
class m190901_000110_0_add_no_lab_dip_column_to_trn_mo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('trn_mo', 'no_lab_dip', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {

    }
}
