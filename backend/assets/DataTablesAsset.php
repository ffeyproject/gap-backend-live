<?php
namespace backend\assets;

use yii\web\AssetBundle;

class DataTablesAsset  extends AssetBundle
{
    public $basePath = '@webroot/DataTables';
    public $baseUrl = '@web/DataTables';
    public $css = [
        'datatables.min.css'
    ];
    public $js = [
        'datatables.min.js'
    ];
    public $depends = [
        'frontend\assets\AppAsset',
    ];
}