<?php

use yii\db\Migration;

/**
 * Class m230101_123456_alter_defect_inspecting_items_make_inspecting_item_id_nullable
 */
class m230101_123456_alter_defect_inspecting_items_make_inspecting_item_id_nullable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->alterColumn('defect_inspecting_items', 'inspecting_item_id', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->alterColumn('defect_inspecting_items', 'inspecting_item_id', $this->integer()->notNull());
    }
}