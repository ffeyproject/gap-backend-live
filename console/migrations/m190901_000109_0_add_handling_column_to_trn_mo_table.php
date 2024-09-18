<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kartu_process_pfp_process}}`.
 */
class m190901_000109_0_add_handling_column_to_trn_mo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('trn_mo', 'handling', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {

    }
}
