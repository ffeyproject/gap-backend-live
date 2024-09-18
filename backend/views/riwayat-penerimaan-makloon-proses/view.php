<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnTerimaMakloonProcess */

$this->title = 'Riwayat Penerimaan Makloon Proses: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Riwayat Penerimaan Makloon Proses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-terima-makloon-process-view">

    <?php echo $this->render('child/detail', ['model' => $model]);?>

    <?php echo $this->render('child/items', ['model' => $model]);?>

</div>
