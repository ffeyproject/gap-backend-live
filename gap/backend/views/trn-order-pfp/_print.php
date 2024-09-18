<?php
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnOrderPfp */
?>

<table>
    <tr>
        <td style="width: 50%;">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'label'=>'Greige Group',
                        'value'=>$model->greigeGroup->nama_kain
                    ],
                    [
                        'label'=>'Handling',
                        'value'=>$model->handling->name
                    ],
                    [
                        'label'=>'Greige',
                        'value'=>$model->greige->nama_kain
                    ],
                    'no',
                    [
                        'attribute'=>'qty',
                        'value'=>Yii::$app->formatter->asDecimal($model->qty).' Batch'
                    ],
                    'note:ntext',
                    [
                        'label'=>'Status',
                        'value'=>$model::statusOptions()[$model->status]
                    ],
                    'dasar_warna',
                    [
                        'attribute'=>'proses_sampai',
                        'value'=>$model->proses_sampai != null ? $model::prosesSampaiOptions()[$model->proses_sampai] : null
                    ],
                ],
            ]) ?>
        </td>
        <td style="width: 50%;">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'date:date',
                    'created_at:datetime',
                    [
                        'label'=>'Dibuat Oleh',
                        'value'=>$model->createdBy->full_name
                    ],
                    'updated_at:datetime',
                    [
                        'label'=>'Diubah Oleh',
                        'value'=>$model->updatedBy->full_name
                    ],
                    'approved_at:datetime',
                    [
                        'attribute'=>'approved_by',
                        'value'=>$model->approvedBy->full_name
                    ],
                    'approved_at:datetime',
                    'approval_note'
                ],
            ]) ?>
        </td>
    </tr>
</table>

<table>
    <tr>
        <td style="width: 30%;" class="text-center">
            <p>Dibuat Oleh</p>
            <p><?=Html::img($model->createdBy->signatureUrl, ['style'=>'height:80px;'])?></p>
            <p><?=$model->createdBy->full_name?></p>
        </td>
        <td style="width: 40%;"></td>
        <td style="width: 30%;" class="text-center">
            <p>Menyetujui</p>
            <?php
            if($model->status == $model::STATUS_APPROVED){
                echo '<p>'.Html::img($model->approvedBy->signatureUrl, ['style'=>'height:80px;']).'</p>';
            }else{
                echo '<p><span style="height:80px;">BELUM DISETUJUI</span></p>';
            }
            ?>
            <p><?=$model->approvedBy->full_name?></p>
        </td>
    </tr>
</table>