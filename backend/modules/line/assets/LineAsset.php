<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace backend\modules\line\assets;

use yii\web\AssetBundle;
/**
 * Description of InputManager
 *
 * @author engiball
 */
class LineAsset extends AssetBundle {
      public $sourcePath='@backend/modules/line/assets';
    
    public $css = [
                'css/form.css'
    ];
    public $js = [

	'js/inputmanager.js'

    ];
      public $jsOptions = [
	'position' => \yii\web\View::POS_HEAD
    ];
    
    public $depends = [
	'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    
      public $publishOptions = [
        'forceCopy'=>true,
      ];

}
