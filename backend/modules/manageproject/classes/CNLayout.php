<?php
 
namespace backend\modules\manageproject\classes;
 
class CNLayout {
     public static function getTitle(){
         $projectName= isset(Yii::$app->params['project_name']) ? Yii::$app->params['project_name'] : '';
         $aconym= isset(Yii::$app->params['aconym']) ? Yii::$app->params['aconym'] : '';
         $sitecode = \common\modules\user\classes\CNSitecode::getSiteValue();
         
         
         $url = isset(Yii::$app->params['current_url']) ? Yii::$app->params['current_url'] : \cpn\chanpan\classes\CNServerConfig::getDomainName();//\cpn\chanpan\classes\CNServer::getDemain();
         $main_url = \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
        if (\Yii::$app->user->isGuest && $url != $main_url) {  
            $html = "";
            $html .= "
               <div class='container-fluid'> 
               <div class='row' > 
                   <div class='col-md-12'>
                       <div class=' ' style='padding: 10px;margin-bottom: -10px;color: #e0e0e0; background-color: #58a4e6; border-color: #bce8f1; border-radius: 0;'>
                           <div>
                               <span style='margin-right: 10px;'><i class='fa fa-globe'></i> You are currently working with: <b style='color:#fff'>
                                 {$projectName} : {$aconym}</b></span>   "
                                . "<span><i class='fa fa-building'></i> Site: <b style='color:#fff'>{$sitecode}</b></span>
                           </div>
                       </div>
                   </div>
                   <div class='clearfix'></div>
               </div>
               </div>
            ";
            return $html;
        } 
     }
}
