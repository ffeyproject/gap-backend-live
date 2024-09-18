<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\MstGreigeGroup */

$this->title = 'Create Mst Greige Group';
$this->params['breadcrumbs'][] = ['label' => 'Mst Greige Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-greige-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
