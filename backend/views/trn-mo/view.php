<?php

use backend\components\ajax_modal\AjaxModal;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMo */

$this->title = 'Marketing Order - '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Marketing Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$formatter = Yii::$app->formatter;
$scGreige = $model->scGreige;
$greigeGroup = $scGreige->greigeGroup;
$sc = $model->sc;

switch ($scGreige->process){
    case $scGreige::PROCESS_DYEING:
        $action = 'dyeing';
        $detailView = $this->render('_detail-dyeing', ['model' => $model]);
        break;
    case $scGreige::PROCESS_PRINTING:
        $action = 'printing';
        $detailView = $this->render('_detail-printing', ['model' => $model]);
        break;
    default:
        $detailView = "Mohon maaf, untuk sementara proses \"{$scGreige->proccess->name}\" belum didukung.";
}
?>
<div class="trn-mo-view">
    <p>
        <?php
        switch ($model->status){
            case $model::STATUS_DRAFT:
                echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']).' ';
                echo Html::a('Delete', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]).' ';
                echo Html::a('Posting', ['posting', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'title' => 'Posting MO: '.$model->id,
                    'data' => [
                        'confirm' => 'Are you sure you want to posting this item?',
                        'method' => 'post',
                    ],
                ]);
                break;
            case $model::STATUS_APPROVED:
                echo Html::a('Close', ['close', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'title' => 'Close MO: '.$model->id,
                    'onclick' => 'closeMo(event);',
                ]).' ';
                echo Html::a('Batalkan MO', ['batal', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'title' => 'Batalkan MO: '.$model->id,
                        'onclick' => 'batalMo(event);',
                ]).' ';
                echo Html::a('Buat WO', ['/trn-wo/create', 'mo_id' => $model->id], [
                        'class' => 'btn btn-success',
                        'title' => 'Buat WO',
                        'target' => '_blank'
                    ]).' ';
                break;
        }
        ?>
    </p>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                        ],
                    ]) ?>
                </div>

                <div class="col-md-3">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'no_urut',
                        ],
                    ]) ?>
                </div>

                <div class="col-md-3">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'no',
                        ],
                    ]) ?>
                </div>

                <div class="col-md-3">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute'=>'status',
                                'value'=>$model::statusOptions()[$model->status]
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'closed_at:datetime',
                            [
                                'label'=>'Closed BY',
                                'value'=>$model->closed_by === null ? null : $model->closedBy->full_name
                            ],
                            'closed_note:ntext',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'batal_at:datetime',
                            [
                                'label'=>'Batal BY',
                                'value'=>$model->batal_by === null ? null : $model->batalBy->full_name
                            ],
                            'batal_note:ntext',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'created_at:datetime',
                            'creatorName',
                        ],
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'updated_at:datetime',
                            'updatorName'
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    echo $this->render('_greige-info', [
        'model'=>$model, 'scGreige'=>$scGreige, 'sc'=>$sc, 'greigeGroup'=>$greigeGroup, 'formatter'=>$formatter
    ]);
    ?>

    <?php
    echo $this->render('_detail', [
        'model'=>$model, 'formatter'=>$formatter, 'detailView'=>$detailView,
    ]);
    ?>

    <?php echo $this->render('_colors', ['model' => $model]);?>

    <?php echo $this->render('_persetujuan', ['model' => $model]);?>

    <?php
    if($model->status == $model::STATUS_APPROVED){
        echo $this->render('_memo-perubahan', ['model' => $model]);
    }
    ?>

    <?php
    /*echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'sc_id',
            'sc_greige_id',
            'approval_id',
            'approved_at',
            'no_urut',
            'no',
            'date',
            're_wo',
            'design',
            'article',
            'strike_off:ntext',
            'heat_cut:boolean',
            'sulam_pinggir',
            'handling:ntext',
            'border_size',
            'block_size',
            'foil:boolean',
            'face_stamping:ntext',
            'selvedge_stamping',
            'selvedge_continues',
            'side_band',
            'tag',
            'hanger',
            'label',
            'folder',
            'album',
            'joint:boolean',
            'joint_qty',
            'packing_method',
            'shipping_method',
            'shipping_sorting',
            'plastic',
            'arsip',
            'jet_black:boolean',
            'piece_length',
            'est_produksi',
            'est_packing',
            'target_shipment',
            'posted_at',
            'closed_at',
            'closed_by',
            'closed_note:ntext',
            'reject_notes:ntext',
            'batal_at',
            'batal_by',
            'batal_note:ntext',
            'status',
            'note:ntext',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ],
    ]);*/
    ?>

</div>

<?php
echo AjaxModal::widget([
    'id' => 'trnMoModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);

$this->registerJs($this->renderFile(__DIR__.'/js/view.js'), View::POS_END);
