<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000020_create_mo_table extends Migration
{
    const TABLE_NAME = "trn_mo";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'sc_id' => $this->integer()->unsigned()->notNull(),
            'sc_greige_id' => $this->integer()->unsigned()->notNull(),
            'process' => $this->tinyInteger(1)->notNull()->comment('mengacu ke field process pada table sc_greige'),
            'approval_id' => $this->integer()->unsigned(),
            'approved_at' => $this->integer()->unsigned(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(255),
            'date' => $this->date()->notNull(),
            're_wo' => $this->string(255)->comment('nomor WO referensi'),
            'design' => $this->string(255),
            'article' => $this->string(255),
            'strike_off' => $this->text(),
            'heat_cut' => $this->boolean()->defaultValue(false),
            'sulam_pinggir' => $this->string(255),
            'border_size' => $this->integer()->unsigned(),
            'block_size' => $this->integer()->unsigned(),
            'foil' => $this->boolean()->defaultValue(false),
            'face_stamping' => $this->text(),
            'selvedge_stamping' => $this->string(255),
            'selvedge_continues' => $this->string(255),
            'side_band' => $this->string(255),
            'tag' => $this->string(255),
            'hanger' => $this->string(255),
            'label' => $this->string(255),
            'folder' => $this->string(255),
            'album' => $this->string(255),
            'joint' => $this->boolean()->defaultValue(false),
            'joint_qty' => $this->integer()->unsigned()->defaultValue(0),
            'packing_method' => $this->tinyInteger(2)->notNull()->unsigned()->comment('1=SINGLE ROLL, 2=DOUBLE FOLDED'),
            'shipping_method' => $this->tinyInteger(2)->notNull()->unsigned()->comment('1=BALE, 2=CARTOON, 3=LOSE'),
            'shipping_sorting' => $this->tinyInteger(2)->notNull()->unsigned()->comment('1=SOLID, 2=ASSORTED'),
            'plastic' => $this->tinyInteger(2)->notNull()->unsigned()->comment('1=VACUM, 2=NON VACUM'),
            'arsip' => $this->string(255),
            'jet_black' => $this->boolean()->defaultValue(false),
            'piece_length' => $this->string(255),
            'est_produksi' => $this->date()->notNull(),
            'est_packing' => $this->date()->notNull(),
            'target_shipment' => $this->date()->notNull(),
            'jenis_gudang' => $this->tinyInteger(1)->notNull()->defaultValue(1)->unsigned()->comment('mereferensi ke tabel trn_stock_greige::jenisGudangOptions'),
            'posted_at' => $this->integer()->unsigned(),
            'closed_at' => $this->integer()->unsigned(),
            'closed_by' => $this->integer()->unsigned(),
            'closed_note' => $this->text(),
            'reject_notes' => $this->text(),
            'batal_at' => $this->integer()->unsigned(),
            'batal_by' => $this->integer()->unsigned(),
            'batal_note' => $this->text(),
            'status' => $this->tinyInteger(1)->notNull()->comment('1=draft, 2=posted, 3=approved, 4=rejected, 5=closed, 6=batal'),
            'note' => $this->text(),
            'created_at' => $this->integer()->unsigned(),
            'created_by' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME, 'sc_greige_id', 'trn_sc_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME, 'sc_id', 'trn_sc', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_apv', self::TABLE_NAME, 'approval_id', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_created', self::TABLE_NAME, 'created_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_updated', self::TABLE_NAME, 'updated_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_batal', self::TABLE_NAME, 'batal_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_closed', self::TABLE_NAME, 'closed_by', 'user', 'id');
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
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_apv', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
