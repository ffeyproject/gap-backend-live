<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/jqueryConfirm/jquery-confirm.min.css'
    ];
    public $js = [
        'js/jquery.blockUI.js',
        //'js/jqueryNumber/jquery.number.min.js',
        'js/jqueryConfirm/jquery-confirm.min.js',
        'js/moment/moment.min.js',
        'js/moment/moment-timezone-with-data.min.js',
    ];
    public $depends = [
        '\dmstr\web\AdminLteAsset'
    ];
}
