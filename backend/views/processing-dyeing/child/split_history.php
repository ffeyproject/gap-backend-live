<?php
use common\models\ar\TrnKartuProsesDyeing;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesDyeing */

$parentCard = null;
$siblingCards = [];
$childCards = [];

if ($model->kartu_proses_id !== null) {
    // This is a child split card
    $parentCard = TrnKartuProsesDyeing::findOne($model->kartu_proses_id);
    if ($parentCard) {
        $siblingCards = TrnKartuProsesDyeing::find()
            ->where(['kartu_proses_id' => $parentCard->id])
            ->andWhere(['<>', 'id', $model->id])
            ->all();
    }
} else {
    // This is a parent card
    $childCards = TrnKartuProsesDyeing::find()
        ->where(['kartu_proses_id' => $model->id])
        ->all();
}

$hasSplitHistory = ($parentCard !== null) || !empty($childCards);
?>

<?php if ($hasSplitHistory): ?>
<div class="box box-solid box-default" style="border-top: 3px solid #605ca8;">
    <div class="box-header with-border" style="background: #fcfaff;">
        <h3 class="box-title" style="color: #605ca8; font-weight: bold;">
            ✂️ Silsilah & Riwayat Split Kartu
        </h3>
    </div>
    <div class="box-body">
        <?php if ($parentCard !== null): ?>
            <div style="background: #f3f0f7; padding: 12px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #dcd3e9;">
                <strong style="color: #605ca8;">🏢 Kartu Induk Asal: </strong>
                <?= Html::a($parentCard->nomor_kartu, ['view', 'id' => $parentCard->id], ['class' => 'label label-primary', 'style' => 'font-size: 13px; font-weight: bold;']) ?>
                <span class="text-muted" style="margin-left: 10px;">
                    (Status: <?= Html::encode($parentCard::statusOptions()[$parentCard->status] ?? '') ?> | <?= count($parentCard->trnKartuProsesDyeingItems) ?> Rolls)
                </span>
            </div>
            
            <?php if (!empty($siblingCards)): ?>
                <h4 style="font-weight: 700; color: #4a4a4a; margin-top: 15px; margin-bottom: 10px;">👥 Kartu Split Saudara (Sibling Split Cards):</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
                        <thead>
                            <tr style="background-color: #f9f9f9; color: #555;">
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Nomor Kartu Split</th>
                                <th>Status</th>
                                <th>Jumlah Roll</th>
                                <th style="width: 120px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($siblingCards as $sibling): ?>
                                <tr>
                                    <td class="text-center" style="vertical-align: middle;"><?= $no++ ?></td>
                                    <td style="vertical-align: middle;">
                                        <strong style="color: #2c3e50;"><?= Html::encode($sibling->nomor_kartu) ?></strong>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span class="label label-default" style="font-size: 11px; padding: .2em .6em .3em;"><?= Html::encode($sibling::statusOptions()[$sibling->status] ?? '') ?></span>
                                    </td>
                                    <td style="vertical-align: middle; font-weight: 600; color: #34495e;"><?= count($sibling->trnKartuProsesDyeingItems) ?> Rolls</td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <?= Html::a('<i class="glyphicon glyphicon-eye-open"></i> View Detail', ['view', 'id' => $sibling->id], ['class' => 'btn btn-xs btn-default', 'style' => 'font-weight: 600;']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div style="background: #f3f0f7; padding: 12px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #dcd3e9;">
                <strong style="color: #605ca8;">🏢 Kartu Induk Utama: </strong>
                <span class="label label-primary" style="font-size: 13px; font-weight: bold;"><?= Html::encode($model->nomor_kartu) ?></span>
                <span class="text-muted" style="margin-left: 10px;">
                    (Status: <?= Html::encode($model::statusOptions()[$model->status] ?? '') ?> | <?= count($model->trnKartuProsesDyeingItems) ?> Rolls)
                </span>
            </div>

            <h4 style="font-weight: 700; color: #4a4a4a; margin-top: 15px; margin-bottom: 10px;">✂️ Hasil Split Kartu (Child Split Cards):</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
                    <thead>
                        <tr style="background-color: #f9f9f9; color: #555;">
                            <th style="width: 50px;" class="text-center">No</th>
                            <th>Nomor Kartu Split</th>
                            <th>Status</th>
                            <th>Jumlah Roll</th>
                            <th>Keterangan / Catatan</th>
                            <th style="width: 120px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($childCards as $child): ?>
                            <tr>
                                <td class="text-center" style="vertical-align: middle;"><?= $no++ ?></td>
                                <td style="vertical-align: middle;">
                                    <strong style="color: #2c3e50;"><?= Html::encode($child->nomor_kartu) ?></strong>
                                </td>
                                <td style="vertical-align: middle;">
                                    <span class="label label-default" style="font-size: 11px; padding: .2em .6em .3em;"><?= Html::encode($child::statusOptions()[$child->status] ?? '') ?></span>
                                </td>
                                <td style="vertical-align: middle; font-weight: 600; color: #34495e;"><?= count($child->trnKartuProsesDyeingItems) ?> Rolls</td>
                                <td style="vertical-align: middle; color: #7f8c8d; font-size: 12px;"><?= Html::encode($child->note) ?></td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <?= Html::a('<i class="glyphicon glyphicon-eye-open"></i> View Detail', ['view', 'id' => $child->id], ['class' => 'btn btn-xs btn-default', 'style' => 'font-weight: 600;']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
