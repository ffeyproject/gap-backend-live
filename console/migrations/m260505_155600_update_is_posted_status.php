<?php

use yii\db\Migration;

/**
 * Class m260505_155600_update_is_posted_status
 */
class m260505_155600_update_is_posted_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Update inspecting_item: set is_posted to true and posted_at to qr_print_at where is_posted is currently false
        $this->execute("
            UPDATE inspecting_item 
            SET is_posted = true, 
                posted_at = CAST(qr_print_at AS DATE) 
            WHERE is_posted = false
        ");

        // Update inspecting_mkl_bj_items: set is_posted to true and posted_at to qr_print_at where is_posted is currently false
        $this->execute("
            UPDATE inspecting_mkl_bj_items 
            SET is_posted = true, 
                posted_at = CAST(qr_print_at AS DATE) 
            WHERE is_posted = false
        ");

        // ...
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260505_155600_update_is_posted_status cannot be reverted.\n";
        return false;
    }
}
