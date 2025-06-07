<?php

use common\models\ar\TrnWo;
use common\models\ar\TrnWoMemo;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model TrnWo */
/* @var $users array */

$dataProvider = new ActiveDataProvider([
    'query' => $model->getTrnWoMemos(),
    'pagination' => false,
    'sort' => false
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'WoMemoItemsGrid',
    'pjax' => true,
    'responsiveWrap' => false,
    'resizableColumns' => false,
    'showPageSummary' => true,
    'toolbar' => [
        [
            'content'=>
                $model->status == $model::STATUS_APPROVED ? Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/trn-wo-memo/create', 'woId' => $model->id], [
                    'class' => 'btn btn-xs btn-success',
                    'title' => 'Add Memo Perubahan',
                    'data-toggle'=>"modal",
                    'data-target'=>"#trnWoModal",
                    'data-title' => 'Add Memo Perubahan'
                ]) : ''
        ]
    ],
    'panel' => [
        'heading' => '<strong>Memo Perubahan</strong>',
        'type' => GridView::TYPE_DEFAULT,
        'after' => false,
        'footer' => false
    ],
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

        'no',
        'memo:html',
        'created_at:datetime',
        [
            'class' => 'kartik\grid\ActionColumn',
            'controller' => 'trn-wo-memo',
            'template' => '{email}',
            'buttons' => [
                'email' => function($url, $model, $key){
                    return Html::a('<i class="glyphicon glyphicon-envelope"></i>', '#', [
                        'class' => 'btn btn-xs btn-info',
                        'title' => 'Kirim Email Memo Perubahan',
                        'data-toggle' => 'modal',
                        'data-target' => '#emailModal',
                        'data-id' => $model->id,
                    ]);
                },
            ]
        ],
    ],
]);
?>

<?php
// === MODAL EMAIL FORM ===
Modal::begin([
    'id' => 'emailModal',
    'header' => '<h4>Kirim Email Memo Perubahan</h4>',
]);

$form = ActiveForm::begin([
    'method' => 'post',
    'action' => ['site/kirim-email-memo'],
]);

echo Html::hiddenInput('id', '', ['id' => 'memo-id-hidden']);
?>

<div style="display: flex; gap: 20px;">
    <!-- Kolom Kiri -->
    <div>
        <label><strong>Daftar User</strong></label><br>
        <select multiple size="12" id="availableUsers" style="min-width: 300px;">
            <?php foreach ($users as $user): ?>
            <option value="<?= $user->email ?>">
                <?= htmlspecialchars($user->full_name) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Tombol Pindah -->
    <div style="display: flex; flex-direction: column; justify-content: center; gap: 10px;">
        <button type="button" onclick="moveSelected('availableUsers', 'selectedUsers')">>></button>
        <button type="button" onclick="moveSelected('selectedUsers', 'availableUsers')">
            << </button>
    </div>

    <!-- Kolom Kanan -->
    <div>
        <label><strong>User yang Akan Dikirimi Email</strong></label><br>
        <select multiple name="selectedEmails[]" id="selectedUsers" size="15" style="min-width: 300px;"></select>
    </div>
</div>

<br>
<?= Html::submitButton('Kirim Email', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>
<?php Modal::end(); ?>

<?php
// === JavaScript untuk handle modal ===
$this->registerJs("
    $('#emailModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var memoId = button.data('id');
        $('#memo-id-hidden').val(memoId);
    });

    window.moveSelected = function(fromId, toId) {
        $('#' + fromId + ' option:selected').each(function() {
            $('#' + toId).append($(this).clone());
            $(this).remove();
        });
    };

    $('#emailModal').on('show.bs.modal', function () {
        $(this).find('.modal-dialog').addClass('modal-lg');
    });

    // Ini yang ditambahkan:
    $('form').on('submit', function () {
        $('#selectedUsers option').prop('selected', true);
    });
");
?>