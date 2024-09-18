<?php
use common\models\ar\TrnWoMemo;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\ar\GudangJadiMutasi */

?>

<iframe src="<?=Url::to(['/gudang-jadi-mutasi/print', 'id'=>$model->id])?>" style="width: 100%; height: 500px;"></iframe>
