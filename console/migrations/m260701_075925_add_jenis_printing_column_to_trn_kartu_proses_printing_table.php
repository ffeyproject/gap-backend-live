<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%trn_kartu_proses_printing}}`.
 */
class m260701_075925_add_jenis_printing_column_to_trn_kartu_proses_printing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_kartu_proses_printing', 'jenis_printing', $this->tinyInteger(1)->notNull()->defaultValue(2)->comment('1=Digital, 2=Konvensional'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trn_kartu_proses_printing', 'jenis_printing');
    }
}
