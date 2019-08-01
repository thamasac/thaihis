<?php

 
namespace cpn\chanpan\assets\zoom;
 
class JZoomAsset extends \yii\web\AssetBundle{
    public $sourcePath='@cpn/chanpan/assets/zoom/assets';
    public $css = [
    ];
    public $js = [
        'jquery.elevatezoom.js',
    ];
    public $depends=[
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
