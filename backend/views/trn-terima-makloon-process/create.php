<?php
use common\models\ar\TrnTerimaMakloonProcessItem;
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnTerimaMakloonProcess */
/* @var  $modelsItem TrnTerimaMakloonProcessItem[]*/

$this->title = 'Buat Penerimaan Makloon Proses';
$this->params['breadcrumbs'][] = ['label' => 'Penerimaan Makloon Proses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-terima-makloon-process-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
