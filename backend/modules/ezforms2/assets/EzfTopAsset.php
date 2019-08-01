<?php

/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\modules\ezforms2\assets;

use yii\web\AssetBundle;

class EzfTopAsset extends AssetBundle {

    public $sourcePath='@backend/modules/ezforms2/assets';
    
    public $css = [
    ];
    
    public $js = [
	'js/jquery.cookie.js',
        
    ];
    
    public $jsOptions = [
	'position' => \yii\web\View::POS_HEAD
    ];
    
    public $depends = [
	'yii\web\YiiAsset'
    ];

}
