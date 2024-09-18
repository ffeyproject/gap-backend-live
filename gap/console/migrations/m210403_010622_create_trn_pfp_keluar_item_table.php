<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_pfp_keluar_item}}`.
 */
class m210403_010622_create_trn_pfp_keluar_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%trn_pfp_keluar_item}}', [
            'pfp_keluar_id' => $this->integer()->unsigned()->notNull(),
            'stock_pfp_id' => $this->integer()->unsigned()->notNull(),
            'note' => $this->text()
        ]);

        $this->addPrimaryKey('trn_pfp_keluar_item_pkey', '{{%trn_pfp_keluar_item}}', ['pfp_keluar_id', 'stock_pfp_id']);
        $this->addForeignKey('fk_trn_pfp_keluar_item_stock_pfp', '{{%trn_pfp_keluar_item}}', 'stock_pfp_id', 'trn_stock_greige', 'id');
        $this->addForeignKey('fk_trn_pfp_keluar_item_pfp_keluar', '{{%trn_pfp_keluar_item}}', 'pfp_keluar_id', 'trn_pfp_keluar', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%trn_pfp_keluar_item}}');
    }
}
