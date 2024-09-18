<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\GudangJadiMutasiItem */

$this->title = 'Create Gudang Jadi Mutasi Item';
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi Mutasi Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gudang-jadi-mutasi-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
