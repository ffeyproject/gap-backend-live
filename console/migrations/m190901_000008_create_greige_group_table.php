<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000008_create_greige_group_table extends Migration
{
    const TABLE_NAME = "mst_greige_group";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'jenis_kain' => $this->tinyInteger(3)->notNull()->comment('1=Suiting Men 2=Suiting Ladies 3=Printing 4=Kniting 5=Georgette 6=Lain-lain'),
            'nama_kain' => $this->string(255)->notNull(),
            'qty_per_batch' => $this->decimal(17,2)->unsigned()->notNull()->defaultValue(0),
            'unit' => $this->tinyInteger(1)->notNull()->comment('1=YARD 2=METER 3=PCS 4=KILOGRAM'),
            'nilai_penyusutan' => $this->decimal(17,2)->notNull()->defaultValue(0),
            'gramasi_kain' => $this->string(150),
            'sulam_pinggir' => $this->string(255),
            'lebar_kain' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('1=44" 2=58"'),
            'created_at' => $this->integer()->unsigned(),
            'created_by' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
            'aktif' => $this->boolean()->defaultValue(true),
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
