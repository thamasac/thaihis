<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\modules\ezmodules\assets;

use yii\web\AssetBundle;

class ModuleAsset extends AssetBundle
{
    public $sourcePath='@backend/modules/ezmodules/assets';

    public $css = [
        'css/style.css?999',
    ];
    
    public $js = [
    ];

    public $depends = [
    ];
}
