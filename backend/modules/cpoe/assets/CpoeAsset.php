<?php

namespace backend\modules\cpoe\assets;

use yii\web\AssetBundle;

class CpoeAsset extends AssetBundle {

    public $sourcePath = '@backend/modules/cpoe/assets';
    public $css = [
        //'css/clarity.css',
        'css/style.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
