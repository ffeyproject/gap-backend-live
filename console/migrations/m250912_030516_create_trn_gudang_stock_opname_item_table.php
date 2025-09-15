<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_gudang_stock_opname_item}}`.
 */
class m250912_030516_create_trn_gudang_stock_opname_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%trn_gudang_stock_opname_item}}', [
            'id' => $this->primaryKey(),
            'trn_gudang_stock_opname_id' => $this->integer()->notNull(),
            'panjang_m' => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'no_set_lusi' => $this->string(50)->notNull(),
            'grade' => $this->string(10)->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Add foreign key relation to trn_gudang_stock_opname
        $this->addForeignKey(
            'fk-gudang_stock_opname_item-opname_id',
            '{{%trn_gudang_stock_opname_item}}',
            'trn_gudang_stock_opname_id',
            '{{%trn_gudang_stock_opname}}',
            'id',
            'CASCADE', // delete children if parent deleted
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-gudang_stock_opname_item-opname_id', '{{%trn_gudang_stock_opname_item}}');
        $this->dropTable('{{%trn_gudang_stock_opname_item}}');
    }
}
