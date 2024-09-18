<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\JualExFinishItem */

$this->title = 'Create Jual Ex Finish Item';
$this->params['breadcrumbs'][] = ['label' => 'Jual Ex Finish Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jual-ex-finish-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
