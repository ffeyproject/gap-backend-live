<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000091_0_modify_database extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('trn_kirim_buyer_header', 'nama_buyer', $this->string()->comment('Isian bebas (nama buyer bisa diganti sesuai permintaan)'));
        $this->addColumn('trn_kirim_buyer_header', 'alamat_buyer', $this->string()->comment('Isian bebas (alamat buyer bisa diganti sesuai permintaan)'));
        $this->addColumn('trn_kirim_buyer_header', 'plat_nomor', $this->string());
        $this->addColumn('trn_kirim_buyer_header', 'is_export', $this->boolean()->defaultValue(false));

        $this->addColumn('trn_gudang_jadi', 'color', $this->string()->comment('Nama color Pada kartu proses'));

        //$this->execute('truncate table trn_beli_kain_jadi restart identity cascade');
        $this->addColumn('trn_beli_kain_jadi', 'wo_color_id', $this->integer()->unsigned()->notNull());
        $this->addForeignKey('fk_trn_beli_kain_jadi_wo_color', 'trn_beli_kain_jadi', 'wo_color_id', 'trn_wo_color', 'id');

        //$this->execute('truncate table trn_terima_makloon_process restart identity cascade');
        $this->addColumn('trn_terima_makloon_process', 'wo_color_id', $this->integer()->unsigned()->notNull());
        $this->addForeignKey('fk_trn_terima_makloon_process_wo_color', 'trn_terima_makloon_process', 'wo_color_id', 'trn_wo_color', 'id');

        //$this->execute('truncate table trn_terima_makloon_finish restart identity cascade');
        $this->addColumn('trn_terima_makloon_finish', 'kirim_makloon_id', $this->integer()->unsigned()->notNull());
        $this->addForeignKey('fk_trn_terima_makloon_finish_kirim_makloon', 'trn_terima_makloon_finish', 'kirim_makloon_id', 'trn_kirim_makloon', 'id');
        $this->addColumn('trn_terima_makloon_finish', 'wo_color_id', $this->integer()->unsigned()->notNull());
        $this->addForeignKey('fk_trn_terima_makloon_finish_wo_color', 'trn_terima_makloon_finish', 'wo_color_id', 'trn_wo_color', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
    }
}
