<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000023_create_wo_table extends Migration
{
    const TABLE_NAME = "trn_wo";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'sc_id' => $this->integer()->unsigned()->notNull(),
            'sc_greige_id' => $this->integer()->unsigned()->notNull(),
            'mo_id' => $this->integer()->unsigned()->notNull(),
            'jenis_order' => $this->tinyInteger(3)->notNull()->comment('Pilihan jenis order sama dengan (mereferensi) jenis order pada SC'),
            'greige_id' => $this->integer()->unsigned()->notNull()->comment('Greige yang digunakan berdasarkan greige_group pada tabel sc_greige'),
            'mengetahui_id' => $this->integer()->unsigned()->notNull(),
            'apv_mengetahui_at' => $this->integer()->unsigned(),
            'reject_note_mengetahui' => $this->text(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(255),
            'date' => $this->date()->unsigned()->notNull(),
            'papper_tube' => $this->tinyInteger(2)->notNull()->comment("1=113x3,8 2=113x5,0 3=150x3,8 4=150x5,0 5=160x3,2 6=122x3,2 7=Lainnya"),
            'plastic_size' => $this->string(255),
            'shipping_mark' => $this->text(),
            'papper_tube_id' => $this->integer()->notNull()->defaultValue(1),
            'note' => $this->text(),
            'note_two' => $this->text(),
            'marketing_id' => $this->integer()->unsigned()->notNull(),
            'apv_marketing_at' => $this->integer()->unsigned(),
            'reject_note_marketing' => $this->text(),
            'posted_at' => $this->integer()->unsigned(),
            'closed_at' => $this->integer()->unsigned(),
            'closed_by' => $this->integer()->unsigned(),
            'closed_note' => $this->text(),
            'batal_at' => $this->integer()->unsigned(),
            'batal_by' => $this->integer()->unsigned(),
            'batal_note' => $this->text(),
            'status' => $this->tinyInteger(1)->notNull()->comment('1=draft, 2=posted, 3=approved by mengetahui, 4=approved by marketing, 5=approved, 6=rejected, 7=closed, 8=batal'),
            'validasi_stock' => $this->boolean()->null()->defaultValue(true)->comment('Jika false, maka tidak akan dilakukan validasi stock ketika proses approval.'),
            'created_at' => $this->integer()->unsigned(),
            'created_by' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME, 'sc_id', 'trn_sc', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME, 'sc_greige_id', 'trn_sc_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME, 'mo_id', 'trn_mo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME, 'greige_id', 'mst_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_mengetahui', self::TABLE_NAME, 'mengetahui_id', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_marketing', self::TABLE_NAME, 'marketing_id', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_created', self::TABLE_NAME, 'created_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_updated', self::TABLE_NAME, 'updated_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_batal', self::TABLE_NAME, 'batal_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_closed', self::TABLE_NAME, 'closed_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_tube', self::TABLE_NAME, 'papper_tube_id', 'mst_papper_tube', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_closed', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_batal', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_updated', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_created', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_marketing', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_mengetahui', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
