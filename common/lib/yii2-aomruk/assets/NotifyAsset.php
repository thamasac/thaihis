<?php
namespace dms\damasac\assets;

use yii\web\AssetBundle;

class NotifyAsset extends AssetBundle
{
    public $sourcePath='@dms/damasac/assets';

    public $css=[
	
    ];

    public $js=[
		
    ];
    
    public $depends=[
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
