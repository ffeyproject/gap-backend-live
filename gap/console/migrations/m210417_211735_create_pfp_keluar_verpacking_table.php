<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%pfp_keluar_verpacking}}`.
 */
class m210417_211735_create_pfp_keluar_verpacking_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%pfp_keluar_verpacking}}', [
            'id' => $this->primaryKey(),
            'pfp_keluar_id' => $this->integer()->notNull(),
            'greige_id' => $this->integer()->notNull(),
            'no_urut' => $this->bigInteger(),
            'no' => $this->string(),
            'jenis' => $this->smallInteger()->defaultValue(1)->notNull()->comment('Mengacu pada TrnPfpKeluar::jenisOptions()'),
            'satuan' => $this->smallInteger()->defaultValue(1)->notNull()->comment('Mengacu pada MstGreigeGroup::unitOptions()'),
            'tgl_kirim' => $this->date()->notNull(),
            'tgl_inspect' => $this->date()->notNull(),
            'note' => $this->text(),
            'send_to_vendor' => $this->boolean()->notNull()->defaultValue(false),
            'vendor_id' => $this->integer(),
            'wo_id' => $this->integer(),
            'vendor_address' => $this->text(),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('1=draft, 2=posted, 3=approved, 4=rejected'),
            'created_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        $this->addForeignKey('fk_pfp_keluar_verpacking_pfp_keluar', '{{%pfp_keluar_verpacking}}', 'pfp_keluar_id', '{{%trn_pfp_keluar}}', 'id');
        $this->addForeignKey('fk_pfp_keluar_verpacking_greige', '{{%pfp_keluar_verpacking}}', 'greige_id', '{{%mst_greige}}', 'id');
        $this->addForeignKey('fk_pfp_keluar_verpacking_vendor', '{{%pfp_keluar_verpacking}}', 'vendor_id', '{{%mst_vendor}}', 'id');
        $this->addForeignKey('fk_pfp_keluar_verpacking_wo', '{{%pfp_keluar_verpacking}}', 'wo_id', '{{%trn_wo}}', 'id');
        $this->createIndex('pfp_keluar_verpacking_idx_status', '{{%pfp_keluar_verpacking}}', 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%pfp_keluar_verpacking}}');
    }
}
