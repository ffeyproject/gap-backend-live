<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_inspecting_makloon_dan_barang_jadi}}`.
 */
class m210706_014056_create_trn_inspecting_makloon_dan_barang_jadi_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%inspecting_mkl_bj}}', [
            'id' => $this->bigPrimaryKey(),
            'wo_id' => $this->integer()->notNull(),
            'wo_color_id' => $this->integer()->notNull(),
            'tgl_inspeksi' => $this->date()->notNull(),
            'tgl_kirim' => $this->date()->notNull(),
            'no_lot' => $this->string()->notNull()->defaultValue('-'),
            'jenis' => $this->smallInteger()->notNull()->defaultValue(1)->comment('1=Makloon Proses, 2=Makloon Finish, 3=Barang Jadi'),
            'satuan' => $this->smallInteger()->notNull()->defaultValue(1)->comment('Mengacu pada MstGreigeGroup::unitOptions()'),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=draft, 2=posted'),
        ]);
        $this->addForeignKey('fk_inspecting_mkl_bj_wo', '{{%inspecting_mkl_bj}}', 'wo_id', '{{%trn_wo}}', 'id');
        $this->addForeignKey('fk_inspecting_mkl_bj_wo_color', '{{%inspecting_mkl_bj}}', 'wo_color_id', '{{%trn_wo_color}}', 'id');

        $this->createTable('{{%inspecting_mkl_bj_items}}', [
            'id' => $this->bigPrimaryKey(),
            'inspecting_id' => $this->bigInteger()->notNull(),
            'grade' => $this->tinyInteger()->notNull()->comment('1=Grade A, 2=Grade B, 3=Grade C, 4=Piece Kecil, 5=Sample'),
            'join_piece' => $this->string(10),
            'qty' => $this->float()->notNull(),
            'note' => $this->text(),
        ]);
        $this->addForeignKey('fk_inspecting_mkl_bj_items_inspecting', '{{%inspecting_mkl_bj_items}}', 'inspecting_id', '{{%inspecting_mkl_bj}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%inspecting_mkl_bj_items}}');
        $this->dropTable('{{%inspecting_mkl_bj}}');
    }
}
