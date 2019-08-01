<?php

namespace appxq\sdii\assets;

use yii\web\AssetBundle;

class VisAsset extends AssetBundle {

	public $sourcePath = '@appxq/sdii/assets';
	public $css = [
		'vis/vis.min.css'
	];
	public $js = [
                'vis/vis.min.js'
	];
        public $jsOptions = [
            'position' => \yii\web\View::POS_HEAD
        ];
	public $depends = [
		
	];

}
