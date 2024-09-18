<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000056_0_create_mst_process_printing extends Migration
{
    const TABLE_NAME = "mst_process_printing";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'order' => $this->integer()->notNull()->comment('nomor urutan proses'),
            'max_pengulangan' => $this->integer()->unsigned()->notNull()->defaultValue(1),
            'nama_proses' => $this->string()->notNull(),
            'tanggal' => $this->boolean()->notNull()->defaultValue(false),
            'start' => $this->boolean()->notNull()->defaultValue(false),
            'stop' => $this->boolean()->notNull()->defaultValue(false),
            'no_mesin' => $this->boolean()->notNull()->defaultValue(false),
            'operator' => $this->boolean()->notNull()->defaultValue(false),
            'temp' => $this->boolean()->notNull()->defaultValue(false),
            'speed_depan' => $this->boolean()->notNull()->defaultValue(false),
            'speed_belakang' => $this->boolean()->notNull()->defaultValue(false),
            'speed' => $this->boolean()->notNull()->defaultValue(false),
            'resep' => $this->boolean()->notNull()->defaultValue(false),
            'density' => $this->boolean()->notNull()->defaultValue(false),
            'jumlah_pcs' => $this->boolean()->notNull()->defaultValue(false),
            'lebar_jadi' => $this->boolean()->notNull()->defaultValue(false),
            'panjang_jadi' => $this->boolean()->notNull()->defaultValue(false),
            'info_kualitas' => $this->boolean()->notNull()->defaultValue(false),
            'gangguan_produksi' => $this->boolean()->notNull()->defaultValue(false),
            'over_feed' => $this->string(),
            'created_at' => $this->integer()->unsigned(),
            'created_by' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
