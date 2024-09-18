<?php
use common\models\ar\TrnSc;
use kartik\dialog\Dialog;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model TrnSc */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kontrak Pemesanan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="sc-view">
    <div class="row">
        <div class="col-md-6">
            <?php
            $dataProvider = new ArrayDataProvider([
                'allModels' => \yii\helpers\Json::decode($model->apv_note_dir),
                'pagination' => false,
            ]);

            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'responsiveWrap' => false,
                'resizableColumns' => false,
                'toolbar' => false,
                'panel' => [
                    'heading' => '<strong>Catatan Penolakan Direktur</strong>',
                    'type' => GridView::TYPE_DEFAULT,
                    'before' => false,
                    'after' => false,
                    'footer' => false
                ],
                'columns' => [
                    ['class' => 'kartik\grid\SerialColumn'],

                    'date_time',
                    'note'
                ],
            ]);
            ?>
        </div>

        <div class="col-md-6">
            <?php
            $dataProvider = new ArrayDataProvider([
                'allModels' => \yii\helpers\Json::decode($model->apv_note_mgr),
                'pagination' => false,
            ]);

            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'responsiveWrap' => false,
                'resizableColumns' => false,
                'toolbar' => false,
                'panel' => [
                    'heading' => '<strong>Catatan Penolakan Manager</strong>',
                    'type' => GridView::TYPE_DEFAULT,
                    'before' => false,
                    'after' => false,
                    'footer' => false
                ],
                'columns' => [
                    ['class' => 'kartik\grid\SerialColumn'],

                    'date_time',
                    'note'
                ],
            ]);
            ?>
        </div>
    </div>
</div>