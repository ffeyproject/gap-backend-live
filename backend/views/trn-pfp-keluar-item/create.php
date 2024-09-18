<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPfpKeluarItem */

$this->title = 'Create Trn Pfp Keluar Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Pfp Keluar Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-pfp-keluar-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
