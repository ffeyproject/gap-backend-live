<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%jual_ex_finish_item}}`.
 */
class m210224_034121_create_jual_ex_finish_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%jual_ex_finish_item}}', [
            'id' => $this->bigPrimaryKey(),
            'jual_id' => $this->bigInteger()->notNull(),
            'greige_id' => $this->integer()->notNull(),
            'grade' => $this->smallInteger()->notNull()->comment("Mengacu pada TrnStockGreige::gradeOptions()"),
            'qty' => $this->decimal()->notNull(),
            'unit' => $this->smallInteger()->notNull()->comment('Mengacu pada MstGreigeGroup::unitOptions()')
        ]);

        $this->addForeignKey('fk_jual_ex_finish_item_jual', '{{%jual_ex_finish_item}}', 'jual_id', '{{%jual_ex_finish}}', 'id');
        $this->addForeignKey('fk_jual_ex_finish_item_greige', '{{%jual_ex_finish_item}}', 'greige_id', '{{%mst_greige}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%jual_ex_finish_item}}');
    }
}
