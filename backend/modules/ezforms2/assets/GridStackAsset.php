<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\modules\ezforms2\assets;

use yii\web\AssetBundle;

class GridStackAsset extends AssetBundle
{
    public $sourcePath='@backend/modules/ezforms2/assets';

    public $css = [
        'gridstack/gridstack.css',
    ];
    
    public $js = [
        'gridstack/lodash.js',
        'gridstack/gridstack.js',
        'gridstack/gridstack.jQueryUI.js',
    ];

    public $depends = [
        'yii\jui\JuiAsset',
    ];
}
