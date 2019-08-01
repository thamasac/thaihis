<?php

namespace appxq\sdii\assets;

use yii\web\AssetBundle;

class SDAsset extends AssetBundle {

	public $sourcePath = '@appxq/sdii/assets';
	public $css = [
		'css/style.css'
	];
	public $js = [
	];
	public $depends = [
		'appxq\sdii\assets\bootbox\BootBoxAsset',
		'appxq\sdii\assets\notify\NotifyAsset',
	];

}
