<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\modules\ezforms2\assets;

use yii\web\AssetBundle;

class ListdataAsset extends AssetBundle
{
    public $sourcePath='@backend/modules/ezforms2/assets';

    public $css = [
        'css/style.css?9',
    ];
    public $js = [
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
