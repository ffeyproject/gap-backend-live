<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000074_0_create_trn_kirim_makloon_table extends Migration
{
    const TABLE_NAME = "trn_kirim_makloon";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'sc_id' => $this->integer()->unsigned(),
            'sc_greige_id' => $this->integer()->unsigned(),
            'vendor_id' => $this->integer()->unsigned()->notNull(),
            'mo_id' => $this->integer()->unsigned(),
            'wo_id' => $this->integer()->unsigned()->notNull(),
            'date' => $this->date()->notNull(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(255),
            'unit' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('Mengacu pada MstGreigeGroup::unitOptions()'),
            'note' => $this->text(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=Draft, 2=Posted'),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
            'penerima' => $this->string(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME, 'sc_id', 'trn_sc', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc_greige', self::TABLE_NAME, 'sc_greige_id', 'trn_sc_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_mo', self::TABLE_NAME, 'mo_id', 'trn_mo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_wo', self::TABLE_NAME, 'wo_id', 'trn_wo', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_vendor', self::TABLE_NAME, 'vendor_id', 'mst_vendor', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
