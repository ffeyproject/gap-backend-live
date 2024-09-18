<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kartu_process_pfp_process}}`.
 */
class m190901_000107_0_add_approval_column_to_trn_sc_greige_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_sc_greige', 'order_grege_approved', $this->boolean()->defaultValue(false)->notNull()->comment('Order greige sudah disetujui atau belum oleh PMC.'));
        $this->addColumn('trn_sc_greige', 'order_grege_approved_at', $this->integer()->unsigned());
        $this->addColumn('trn_sc_greige', 'order_grege_approved_by', $this->integer()->unsigned());
        $this->addColumn('trn_sc_greige', 'order_grege_approval_note', $this->text());

        $this->addColumn('trn_sc_greige', 'order_grege_approved_dir', $this->boolean()->defaultValue(false)->notNull()->comment('Order greige sudah disetujui atau belum oleh DIR Marketing.'));
        $this->addColumn('trn_sc_greige', 'order_grege_approved_at_dir', $this->integer()->unsigned());
        $this->addColumn('trn_sc_greige', 'order_grege_approval_note_dir', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {

    }
}
