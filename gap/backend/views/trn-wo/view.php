<?php
use backend\components\ajax_modal\AjaxModal;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnWo */
/* @var $stockM string */
/* @var $bookedM string */
/* @var $stockLabel string */
/* @var $bookkLabel string */
/* @var $avM string */

$this->title = 'Work Order - '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Work Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-wo-view">
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
                    'title' => 'Posting WO: '.$model->id,
                    'data' => [
                        'confirm' => 'Are you sure you want to posting this item?',
                        'method' => 'post',
                    ],
                ]);
                break;
            case $model::STATUS_APPROVED:
                echo Html::a('Close', ['close', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'onclick' => 'closeWo(event);',
                        'title' => 'Close WO: '.$model->id
                    ]).' '.
                    Html::a('Batalkan', ['batal', 'id' => $model->id], [
                        'class' => 'btn btn-warning',
                        'title' => 'Batalkan WO: '.$model->id,
                        'onclick' => 'batalWo(event);',
                    ])
                ;
                break;
        }
        ?>
    </p>

    <?=$this->render('_mo-info', ['mo' => $model->mo, 'sc'=>$model->sc, 'scGreige'=>$model->scGreige])?>

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

    <?php
    if($model->status === $model::STATUS_POSTED){
        echo '<p>';
        if($model->validasi_stock){
            echo 'Validasi Stock: Aktif '.Html::a('Jangan Validasi Stock Ketika Approval', ['validasi-stock-off', 'id'=>$model->id], ['class'=>'btn btn-xs btn-warning', 'title' => 'Matikan validasi stock ketika approval.', 'data' => ['confirm' => 'Are you sure you want to proccess this item?', 'method' => 'post']]);
        }else{
            echo 'Validasi Stock: Tidak Aktif '.Html::a('Validasi Stock Ketika Approval', ['validasi-stock-on', 'id'=>$model->id], ['class'=>'btn btn-xs btn-success', 'title' => 'Aktifkan validasi stock ketika approval.', 'data' => ['confirm' => 'Are you sure you want to proccess this item?', 'method' => 'post']]);
        }
        echo '</p>';
    }
    ?>
    <?=$this->render('_detail', [
        'model'=>$model,
        'mo' => $model->mo,
        'sc'=>$model->sc,
        'scGreige'=>$model->scGreige,
        'stockM' => $stockM,
        'bookedM' => $bookedM,
        'stockLabel' => $stockLabel,
        'bookkLabel' => $bookkLabel,
        'avM' => $avM
    ])?>

    <?=$this->render('_colors', ['model' => $model])?>

    <?=$this->render('_persetujuan', ['model'=>$model])?>

    <?php
    echo $this->render('_memo-perubahan', ['model' => $model]);
    ?>
</div>

<?php
echo AjaxModal::widget([
    'id' => 'trnWoModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);
$this->registerJs($this->renderFile(__DIR__.'/js/view.js'), View::POS_END);
