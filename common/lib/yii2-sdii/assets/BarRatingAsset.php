<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace appxq\sdii\assets;

use yii\web\AssetBundle;

class BarRatingAsset extends AssetBundle
{
    public $sourcePath='@appxq/sdii/assets/barrating';

//    public $css = [
//        'themes/bootstrap-stars.css',
//    ];
    
    public $js = [
        'jquery.barrating.min.js',
    ];

//    public $jsOptions = [
//        'position' => \yii\web\View::POS_HEAD
//    ];
//
//    public $depends = [
//        'yii\web\YiiAsset',
//    ];
    
}
