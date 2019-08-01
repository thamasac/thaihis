<?php

namespace cpn\chanpan\assets;
use yii\web\AssetBundle;
class WizardWidgetAsset extends AssetBundle{
    public $sourcePath='@cpn/chanpan/assets';
    public $css = [
        'css/smart_wizard.min.css',
    ];
    public $js = [
        'js/jquery.min.js',
        'js/jquery.smartWizard.min.js'
    ];
    public $depends=[
        
    ];
}
