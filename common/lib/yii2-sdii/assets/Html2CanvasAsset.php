<?php
namespace appxq\sdii\assets;

use yii\web\AssetBundle;

class Html2CanvasAsset extends AssetBundle
{
    public $sourcePath='@appxq/sdii/assets';

    public $css=[
    ];

    public $js=[
	'js/html2canvas.js',
    ];
    
    public $depends=[
    ];
}
