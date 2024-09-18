<?php

use backend\models\form\InspectingHeaderForm;
use backend\models\form\InspectingItemsForm;
use common\models\ar\TrnInspecting;

/* @var $this yii\web\View */
/* @var $model TrnInspecting */
/* @var $modelHeader InspectingHeaderForm */
/* @var $modelItem InspectingItemsForm */
/* @var $nomorKartu string*/
/* @var $kombinasi string*/
/* @var $items array*/


$this->title = 'Upgrade Inspecting: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inspecting', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id'=>$model->id]];
$this->params['breadcrumbs'][] = 'Upgrade';
?>
<div class="trn-inspecting-upgrade">
    <?= $this->render('_form_upgrade', [
        'model' => $model,
        'modelHeader' => $modelHeader,
        'modelItem' => $modelItem,
        'nomorKartu' => $nomorKartu,
        'kombinasi' => $kombinasi,
        'k3l_code' => $k3l_code,
        'items' => $items
    ]) ?>
</div>
