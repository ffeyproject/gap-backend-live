<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnKirimBuyer;
use common\models\ar\TrnKirimBuyerItem;
use kartik\dialog\Dialog;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimBuyerHeader */
/* @var $providerStock \yii\data\ActiveDataProvider*/
/* @var $dataProviderKirimBuyer \yii\data\ActiveDataProvider*/

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pengiriman Ke Buyer', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$formatter = Yii::$app->formatter;
?>
<div class="trn-kirim-buyer-header-view">
    <p>
        <?php if($model->status == $model::STATUS_DRAFT):?>
            <?= Html::a('Ubah', ['update', 'id'=>$model->id], ['class' => 'btn btn-primary']) ?>

            <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Posting', ['posting', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Anda yakin akan memposting item ini?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif;?>

        <?= Html::a('Buat Baru', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'label'=>'Buyer',
                                'value'=>$model->customer->name
                            ],
                            'date:date',
                            'no_urut',
                            'no',
                            'pengirim',
                            'penerima',
                            'kepala_gudang',
                            [
                                'attribute'=>'status',
                                'value'=>$model::statusOptions()[$model->status]
                            ],
                            'plat_nomor',
                            'is_export:boolean',
                            'is_resmi:boolean'
                        ],
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'nama_buyer',
                            'alamat_buyer',
                            'created_at:datetime',
                            'created_by',
                            'updated_at:datetime',
                            'updated_by',
                            'note:ntext',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= GridView::widget([
                'dataProvider' => $providerStock,
                'id' => 'GdJadiGrid',
                'resizableColumns' => false,
                'responsiveWrap' => false,
                'toolbar' => false,
                'panel' => [
                    'type' => 'default',
                    'before'=>false,
                    'after'=>Html::a('Ambil', ['ambil', 'id'=>$model->id], [
                        'class' => 'btn btn-info',
                        'onclick' => 'ambil(event);',
                        'title' => 'Ambil'
                    ]),
                    'footer'=>false
                ],
                'columns' => [
                    //['class' => 'kartik\grid\SerialColumn'],
                    [
                        'class' => 'kartik\grid\ActionColumn', 'template' => '{view}',
                        'buttons'=>[
                            'view'=>function ($url, $model, $key) {
                                /* @var $model TrnGudangJadi*/
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', ['/trn-gudang-jadi/view', 'id'=>$model->id], ['title'=>'Detail Stock', 'target'=>'_blank']);
                            },
                        ]
                    ],
                    [
                        'class' => 'kartik\grid\CheckboxColumn',
                        // you may configure additional properties here
                    ],

                    [
                        'label'=>'Marketing',
                        'value'=>'wo.mo.scGreige.sc.marketing.full_name'
                    ],
                    [
                        'label'=>'Nomor WO',
                        'value'=>function($data){
                            /* @var $data TrnGudangJadi*/
                            return $data->wo->no;
                        }
                    ],
                    'qty:decimal',
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
                ],
            ]); ?>
        </div>

        <div class="col-md-6">
            <?php foreach ($dataProviderKirimBuyer->models as $kirimBuyerModel):?>
                <?php
                /* @var $kirimBuyerModel TrnKirimBuyer*/
                $modelsKirimBuyerItem = $kirimBuyerModel->trnKirimBuyerItems;
                ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">WO: <?=$kirimBuyerModel->wo->no?></h3>
                        <div class="box-tools pull-right">
                            <span class="label label-primary"><?=count($modelsKirimBuyerItem)?></span>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                echo DetailView::widget([
                                    'model' => $kirimBuyerModel,
                                    'attributes' => [
                                        [
                                            'label'=>'NOMOR SC',
                                            'value'=>$kirimBuyerModel->sc->no
                                        ],
                                        [
                                            'label'=>'GREIGE',
                                            'value'=>$kirimBuyerModel->wo->greige->nama_kain
                                        ],
                                        [
                                            'label'=>'ALIAS',
                                            'value'=>$model->status === $model::STATUS_DRAFT ? $kirimBuyerModel->nama_kain_alias.' '.
                                                Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                                    ['/trn-kirim-buyer/edit-alias', 'id'=>$kirimBuyerModel->id],
                                                    ['onclick' => 'changeAlias(event);', 'title' => 'Ganti Alias']
                                                ) : $kirimBuyerModel->nama_kain_alias,
                                            'format'=>'raw'
                                        ],
                                        [
                                            'label'=>'Keterangan',
                                            'value'=>$model->status === $model::STATUS_DRAFT ? $kirimBuyerModel->note.' '.
                                                Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                                    ['/trn-kirim-buyer/edit-note', 'id'=>$kirimBuyerModel->id],
                                                    ['onclick' => 'changeNote(event);', 'title' => 'Ganti Keterangan']
                                                ) : $kirimBuyerModel->note,
                                            'format'=>'raw'
                                        ],
                                    ],
                                ]);
                                ?>
                            </div>
                            <div class="col-md-6">
                                <?= DetailView::widget([
                                    'model' => $kirimBuyerModel,
                                    'attributes' => [
                                        [
                                            'label'=>'NOMOR MO',
                                            'value'=>$kirimBuyerModel->mo->no
                                        ],
                                        [
                                            'label'=>'NOMOR WO',
                                            'value'=>$kirimBuyerModel->wo->no
                                        ],
                                        [
                                            'label'=>'UNIT',
                                            'value'=> MstGreigeGroup::unitOptions()[$kirimBuyerModel->unit]
                                        ],
                                    ],
                                ]) ?>
                            </div>
                        </div>

                        <?php
                        $providerStockSiapKirim = new ActiveDataProvider([
                            'query' => $kirimBuyerModel->getTrnKirimBuyerItems(),
                            'pagination' => false,
                            'sort' => false,
                        ]);

                        echo GridView::widget([
                            'dataProvider' => $providerStockSiapKirim,
                            'id' => 'SiapKirimGrid',
                            'resizableColumns' => false,
                            'responsiveWrap' => false,
                            'toolbar' => false,
                            'panel' => [
                                'type' => 'default',
                                'before'=>false,
                                'after'=>Html::a('Kembalikan', ['kembalikan', 'id'=>$model->id], [
                                    'class' => 'btn btn-danger',
                                    'onclick' => 'kembalikan(event);',
                                    'title' => 'Kembalikan'
                                ]),
                                'footer'=>false
                            ],
                            'columns' => [
                                ['class' => 'kartik\grid\SerialColumn'],
                                [
                                    'class' => 'kartik\grid\CheckboxColumn',
                                    // you may configure additional properties here
                                ],
                                'qty:decimal',
                                [
                                    'label'=>'Unit',
                                    'value'=>function($data) use($kirimBuyerModel){
                                        /* @var $data TrnKirimBuyerItem*/
                                        return MstGreigeGroup::unitOptions()[$kirimBuyerModel->unit];
                                    }
                                ],
                            ]
                        ]);
                        ?>
                    </div>
                    <div class="box-footer">

                    </div>
                </div>
            <?php endforeach;?>
        </div>
    </div>

    <?php
    if(!empty($dataProviderKirimBuyer->models)){
        echo '<div class="text-right"><p>Ukuran Font: <input type="number" id="SizeText" min="1" max="99" step="1" value="11"></p></div>';

        if($model->is_export){
            echo $this->render('child/_sj_export', ['model' => $model, 'dataProviderKirimBuyer'=>$dataProviderKirimBuyer, 'formatter'=>$formatter]);
        }else{
            if($model->is_resmi){
                echo $this->render('child/_sj_resmi', ['model' => $model, 'dataProviderKirimBuyer'=>$dataProviderKirimBuyer, 'formatter'=>$formatter]);
            }else{
                echo $this->render('child/_sj_non_resmi', ['model' => $model, 'dataProviderKirimBuyer'=>$dataProviderKirimBuyer, 'formatter'=>$formatter]);
            }
        }
    }
    ?>
</div>

<?php
$JGForm = [
    '<form action="" class="formName">',
    '<div class="form-group">',
    '<label>Ubah Nama Kain Untuk Buyer:</label>',
    '<input type="text" class="JGudang form-control" maxlength="255">'
];
$JGForm[] = '<div class="err-block text-danger"></div></div></form>';

$inputNamaKain = implode(' ', $JGForm);
$js = <<<JS
var inputNamaKain = '{$inputNamaKain}';
JS;

$this->registerJs($js.$this->renderFile(__DIR__.'/js/view.js'), View::POS_END);
$this->registerCss('
    
');
