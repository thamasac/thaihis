<?php

/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\modules\ezforms2\assets;

use yii\web\AssetBundle;

class EzfColorInputAsset extends AssetBundle {

    public $sourcePath='@backend/modules/ezforms2/assets';
    
    public $css = [
	'css/spectrum.css'
    ];
    
    public $js = [
	'js/spectrum.js'
    ];
    
    public $jsOptions = [
	'position' => \yii\web\View::POS_HEAD
    ];
    
    public $depends = [
	'yii\web\YiiAsset'
    ];

}
