<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%trn_mo}}`.
 */
class m250801_092144_add_no_po_column_to_trn_mo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%trn_mo}}', 'no_po', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%trn_mo}}', 'no_po');
    }
}