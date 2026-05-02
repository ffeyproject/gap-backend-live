<?php

use yii\db\Migration;

/**
 * Class m260502_044020_add_is_posted_to_inspecting_items
 */
class m260502_044020_add_is_posted_to_inspecting_items extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('inspecting_item', 'is_posted', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('inspecting_mkl_bj_items', 'is_posted', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('inspecting_item', 'is_posted');
        $this->dropColumn('inspecting_mkl_bj_items', 'is_posted');
    }
}
