<?php

use yii\db\Migration;

/**
 * Class m250916_043329_drop_fk_greige_id_from_trn_stock_greige_opname
 */
class m250916_043329_drop_fk_greige_id_from_trn_stock_greige_opname extends Migration
{
    /**
     * {@inheritdoc}
     */
     public function safeUp()
    {
        $this->dropForeignKey(
            'fk-trn_stock_greige_opname-greige_id',
            '{{%trn_stock_greige_opname}}'
        );
    }

    public function safeDown()
    {
        $this->addForeignKey(
            'fk-trn_stock_greige_opname-greige_id',
            '{{%trn_stock_greige_opname}}',
            'greige_id',
            '{{%trn_stock_greige}}',
            'id',
            'CASCADE'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250916_043329_drop_fk_greige_id_from_trn_stock_greige_opname cannot be reverted.\n";

        return false;
    }
    */
}