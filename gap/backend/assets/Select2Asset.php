<?php


namespace backend\assets;


use yii\web\AssetBundle;

class Select2Asset extends AssetBundle
{
    //bootstrap-datepicker-1.9.0
    public $basePath = '@webroot/select2-4.0.13';
    public $baseUrl = '@web/select2-4.0.13';
    public $css = [
        //'dist/css/select2.min.css',
    ];
    public $js = [
        'dist/js/select2.min.js',
        //'dist/js/select2.full.js',
    ];
    public $depends = [
        AppAsset::class
    ];
}