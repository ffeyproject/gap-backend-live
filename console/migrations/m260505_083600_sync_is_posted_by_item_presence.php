<?php

use yii\db\Migration;

/**
 * Class m260505_083600_sync_is_posted_by_item_presence
 */
class m260505_083600_sync_is_posted_by_item_presence extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Sync Standard Inspecting Items (INS)
        // Update items that are directly in GJ
        $this->execute("
            UPDATE inspecting_item it
            SET is_posted = true, posted_at = NOW()
            FROM trn_gudang_jadi gj
            WHERE it.id = gj.id_from 
            AND gj.trans_from = 'INS'
            AND it.is_posted = false
        ");

        // Update items that are part of a join piece that has been received
        $this->execute("
            UPDATE inspecting_item it
            SET is_posted = true, posted_at = NOW()
            FROM (
                SELECT DISTINCT it2.inspecting_id, it2.join_piece
                FROM inspecting_item it2
                JOIN trn_gudang_jadi gj ON it2.id = gj.id_from AND gj.trans_from = 'INS'
                WHERE it2.join_piece IS NOT NULL AND it2.join_piece <> ''
            ) sub
            WHERE it.inspecting_id = sub.inspecting_id
            AND it.join_piece = sub.join_piece
            AND it.is_posted = false
        ");

        // 2. Sync Makloon / Barang Jadi Items (MKL)
        // Update items that are directly in GJ
        $this->execute("
            UPDATE inspecting_mkl_bj_items it
            SET is_posted = true, posted_at = NOW()
            FROM trn_gudang_jadi gj
            WHERE it.id = gj.id_from 
            AND gj.trans_from = 'MKL'
            AND it.is_posted = false
        ");

        // Update items that are part of a join piece that has been received
        $this->execute("
            UPDATE inspecting_mkl_bj_items it
            SET is_posted = true, posted_at = NOW()
            FROM (
                SELECT DISTINCT it2.inspecting_id, it2.join_piece
                FROM inspecting_mkl_bj_items it2
                JOIN trn_gudang_jadi gj ON it2.id = gj.id_from AND gj.trans_from = 'MKL'
                WHERE it2.join_piece IS NOT NULL AND it2.join_piece <> ''
            ) sub
            WHERE it.inspecting_id = sub.inspecting_id
            AND it.join_piece = sub.join_piece
            AND it.is_posted = false
        ");
        
        // 3. Update Header Statuses
        $this->execute("
            UPDATE trn_inspecting
            SET status = 3 -- STATUS_APPROVED
            WHERE status = 1 -- STATUS_DRAFT
            AND id IN (SELECT DISTINCT inspecting_id FROM inspecting_item WHERE is_posted = true)
        ");

        $this->execute("
            UPDATE inspecting_mkl_bj
            SET status = 2 -- STATUS_POSTED
            WHERE status = 1 -- STATUS_DRAFT
            AND id IN (SELECT DISTINCT inspecting_id FROM inspecting_mkl_bj_items WHERE is_posted = true)
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260505_083600_sync_is_posted_by_item_presence cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260505_083600_sync_is_posted_by_item_presence cannot be reverted.\n";

        return false;
    }
    */
}
