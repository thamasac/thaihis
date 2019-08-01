<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\modules\ezmodules\assets;

use yii\web\AssetBundle;

class StarRatingsAsset extends AssetBundle
{
    public $sourcePath='@backend/modules/ezmodules/assets';

    public $css = [
        'css/bootstrap-stars.css',
    ];
    
    public $js = [
        'js/jquery.barrating.js',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
