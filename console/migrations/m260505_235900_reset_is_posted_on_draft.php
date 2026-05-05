<?php

use yii\db\Migration;

/**
 * Class m260505_235900_reset_is_posted_on_draft
 */
class m260505_235900_reset_is_posted_on_draft extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Update inspecting_item: set is_posted to false where header status is STATUS_DRAFT (1)
        $this->execute("
            UPDATE inspecting_item 
            SET is_posted = false 
            WHERE inspecting_id IN (SELECT id FROM trn_inspecting WHERE status = 1)
        ");

        // Update inspecting_mkl_bj_items: set is_posted to false where header status is STATUS_DRAFT (1)
        $this->execute("
            UPDATE inspecting_mkl_bj_items 
            SET is_posted = false 
            WHERE inspecting_id IN (SELECT id FROM inspecting_mkl_bj WHERE status = 1)
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260505_235900_reset_is_posted_on_draft cannot be reverted.\n";
        return false;
    }
}
