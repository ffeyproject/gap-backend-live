<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_potong_stock}}`.
 */
class m211225_070623_create_trn_potong_stock_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%trn_potong_stock}}', [
            'id' => $this->bigPrimaryKey(),
            'stock_id' => $this->integer()->notNull(),
            'no_urut' => $this->integer(),
            'no' => $this->string(),
            'note' => $this->text(),
            'date' => $this->date()->notNull(),
            'diperintahkan_oleh' => $this->string(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('1=draft, 2=posted'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk_trn_potong_stock_stock', '{{%trn_potong_stock}}', 'stock_id', '{{%trn_gudang_jadi}}', 'id', 'cascade');

        $this->createTable('{{%trn_potong_stock_item}}', [
            'id' => $this->bigPrimaryKey(),
            'potong_stock_id' => $this->bigInteger()->notNull(),
            'qty' => $this->float()->notNull(),
        ]);
        $this->addForeignKey('fk_trn_potong_stock_item_potong_stock', '{{%trn_potong_stock_item}}', 'potong_stock_id', '{{%trn_potong_stock}}', 'id', 'cascade');

        $this->addColumn('{{%trn_gudang_jadi}}', 'hasil_pemotongan', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('{{%trn_gudang_jadi}}', 'dipotong', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%trn_potong_stock_item}}');
        $this->dropTable('{{%trn_potong_stock}}');
    }
}
