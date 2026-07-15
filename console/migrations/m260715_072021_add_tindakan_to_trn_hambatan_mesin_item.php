<?php

use yii\db\Migration;

/**
 * Class m260715_072021_add_tindakan_to_trn_hambatan_mesin_item
 */
class m260715_072021_add_tindakan_to_trn_hambatan_mesin_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%trn_hambatan_mesin_item}}', 'tindakan', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%trn_hambatan_mesin_item}}', 'tindakan');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260715_072021_add_tindakan_to_trn_hambatan_mesin_item cannot be reverted.\n";

        return false;
    }
    */
}
