<?php

namespace appxq\sdii\assets;

use yii\web\AssetBundle;

class CroppieAssets extends AssetBundle {

	public $sourcePath = '@appxq/sdii/assets';
        //public $sourcePath = '@bower';
        
	public $css = [
		'Croppie/croppie.css',
                //'Croppie/style.css',
	];
	public $js = [
                'Croppie/croppie.js'
	];
       public $depends=[
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

}
