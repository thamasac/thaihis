<?php

namespace appxq\sdii\assets;

use yii\web\AssetBundle;

class JSPdfAsset extends AssetBundle {

	public $sourcePath = '@appxq/sdii/assets';
	public $css = [
		
	];
	public $js = [
            'https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.4.1/jspdf.debug.js'
	];
        public $jsOptions = [
            'position' => \yii\web\View::POS_HEAD
        ];
	public $depends = [
		'yii\web\YiiAsset',
	];

}
