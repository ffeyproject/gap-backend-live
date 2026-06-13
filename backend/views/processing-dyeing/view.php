<?php
use backend\components\ajax_modal\AjaxModal;
use common\models\ar\MstProcessDyeing;
use common\models\ar\TrnKartuProsesDyeing;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesDyeing */
/* @var $attrsLabels array*/
/* @var $processModels MstProcessDyeing[]*/
/* @var $processesUlang array*/

$this->title = 'Processing Dyeing - '.$model->no;
$this->params['breadcrumbs'][] = ['label' => 'Processing Dyeing Dyeing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$formatter = Yii::$app->formatter;
?>
<div class="kartu-proses-dyeing-view">
    <p>
        <?php if($model->status == $model::STATUS_DELIVERED):?>
        <?= Html::a('Buat Memo Penggantian Greige', ['ganti-greige', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'onclick' => 'memoPg(event, "Memo Penggantian Greige");',
                'title' => 'Buat Memo Penggantian Greige'
            ]) ?>
        <?php endif; ?>

        <?php
        $activeChildCards = \common\models\ar\TrnKartuProsesDyeing::find()
            ->where([
                'kartu_proses_id' => $model->id,
                'status' => \common\models\ar\TrnKartuProsesDyeing::STATUS_DELIVERED
            ])
            ->all();
        
        $showApproveButton = ($model->status == $model::STATUS_DELIVERED) || ($model->status == $model::STATUS_APPROVED && !empty($activeChildCards));
        ?>

        <?php if ($showApproveButton): ?>
            <?php if (!empty($activeChildCards)): ?>
                <?= Html::button('Setujui Dan Teruskan Ke Inspecting', [
                    'class' => 'btn btn-warning',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal-approve-split',
                    'title' => 'Setujui Dan Teruskan Ke Inspecting'
                ]) ?>
            <?php else: ?>
                <?= Html::a('Setujui Dan Teruskan Ke Inspecting', ['approve', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => 'Are you sure you want to approve this item?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php
        if (!empty($activeChildCards)):
            \yii\bootstrap\Modal::begin([
                'header' => '<h4><strong>Setujui Kartu Proses & Split Card</strong></h4>',
                'id' => 'modal-approve-split',
            ]);
        ?>
            <?php $form = \yii\widgets\ActiveForm::begin([
                'action' => ['approve', 'id' => $model->id],
                'method' => 'post',
            ]); ?>
                <p class="text-info" style="font-weight: 600; font-size: 14px;">
                    💡 Kartu proses induk ini memiliki beberapa kartu split yang belum diterima. Silakan centang kartu split mana saja yang ingin disetujui dan diteruskan ke inspecting bersama dengan kartu induk:
                </p>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e4e7eb;">
                    <!-- Parent Card is shown as checked and disabled -->
                    <div class="checkbox" style="margin-top: 0; margin-bottom: 12px;">
                        <label style="font-weight: 700; color: #2f3542;">
                            <input type="checkbox" checked disabled> 
                            🏢 Kartu Induk: <?= Html::encode($model->nomor_kartu) ?> (Status: <?= Html::encode(isset($model::statusOptions()[$model->status]) ? $model::statusOptions()[$model->status] : '') ?>)
                        </label>
                    </div>
                    
                    <hr style="margin: 10px 0; border-top: 1px solid #dfe4ea;">
                    
                    <?php foreach ($activeChildCards as $child): ?>
                        <div class="checkbox" style="margin-bottom: 8px;">
                            <label style="font-weight: 600; color: #57606f;">
                                <input type="checkbox" name="approve_child_ids[]" value="<?= $child->id ?>" checked> 
                                ✂️ Kartu Split: <strong style="color: #2f3542;"><?= Html::encode($child->nomor_kartu) ?></strong> (Status: <?= Html::encode(isset($child::statusOptions()[$child->status]) ? $child::statusOptions()[$child->status] : 'Unknown') ?> - <?= count($child->trnKartuProsesDyeingItems) ?> Rolls)
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-group text-right" style="margin-bottom: 0;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <?= Html::submitButton('⚡ Setujui Semua yang Terpilih', ['class' => 'btn btn-success', 'style' => 'font-weight: 700;']) ?>
                </div>
            <?php \yii\widgets\ActiveForm::end(); ?>
        <?php 
            \yii\bootstrap\Modal::end(); 
        endif;
        ?>

        <?php if($model->status == $model::STATUS_DELIVERED):?>
            <?= Html::a('Batalkan Kartu Proses', ['batal', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to batalkan this item?',
                        'method' => 'post',
                    ],
                ]) ?>

            <?=Html::a('Ganti WO', ['ganti-wo', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'onclick' => 'gantiWo(event);',
                    'title' => 'Ganti WO Kartu Proses: '.$model->id
                ]);?>

            <?=Html::a('Ganti Warna', ['ganti-warna', 'id' => $model->id], [
                    'class' => 'btn btn-info',
                    'onclick' => 'gantiWarna(event);',
                    'title' => 'Ganti Warna Kartu Proses: '.$model->id
                ]);?>

            <?=Html::a('Split Kartu', ['split', 'id' => $model->id], [
                    'class' => 'btn bg-purple',
                    'title' => 'Split Kartu Proses: '.$model->id
                ]);?>

            <?=Html::a('Gabung Kartu', ['gabung', 'id' => $model->id], [
                    'class' => 'btn btn-primary',
                    'title' => 'Gabung Kartu Proses: '.$model->id
                ]);?>
        <?php endif;?>

        <?=Html::a('Ganti Ke Kartu PFP', ['ganti-ke-pfp', 'id' => $model->id], [
            'class' => 'btn btn-default',
            'onclick' => 'gantiKePfp(event, "Ganti Ke Kartu PFP");',
            'title' => 'Ganti Ke Kartu PFP' 
        ]);?>

        <?php $label = $model->tunggu_marketing ? 'Batalkan Tunggu Marketing' : 'Set Tunggu Marketing' ?>
        <?= Html::a($label, ['set-tunggu-mkt', 'id' => $model->id], [
            'class' => 'btn btn-primary',
            'data' => [
                'confirm' => 'Apakah anda yakin?',
                'method' => 'post',
            ],
        ]) ?>

        <?php $labelToping = $model->toping_matching ? 'Batalkan Toping Matching' : 'Set Toping Matching' ?>
        <?= Html::a($labelToping, ['set-toping-matching', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => 'Apakah anda yakin?',
                'method' => 'post',
            ],
        ]) ?>

        <?= Html::a('Kembalikan Stock Ke Gudang Greige', 
    ['kembali-stock', 'id' => $model->id], 
    [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Yakin ingin mengembalikan stock ke Gudang Greige?',
            'method' => 'post',
        ],
    ]
) ?>

    </p>

    <?php echo $this->render('/trn-kartu-proses-dyeing/child/detail', ['model' => $model]);?>

    <?php echo $this->render('/trn-kartu-proses-dyeing/child/items_processing', ['model' => $model]);?>

    <?php
    if (!in_array($model->status, [
        $model::STATUS_DRAFT, 
        $model::STATUS_POSTED, 
        $model::STATUS_BATAL
    ])) {
        echo $this->render('child/proses', ['model' => $model, 'attrsLabels'=>$attrsLabels, 'processModels'=>$processModels, 'processesUlang'=>$processesUlang, 'formatter'=>$formatter]);
    }
    ?>

    <?php echo $this->render('child/split_history', ['model' => $model]);?>

    <?php echo $this->render('/trn-kartu-proses-dyeing/child/persetujuan', ['model' => $model]);?>

    <?php echo $this->render('child/history', ['model' => $model]);?>
</div>

<?php
echo AjaxModal::widget([
    'id' => 'kartuProsesDyeingModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);

if(!in_array($model->status, [$model::STATUS_DRAFT, $model::STATUS_POSTED, $model::STATUS_BATAL])){
    $warnaList = $model->wo->getTrnWoColors()->joinWith('moColor')->asArray()->all();
    $this->registerJsVar('warnaList', $warnaList);
    $this->registerJsVar('indexUrl', Url::to(['index']));

    $js = $this->renderFile(Yii::$app->controller->viewPath.'/js/view.js');
    $this->registerJs($js, $this::POS_END);
}