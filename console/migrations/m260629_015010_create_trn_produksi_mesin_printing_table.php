<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_produksi_mesin_printing}}`.
 */
class m260629_015010_create_trn_produksi_mesin_printing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%trn_produksi_mesin_printing}}', [
            'id' => $this->primaryKey(),
            'jenis_input' => $this->string(50)->notNull(),
            'tanggal' => $this->date()->notNull(),
            'shift' => $this->string(10)->notNull(),
            'pembagian_hari' => $this->string(50)->notNull(),
            'start' => $this->string(50),
            'stop' => $this->string(50),
            'mst_mesin_proses_id' => $this->integer()->notNull(),
            'kartu_proses_id' => $this->integer(),
            'wo_id' => $this->integer(),
            'wo_no' => $this->string(255),
            'nk_no' => $this->string(255),
            'design' => $this->string(255),
            'motif' => $this->string(255),
            'warna' => $this->string(255),
            'jumlah_pesanan' => $this->string(50),
            'realisasi' => $this->string(50),
            'kurang' => $this->string(50),
            'panjang_greige' => $this->string(50),
            'panjang_jadi' => $this->string(50),
            'keterangan' => $this->text(),
            'mst_jenis_hambatan_id' => $this->integer(),
            'created_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk_trn_produksi_mesin_printing_mesin',
            '{{%trn_produksi_mesin_printing}}',
            'mst_mesin_proses_id',
            '{{%mst_mesin_proses}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_trn_produksi_mesin_printing_kp',
            '{{%trn_produksi_mesin_printing}}',
            'kartu_proses_id',
            '{{%trn_kartu_proses_printing}}',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_trn_produksi_mesin_printing_wo',
            '{{%trn_produksi_mesin_printing}}',
            'wo_id',
            '{{%trn_wo}}',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_trn_produksi_mesin_printing_hambatan',
            '{{%trn_produksi_mesin_printing}}',
            'mst_jenis_hambatan_id',
            '{{%mst_jenis_hambatan}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_trn_produksi_mesin_printing_mesin', '{{%trn_produksi_mesin_printing}}');
        $this->dropForeignKey('fk_trn_produksi_mesin_printing_kp', '{{%trn_produksi_mesin_printing}}');
        $this->dropForeignKey('fk_trn_produksi_mesin_printing_wo', '{{%trn_produksi_mesin_printing}}');
        $this->dropForeignKey('fk_trn_produksi_mesin_printing_hambatan', '{{%trn_produksi_mesin_printing}}');
        $this->dropTable('{{%trn_produksi_mesin_printing}}');
    }
}

