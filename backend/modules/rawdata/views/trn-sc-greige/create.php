<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnScGreige */

$this->title = 'Create Trn Sc Greige';
$this->params['breadcrumbs'][] = ['label' => 'Trn Sc Greiges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-sc-greige-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
