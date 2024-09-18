<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000094_0_modify_trn_inspecting_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('trn_inspecting', 'memo_repair_id', $this->integer()->unsigned());
        $this->addForeignKey('fk_trn_inspecting_memo_repair', 'trn_inspecting', 'memo_repair_id', 'trn_memo_repair', 'id');

        $this->dropColumn('trn_inspecting', 'kartu_process_maklon_id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
    }
}
