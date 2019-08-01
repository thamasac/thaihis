<?php
namespace backend\modules\manageproject\classes;
use Yii; 
class CNImages {
    public static function getStorageUrl(){
        return \Yii::getAlias('@storageUrl');
    }
    public static function getBackendUrl(){
        return Yii::getAlias('@backendUrl');
    }
    public static function getNoImage($imagePath = ''){   
        return self::getBackendUrl() . ($imagePath != '') ? $imagePath : "/img/health-icon.png";
    }
}
