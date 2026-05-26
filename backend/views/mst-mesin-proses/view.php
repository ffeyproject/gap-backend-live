<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\dialog\Dialog;
use common\models\ar\MstMesinProses;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstMesinProses */

$this->title = $model->nama_mesin;
$this->params['breadcrumbs'][] = ['label' => 'Master Mesin Proses Processing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="mst-mesin-proses-view">
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
                            'nama_mesin',
                            'model_mesin',
                            [
                                'label' => 'Jenis Hambatan',
                                'value' => function ($model) {
                                    /* @var $model MstMesinProses */
                                    $hambatans = \yii\helpers\ArrayHelper::getColumn($model->mstJenisHambatans, 'nama');
                                    if (empty($hambatans)) {
                                        return '-';
                                    }
                                    return implode(', ', $hambatans);
                                }
                            ],
                            'created_at:datetime',
                            'updated_at:datetime',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
