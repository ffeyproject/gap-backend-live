<?php

use yii\db\Migration;
use common\models\ar\InspectingMklBj;
use common\models\ar\InspectingMklBjItems;
use common\models\ar\TrnGudangJadi;

/**
 * Class m260626_031700_sync_inspecting_mkl_bj_partial_statuses
 */
class m260626_031700_sync_inspecting_mkl_bj_partial_statuses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $db = $this->db;
        
        // Count the total number of inspecting_mkl_bj records that are Posted, Delivered, or Posted Partial
        $startDate = strtotime('2026-06-01 00:00:00');
        $endDate = strtotime('2026-06-30 23:59:59');

        $count = (new \yii\db\Query())
            ->from('inspecting_mkl_bj')
            ->where(['status' => [2, 3, 4]])
            ->andWhere(['>=', 'created_at', $startDate])
            ->andWhere(['<=', 'created_at', $endDate])
            ->count('*', $db);
            
        echo "Found {$count} Inspecting Mkl Bj records to check/sync.\n";
        
        $limit = 500;
        $offset = 0;
        
        while ($offset < $count) {
            $rows = (new \yii\db\Query())
                ->select(['id', 'no', 'status', 'delivered_at', 'delivered_by'])
                ->from('inspecting_mkl_bj')
                ->where(['status' => [2, 3, 4]])
                ->andWhere(['>=', 'created_at', $startDate])
                ->andWhere(['<=', 'created_at', $endDate])
                ->orderBy(['id' => SORT_ASC])
                ->limit($limit)
                ->offset($offset)
                ->all($db);
                
            if (empty($rows)) {
                break;
            }
            
            $inspectingIds = array_column($rows, 'id');
            
            // Get all items for these inspecting documents
            $items = (new \yii\db\Query())
                ->select(['id', 'inspecting_id', 'qty', 'is_head', 'join_piece'])
                ->from('inspecting_mkl_bj_items')
                ->where(['inspecting_id' => $inspectingIds])
                ->all($db);
                
            // Group items by inspecting_id
            $itemsByInspecting = [];
            $itemIds = [];
            foreach ($items as $item) {
                $itemsByInspecting[$item['inspecting_id']][] = $item;
                $itemIds[] = (int)$item['id'];
            }
            
            // Find which item IDs are received in trn_gudang_jadi
            $receivedItemIds = [];
            if (!empty($itemIds)) {
                $receivedRows = (new \yii\db\Query())
                    ->select(['id_from'])
                    ->from('trn_gudang_jadi')
                    ->where(['id_from' => $itemIds, 'trans_from' => 'MKL'])
                    ->all($db);
                foreach ($receivedRows as $r) {
                    $receivedItemIds[(int)$r['id_from']] = true;
                }
            }
            
            foreach ($rows as $row) {
                $inspectingId = (int)$row['id'];
                $oldStatus = (int)$row['status'];
                
                $docItems = $itemsByInspecting[$inspectingId] ?? [];
                if (empty($docItems)) {
                    continue;
                }
                
                // Track received join pieces
                $joinPieceHasReceived = [];
                foreach ($docItems as $ii) {
                    if (!empty($ii['join_piece']) && isset($receivedItemIds[(int)$ii['id']])) {
                        $joinPieceHasReceived[$ii['join_piece']] = true;
                    }
                }
                
                $allReceived = true;
                $receivedCount = 0;
                $totalItemsCount = 0;
                
                foreach ($docItems as $item) {
                    if ((int)$item['is_head'] === 1 && (float)$item['qty'] > 0) {
                        $totalItemsCount++;
                        $isReceived = isset($receivedItemIds[(int)$item['id']]);
                        if (!$isReceived && !empty($item['join_piece']) && isset($joinPieceHasReceived[$item['join_piece']])) {
                            $isReceived = true;
                        }
                        if ($isReceived) {
                            $receivedCount++;
                        } else {
                            $allReceived = false;
                        }
                    }
                }
                
                $newStatus = $oldStatus;
                if ($totalItemsCount > 0) {
                    if ($allReceived) {
                        $newStatus = 3; // STATUS_DELIVERED
                    } elseif ($receivedCount > 0) {
                        $newStatus = 4; // STATUS_POSTED_PARTIAL
                    } else {
                        $newStatus = 2; // STATUS_POSTED
                    }
                }
                
                if ($oldStatus !== $newStatus) {
                    if ($newStatus === 3) {
                        $deliveredAt = $row['delivered_at'] ?: time();
                        $deliveredBy = $row['delivered_by'] ?: 1;
                        $db->createCommand()->update('inspecting_mkl_bj', [
                            'status' => 3,
                            'delivered_at' => $deliveredAt,
                            'delivered_by' => $deliveredBy
                        ], ['id' => $inspectingId])->execute();
                    } else {
                        $db->createCommand()->update('inspecting_mkl_bj', [
                            'status' => $newStatus
                        ], ['id' => $inspectingId])->execute();
                    }
                    echo "Updated Inspecting Mkl Bj ID {$inspectingId} (No: {$row['no']}) status: {$oldStatus} -> {$newStatus}\n";
                }
            }
            
            $offset += $limit;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260626_031700_sync_inspecting_mkl_bj_partial_statuses cannot be reverted.\n";
        return false;
    }
}
