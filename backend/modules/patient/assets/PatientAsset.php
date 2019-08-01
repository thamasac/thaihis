<?php

namespace backend\modules\patient\assets;

use yii\web\AssetBundle;

class PatientAsset extends AssetBundle {

    public $sourcePath = '@backend/modules/patient/assets';
    public $css = [
        'css/patient.css',
    ];
    public $js = [
        //'js/patient.js'
    ];
    public $depends = [
        //'yii\web\YiiAsset',
    ];

}
