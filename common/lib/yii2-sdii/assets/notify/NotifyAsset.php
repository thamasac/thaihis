<?php

namespace appxq\sdii\assets\notify;

use yii\web\AssetBundle;

class NotifyAsset extends AssetBundle {

	public $sourcePath = '@appxq/sdii/assets/notify';
	public $css = [
		'css/jquery.noty.css',
		'css/noty_theme_twitter.css',
	];
	public $js = [
		'js/jquery.noty.js',
	];
	public $depends = [
            'yii\web\YiiAsset',
	];

}
