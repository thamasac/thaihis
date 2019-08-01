<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\themes\standard\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ThemeAsset extends AssetBundle {

    //public $basePath = '@frontend/themes/admin/assets';
    //public $baseUrl = '@web/../themes/admin/assets';
    public $sourcePath = '@frontend/themes/standard/assets';
    public $css = [
        'css/layout.css',
        'css/layout-responsive.css',
        'css/default.css',
        'css/font-awesome.min.css',
        'css/style.css?9932644',
    ];
    public $js = [
        'js/jquery.slimscroll.min.js',
        'js/app.js',
        'js/application.js',
        'js/jquery.cookie.js',
        //'js/bootstrap.min.js'
    ];
    public $depends = [
        'yii\jui\JuiAsset',
    ];
    

}
