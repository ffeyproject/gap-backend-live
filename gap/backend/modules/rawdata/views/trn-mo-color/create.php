<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnMoColor */

$this->title = 'Create Trn Mo Color';
$this->params['breadcrumbs'][] = ['label' => 'Trn Mo Colors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-mo-color-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
