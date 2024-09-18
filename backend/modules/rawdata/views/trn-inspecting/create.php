<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnInspecting */

$this->title = 'Create Trn Inspecting';
$this->params['breadcrumbs'][] = ['label' => 'Trn Inspectings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-inspecting-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
