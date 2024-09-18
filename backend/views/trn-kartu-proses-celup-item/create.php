<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesCelupItem */
/* @var $stockOptionListMap array */
/* @var $searchHint string*/

$this->title = 'Create Trn Kartu Proses Celup Item';
$this->params['breadcrumbs'][] = ['label' => 'Trn Kartu Proses Celup Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-celup-item-create">

    <?= $this->render('_form', [
        'model' => $model,
        'stockOptionListMap'=>$stockOptionListMap,
        'searchHint'=>$searchHint
    ]) ?>

</div>
