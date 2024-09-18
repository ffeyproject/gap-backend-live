<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnScGreige */

$formatter = Yii::$app->formatter;
?>
<div class="trn-sc-greige-view">
    <p>
        <?php
        if($model->order_grege_approved === false){
            echo Html::a('Persetujuan PMC', ['approve-pmc', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'onclick' => 'approval(event);',
                'title' => 'Approve Order Greige: '.$model->id
            ]);
        }
        ?>

        <?php
        if($model->order_grege_approved_dir === false){
            echo Html::a('Persetujuan Direktur', ['approve-dir', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'onclick' => 'approval(event);',
                'title' => 'Approve Order Greige: '.$model->id
            ]);
        }
        ?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    //'id',
                    [
                        'label'=>'Nomor SC',
                        'attribute'=>'nomorSc',
                        'value'=>$model->sc->no
                    ],
                    [
                        'attribute'=>'process',
                        'value'=>$model::processOptions()[$model->process]
                    ],
                    [
                        'attribute'=>'lebar_kain',
                        'value'=>$model::lebarKainOptions()[$model->lebar_kain]
                    ],
                    'merek',
                    [
                        'attribute'=>'grade',
                        'value'=>$model::gradeOptions()[$model->grade]
                    ],
                    'piece_length',
                    'unit_price:decimal',
                    [
                        'attribute'=>'price_param',
                        'value'=>$model::priceParamOptions()[$model->price_param]
                    ],
                    'qty:decimal',
                    'woven_selvedge:ntext',
                    'note:ntext',
                    'closed:boolean',
                    'closing_note:ntext',
                ],
            ]) ?>
        </div>

        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'no_order_greige',
                    'no_urut_order_greige',
                    'order_greige_note:ntext',
                    'order_grege_approved:boolean',
                    'order_grege_approved_at:datetime',
                    'order_grege_approved_by',
                    'order_grege_approval_note',
                    'order_grege_approved_dir:boolean',
                    'order_grege_approved_at_dir:datetime',
                    'order_grege_approval_note_dir',
                ],
            ]) ?>

            <?= DetailView::widget([
                'model' => $model->greigeGroup,
                'attributes' => [
                    'nama_kain',
                    'unitName',
                    'qtyFinish',
                    'qtyFinishToYard',
                ],
            ]) ?>
        </div>
    </div>

    <?=$this->render('child/summary', ['model'=>$model, 'formatter'=>$formatter])?>

</div>

<?php
$jsStr = <<<JS

JS;

$js = $jsStr.$this->renderFile(Yii::$app->controller->viewPath.'/js/view.js');
$this->registerJs($js, $this::POS_END, 'jsScGreigeView');