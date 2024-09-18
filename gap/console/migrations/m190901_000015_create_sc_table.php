<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000015_create_sc_table extends Migration
{
    const TABLE_NAME = "trn_sc";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'cust_id' => $this->integer()->unsigned()->notNull(),
            'jenis_order' => $this->tinyInteger(3)->notNull()->comment('1=FRESH ORDER, 2=MAKLOON, 3=BARANG JADI, 4=STOCK'),
            'currency' => $this->tinyInteger(3)->notNull()->comment('1=IDR, 2=USD'),
            'bank_acct_id' => $this->integer()->unsigned(),
            'direktur_id' => $this->integer()->unsigned()->notNull(),
            'manager_id' => $this->integer()->unsigned()->notNull(),
            'marketing_id' => $this->integer()->unsigned()->notNull(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(255),
            'tipe_kontrak' => $this->tinyInteger(1)->notNull()->comment('1=LOKAL, 2=EXPORT'),
            'date' => $this->date()->notNull(),
            'pmt_term' => $this->integer()->unsigned()->notNull(),
            'pmt_method' => $this->string()->notNull(),
            'ongkos_angkut' => $this->tinyInteger(1)->notNull()->comment('1=Pemesan, 2=Penjual/Pabrik, 3=FOB, 4=CNF, 5=CIF'),
            'due_date' => $this->date()->notNull(),
            'delivery_date' => $this->date()->notNull(),
            'destination' => $this->text()->notNull(),
            'packing' => $this->string(255),
            'jet_black' => $this->boolean()->defaultValue(false),
            'no_po' => $this->string(255),
            'disc_grade_b' => $this->decimal(5,2)->unsigned()->notNull()->defaultValue(0),
            'disc_piece_kecil' => $this->decimal(5,2)->unsigned()->notNull()->defaultValue(0),
            'consignee_name' => $this->string(255),
            'apv_dir_at' => $this->integer()->unsigned(),
            'reject_note_dir' => $this->text(),
            'apv_mgr_at' => $this->integer()->unsigned(),
            'reject_note_mgr' => $this->text(),
            'notify_party' => $this->text(),
            'buyer_name_in_invoice' => $this->string(255),
            'note' => $this->text(),
            'posted_at' => $this->integer()->unsigned(),
            'closed_at' => $this->integer()->unsigned(),
            'closed_by' => $this->integer()->unsigned(),
            'closed_note' => $this->text(),
            'batal_at' => $this->integer()->unsigned(),
            'batal_by' => $this->integer()->unsigned(),
            'batal_note' => $this->text(),
            'status' => $this->tinyInteger(1)->notNull()->comment('1=draft, 2=posted, 3=approved by dir, 4=approved by mgr, 5=approved, 6=rejected, 7=closed, 8=batal'),
            'created_at' => $this->integer()->unsigned(),
            'created_by' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'sc_cust', self::TABLE_NAME, 'cust_id', 'mst_customer', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_bank_acct', self::TABLE_NAME, 'bank_acct_id', 'mst_bank_account', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_dir', self::TABLE_NAME, 'direktur_id', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_mgr', self::TABLE_NAME, 'manager_id', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_marketing', self::TABLE_NAME, 'marketing_id', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_created', self::TABLE_NAME, 'created_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_updated', self::TABLE_NAME, 'updated_by', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_updated', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_created', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_marketing', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_mgr', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_dir', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_bank_acct', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'sc_cust', self::TABLE_NAME);

        $this->dropTable(self::TABLE_NAME);
    }
}
