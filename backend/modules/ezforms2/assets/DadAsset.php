<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\modules\ezforms2\assets;

use yii\web\AssetBundle;

class DadAsset extends AssetBundle
{
    public $sourcePath='@backend/modules/ezforms2/assets';

    public $css = [
        'css/dad/jquery.dad.css',
    ];
    public $js = [
        'js/dad/jquery.dad.js?987',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
