<?php
use kartik\dialog\Dialog;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimBuyer */
/* @var $dataStocks array*/
/* @var $dataItems array*/

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pengiriman Ke Buyer', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$formatter = Yii::$app->formatter;
?>
<div class="trn-kirim-buyer-view">
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

    <?=$this->render('view_header', ['model'=>$model, 'formatter'=>$formatter])?>

    <?php
    if($model->status === $model::STATUS_POSTED){
        echo $this->render('view_posted', ['model'=>$model, 'formatter'=>$formatter, 'dataItems'=>$dataItems]);
    }else{
        echo $this->render('view_draft', ['model'=>$model, 'formatter'=>$formatter, 'dataStocks'=>$dataStocks, 'dataItems'=>$dataItems]);
    }
    ?>
</div>