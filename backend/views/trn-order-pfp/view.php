<?php
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\components\ajax_modal\AjaxModal;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnOrderPfp */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order PFP', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="trn-order-pfp-view">
    <?= $this->render('_view-header', ['model'=>$model])?>

    <?php if($model->status == $model::STATUS_APPROVED):?>
    <?=     
                Html::a('Batalkan', ['batal', 'id' => $model->id], [
                        'class' => 'btn btn-warning',
                        'title' => 'Batalkan Order PFP: '.$model->id,
                        'onclick' => 'batalOrderPFP(event);',
                    ])
                ;
            ?>
    <?= Html::a(
        'Pilih Handling',
        ['trn-order-pfp/select-handling', 'id' => $model->id],
        [
            'class' => 'btn btn-primary ajaxModal',
            'title' => 'Pilih Handling untuk Order PFP: '.$model->id,
            'data-target' => '#trnOrderPfpModal',
            'data-toggle' => 'modal',
        ]
    ) ?>
    <?php endif;?>

    <?php
    if($model->status === $model::STATUS_DRAFT){
        echo '<p>';
        if($model->validasi_stock){
            echo 'Habiskan Stock: TIDAK '.Html::a('Habiskan Stock Ketika Approval', ['validasi-stock-off', 'id'=>$model->id], ['class'=>'btn btn-xs btn-warning', 'title' => 'Matikan habiskan stock ketika approval.', 'data' => ['confirm' => 'Are you sure you want to proccess this item?', 'method' => 'post']]);
        }else{
            echo 'Habiskan Stock: YA '.Html::a('Jangan habiskan Stock Ketika Approval', ['validasi-stock-on', 'id'=>$model->id], ['class'=>'btn btn-xs btn-success', 'title' => 'Aktifkan habiskan stock ketika approval.', 'data' => ['confirm' => 'Are you sure you want to proccess this item?', 'method' => 'post']]);
        }
        echo '</p>';
    }
    ?>

    <?= $this->render('_view-content', ['model'=>$model])?>

</div>

<?php
echo AjaxModal::widget([
    'id' => 'trnOrderPfpModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">Update</h4>',
]);
$this->registerJs($this->renderFile(__DIR__.'/js/view.js'), View::POS_END);