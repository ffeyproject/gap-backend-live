<?php
namespace backend\components\ajax_modal;

/**
 * Contoh penggunaan
    <?php
    echo AjaxModal::widget([
        'id' => 'userModal',
        'size' => 'modal-lg',
        'header' => '<h4 class="modal-title">...</h4>',
    ]);
    ?>
 *
 * Tombol
    <?=Html::a('Terima', ['terima', 'id' => $model->id], [
        'class' => 'btn btn-success',
        'title' => 'Penerimaan Packing',
        'data-toggle'=>"modal",
        'data-target'=>"#penerimaanPackingModal",
        'data-title' => 'Penerimaan Packing'
    ])?>
 *
*/

class AjaxModal extends \yii\bootstrap\Modal
{
    /**
     * Initializes the widget.
     */
    public function init(){
        $this->options = [
            'tabindex' => false // important for Select2 to work properly
        ];

        $this->clientOptions = [
            'backdrop' => 'static',
            'keyboard' => false
        ];
        parent::init();
    }

    public function run(){
        $modalId = $this->id;
        $this->view->registerCss('.ajax-modal-loader {border: 16px solid #f3f3f3; /* Light grey */ border-top: 16px solid #3498db; /* Blue */ border-radius: 50%;width: 120px; height: 120px; animation: spin 2s linear infinite;}@keyframes spin {0% { transform: rotate(0deg); }100% { transform: rotate(360deg); }}', [], 'ModalAjax'.$modalId.'CSS');

        $addJs = 'var ajaxModalId = "#'.$modalId.'";';
        $this->view->registerJs($addJs.$this->view->renderFile(__DIR__.'/modal.js'), \yii\web\View::POS_END, 'ModalAjax'.$modalId.'JS');
        parent::run(); // TODO: Change the autogenerated stub
    }
}