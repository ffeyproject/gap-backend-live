<?php
use common\models\ar\TrnMoMemo;

/* @var $this yii\web\View */
/* @var $model TrnMoMemo */
?>

<iframe src="<?=\yii\helpers\Url::to(['/trn-mo-memo/print', 'id'=>$model->id])?>" style="width: 100%; height: 500px;"></iframe>
