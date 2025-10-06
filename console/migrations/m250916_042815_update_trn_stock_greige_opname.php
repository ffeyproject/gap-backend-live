<?php

use yii\db\Migration;

/**
 * Class m250916_042815_update_trn_stock_greige_opname
 */
class m250916_042815_update_trn_stock_greige_opname extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
{
    $table = '{{%trn_stock_greige_opname}}';
    if ($this->db->schema->getTableSchema($table, true)->getColumn('stock_greige_id') === null) {
        $this->addColumn($table, 'stock_greige_id', $this->integer()->notNull());
    }

    // tambahkan FK jika belum ada
    if (!array_key_exists('fk-trn_stock_greige_opname-stock_greige_id', $this->db->schema->getTableSchema($table, true)->foreignKeys)) {
        $this->addForeignKey(
            'fk-trn_stock_greige_opname-stock_greige_id',
            $table,
            'stock_greige_id',
            '{{%trn_stock_greige}}',
            'id',
            'CASCADE'
        );
    }
}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // 1. Hapus foreign key stock_greige_id
        $this->dropForeignKey('{{%fk-trn_stock_greige_opname-stock_greige_id}}', '{{%trn_stock_greige_opname}}');
        $this->dropIndex('{{%idx-trn_stock_greige_opname-stock_greige_id}}', '{{%trn_stock_greige_opname}}');

        // 2. Tambahkan kembali foreign key lama greige_id
        $this->createIndex(
            '{{%idx-trn_stock_greige_opname-greige_id}}',
            '{{%trn_stock_greige_opname}}',
            'greige_id'
        );

        $this->addForeignKey(
            '{{%fk-trn_stock_greige_opname-greige_id}}',
            '{{%trn_stock_greige_opname}}',
            'greige_id',
            '{{%trn_stock_greige}}',
            'id',
            'CASCADE'
        );

        // 3. Hapus kolom stock_greige_id
        $this->dropColumn('{{%trn_stock_greige_opname}}', 'stock_greige_id');
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250916_042815_update_trn_stock_greige_opname cannot be reverted.\n";

        return false;
    }
    */
}