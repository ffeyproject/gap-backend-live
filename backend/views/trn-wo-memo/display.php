<?php
use common\models\ar\TrnWoMemo;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model TrnWoMemo */

?>

<iframe src="<?=Url::to(['/trn-wo-memo/print', 'id'=>$model->id])?>" style="width: 100%; height: 500px;"></iframe>
