<?php

namespace appxq\sdii\assets;

use yii\web\AssetBundle;

class DatePickerAsset extends AssetBundle
{
    public $sourcePath = '@bower/bootstrap-datepicker/dist';

    public $css = [
        'css/bootstrap-datepicker3.css',
    ];

    public $js = [
        'js/bootstrap-datepicker.js'
    ];

    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}
