<?php
use common\models\ar\TrnTerimaMakloonFinishItem;
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnTerimaMakloonFinish */
/* @var  $modelsItem TrnTerimaMakloonFinishItem[]*/

$this->title = 'Buat Penerimaan Makloon Finish';
$this->params['breadcrumbs'][] = ['label' => 'Penerimaan Makloon Finish', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-terima-makloon-finish-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
