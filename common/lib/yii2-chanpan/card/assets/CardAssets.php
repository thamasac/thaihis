<?php

namespace cpn\chanpan\card\assets;
use yii\web\AssetBundle;
class CardAssets extends AssetBundle{
    public $sourcePath='@cpn/chanpan/card/assets';
    public $css = [
        'css/themify-icons.css',
       // 'css/paper-dashboard.css',
        'css/custom.css',
    ];
    public $js = [ 
    ];
    public $depends=[
        
    ];
}
