<?php
 
namespace backend\modules\manage_user\controllers;
use yii\web\Controller;
 
class SettingController extends Controller{
   public function actionIndex()
   {
        return $this->render('index');
   }
   public function actionVerifyEmail()
   {
       if(!\Yii::$app->user->can('administrator')){
           return $this->redirect(['/site/index']);
       }
       $paramName = 'email_register';
       $detail = \backend\modules\core\classes\CoreFunc::getParams($paramName, 'email');
       if(\Yii::$app->request->post()){
          $detail = \Yii::$app->request->post('detail', '');
          
          $data=['option_value'=>$detail];
          if(\backend\modules\core\classes\CoreFunc::updateCoreOptionValueByNameInDb($paramName, $data)){
              return \backend\modules\manageproject\classes\CNMessage::getSuccess('Save success.');
          }else{
              return \backend\modules\manageproject\classes\CNMessage::getError('error');
          }
       }
       
       return $this->render('verify-email',['detail'=>$detail]);
   }
   public function actionRecoverPassword()
   {
       if(!\Yii::$app->user->can('administrator')){
           return $this->redirect(['/site/index']);
       }
       $paramName = 'email_recover';
       $detail = \backend\modules\core\classes\CoreFunc::getParams($paramName, 'email');
       if(\Yii::$app->request->post()){
          $detail = \Yii::$app->request->post('detail', '');
          
          $data=['option_value'=>$detail];
          if(\backend\modules\core\classes\CoreFunc::updateCoreOptionValueByNameInDb($paramName, $data)){
              return \backend\modules\manageproject\classes\CNMessage::getSuccess('Save success.');              
          }else{
              return \backend\modules\manageproject\classes\CNMessage::getError('error');
          }
       }
       
       return $this->render('recover-password',['detail'=>$detail]);
   }
}
