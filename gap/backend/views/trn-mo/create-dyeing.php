<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMo */

$this->title = 'Create Marketing Order Dyeing';
$this->params['breadcrumbs'][] = ['label' => 'Marketing Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="trn-mo-create">
    <?=$this->render('_form-dyeing', [
        'model' => $model,
    ]);?>
</div>
