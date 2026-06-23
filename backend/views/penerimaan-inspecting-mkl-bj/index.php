<?php
use common\models\ar\InspectingMklBj;
use common\models\ar\InspectingMklBjSearch;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel InspectingMklBjSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Penerimaan Packing Makloon Dan Barang Jadi';
$this->params['breadcrumbs'][] = $this->title;

$woNoFilter = '';
if(!empty($searchModel->wo_id)){
    $woNoFilter = \common\models\ar\TrnWo::findOne($searchModel['wo_id'])->no;
}
?>
<div class="inspecting-index">
    <p>
        <?= Html::button('<i class="fa fa-refresh"></i> Sync Data Stuck', [
            'class' => 'btn btn-warning',
            'title' => 'Perbaiki data yang sudah di-receive semua tapi masih muncul',
            'data-toggle' => 'modal',
            'data-target' => '#syncModal'
        ]) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>false,
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            'id',
            'no',
            [
                'attribute'=>'wo_id',
                'label'=>'WO No.',
                'value'=>'woNo',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'initValueText' => $woNoFilter, // set the initial display text
                    'options' => ['placeholder' => 'Cari ...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['/ajax/lookup-wo-all']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(member) { return member.text; }'),
                        'templateSelection' => new JsExpression('function (member) { return member.text; }'),
                    ],
                ],
            ],
            'greigeName',
            'colorName',
            'designName',
            'articleName',
            'tgl_inspeksi:date',
            'tgl_kirim:date',
            'no_lot',
            [
                'attribute'=>'satuan',
                'value'=>'satuanName',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\MstGreigeGroup::unitOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'label' => 'Total Qty',
                'value'=>function($data){
                    /* @var $data InspectingMklBj */
                    $total = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBjItems::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->sum('qty')
                    ;
                    return $total > 0 ? $total : 0;
                },
                'format' => 'decimal'
            ],
            //'jenis',
            [
                'attribute'=>'jenis',
                'value'=>'jenisName',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => InspectingMklBj::jenisOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            /*[
                'attribute'=>'status',
                'value'=>'statusName',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => InspectingMklBj::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],*/
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'status',
        ],
    ]); ?>


</div>

<!-- Modal Sync -->
<div class="modal fade" id="syncModal" tabindex="-1" role="dialog" aria-labelledby="syncModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <?php $form = \yii\widgets\ActiveForm::begin(['action' => ['sync-status'], 'method' => 'get']); ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="syncModalLabel">Sync Data Stuck</h4>
      </div>
      <div class="modal-body">
        <p>Proses sinkronisasi akan mengecek semua data yang sudah di-receive namun statusnya masih nyangkut di Penerimaan. Karena proses ini membutuhkan waktu, silakan filter berdasarkan bulan dan tahun.</p>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Bulan</label>
                    <?= Html::dropDownList('bulan', date('m'), [
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ], ['class' => 'form-control']) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tahun</label>
                    <?= Html::dropDownList('tahun', date('Y'), array_combine(range(date('Y')-5, date('Y')), range(date('Y')-5, date('Y'))), ['class' => 'form-control']) ?>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-warning"><i class="fa fa-refresh"></i> Mulai Sync</button>
      </div>
      <?php \yii\widgets\ActiveForm::end(); ?>
    </div>
  </div>
</div>
