<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\modules\ezforms2\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class EzfAsset extends AssetBundle {

    public $sourcePath='@backend/modules/ezforms2/assets';
    
    public $css = [
	//'css/condition.css',
	//'css/dad/jquery.dad.css',
	'css/ezform.css?2343243433',
	//'css/jloading.css',
        'css/waitMe.min.css',
	//'css/fontawesome-iconpicker.min.css',
    ];
    public $js = [
	//'js/jscondition.js',
	//'js/dad/jquery.dad.js',
	'js/dad/jquery.nicescroll.min.js',
	//'js/jloading.js',
        'js/waitMe.min.js',
	//'js/fontawesome-iconpicker.min.js',
        'js/ezform-custom.js?2343243499',
    ];
    public $depends = [
	'yii\web\YiiAsset',
	'backend\modules\ezforms2\assets\EzfTopAsset'
    ];

}
