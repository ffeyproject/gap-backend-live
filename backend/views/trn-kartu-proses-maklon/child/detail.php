<?php
use common\models\ar\TrnKartuProsesMaklon;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesMaklon */
?>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        [
                            'label'=>'Nomor WO',
                            'value'=>Html::a($model->wo->no, ['/trn-wo/view', 'id'=>$model->wo_id], ['title'=>'Detail WO', 'target'=>'blank']),
                            'format'=>'raw'
                        ],
                        'no',
                        [
                            'attribute'=>'vendor_id',
                            'value'=>$model->vendor->name
                        ],
                        [
                            'attribute'=>'unit',
                            'value'=>\common\models\ar\MstGreigeGroup::unitOptions()[$model->unit]
                        ],
                        [
                            'attribute'=>'status',
                            'value'=>$model::statusOptions()[$model->status]
                        ],
                        'note:ntext',
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'date:date',
                        'created_at:datetime',
                        'created_by',
                        'updated_at:datetime',
                        'updated_by',
                        'approved_at:datetime',
                        'approved_by'
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>

<?php
/*echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'sc_id',
        'sc_greige_id',
        'mo_id',
        'wo_id',
        'vendor_id',
        'process',
        'no_urut',
        'no',
        'asal_greige',
        'penerima',
        'note:ntext',
        'date',
        'posted_at',
        'approved_at',
        'approved_by',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ],
]);*/
?>
