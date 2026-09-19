<?php

use yii\db\Migration;

/**
 * Class m260919_033500_add_is_hasil_setting_to_trn_buy_greige_and_trn_stock_greige
 */
class m260919_033500_add_is_hasil_setting_to_trn_buy_greige_and_trn_stock_greige extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_buy_greige', 'is_hasil_setting', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('trn_stock_greige', 'is_hasil_setting', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trn_buy_greige', 'is_hasil_setting');
        $this->dropColumn('trn_stock_greige', 'is_hasil_setting');
    }
}
