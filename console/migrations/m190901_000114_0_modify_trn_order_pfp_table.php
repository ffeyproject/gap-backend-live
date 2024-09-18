<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kartu_process_pfp_process}}`.
 */
class m190901_000114_0_modify_trn_order_pfp_table extends Migration
{
    const TABLE_NAME = "trn_order_pfp";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_NAME, 'approved_by', $this->integer()->unsigned()->notNull()->defaultValue(1));
        $this->addColumn(self::TABLE_NAME, 'approved_at', $this->integer()->unsigned());
        $this->addColumn(self::TABLE_NAME, 'approval_note', $this->text());
        $this->addColumn(self::TABLE_NAME, 'proses_sampai', $this->smallInteger()->comment('1=Sampai Preset, 2=Sampai Setting'));
        $this->addColumn(self::TABLE_NAME, 'dasar_warna', $this->string());

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_approved_by',self::TABLE_NAME,'approved_by','user','id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {

    }
}
