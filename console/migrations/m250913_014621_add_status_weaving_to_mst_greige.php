<?php

use yii\db\Migration;

/**
 * Class m250913_014621_add_status_weaving_to_mst_greige
 */
class m250913_014621_add_status_weaving_to_mst_greige extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mst_greige', 'status_weaving', $this->smallInteger()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mst_greige', 'status_weaving');
    }
}