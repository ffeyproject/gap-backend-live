<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesMaklonItem */
/* @var $stockOptionListMap array */
/* @var $searchHint string*/
?>
<div class="trn-kartu-proses-maklon-item-create">
    <?= $this->render('_form', [
        'model' => $model,
        'stockOptionListMap'=>$stockOptionListMap,
        'searchHint'=>$searchHint
    ]) ?>
</div>
