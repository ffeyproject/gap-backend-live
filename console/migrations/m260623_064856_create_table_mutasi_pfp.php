<?php

use yii\db\Migration;

/**
 * Class m260623_064856_create_table_mutasi_pfp
 */
class m260623_064856_create_table_mutasi_pfp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mutasi_pfp}}', [
            'id' => $this->primaryKey(),
            'greige_group_id' => $this->integer(),
            'greige_id' => $this->integer()->notNull(),
            'no_wo' => $this->string()->notNull(),
            'no_urut' => $this->integer(),
            'no' => $this->string(),
            'date' => $this->date()->notNull(),
            'note' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_at' => $this->integer(),
            'updated_by' => $this->integer(),
            'approval_id' => $this->integer(),
            'approval_time' => $this->integer(),
            'reject_note' => $this->text(),
            'status' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_mutasi_pfp_greige_group', '{{%mutasi_pfp}}', 'greige_group_id', '{{%mst_greige_group}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_mutasi_pfp_greige', '{{%mutasi_pfp}}', 'greige_id', '{{%mst_greige}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%mutasi_pfp_item}}', [
            'id' => $this->primaryKey(),
            'mutasi_id' => $this->integer()->notNull(),
            'stock_pfp_id' => $this->integer()->notNull(),
            'note' => $this->text(),
        ]);

        $this->addForeignKey('fk_mutasi_pfp_item_mutasi', '{{%mutasi_pfp_item}}', 'mutasi_id', '{{%mutasi_pfp}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_mutasi_pfp_item_stock', '{{%mutasi_pfp_item}}', 'stock_pfp_id', '{{%trn_stock_greige}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_mutasi_pfp_item_stock', '{{%mutasi_pfp_item}}');
        $this->dropForeignKey('fk_mutasi_pfp_item_mutasi', '{{%mutasi_pfp_item}}');
        $this->dropTable('{{%mutasi_pfp_item}}');

        $this->dropForeignKey('fk_mutasi_pfp_greige', '{{%mutasi_pfp}}');
        $this->dropForeignKey('fk_mutasi_pfp_greige_group', '{{%mutasi_pfp}}');
        $this->dropTable('{{%mutasi_pfp}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260623_064856_create_table_mutasi_pfp cannot be reverted.\n";

        return false;
    }
    */
}
