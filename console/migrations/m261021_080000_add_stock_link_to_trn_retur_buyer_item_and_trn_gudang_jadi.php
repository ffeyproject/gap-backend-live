<?php

use yii\db\Migration;

/**
 * Menautkan item retur buyer ke surat jalan pengiriman & roll stok gudang jadi.
 * Roll asal (stock_id) boleh kosong jika barang retur tidak bisa diidentifikasi (label hilang),
 * tetapi surat jalan (kirim_buyer_id) tetap dicatat supaya masih bisa ditelusuri.
 */
class m261021_080000_add_stock_link_to_trn_retur_buyer_item_and_trn_gudang_jadi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trn_retur_buyer_item', 'kirim_buyer_id', $this->integer()->null()->comment('Surat jalan asal (trn_kirim_buyer.id)'));
        $this->addColumn('trn_retur_buyer_item', 'stock_id', $this->integer()->null()->comment('Roll asal yang dikirim (trn_gudang_jadi.id), NULL jika tidak diketahui'));
        $this->addColumn('trn_retur_buyer_item', 'gudang_jadi_id', $this->integer()->null()->comment('Stok baru hasil retur (trn_gudang_jadi.id), terisi saat posting'));

        $this->addForeignKey('fk_trn_retur_buyer_item_kirim_buyer', 'trn_retur_buyer_item', 'kirim_buyer_id', 'trn_kirim_buyer', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_trn_retur_buyer_item_stock', 'trn_retur_buyer_item', 'stock_id', 'trn_gudang_jadi', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_trn_retur_buyer_item_gudang_jadi', 'trn_retur_buyer_item', 'gudang_jadi_id', 'trn_gudang_jadi', 'id', 'SET NULL', 'CASCADE');
        $this->createIndex('idx_trn_retur_buyer_item_kirim_buyer_id', 'trn_retur_buyer_item', 'kirim_buyer_id');
        $this->createIndex('idx_trn_retur_buyer_item_stock_id', 'trn_retur_buyer_item', 'stock_id');

        $this->addColumn('trn_gudang_jadi', 'retur_from_id', $this->integer()->null()->comment('ID stok asal yang dikirim ke buyer (khusus stok hasil retur buyer)'));
        $this->addForeignKey('fk_trn_gudang_jadi_retur_from', 'trn_gudang_jadi', 'retur_from_id', 'trn_gudang_jadi', 'id', 'SET NULL', 'CASCADE');
        $this->createIndex('idx_trn_gudang_jadi_retur_from_id', 'trn_gudang_jadi', 'retur_from_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_trn_gudang_jadi_retur_from', 'trn_gudang_jadi');
        $this->dropIndex('idx_trn_gudang_jadi_retur_from_id', 'trn_gudang_jadi');
        $this->dropColumn('trn_gudang_jadi', 'retur_from_id');

        $this->dropForeignKey('fk_trn_retur_buyer_item_kirim_buyer', 'trn_retur_buyer_item');
        $this->dropForeignKey('fk_trn_retur_buyer_item_stock', 'trn_retur_buyer_item');
        $this->dropForeignKey('fk_trn_retur_buyer_item_gudang_jadi', 'trn_retur_buyer_item');
        $this->dropIndex('idx_trn_retur_buyer_item_kirim_buyer_id', 'trn_retur_buyer_item');
        $this->dropIndex('idx_trn_retur_buyer_item_stock_id', 'trn_retur_buyer_item');
        $this->dropColumn('trn_retur_buyer_item', 'kirim_buyer_id');
        $this->dropColumn('trn_retur_buyer_item', 'stock_id');
        $this->dropColumn('trn_retur_buyer_item', 'gudang_jadi_id');
    }
}
