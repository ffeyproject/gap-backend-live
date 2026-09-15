<?php

use yii\db\Migration;

/**
 * Class m260915_060740_add_posted_at_and_posted_by_to_trn_order_pfp_table
 */
class m260915_060740_add_posted_at_and_posted_by_to_trn_order_pfp_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%trn_order_pfp}}', 'posted_at', $this->integer()->unsigned()->null());
        $this->addColumn('{{%trn_order_pfp}}', 'posted_by', $this->integer()->unsigned()->null());
        $this->addForeignKey('fk_trn_order_pfp_posted_by', '{{%trn_order_pfp}}', 'posted_by', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_trn_order_pfp_posted_by', '{{%trn_order_pfp}}');
        $this->dropColumn('{{%trn_order_pfp}}', 'posted_by');
        $this->dropColumn('{{%trn_order_pfp}}', 'posted_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260915_060740_add_posted_at_and_posted_by_to_trn_order_pfp_table cannot be reverted.\n";

        return false;
    }
    */
}
