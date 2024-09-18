<?php

use yii\db\Migration;

/**
 * Class m210503_053754_create_addition_data
 */
class m210503_053754_create_addition_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%pfp_keluar_verpacking_item}}', 'status', $this->smallInteger()->notNull()->defaultValue(1)->comment('1=Stock, 2=Dijual'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210503_053754_create_addition_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210503_053754_create_addition_data cannot be reverted.\n";

        return false;
    }
    */
}
