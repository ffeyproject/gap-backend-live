<?php

use yii\db\Migration;

/**
 * Class m260506_002600_sync_printed_drafts_is_posted
 */
class m260506_002600_sync_printed_drafts_is_posted extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Khusus untuk inspecting_mkl_bj_items yang sudah ada qr_print_at tapi header masih status DRAFT (1)
        // Set is_posted = true agar muncul di packing list / warehouse receipt
        $this->execute("
            UPDATE inspecting_mkl_bj_items 
            SET is_posted = true,
                posted_at = COALESCE(posted_at, CAST(qr_print_at AS DATE))
            WHERE qr_print_at IS NOT NULL 
            AND inspecting_id IN (SELECT id FROM inspecting_mkl_bj WHERE status = 1)
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260506_002600_sync_printed_drafts_is_posted cannot be reverted.\n";
        return false;
    }
}
