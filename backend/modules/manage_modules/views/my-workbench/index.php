<?php
use backend\modules\manage_modules\components\CNMyModule;

$imgPath = Yii::getAlias('@storageUrl');
$noImage = $imgPath . '/ezform/img/no_icon.png';
   
    if(!empty($modules)){  
        foreach($modules as $key=>$value){
            $module = (new \yii\db\Query())
                  ->select('*')
                  ->from('zdata_manage_modules')
                  ->where('rstat not in(0,3)')
                  ->andWhere('enableds=1 AND module_id=:module_id', [":module_id"=>$value['module_id']])  
                  ->orderBy(['order_by'=>SORT_ASC])  
                  ->one();
         
            if(!empty($module)){
                echo CNMyModule::classNames()
                    ->setImgPath($imgPath)
                    ->setNoImage($noImage)
                    ->setCardWidth(6)
                    ->setDataModule($module)
                    ->setLink(TRUE)
                    ->setTargetLink('_self')
                    ->buildCard();
            }

        }
    }else{
        

        $module = (new \yii\db\Query())
                  ->select('*')
                  ->from('zdata_manage_modules')
                  ->where('rstat not in(0,3)')
                  ->andWhere('enableds=1')  
                  ->orderBy(['order_by'=>SORT_ASC])  
                  ->all();
        
            if(!empty($module)){
                if(\cpn\chanpan\classes\CNUser::canAdmin()){
                    
                    foreach($module as $module){
                        echo CNMyModule::classNames()
                            ->setImgPath($imgPath)
                            ->setNoImage($noImage)
                            ->setCardWidth(6)
                            ->setDataModule($module)
                            ->setLink(TRUE)
                            ->setTargetLink('_self')
                            ->buildCard();
                    }
                }
                
            }
    }
?>
