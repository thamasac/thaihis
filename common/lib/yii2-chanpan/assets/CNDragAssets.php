<?php

namespace cpn\chanpan\assets;
use yii\web\AssetBundle;
class CNDragAssets extends AssetBundle{
    public $sourcePath='@cpn/chanpan/assets/drag';
    public $css = [
        'jquery.dad.css',
    ];
    public $js = [ 
        'jquery.dad.js'
    ];
    public $depends=[
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}
