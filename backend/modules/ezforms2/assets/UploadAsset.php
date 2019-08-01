<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\modules\ezforms2\assets;

use yii\web\AssetBundle;

class UploadAsset extends AssetBundle
{
    public $sourcePath = '@bower';

    public $css = [
        'Croppie/croppie.css'
    ];
    public $js = [
		'Croppie/croppie.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}