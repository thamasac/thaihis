<?php

namespace appxq\sdii\assets;

use yii\web\AssetBundle;

class DateRangePickerAsset extends AssetBundle
{
    public $sourcePath = '@appxq/sdii/assets';

    public $css = [
        'css/bootstrap-daterangepicker.css'
    ];

    public $depends = [
        'appxq\sdii\assets\DatePickerAsset'
    ];

}
