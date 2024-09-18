<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnWo */

$this->title = 'Create Trn Wo';
$this->params['breadcrumbs'][] = ['label' => 'Trn Wos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-wo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
