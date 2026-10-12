<?php

use yii\db\Migration;

/**
 * Class m261012_032915_add_lokasi_setting_to_trn_buy_greige_and_trn_stock_greige
 */
class m261012_032915_add_lokasi_setting_to_trn_buy_greige_and_trn_stock_greige extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_buy_greige', 'lokasi_setting', $this->smallInteger()->null()->comment('1=Di GAP, 2=Di Luar'));
        $this->addColumn('trn_stock_greige', 'lokasi_setting', $this->smallInteger()->null()->comment('1=Di GAP, 2=Di Luar'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trn_buy_greige', 'lokasi_setting');
        $this->dropColumn('trn_stock_greige', 'lokasi_setting');
    }
}
