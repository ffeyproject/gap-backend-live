<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnWoColor */

$this->title = 'Create Trn Wo Color';
$this->params['breadcrumbs'][] = ['label' => 'Trn Wo Colors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-wo-color-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
