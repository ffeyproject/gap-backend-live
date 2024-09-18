<?php

use common\models\ar\PfpKeluarVerpackingItem;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\PfpKeluarVerpackingItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gudang PFP Keluar';
$this->params['breadcrumbs'][] = $this->title;

\backend\assets\DataTablesAsset::register($this);
\backend\assets\BootstrapDatePickerAsset::register($this);
\kartik\select2\Select2Asset::register($this);

$greigeNameFilter = '';
if(!empty($searchModel->greigeId)){
    $greigeNameFilter = \common\models\ar\MstGreige::findOne($searchModel['greigeId'])->nama_kain;
}
?>
<div class="pfp-keluar-verpacking-item-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'pjax' => true,
        'toolbar' => [
            '{toggleData}'
        ],
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
            //'after'=>'',
            //'footer'=>false
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            [
                'class' => 'kartik\grid\ActionColumn',
                'template'=>'{add-mix}',
                'buttons'=>[
                    'add-mix' => function($url, $model, $key){
                        /* @var $model PfpKeluarVerpackingItem*/

                        $data = [
                            'id' => $model->id,
                            'greige_id' => $model->pfpKeluarVerpacking->greige_id,
                            'motif' => $model->pfpKeluarVerpacking->greigeNamaKain,
                            'grade' => TrnStockGreige::GRADE_NG,
                            'grade_name' => TrnStockGreige::gradeOptions()[TrnStockGreige::GRADE_NG],
                            'qty' => $model->ukuran,
                            'qty_fmt' => Yii::$app->formatter->asDecimal($model->ukuran),
                            'unit' => $model->pfpKeluarVerpacking->satuan,
                            'unit_name' => $model->pfpKeluarVerpacking->satuanName,
                            'no_wo' => $model->pfpKeluarVerpacking->wo_id !== null ? $model->pfpKeluarVerpacking->wo->no : null,
                            'nama_buyer' => '',
                        ];
                        $dataStr = \yii\helpers\Json::encode($data);
                        return Html::a('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>', '#', [
                            'title' => 'Tambah kedalam item untuk dijual',
                            'onclick' => "addItem(event, {$dataStr})"
                        ]);
                    }
                ]
            ],
            'id',
            [
                'attribute'=>'greigeId',
                'value'=>'pfpKeluarVerpacking.greigeNamaKain',
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
                            'url' => Url::to(['ajax/lookup-greige']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(member) { return member.text; }'),
                        'templateSelection' => new JsExpression('function (member) { return member.text; }'),
                    ],
                ],
            ],
            //'pfp_keluar_verpacking_id',
            'ukuran:decimal',
            'join_piece',
            [
                'attribute'=>'status',
                'value' => 'statusName',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => PfpKeluarVerpackingItem::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'keterangan:ntext',
        ],
    ]); ?>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><strong>Item Untuk Dijual</strong></div>

    <div class="panel-body">
        <table id="ItemsTable" class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Motif</th>
                <th>Grade</th>
                <th>Qty</th>
                <th>Unit</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="panel-footer">
        <?=Html::button('Jual', ['id'=>'BtnJual', 'class'=>'btn btn-success'])?>
    </div>
</div>

<?php
$actionUrl = \yii\helpers\Url::to(['jual-ex-finish/create-pfp-keluar-verpacking']);
$urlCustomerSearch = \yii\helpers\Url::to(['/ajax/customer-search']);

$this->registerJsVar('formJual', $this->render('_form_jual'));
$this->registerJsVar('actionUrl', $actionUrl);
$this->registerJsVar('urlCustomerSearch', $urlCustomerSearch);

/*$js = <<<JS
var actionUrl = "{$actionUrl}";
JS;*/

$this->registerJs($this->render('js/index.js'), $this::POS_END);