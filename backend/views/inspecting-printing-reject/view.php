<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingPrintingReject */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inspecting Printing Rejects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inspecting-printing-reject-view">

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            //'kartu_proses_id',
                            'kartuProsesNo',
                            'no_urut',
                            'no',
                            'date:date',
                            'untuk_bagian',
                            'pcs:decimal',
                            'keterangan',
                        ],
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'penerima',
                            'mengetahui',
                            'pengirim',
                            'created_at:datetime',
                            'created_by',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <iframe src="<?=Url::to(['print', 'id'=>$model->id])?>" style="width: 100%; height: 500px;"></iframe>
        </div>
    </div>

</div>
