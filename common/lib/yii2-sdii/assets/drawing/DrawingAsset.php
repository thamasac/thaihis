<?php
namespace appxq\sdii\assets\drawing;

use yii\web\AssetBundle;

class DrawingAsset extends AssetBundle
{
    public $sourcePath='@appxq/sdii/assets/drawing';

    public $css=[
	'css/icon.css',
	'css/style.css?99'
    ];

    public $js=[
		'js/text.js',
        //'js/drawingLive.js?95',
        'js/excanvas.min.js',
		//'js/jquery.event.drag-2.2.js', //jquery 1.7
		'js/jquery.fullscreen.min.js'
    ];
    
    public $depends=[
        'yii\web\YiiAsset',
        'yii\jui\JuiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
