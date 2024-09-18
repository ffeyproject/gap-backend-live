<?php
use common\models\ar\TrnScMemo;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model TrnScMemo */

?>

<iframe src="<?=Url::to(['/trn-sc-memo/print', 'id'=>$model->id])?>" style="width: 100%; height: 500px;"></iframe>
