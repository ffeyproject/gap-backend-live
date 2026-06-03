<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_rekap_proses_mesin_input}}`.
 */
class m260601_141400_create_trn_rekap_proses_mesin_input_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%trn_rekap_proses_mesin_input}}', [
            'id' => $this->primaryKey(),
            'mst_mesin_proses_id' => $this->integer()->notNull(),
            'tanggal' => $this->date()->notNull(),
            'tipe' => $this->string(50)->notNull(),
            'shift' => $this->string(10)->notNull(),
            'wo_no' => $this->string(255),
            'nk_no' => $this->string(255),
            'nama_proses' => $this->string(255),
            'temp' => $this->string(50),
            'panjang_jadi' => $this->string(50),
            'keterangan' => $this->text(),
            'created_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);
        
        $this->addForeignKey(
            'fk-trn_rekap_proses_mesin_input-mst_mesin_proses_id',
            '{{%trn_rekap_proses_mesin_input}}',
            'mst_mesin_proses_id',
            '{{%mst_mesin_proses}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-trn_rekap_proses_mesin_input-mst_mesin_proses_id', '{{%trn_rekap_proses_mesin_input}}');
        $this->dropTable('{{%trn_rekap_proses_mesin_input}}');
    }
}
