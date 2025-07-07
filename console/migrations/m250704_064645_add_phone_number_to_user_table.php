<?php

use yii\db\Migration;

/**
 * Class m250704_064645_add_phone_number_to_user_table
 */
class m250704_064645_add_phone_number_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'phone_number', $this->string(20)->after('email')->null()->comment('Nomor WhatsApp'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'phone_number');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250704_064645_add_phone_number_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}