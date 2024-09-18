<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMixedGreigeItem */

$this->title = 'Create Trn Mixed Greige Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Mixed Greige Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-mixed-greige-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
