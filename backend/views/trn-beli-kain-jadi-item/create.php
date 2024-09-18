<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnBeliKainJadiItem */

$this->title = 'Create Trn Beli Kain Jadi Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Beli Kain Jadi Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-beli-kain-jadi-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
