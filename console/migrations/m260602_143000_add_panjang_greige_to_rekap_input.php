<?php

use yii\db\Migration;

/**
 * Class m260602_143000_add_panjang_greige_to_rekap_input
 */
class m260602_143000_add_panjang_greige_to_rekap_input extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%trn_rekap_proses_mesin_input}}', 'panjang_greige', $this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%trn_rekap_proses_mesin_input}}', 'panjang_greige');
    }
}
