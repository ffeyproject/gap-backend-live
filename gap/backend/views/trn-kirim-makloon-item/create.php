<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimMakloonItem */

$this->title = 'Create Trn Kirim Makloon Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Kirim Makloon Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kirim-makloon-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
