<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnTerimaMakloonProcessItem */

$this->title = 'Create Trn Terima Makloon Process Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Terima Makloon Process Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-terima-makloon-process-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
