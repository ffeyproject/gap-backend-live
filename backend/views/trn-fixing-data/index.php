<?php
use common\models\ar\{ TrnInspecting, InspectingMklBj };
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnGudangJadiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

\backend\assets\DataTablesAsset::register($this);

$this->title = 'Fixing Data - Index';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="trn-fixing-data-index">
    <div class="box">
        <div class="box-body">
            <?php
                $form = ActiveForm::begin(['method' => 'post', 'action' => ['trn-fixing-data/index']]);
            ?>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <?php
                            echo '<label>Start Date</label>';
                            echo DatePicker::widget([
                                'name' => 'startDate',
                                'options' => ['placeholder' => 'Select start date'],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd'
                                ]
                            ]);
                        ?>
                    </div>
                    <div class="form-group col-md-6">
                        <?php
                            echo '<label>End Date</label>';
                            echo DatePicker::widget([
                                'name' => 'endDate',
                                'options' => ['placeholder' => 'Select end date'],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd'
                                ]
                            ]);
                        ?>
                    </div>
                    <div class="form-group col-md-12">
                        <?php
                            echo '<label>Pilih Tabel</label>';
                            echo Select2::widget([
                                'name' => 'tableName',
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                'data' => [TrnInspecting::tableName() => TrnInspecting::tableName(), InspectingMklBj::tableName() => InspectingMklBj::tableName()],
                                'options' => ['multiple' => false, 'placeholder' => 'Select Tabel ...']
                            ]); 
                        ?>
                    </div>
                    <div class="form-group col-md-12">
                        <?php
                            echo '<div class="form-group">';
                            echo Html::submitButton('Submit', ['class' => 'btn btn-primary btn-block']);
                            echo '</div>';
                        ?>
                    </div>
                </div>
            <?php 
                ActiveForm::end();
            ?>
        </div>
    </div>
</div>