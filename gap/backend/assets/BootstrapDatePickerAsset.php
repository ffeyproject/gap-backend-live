<?php


namespace backend\assets;


use yii\web\AssetBundle;

class BootstrapDatePickerAsset extends AssetBundle
{
    //bootstrap-datepicker-1.9.0
    public $basePath = '@webroot/bootstrap-datepicker-1.9.0';
    public $baseUrl = '@web/bootstrap-datepicker-1.9.0';
    public $css = [
        'css/bootstrap-datepicker.css',
    ];
    public $js = [
        'js/bootstrap-datepicker.js',
    ];
    public $depends = [
        AppAsset::class
    ];
}