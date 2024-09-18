<?php

use backend\components\ajax_modal\AjaxModal;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnSc */

$this->title = 'Sales Contract - ID:'.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Sales Contract', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

if($model->tipe_kontrak === $model::TIPE_KONTRAK_LOKAL){
    $jenis = 'local';
}else $jenis = 'export';
?>
<div class="trn-sc-view">
    <p>
        <?php
        switch ($model->status){
            case $model::STATUS_DRAFT:
                echo Html::a('Update', ['update-'.$jenis, 'id' => $model->id], ['class' => 'btn btn-primary']).' ';
                echo Html::a('Delete', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]).' ';
                echo Html::a('Posting', ['posting', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => 'Are you sure you want to posting this SC?',
                        'method' => 'post',
                    ],
                ]);
                break;
            case $model::STATUS_APPROVED:
                if($model->status != $model::STATUS_CLOSED){
                    echo Html::a('Close', ['close', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to close this SC?',
                            'method' => 'post',
                        ],
                    ]);
                }
                break;
        }
        ?>
    </p>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?=$model->getAttributeLabel('id')?></th>
                            <td><?=$model->id?></td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-4">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?=$model->getAttributeLabel('no_urut')?></th>
                            <td><?=$model->no_urut?></td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-4">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><?=$model->getAttributeLabel('no')?></th>
                            <td><?=$model->no?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php echo $this->render('child/_persetujuan', ['model' => $model]); ?>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute'=>'status',
                                'value'=>$model::statusOptions()[$model->status]
                            ],
                            'posted_at:datetime'
                        ],
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'created_at:datetime',
                            'creatorName',
                        ],
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'updated_at:datetime',
                            'updatorName'
                        ],
                    ]) ?>
                </div>
            </div>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'closed_note:html'
                ],
            ]) ?>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">SC Detail</h3>
            <div class="box-tools pull-right">
                <?=Html::a('<span class="glyphicon glyphicon-print" aria-hidden="true"></span>', ['print-sc', 'id' => $model->id], [
                    'class' => 'btn btn-sm btn-default',
                    'title' => 'Print',
                    'target' => '_blank'
                ])?>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'customerName',
                            'destination:ntext',
                            'date:date',
                            [
                                'attribute' => 'tipe_kontrak',
                                'value' => $model::tipeKontrakOptions()[$model->tipe_kontrak]
                            ],
                            [
                                'attribute' => 'jenis_order',
                                'value' => $model::jenisOrderOptions()[$model->jenis_order]
                            ],
                            [
                                'attribute' => 'currency',
                                'value' => $model::currencyOptions()[$model->currency]
                            ],
                            [
                                'attribute' => 'ongkos_angkut',
                                'value' => $model::ongkosAngkutOptions()[$model->ongkos_angkut]
                            ]
                        ],
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'bankAcctName',
                            'pmt_term',
                            'due_date:date',
                            'delivery_date:date',
                            'pmt_method',
                            'marketingName',
                            'jet_black:boolean'
                        ],
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'no_po',
                            'disc_grade_b:decimal',
                            'disc_piece_kecil:decimal',
                            'consignee_name',
                            'notify_party',
                            'buyer_name_in_invoice'
                        ],
                    ]) ?>
                </div>
            </div>

            <p>
                <?=DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'packing:html',
                        'note:html'
                    ],
                ]) ?>
            </p>
        </div>
    </div>

    <?php echo $this->render('child/_sc_greige_groups', ['model' => $model]);?>

    <?php echo $this->render('child/_sc_agens', ['model' => $model]);?>

    <?php echo $this->render('child/_sc_komisis', ['model' => $model]);?>

    <?php
    if($model->status == $model::STATUS_APPROVED){
        echo $this->render('child/_memo_perubahan', ['model' => $model]);
    }
    ?>
</div>

<?php
echo AjaxModal::widget([
    'id' => 'trnScModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);
$this->registerJs($this->renderFile(__DIR__.'/js/view.js'), View::POS_END);
