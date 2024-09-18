<?php
use common\models\ar\TrnSc;
use common\models\ar\TrnScSearch;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel TrnScSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Persetujuan Kontrak Pemesanan';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sc-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'resizableColumns' => false,
        'toolbar' => [
            [
                'content'=>
                    Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], [
                        'class' => 'btn btn-default',
                        'title' => 'Refresh data'
                    ])
            ],
            '{toggleData}'
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template' => '{view}'],

            //'id',
            'customerName',
            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL SC',
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
                ],
                'headerOptions' => ['class'=>'text-center', 'style'=>'vertical-align:middle;']
            ],
            [
                'attribute' => 'tipe_kontrak',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnSc::tipeKontrakOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'value' => function($data){
                    /* @var $data TrnSc*/
                    return TrnSc::tipeKontrakOptions()[$data->tipe_kontrak];
                },
            ],
            [
                'attribute' => 'jenis_order',
                'value' => function($data){
                    /* @var $data TrnSc*/
                    return TrnSc::jenisOrderOptions()[$data->jenis_order];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnSc::jenisOrderOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            //'date',
            //'pmt_term',
            //'pmt_method',
            //'ongkos_angkut',
            //'due_date',
            //'delivery_date',
            //'destination:ntext',
            //'packing',
            //'currency_id',
            //'no_po',
            //'disc_grade_b',
            //'consignee_name',
            //'direktur_id',
            //'manager_id',
            'marketingName',
            //'apv_by_dir',
            //'apv_dir_time',
            //'apv_note_dir:ntext',
            //'apv_by_mgr',
            //'apv_mgr_time',
            //'apv_note_mgr:ntext',
            //'notify_party',
            //'buyer_name_in_invoice',
            //'note:ntext',
            //'bank_acct_id',
            //'posted',
            //'closed',
            //'closing_note:ntext',
            'created_at:datetime',
            'creatorName',
            //'updated_at',
            //'updated_by',
        ],
    ]); ?>
</div>
