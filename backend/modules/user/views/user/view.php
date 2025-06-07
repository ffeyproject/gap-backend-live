<?php

use backend\modules\user\models\form\SignatureForm;
use kartik\dialog\Dialog;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\user\models\User */
/* @var $form kartik\widgets\ActiveForm */
/* @var $modelSignature SignatureForm */

$this->title = 'ID: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$opts = Json::htmlEncode([
    'items' => $model->getRbacItems(),
]);
$this->registerJs("var _opts = {$opts};");
$this->registerJs($this->render('_script.js'));
$animateIcon = ' <i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i>';
?>

<!--
<p>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-flat']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger btn-flat',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ]) ?>
</p>
-->

<div class="row">
    <div class="col-md-9">
        <div class="box box-primary">
            <div class="box-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        //'id',
                        'username',
                        'full_name',
                        'auth_key',
                        //'password_hash',
                        //'password_reset_token',
                        'email:email',
                        [
                            'label'=>'Status',
                            'value'=>\backend\modules\user\models\User::getStatusOptions()[$model->status]
                        ],
                        [
                            'label'=>'Status Notif Email',
                            'value'=>$model->getNotificationStatus()
                        ],
                        'created_at:datetime',
                        'updated_at:datetime',
                        //'verification_token',
                        [
                            'label'=>'Verification Link',
                            'value'=>$model->status == $model::STATUS_INACTIVE ? Yii::$app->urlManager->createAbsoluteUrl(['/site/verify-email', 'token'=>$model->verification_token]) : '-'
                        ],
                        [
                            'label'=>'Reset Password Link',
                            'value'=>$model->password_reset_token !== null ? Yii::$app->urlManager->createAbsoluteUrl(['/site/reset-password', 'token'=>$model->password_reset_token]) : '-'
                        ],
                        [
                            'label' => 'Get Email Notification?',
                            'format' => 'raw',
                            'value' => function($model) {
                                $isActive = (bool) $model->status_notif_email;
                                return Html::a(
                                    $isActive ? 'Aktif' : 'Tidak Aktif',
                                    ['change-status', 'id' => $model->id],
                                    [
                                        'class' => 'btn btn-' . ($isActive ? 'success' : 'warning'),
                                        'data' => ['method' => 'post'],
                                    ]
                                );
                            },
                        ],

                        [
                            'label' => 'Change Status',
                            'format' => 'raw',
                            'value' => function($model) {
                                $newStatus = $model->status == $model::STATUS_ACTIVE ? $model::STATUS_INACTIVE : $model::STATUS_ACTIVE;
                                $buttonLabel = $newStatus == $model::STATUS_ACTIVE ? 'Activate' : 'Deactivate';
                                return Html::a(
                                    $buttonLabel,
                                    ['change-status-aktif', 'id' => $model->id, 'newStatus' => $newStatus],
                                    [
                                        'class' => 'btn btn-' . ($newStatus == $model::STATUS_ACTIVE ? 'success' : 'warning'),
                                        'data' => ['method' => 'post'],
                                    ]
                                );
                            },
                        ],


                    ],
                ]) ?>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Tanda Tangan</h3>
            </div>
            <div class="box-body">
                <?=Html::img($model->signatureUrl, ['class'=>'img img-responsive'])?>
            </div>
            <div class="box-footer">
                <?=$form->field($modelSignature, 'signatureFile')->fileInput()->label(false)?>

                <div class="form-group">
                    <?= Html::submitButton('Unggah', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        <div class="box box-primary">
            <div class="box-header">
                <input class="form-control search" data-target="available" placeholder="Search for available">
            </div>
            <div class="box-body table-responsive">
                <select multiple size="15" class="form-control list" data-target="available">
                </select>
            </div>
        </div>
    </div>
    <div class="col-sm-2 text-center">
        <br><br><br><br><br><br><br>
        <?=Html::a('&gt;&gt;' . $animateIcon, ['assign', 'id' => (string) $model->id], [
            'class' => 'btn btn-success btn-assign',
            'data-target' => 'available',
            'title' => 'Assign',
        ]);?><br><br>
        <?=Html::a('&lt;&lt;' . $animateIcon, ['revoke', 'id' => (string) $model->id], [
            'class' => 'btn btn-danger btn-assign',
            'data-target' => 'assigned',
            'title' => 'Remove',
        ]);?>
    </div>
    <div class="col-sm-5">
        <div class="box box-primary">
            <div class="box-header">
                <input class="form-control search" data-target="assigned" placeholder="Search for assigned">
            </div>
            <div class="box-body table-responsive">
                <select multiple size="15" class="form-control list" data-target="assigned">
                </select>
            </div>
        </div>
    </div>
</div>