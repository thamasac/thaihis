<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\modules\core\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CoreAsset extends AssetBundle {

    //public $basePath = '@frontend/themes/admin/assets';
    //public $baseUrl = '@web/../themes/admin/assets';
    public $sourcePath = '@backend/modules/core/assets';
    public $css = [
        'prettify/css/prettify.css',
        'prettify/css/monokai.css',
    ];
    public $js = [
        'prettify/prettify.js',
    ];
    public $depends = [
        'yii\jui\JuiAsset',
    ];

}
