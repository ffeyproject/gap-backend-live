<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGreigeKeluarItem */

$this->title = 'Create Trn Greige Keluar Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Greige Keluar Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-greige-keluar-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
