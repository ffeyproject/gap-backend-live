<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnTerimaMakloonFinishItem */

$this->title = 'Create Trn Terima Makloon Finish Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Terima Makloon Finish Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-terima-makloon-finish-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
