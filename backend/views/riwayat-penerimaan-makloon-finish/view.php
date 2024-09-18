<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnTerimaMakloonFinish */

$this->title = 'Riwayat Penerimaan Makloon Finish: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Riwayat Penerimaan Makloon Finish', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-terima-makloon-finish-view">

    <?php echo $this->render('child/detail', ['model' => $model]);?>

    <?php echo $this->render('child/items', ['model' => $model]);?>

</div>
