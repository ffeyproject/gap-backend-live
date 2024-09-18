<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mutasi_ex_finish_alt_item}}`.
 */
class m210210_040803_create_mutasi_ex_finish_alt_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mutasi_ex_finish_alt_item}}', [
            'id' => $this->primaryKey(),
            'mutasi_id' => $this->integer()->notNull(),
            'gudang_jadi_id' => $this->integer()->notNull(),
            'grade' => $this->smallInteger()->notNull()->comment('mengacu kepada TrnStockGreige::gradeOptions()'),
            'qty' => $this->float()->notNull(),
        ]);

        $this->addForeignKey('fk_mutasi_ex_finish_alt_item_mutasi', '{{%mutasi_ex_finish_alt_item}}', 'mutasi_id', '{{%mutasi_ex_finish_alt}}', 'id');
        $this->addForeignKey('fk_mutasi_ex_finish_alt_item_gdjadi', '{{%mutasi_ex_finish_alt_item}}', 'gudang_jadi_id', '{{%trn_gudang_jadi}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mutasi_ex_finish_alt_item}}');
    }
}
