<?php

use yii\db\Migration;

/**
 * Class m250602_035547_add_status_notif_email_to_user
 */
class m250602_035547_add_status_notif_email_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'status_notif_email', $this->boolean()->defaultValue(true)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'status_notif_email');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250602_035547_add_status_notif_email_to_user cannot be reverted.\n";

        return false;
    }
    */
}