<?php

namespace cpn\chanpan\assets\tour;
use yii\web\AssetBundle;
class BootstrapTourAssets extends AssetBundle{
    public $sourcePath='@cpn/chanpan/assets/tour';
    public $css = [
        'css/bootstrap-tour.css',
        'css/bootstrap-tour-standalone.css',
    ];
    public $js = [ 
        'js/bootstrap-tour.min.js',
        'js/bootstrap-tour-standalone.min.js'
    ];
    public $depends=[
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
