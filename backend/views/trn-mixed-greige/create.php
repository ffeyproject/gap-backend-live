<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMixedGreige */

$this->title = 'Create Trn Mixed Greige';
$this->params['breadcrumbs'][] = ['label' => 'Trn Mixed Greiges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-mixed-greige-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
