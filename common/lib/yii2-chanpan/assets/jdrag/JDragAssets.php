<?php

namespace cpn\chanpan\assets\jdrag;
use yii\web\AssetBundle;
class JDragAssets extends AssetBundle{
    public $sourcePath='@cpn/chanpan/assets/jdrag/assets';
    public $css = [
        'jquery-ui.css',
        'jquery.dad.css',
    ];
    public $js = [ 
        'jquery-ui.js',
        'jquery.dad.js'
    ];
    public $depends=[
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
