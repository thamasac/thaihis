<?php

namespace cpn\chanpan\assets;
use yii\web\AssetBundle;
class CNCroppieAssets extends AssetBundle{
    public $sourcePath='@cpn/chanpan/assets/croppie';
    public $css = [
        'croppie.css',
        'style.css',
    ];
    public $js = [ 
        'croppie.min.js'
    ];
    public $depends=[
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
