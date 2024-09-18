<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\MstGreige */

$this->title = 'Create Mst Greige';
$this->params['breadcrumbs'][] = ['label' => 'Mst Greiges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-greige-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
