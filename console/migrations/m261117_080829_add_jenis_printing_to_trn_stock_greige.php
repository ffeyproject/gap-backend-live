<?php

use yii\db\Migration;

/**
 * Class m261117_080829_add_jenis_printing_to_trn_stock_greige
 */
class m261117_080829_add_jenis_printing_to_trn_stock_greige extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_stock_greige', 'jenis_printing', $this->smallInteger()->null()->comment('1=Digital Printing, 2=Konvensional'));
        // Set existing PFP stock Gudang 2 (jenis_gudang = 3 and pfp_jenis_gudang = 2) to 2 (Konvensional)
        // Gudang 1 (dari trn-buy-pfp) dibiarkan null (tidak diganggu)
        $this->update('trn_stock_greige', ['jenis_printing' => 2], ['jenis_gudang' => 3, 'pfp_jenis_gudang' => 2]);
        $this->createIndex('idx-trn_stock_greige-jenis_printing', 'trn_stock_greige', 'jenis_printing');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trn_stock_greige', 'jenis_printing');
    }
}
