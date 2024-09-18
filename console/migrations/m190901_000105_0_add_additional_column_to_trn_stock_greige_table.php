<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kartu_process_pfp_process}}`.
 */
class m190901_000105_0_add_additional_column_to_trn_stock_greige_table extends Migration
{
    const TABLE_NAME = "trn_stock_greige";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn(self::TABLE_NAME, 'pfp_jenis_gudang', $this->smallInteger()->comment('Pembagian jenis gudang untuk kain PFP, 1=Fudang 1, 2=Gudang 2'));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn(self::TABLE_NAME, 'pfp_jenis_gudang');
    }
}
