<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\modules\ezforms2\assets;

use yii\web\AssetBundle;

class DatePickerAsset extends AssetBundle
{
    public $sourcePath='@backend/modules/ezforms2/assets';

    public $css = [
        'css/datepicker.css',
    ];
    public $js = [
        'js/bootstrap-datepicker-thai.js',
        'js/bootstrap-datepicker.js',
        'js/locales/bootstrap-datepicker.th.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\jui\JuiAsset',
    ];
}
