<?php

use yii\db\Migration;

/**
 * Class m250916_042417_add_stock_greige_id_to_trn_stock_greige_opname
 */
class m250916_042417_add_stock_greige_id_to_trn_stock_greige_opname extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Tambah kolom stock_greige_id
        $this->addColumn('{{%trn_stock_greige_opname}}', 'stock_greige_id', $this->integer()->notNull()->after('greige_id'));

        // Buat index
        $this->createIndex(
            '{{%idx-trn_stock_greige_opname-stock_greige_id}}',
            '{{%trn_stock_greige_opname}}',
            'stock_greige_id'
        );

        // Tambah foreign key ke trn_stock_greige
        $this->addForeignKey(
            '{{%fk-trn_stock_greige_opname-stock_greige_id}}',
            '{{%trn_stock_greige_opname}}',
            'stock_greige_id',
            '{{%trn_stock_greige}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
     public function safeDown()
    {
        // Drop FK & index
        $this->dropForeignKey('{{%fk-trn_stock_greige_opname-stock_greige_id}}', '{{%trn_stock_greige_opname}}');
        $this->dropIndex('{{%idx-trn_stock_greige_opname-stock_greige_id}}', '{{%trn_stock_greige_opname}}');

        // Drop kolom
        $this->dropColumn('{{%trn_stock_greige_opname}}', 'stock_greige_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250916_042417_add_stock_greige_id_to_trn_stock_greige_opname cannot be reverted.\n";

        return false;
    }
    */
}