<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimMakloonV2Item */

$this->title = 'Update Trn Kirim Makloon V2 Item: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Kirim Makloon V2 Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-kirim-makloon-v2-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
