<?php

use common\models\ar\MstCustomer;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstHandling */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Master Data Handling', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$buyerIds = $model->buyer_ids !== null ? explode(',', $model->buyer_ids) : [];
?>
<div class="mst-handling-view">
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Add New', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'attribute'=>'greige_id',
                                'value'=>$model->greige->nama_kain.' (Alias: '.$model->greige->alias.')'
                            ],
                            'name',
                            'lebar_preset',
                            'lebar_finish',
                            'berat_finish',
                            'densiti_lusi',
                            'densiti_pakan',
                            'no_hanger',
                            'ket_washing:boolean',
                            'ket_wr:boolean',
                            'berat_persiapan:decimal',
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

        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Buyers</h3>
                    <div class="box-tools pull-right">
                        <span class="label label-primary"><?=count($buyerIds)?></span>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <div class="list-group">
                        <?php
                        foreach ($buyerIds as $id) {
                            $cust = MstCustomer::findOne($id);
                            echo Html::a($cust->name, ['/mst-customer/view', 'id'=>$id], ['class'=>'list-group-item']);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
