<?php
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnOrderPfp */
?>

<iframe src="<?=Url::to(['/trn-order-pfp/print', 'id'=>$model->id])?>" style="width: 100%; height: 500px;"></iframe>