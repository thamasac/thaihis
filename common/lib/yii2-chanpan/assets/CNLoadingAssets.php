<?php

namespace cpn\chanpan\assets;
use yii\web\AssetBundle;
class CNLoadingAssets extends AssetBundle{
    public $sourcePath='@cpn/chanpan/assets/loading';
    public $css = [
        'waitMe.min.css',
    ];
    public $js = [ 
        'waitMe.min.js'
    ];
    public $depends=[
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}
