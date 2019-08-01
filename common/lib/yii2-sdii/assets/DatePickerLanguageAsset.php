<?php

namespace appxq\sdii\assets;

use yii\web\AssetBundle;

class DatePickerLanguageAsset extends AssetBundle
{
    public $sourcePath = '@bower/bootstrap-datepicker/dist/locales';

    public $depends = [
        'appxq\sdii\assets\DateRangePickerAsset'
    ];
}
