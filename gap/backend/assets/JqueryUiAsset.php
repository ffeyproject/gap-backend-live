<?php
namespace backend\assets;

use yii\web\AssetBundle;

class JqueryUiAsset  extends AssetBundle
{
    public $basePath = '@webroot/jquery-ui-1.12.1';
    public $baseUrl = '@web/jquery-ui-1.12.1';
    public $css = [
        'jquery-ui.css',
    ];
    public $js = [
        'jquery-ui.js',
    ];
    public $depends = [
        AppAsset::class
    ];
}