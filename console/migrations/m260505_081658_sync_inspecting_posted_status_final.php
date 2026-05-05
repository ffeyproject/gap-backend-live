<?php

use yii\db\Migration;

/**
 * Class m260505_081658_sync_inspecting_posted_status_final
 */
class m260505_081658_sync_inspecting_posted_status_final extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Sync standard Inspecting
        // Mencari TrnInspecting yang:
        // - Masih DRAFT tapi ada itemnya di Gudang Jadi
        // - ATAU statusnya sudah APPROVED tapi nomor (no) nya masih kosong (karena migrasi sebelumnya menggunakan SQL mentah)
        $inspectings = \common\models\ar\TrnInspecting::find()
            ->alias('t')
            ->innerJoin('inspecting_item it', 'it.inspecting_id = t.id')
            ->innerJoin('trn_gudang_jadi gj', 'gj.id_from = it.id AND gj.trans_from = \'INS\'')
            ->where(['or',
                ['t.status' => 1], // STATUS_DRAFT
                ['and', ['>', 't.status', 1], ['t.no' => null]]
            ])
            ->distinct()
            ->all();

        foreach ($inspectings as $model) {
            \common\models\ar\InspectingItem::updateAll(
                ['is_posted' => true, 'posted_at' => new \yii\db\Expression('NOW()')],
                ['inspecting_id' => $model->id, 'is_posted' => false]
            );

            if ($model->status == 1 || empty($model->no)) {
                if($model->status == 1) {
                    $model->status = 3; // STATUS_APPROVED
                }
                $model->setNomor();
                if (empty($model->approved_at)) {
                    $model->approved_at = time();
                }
                $model->save(false, ['status', 'no', 'no_urut', 'approved_at']);
            }
        }

        // 2. Sync Makloon / Barang Jadi
        $mklBjs = \common\models\ar\InspectingMklBj::find()
            ->alias('t')
            ->innerJoin('inspecting_mkl_bj_items it', 'it.inspecting_id = t.id')
            ->innerJoin('trn_gudang_jadi gj', 'gj.id_from = it.id AND gj.trans_from = \'MKL\'')
            ->where(['or',
                ['t.status' => 1], // STATUS_DRAFT
                ['and', ['>', 't.status', 1], ['t.no' => null]]
            ])
            ->distinct()
            ->all();

        foreach ($mklBjs as $model) {
            \common\models\ar\InspectingMklBjItems::updateAll(
                ['is_posted' => true, 'posted_at' => new \yii\db\Expression('NOW()')],
                ['inspecting_id' => $model->id, 'is_posted' => false]
            );

            if ($model->status == 1 || empty($model->no)) {
                if($model->status == 1) {
                    $model->status = 2; // STATUS_POSTED
                }
                $model->setNomor();
                $model->save(false, ['status', 'no', 'no_urut']);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260505_081658_sync_inspecting_posted_status_final cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260505_081658_sync_inspecting_posted_status_final cannot be reverted.\n";

        return false;
    }
    */
}
