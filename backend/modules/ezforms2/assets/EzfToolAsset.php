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
class EzfToolAsset extends AssetBundle {

    public $sourcePath='@backend/modules/ezforms2/assets';
    
    public $css = [
	'css/condition.css',
	'css/dad/jquery.dad.css',
	//'css/ezform.css?9911',
	//'css/jloading.css',
        'css/waitMe.min.css',
	//'css/fontawesome-iconpicker.min.css',
    ];
    public $js = [
	'js/jscondition.js?959',
	'js/dad/jquery.dad.js?89',
	//'js/dad/jquery.nicescroll.min.js',
	//'js/jloading.js',
        'js/waitMe.min.js',
	//'js/fontawesome-iconpicker.min.js',
        
    ];
    public $depends = [
	'yii\web\YiiAsset',
	'backend\modules\ezforms2\assets\EzfTopAsset',
        'backend\modules\ezforms2\assets\EzfAsset'
    ];

}
