<?php

use common\models\ar\MstGreige;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnGudangJadiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

\backend\assets\DataTablesAsset::register($this);

$this->title = 'Gudang Jadi';
$this->params['breadcrumbs'][] = $this->title;

$greigeNameFilter = '';
if(!empty($searchModel->greige_id)){
    $greigeNameFilter = MstGreige::findOne($searchModel['greige_id'])->nama_kain;
}
?>
<div class="trn-gudang-jadi-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'GdJadiGrid',
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'pjax' => true,
        'toolbar' => [
            '{toggleData}',
            '{export}'
        ],
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
            'after'=>false,
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            /*['class' => 'kartik\grid\ActionColumn', 'template' => '{view}'],
            [
                'class' => 'kartik\grid\CheckboxColumn',
                // you may configure additional properties here
            ],*/
            [
                'class' => 'kartik\grid\ActionColumn',
                'template'=>'{add-mix}',
                'buttons'=>[
                    'add-mix' => function($url, $model, $key){
                        /* @var $model TrnGudangJadi*/

                        if($model->status === $model::STATUS_STOCK || $model->status === $model::STATUS_SIAP_KIRIM){$data = $model->attributes;
                            $data['jenisGudangName'] = $model::jenisGudangOptions()[$model->jenis_gudang];
                            $data['marketingName'] = $model->wo->mo->scGreige->sc->marketing->full_name;
                            $data['customerName'] = $model->wo->mo->scGreige->sc->customerName;
                            $data['scNo'] = $model->wo->mo->scGreige->sc->no;
                            $data['woNo'] = $model->wo->no;
                            $data['sourceName'] = TrnGudangJadi::sourceOptions()[$model->source];
                            $data['unitName'] = MstGreigeGroup::unitOptions()[$model->unit];
                            $data['gradeName'] = $model->gradeName;
                            $data['motif'] = $model->wo->greigeNamaKain;
                            $data['qtyFormatted'] = Yii::$app->formatter->asDecimal($model->qty);

                            $dataStr = \yii\helpers\Json::encode($data);
                            return Html::a('<span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>', '#', [
                                'title' => 'Tambah kedalam item',
                                'onclick' => "addSelectedItem(event, {$dataStr})"
                            ]);
                        }

                        return '';
                    }
                ]
            ],

            'id',
            [
                'attribute' => 'jenis_gudang',
                'value' => function($data){
                    /* @var $data TrnGudangJadi*/
                    return TrnGudangJadi::jenisGudangOptions()[$data->jenis_gudang];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangJadi::jenisGudangOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'attribute'=>'marketingName',
                'label'=>'Marketing',
                'value'=>'wo.mo.scGreige.sc.marketing.full_name'
            ],
            [
                'attribute'=>'customerName',
                'label'=>'Buyer',
                'value'=>'wo.mo.scGreige.sc.cust.name'
            ],
            [
                'attribute'=>'scNo',
                'label'=>'Nomor SC',
                'value'=>'wo.mo.scGreige.sc.no'
            ],
            [
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'wo.no'
            ],
            'color',
            [
                'label' => 'No. Lot',
                'value' => function($data){
                    /* @var $data TrnGudangJadi*/
                    if($data->source_ref !== null){
                        $noLot = (new \yii\db\Query())->from(\common\models\ar\TrnInspecting::tableName())
                            ->select('no_lot')
                            ->where(['no'=>$data->source_ref])
                            ->one()
                        ;
                        if($noLot){
                            return $noLot['no_lot'];
                        }else{
                            $noLot = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBj::tableName())
                                ->select('no_lot')
                                ->where(['no'=>$data->source_ref])
                                ->one()
                            ;
                            if($noLot){
                                return $noLot['no_lot'];
                            }
                        }
                    }

                    return '-';
                },
            ],
            [
                'attribute' => 'source',
                'value' => function($data){
                    /* @var $data TrnGudangJadi*/
                    return TrnGudangJadi::sourceOptions()[$data->source];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangJadi::sourceOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            'source_ref',
            [
                'attribute' => 'unit',
                'value' => function($data){
                    /* @var $data TrnGudangJadi*/
                    return MstGreigeGroup::unitOptions()[$data->unit];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => MstGreigeGroup::unitOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            'qty:decimal',
            //'no_urut',
            //'no',

            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal',
                'value' => 'date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        //'timePicker'=>true,
                        //'timePickerIncrement'=>5,
                        'locale'=>[
                            //'format'=>'Y-m-d H:i:s',
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ]
                    ]
                ]
            ],
            [
                'attribute'=>'grade',
                'value'=>function($data){
                    /* @var $data TrnGudangJadi*/
                    return TrnStockGreige::gradeOptions()[$data->grade];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::gradeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute'=>'greige_id',
                'label' => 'Motif',
                'value'=>'wo.greigeNamaKain',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'initValueText' => $greigeNameFilter, // set the initial display text
                    'options' => ['placeholder' => 'Cari ...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['ajax/greige-search']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(member) { return member.text; }'),
                        'templateSelection' => new JsExpression('function (member) { return member.text; }'),
                    ],
                ],
            ],
            [
                'attribute' => 'status',
                'value' => function($data){
                    /* @var $data TrnGudangJadi*/
                    return TrnGudangJadi::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangJadi::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            'dipotong:boolean',
            'hasil_pemotongan:boolean',
            //'note:ntext',
            'created_at:datetime',
            'created_by',
            //'updated_at',
            //'updated_by',
        ],
    ]); ?>

    <?=$this->render('_selected-items')?>
</div>

<?php
$this->registerJsVar('selectedItems', []);

$this->registerJs($this->renderFile(__DIR__.'/js/index.js'), View::POS_END);
