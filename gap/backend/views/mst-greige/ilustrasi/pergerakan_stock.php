<?php
use yii\bootstrap\Collapse;

/* @var $this yii\web\View */

$this->registerCss('.alert a {color:black !important; text-decoration: none !important;}')
?>

<div class="panel panel-default">
    <div class="panel-body">
        <p>
            <?=Collapse::widget([
                'items' => [
                    [
                        'label' => 'Ilustrasi Alur Perubahan Stock Fresh',
                        'content' => $this->render('stock_fresh'),
                        'contentOptions' => [],
                        'options' => [],
                        'footer' => 'Footer' // the footer label in list-group
                    ],
                    [
                        'label' => 'Ilustrasi Alur Perubahan Stock PFP',
                        'content' => '',
                        'contentOptions' => [],
                        'options' => [],
                        'footer' => 'Footer' // the footer label in list-group
                    ],
                    [
                        'label' => 'Ilustrasi Alur Perubahan Stock Ex Finish',
                        'content' => '',
                        'contentOptions' => [],
                        'options' => [],
                        'footer' => 'Footer' // the footer label in list-group
                    ],
                ]
            ])?>
        </p>
        <p class="label label-danger">
            *) Pada ilustrasi ini diasumsikan ada WO yang dibuat dengan menggunakan greige sepanjang 500, dan kartu proses dibuat sepanjang 500.
        </p>
    </div>
</div>
