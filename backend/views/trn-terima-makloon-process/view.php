<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnTerimaMakloonProcess */

$this->title = 'Penerimaan Makloon Proses: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Penerimaan Makloon Proses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-terima-makloon-process-view">
    <p>
        <?php if($model->status == $model::STATUS_DRAFT):?>
            <?= Html::a('Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Simpan Sebagai Stock', ['posting', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Anda yakin akan memposting item ini?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Teruskan Ke Inspecting', ['inspect', 'id' => $model->id], [
                'class' => 'btn btn-info',
                'data' => [
                    'confirm' => 'Anda yakin akan meneruskan ke inspecting?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php else:?>
        <?php endif;?>

        <?= Html::a('Buat Baru', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo $this->render('child/detail', ['model' => $model]);?>

    <?php echo $this->render('child/items', ['model' => $model]);?>

</div>
