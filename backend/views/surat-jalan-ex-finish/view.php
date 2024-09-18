<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\SuratJalanExFinish */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Surat Jalan Ex Finishes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="surat-jalan-ex-finish-view">
    <!--<p>
        <?/*= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) */?>
        <?/*= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) */?>
    </p>-->

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Memo Info</h3>
        </div>

        <div class="box-body">
            <div class="row">
                <div class="col-sm-6">
                    <?= DetailView::widget([
                        'model' => $model->memo,
                        'attributes' => [
                            'id',
                            'no_urut',
                            'no',
                            'jenisGudangName',
                            'customerName',
                            'gradeName',
                            'harga:decimal',
                            'ongkirName',
                            'pembayaran',
                        ],
                    ]) ?>
                </div>

                <div class="col-sm-6">
                    <?= DetailView::widget([
                        'model' => $model->memo,
                        'attributes' => [
                            'tanggal_pengiriman:date',
                            'komisi',
                            'jenisOrderName',
                            'is_resmi:boolean',
                            'keterangan:ntext',
                            'created_at:datetime',
                            'created_by',
                            'updated_at:datetime',
                            'updated_by',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Detail Surat Jalan</h3>
        </div>

        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'memo_id',
                    'no',
                    'pengirim',
                    'penerima',
                    'kepala_gudang',
                    'plat_nomor',
                    'note:ntext',
                    'created_at:datetime',
                    'created_by',
                    'updated_at:datetime',
                    'updated_by',
                ],
            ]) ?>
        </div>
    </div>

    <?php
    if($model->memo->is_resmi){
        echo $this->render('child/_sj_resmi', ['model' => $model]);
    }else{
        echo $this->render('child/_sj_non_resmi', ['model' => $model]);
    }
    ?>

</div>

<?php
$this->registerJs($this->renderFile(__DIR__.'/js/view.js'), $this::POS_END);
