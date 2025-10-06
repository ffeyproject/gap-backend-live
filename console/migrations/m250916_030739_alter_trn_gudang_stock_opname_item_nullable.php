<?php

use yii\db\Migration;

/**
 * Class m250916_030739_alter_trn_gudang_stock_opname_item_nullable
 */
class m250916_030739_alter_trn_gudang_stock_opname_item_nullable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Drop foreign key lama dulu
        $this->dropForeignKey(
            'fk-gudang_stock_opname_item-opname_id',
            'trn_gudang_stock_opname_item'
        );

        // 2. Alter kolom jadi nullable
        $this->alterColumn(
            'trn_gudang_stock_opname_item',
            'trn_gudang_stock_opname_id',
            $this->integer()->null()
        );

        // 3. Add foreign key lagi dengan ON DELETE SET NULL
        $this->addForeignKey(
            'fk-gudang_stock_opname_item-opname_id',
            'trn_gudang_stock_opname_item',
            'trn_gudang_stock_opname_id',
            'trn_gudang_stock_opname',
            'id',
            'SET NULL', // <-- ON DELETE
            'RESTRICT'  // <-- ON UPDATE
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Revert perubahan

        // Drop foreign key yang baru
        $this->dropForeignKey(
            'fk-gudang_stock_opname_item-opname_id',
            'trn_gudang_stock_opname_item'
        );

        // Alter kolom jadi NOT NULL lagi
        $this->alterColumn(
            'trn_gudang_stock_opname_item',
            'trn_gudang_stock_opname_id',
            $this->integer()->notNull()
        );

        // Tambahkan foreign key lama (ON DELETE CASCADE)
        $this->addForeignKey(
            'fk-gudang_stock_opname_item-opname_id',
            'trn_gudang_stock_opname_item',
            'trn_gudang_stock_opname_id',
            'trn_gudang_stock_opname',
            'id',
            'CASCADE', // default sebelumnya
            'RESTRICT'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250916_030739_alter_trn_gudang_stock_opname_item_nullable cannot be reverted.\n";

        return false;
    }
    */
}