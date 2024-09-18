<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnScMemo */

$this->title = 'Create Trn Sc Memo';
$this->params['breadcrumbs'][] = ['label' => 'Trn Sc Memos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-sc-memo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
