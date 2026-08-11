<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_gudang_jadi_opname_pcs}}`.
 */
class m260808_120000_create_trn_gudang_jadi_opname_pcs_table extends Migration
{
    const TABLE_NAME = "trn_gudang_jadi_opname_pcs";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'id_trn_gudang_jadi' => $this->integer()->unsigned()->null()->comment('Relasi ke trn_gudang_jadi(id)'),
            'opname_code' => $this->string(100)->notNull()->comment('Nomor / Kode Dokumen Opname'),
            'qr_code' => $this->string(100)->notNull()->comment('QR Code Barang Pcs Roll'),
            'qr_code_desc' => $this->text()->comment('Deskripsi / Spesifikasi Kain Pcs Roll'),
            'qty' => $this->decimal(12, 2)->notNull()->comment('Jumlah Yard / Meter'),
            'unit' => $this->string(20)->notNull()->defaultValue('YARDS')->comment('Satuan Barang'),
            'grade' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=Grade A, 2=Grade B, 3=Grade C, dst'),
            'join_piece' => $this->string(50)->null()->comment('Keterangan Join Piece'),
            'locs_code' => $this->string(50)->notNull()->defaultValue('TRANSIT')->comment('Kode Lokasi Gudang'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=Draft/Submitted, 2=Verified'),
            'remark' => $this->text()->null()->comment('Catatan Tambahan'),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->null(),
            'updated_at' => $this->integer()->unsigned()->null(),
            'updated_by' => $this->integer()->unsigned()->null(),
        ]);

        $this->addForeignKey(
            'fk_' . self::TABLE_NAME . '_gudang_jadi',
            self::TABLE_NAME,
            'id_trn_gudang_jadi',
            'trn_gudang_jadi',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex(
            'idx_' . self::TABLE_NAME . '_opname_code',
            self::TABLE_NAME,
            'opname_code'
        );

        $this->createIndex(
            'idx_' . self::TABLE_NAME . '_qr_code',
            self::TABLE_NAME,
            'qr_code'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
