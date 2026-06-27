<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use common\models\ar\TrnGreigeStockHistory;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnGreigeStockHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Greige Stock History';
$this->params['breadcrumbs'][] = $this->title;

function formatChange($old, $new) {
    $oldVal = (float)$old;
    $newVal = (float)$new;
    if ($oldVal === $newVal) {
        return Yii::$app->formatter->asDecimal($oldVal);
    }
    $diff = $newVal - $oldVal;
    $class = $diff > 0 ? 'text-success' : 'text-danger';
    $sign = $diff > 0 ? '+' : '';
    $formattedDiff = Yii::$app->formatter->asDecimal($diff);
    
    return Yii::$app->formatter->asDecimal($oldVal) . ' &rarr; ' . Yii::$app->formatter->asDecimal($newVal) . 
        ' (' . Html::tag('strong', Html::tag('span', $sign . $formattedDiff, ['class' => $class])) . ')';
}
?>
<div class="trn-greige-stock-history-index">

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before' => Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['index'], ['class' => 'btn btn-default']),
                ['class' => 'btn-group', 'role' => 'group']
            ),
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            [
                'attribute' => 'created_at',
                'label' => 'WAKTU',
                'format' => 'datetime',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' to ',
                        ]
                    ]
                ],
            ],
            [
                'attribute' => 'greigeName',
                'label' => 'GREIGE',
                'value' => 'greige.nama_kain',
            ],
            [
                'label' => 'PERUBAHAN DETAIL',
                'value' => function($data) {
                    $fields = [
                        'gap' => 'Gap',
                        'stock' => 'Stock',
                        'available' => 'Available',
                        'booked_wo' => 'Booked WO',
                        'stock_pfp' => 'Stock PFP',
                        'stock_wip' => 'Stock WIP',
                        'stock_ef' => 'Stock EF',
                        'booked' => 'Booked',
                        'booked_pfp' => 'Booked PFP',
                        'booked_wip' => 'Booked WIP',
                        'booked_ef' => 'Booked EF',
                        'booked_opfp' => 'Booked OPFP',
                        'available_pfp' => 'Available PFP',
                        'stock_opname' => 'Stock Opname',
                    ];
                    
                    $changes = [];
                    foreach ($fields as $field => $label) {
                        $oldAttr = $field . '_old';
                        $newAttr = $field . '_new';
                        $oldVal = (float)$data->$oldAttr;
                        $newVal = (float)$data->$newAttr;
                        
                        if ($oldVal !== $newVal) {
                            $changes[] = Html::tag('div', 
                                Html::tag('span', $label, ['style' => 'display:inline-block; width:120px; font-weight:bold;']) . ': ' . 
                                formatChange($oldVal, $newVal),
                                ['style' => 'margin-bottom: 2px;']
                            );
                        }
                    }
                    
                    return empty($changes) ? '-' : implode('', $changes);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'context',
                'label' => 'KONTEKS / TRANSAKSI',
            ],
            [
                'attribute' => 'created_by',
                'label' => 'OLEH',
                'value' => function($data) {
                    return $data->createdBy ? $data->createdBy->username : '-';
                },
            ],

            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
