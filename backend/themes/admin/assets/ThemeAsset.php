<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\themes\admin\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ThemeAsset extends AssetBundle {

    //public $basePath = '@frontend/themes/admin/assets';
    //public $baseUrl = '@web/../themes/admin/assets';
    public $sourcePath = '@backend/themes/admin/assets';
    public $css = [
        'css/layout.css?1',
        'css/layout-responsive.css?11',
        'css/default.css',
        //'css/font-awesome.min.css',
        'css/style.css?95656',
    ];
    public $js = [
        'js/jquery.slimscroll.min.js',
        'js/app.js',
        'js/application.js',
        'js/jquery.cookie.js',
        'js/pwstrength-bootstrap.min.js',
        //'js/bootstrap.min.js'
    ];
    public $depends = [
        'yii\jui\JuiAsset',
        'dominus77\iconpicker\FontAwesomeAsset',
    ];

}
