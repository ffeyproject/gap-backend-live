<?php

use yii\db\Migration;

/**
 * Class m260505_081203_sync_is_posted_with_gudang_jadi
 */
class m260505_081203_sync_is_posted_with_gudang_jadi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Sync inspecting_item
        // Mark ALL items of an inspection as posted if at least one item of that inspection is in trn_gudang_jadi
        $this->execute("
            UPDATE inspecting_item
            SET is_posted = true, posted_at = NOW()
            WHERE is_posted = false
            AND inspecting_id IN (
                SELECT DISTINCT it.inspecting_id
                FROM inspecting_item it
                JOIN trn_gudang_jadi gj ON it.id = gj.id_from AND gj.trans_from = 'INS'
            )
        ");

        // 2. Sync trn_inspecting header status (if items are posted, header should be at least POSTED or APPROVED)
        // Using 3 (STATUS_APPROVED) as it is the standard next step after posting.
        $this->execute("
            UPDATE trn_inspecting
            SET status = 3 
            WHERE status = 1 
            AND id IN (
                SELECT DISTINCT inspecting_id FROM inspecting_item WHERE is_posted = true
            )
        ");

        // 3. Sync inspecting_mkl_bj_items
        $this->execute("
            UPDATE inspecting_mkl_bj_items
            SET is_posted = true, posted_at = NOW()
            WHERE is_posted = false
            AND inspecting_id IN (
                SELECT DISTINCT it.inspecting_id
                FROM inspecting_mkl_bj_items it
                JOIN trn_gudang_jadi gj ON it.id = gj.id_from AND gj.trans_from = 'MKL'
            )
        ");

        // 4. Sync inspecting_mkl_bj header status
        // Using 2 (STATUS_POSTED) as per InspectingMklBj constants
        $this->execute("
            UPDATE inspecting_mkl_bj
            SET status = 2 
            WHERE status = 1 
            AND id IN (
                SELECT DISTINCT inspecting_id FROM inspecting_mkl_bj_items WHERE is_posted = true
            )
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260505_081203_sync_is_posted_with_gudang_jadi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260505_081203_sync_is_posted_with_gudang_jadi cannot be reverted.\n";

        return false;
    }
    */
}
