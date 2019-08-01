<?php

namespace cpn\chanpan\classes;
use cpn\chanpan\classes\CNManageProjectQuery;
class CNManageProjectFunc {
   public static function UpdateCoreForm(){
       $main_url = \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
       $current_url = Yii::$app->params['model_dynamic'];
       if($main_url != $current_url['url']){
            $tbName = CNManageProjectQuery::getCoreTable();
            //\appxq\sdii\utils\VarDumper::dump($current_url['aconym']);
            if(CNManageProjectQuery::dropTableOld($tbName) && CNManageProjectQuery::updateCoreTable($tbName)){
                CNManageProjectQuery::updateAconym($current_url['aconym']);
                return \backend\modules\manageproject\classes\CNMessage::getSuccess("Update Success");
            }else if(!CNManageProjectQuery::dropTableOld($tbName) && CNManageProjectQuery::updateCoreTable($tbName)){       
                CNManageProjectQuery::updateAconym($current_url['aconym']);
                return \backend\modules\manageproject\classes\CNMessage::getSuccess("Update Success");       
            }
            
       } 
       
   }
}
