<?php

namespace cpn\chanpan\assets\copy;
use yii\web\AssetBundle;
class CNCopy extends AssetBundle{
    public $sourcePath='@cpn/chanpan/assets/copy';
    public $css = [
//        'croppie.css',
//        'style.css',
    ];
    public $js = [ 
        'clipboard.js'
    ];
    public $depends=[
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
