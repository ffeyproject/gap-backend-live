<?php

use yii\db\Migration;

/**
 * Class m261119_030351_add_ex_finish_to_trn_retur_buyer_item
 */
class m261119_030351_add_ex_finish_to_trn_retur_buyer_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_retur_buyer_item', 'surat_jalan_ex_finish_id', $this->integer()->null()->comment('Surat jalan asal dari Ex-Finish (surat_jalan_ex_finish.id)'));
        $this->addColumn('trn_retur_buyer_item', 'jual_ex_finish_item_id', $this->integer()->null()->comment('Item/roll asal dari Ex-Finish (jual_ex_finish_item.id)'));

        $this->addForeignKey('fk_trn_retur_buyer_item_sj_ex_finish', 'trn_retur_buyer_item', 'surat_jalan_ex_finish_id', 'surat_jalan_ex_finish', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_trn_retur_buyer_item_jual_ef_item', 'trn_retur_buyer_item', 'jual_ex_finish_item_id', 'jual_ex_finish_item', 'id', 'RESTRICT', 'CASCADE');

        $this->createIndex('idx_trn_retur_buyer_item_sj_ex_finish_id', 'trn_retur_buyer_item', 'surat_jalan_ex_finish_id');
        $this->createIndex('idx_trn_retur_buyer_item_jual_ef_item_id', 'trn_retur_buyer_item', 'jual_ex_finish_item_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_trn_retur_buyer_item_sj_ex_finish', 'trn_retur_buyer_item');
        $this->dropForeignKey('fk_trn_retur_buyer_item_jual_ef_item', 'trn_retur_buyer_item');

        $this->dropIndex('idx_trn_retur_buyer_item_sj_ex_finish_id', 'trn_retur_buyer_item');
        $this->dropIndex('idx_trn_retur_buyer_item_jual_ef_item_id', 'trn_retur_buyer_item');

        $this->dropColumn('trn_retur_buyer_item', 'jual_ex_finish_item_id');
        $this->dropColumn('trn_retur_buyer_item', 'surat_jalan_ex_finish_id');
    }
}
