<?php
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $stocks yii\data\ActiveDataProvider */
/* @var $model common\models\ar\TrnKartuProsesDyeingItem */

echo GridView::widget([
    'dataProvider' => $stocks,
    'columns' => [
        'id',
        'no_lot',
        'panjang_m',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{pilih}',
            'buttons' => [
                'pilih' => function ($url, $stock) use ($model) {
                    return Html::a('Pilih', ['trn-kartu-proses-dyeing/set-stock',
                        'item_id' => $model->id,
                        'stock_id' => $stock->id,
                    ], ['class' => 'btn btn-success']);
                }
            ],
        ],
    ],
]);