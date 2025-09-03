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

        <?= Html::a('Setujui Dan Teruskan Ke Inspecting', ['approve', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Are you sure you want to approve this item?',
                    'method' => 'post',
                ],
            ]) ?>
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

    <?php echo $this->render('/trn-kartu-proses-dyeing/child/persetujuan', ['model' => $model]);?>

    <?php
    switch ($model->status){
        case $model::STATUS_DELIVERED:
            echo $this->render('child/proses', ['model' => $model, 'attrsLabels'=>$attrsLabels, 'processModels'=>$processModels, 'processesUlang'=>$processesUlang, 'formatter'=>$formatter]);
            break;
        case $model::STATUS_APPROVED:
        case $model::STATUS_INSPECTED:
            echo $this->render('child/proses_disabled', ['model' => $model, 'attrsLabels'=>$attrsLabels, 'processModels'=>$processModels, 'processesUlang'=>$processesUlang, 'formatter'=>$formatter]);
            break;
        default:
            echo '';
    }
    ?>
</div>

<?php
echo AjaxModal::widget([
    'id' => 'kartuProsesDyeingModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);

if($model->status == $model::STATUS_DELIVERED){
    $warnaList = $model->wo->getTrnWoColors()->joinWith('moColor')->asArray()->all();
    $this->registerJsVar('warnaList', $warnaList);
    $this->registerJsVar('indexUrl', Url::to(['index']));

    $js = $this->renderFile(Yii::$app->controller->viewPath.'/js/view.js');
    $this->registerJs($js, $this::POS_END);
}