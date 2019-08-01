<?php
namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\View;

class AngularAsset extends AssetBundle
{
//    public $sourcePath = '@web';
    public $baseUrl = '@web';
    public $js = [
        'js/angular.min.js',
        'js/socket.io.js'
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}