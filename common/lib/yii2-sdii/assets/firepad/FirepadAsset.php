<?php

namespace appxq\sdii\assets\firepad;

use yii\web\AssetBundle;

class FirepadAsset extends AssetBundle {

    public $sourcePath = '@appxq/sdii/assets/firepad';
    public $css = [
        'lib/codemirror.css',
        'dist/firepad.css',
        'lib/firepad-userlist.css',
        
    ];
    public $js = [
        'lib/firebase.js',
        'lib/codemirror.js',
        'https://cdn.firebase.com/libs/firepad/1.5.0/firepad.min.js',
        'lib/firepad-userlist.js',
        'lib/jquery.fullscreen.min.js'
        
    ];
    public $jsOptions = [
	'position' => \yii\web\View::POS_HEAD
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

}
