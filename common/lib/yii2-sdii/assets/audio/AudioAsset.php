<?php
namespace appxq\sdii\assets\audio;

use yii\web\AssetBundle;

class AudioAsset extends AssetBundle
{
    public $sourcePath='@appxq/sdii/assets/audio';

    public $css=[
	'css/jplayer.blue.monday.min.css',
    ];

    public $js=[
	    'js/MediaStreamRecorder.js',
	    'js/gumadapter.js',
	    'js/app.js',
	    'js/jquery.jplayer.min.js',
    ];
    
    public $depends=[
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
