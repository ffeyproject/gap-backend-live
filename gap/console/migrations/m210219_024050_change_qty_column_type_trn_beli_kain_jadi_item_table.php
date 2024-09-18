<?php

use yii\db\Migration;

/**
 * Class m210219_024050_change_qty_column_type_trn_beli_kain_jadi_item_table
 */
class m210219_024050_change_qty_column_type_trn_beli_kain_jadi_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('trn_beli_kain_jadi_item', 'qty', $this->float()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210219_024050_change_qty_column_type_trn_beli_kain_jadi_item_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210219_024050_change_qty_column_type_trn_beli_kain_jadi_item_table cannot be reverted.\n";

        return false;
    }
    */
}
