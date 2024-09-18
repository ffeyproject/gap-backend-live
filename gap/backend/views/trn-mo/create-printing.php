<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMo */

$this->title = 'Create Marketing Order Printing';
$this->params['breadcrumbs'][] = ['label' => 'Marketing Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="trn-mo-create">
    <?=$this->render('_form-printing', [
        'model' => $model,
    ]);?>
</div>
