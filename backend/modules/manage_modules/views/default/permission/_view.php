<?php

use yii\helpers\Url;

$imgPath = Yii::getAlias('@storageUrl');
$noImage = $imgPath . '/ezform/img/no_icon.png';
use backend\modules\manage_modules\components\CNMyModule;
echo CNMyModule::classNames()
        ->setImgPath($imgPath)
        ->setNoImage($noImage)
        ->setCardWidth(12)
        ->setDataModule($rs)
//        ->setLink(TRUE)
//        ->setTargetLink('_blank')
        ->buildCard();
        
     
?>
 
    
     