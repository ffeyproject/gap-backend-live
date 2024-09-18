<?php
use kartik\dialog\Dialog;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesMaklon */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kartu Proses Maklon', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-kartu-proses-maklon-view">
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
            <?= Html::a('Posting', ['posting', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Anda yakin akan memposting item ini?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php else:?>
            <?= Html::a('Cetak Surat Pengantar', ['print-pengantar', 'id' => $model->id], ['class' => 'btn btn-primary', 'target'=>'blank']) ?>
        <?php endif;?>

        <?= Html::a('Buat Baru', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo $this->render('child/detail', ['model' => $model]);?>

    <?php echo $this->render('child/items', ['model' => $model]);?>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Surat Pengantar</h3>
            <div class="box-tools pull-right">
                <span class="label label-info">Akan tampil jika sudah diposting</span>
            </div>
        </div>
        <div class="box-body">
            <?=$model->status == $model::STATUS_APPROVED ? $this->render('display-pengantar', ['model' => $model]) : '';?>
        </div>
    </div>

</div>