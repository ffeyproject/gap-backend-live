<?php

use yii\db\Migration;

/**
 * Class m261118_084500_add_jenis_printing_to_trn_buy_pfp
 */
class m261118_084500_add_jenis_printing_to_trn_buy_pfp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_buy_pfp', 'pfp_jenis_gudang', $this->smallInteger()->defaultValue(1));
        $this->addColumn('trn_buy_pfp', 'jenis_printing', $this->smallInteger()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trn_buy_pfp', 'jenis_printing');
        $this->dropColumn('trn_buy_pfp', 'pfp_jenis_gudang');
    }
}
