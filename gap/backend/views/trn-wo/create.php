<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnWo */

$this->title = 'Create Work Order';
$this->params['breadcrumbs'][] = ['label' => 'Work Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-wo-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
