<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\manageproject\controllers;

use Yii;
use yii\web\Controller;
class StepController extends Controller{
    
   public function actionCheckData(){
       $step = \backend\modules\core\classes\CoreFunc::getParams('step', 'step');
       if($step == '1' || $step == '2'){
           $urlModuleDefault = isset(\Yii::$app->params['url_index']) ? \Yii::$app->params['url_index'] : '/ezmodules/ezmodule/view?id=1521647584047559700&addon=0&tab=1528945511006792400';
           return $this->redirect([$urlModuleDefault]);
       }
       return $this->render("check-data");
   }
   public function actionGetStatus(){
       $dataId  = isset($_GET['data_id']) ? $_GET['data_id'] : '';
       $data = (new \yii\db\Query())
               ->select("*")
               ->from('zdata_create_project')
               ->where(['id'=>$dataId])
               ->andWhere("rstat <> 3 AND rstat <> 0")
               ->one();
       if(!empty($data)){
           return '1';
       }else{
           return '2';
       }
   }
   public function actionSaveData(){
       try{
           $id = isset($_POST['data']['id']) ? $_POST['data']['id'] : '';
           $data = isset($_POST['data']) ? $_POST['data'] : '';
           $status=2; 
           $dataCreate = (new \yii\db\Query())
                   ->select("*")
                   ->from("zdata_create_project")
                   ->where(['id'=>$id])
                   ->one();
           if(!empty($dataCreate)){
              unset($data['id']);
                $exec = \Yii::$app->db->createCommand()
                ->update("zdata_create_project", $data, "id=:id", [":id"=>$id])
                ->execute(); 
                if($exec){
                   $status=1;
               } 
           }else{
               $exec=\Yii::$app->db->createCommand()
                ->insert("zdata_create_project", $data)
                ->execute();
               if($exec){
                   $status=1;
               }               
           }
           
            
       }catch(\yii\db\Exception $ex){}
   }
   
   public function actionSetStart(){
       
       \backend\modules\core\classes\CoreFunc::updateCoreOptionValueByNameInDb('step', ['option_value'=>'2']);
 
   }
   public function actionSetCookie($step){
       $value = [1];
       $domain = \cpn\chanpan\classes\CNServerConfig::getDomainName();  
       $step = isset($step) ? $step : '1';
       $dataValue = (new \yii\db\Query())->select('*')->from('wizard_config')->where('domain=:domain',[':domain'=>$domain])->one();

       if(!empty($dataValue)){
           $wizard_data = explode(',', $dataValue['data']); 
           if(!in_array($step, $wizard_data)){
               $value = $wizard_data;
               array_push($value, $step);
               $data = [
                    'data'=> implode(',', $value),
                    'create_at'=>Date('Y-m-d')
                ];
               if(Yii::$app->db->createCommand()->update('wizard_config', $data, ['id'=>$dataValue['id']])->execute()){
                   return \backend\modules\manageproject\classes\CNMessage::getSuccess("Success");
               }else{
                   return \backend\modules\manageproject\classes\CNMessage::getError("Error");
               }
           } 
       }else{
           array_push($value, $step);
           $data = [
               'domain'=>$domain,
               'data'=> implode(',', $value),
               'user_id'=> Yii::$app->user->id,
               'create_at'=>Date('Y-m-d')
           ];            
           if(Yii::$app->db->createCommand()->insert('wizard_config', $data)->execute()){
               return \backend\modules\manageproject\classes\CNMessage::getSuccess("Success");
           }else{
               return \backend\modules\manageproject\classes\CNMessage::getError("Error");
           }
            
       }
       
   }
 public function actionProjectSetting(){
    return $this->render('project-setting'); 
 }
   public function actionIndex(){
       $urlModuleDefault = isset(\Yii::$app->params['url_index']) ? \Yii::$app->params['url_index'] : '/ezmodules/ezmodule/view?id=1521647584047559700&addon=0&tab=1528945511006792400';
       if(!\Yii::$app->user->can('administrator')){
          return $this->redirect([$urlModuleDefault]);
       }
       
       
       $stepParams = \backend\modules\core\classes\CoreFunc::getParams("step", 'step');
       if($stepParams == 1){
           return $this->redirect(['/site/index']);
       }
       $step = isset($_GET['step']) ? $_GET['step'] : '';
       if($step == 1){
           $mainUrl = \backend\modules\core\classes\CoreFunc::getParams('start_project', 'project');
           if(empty($mainUrl)){               
              return "<script>location.href='/ezmodules/ezmodule/view?id=1521647584047559700&addon=0&tab=1528945511006792400';</script>";
           }else{
               return "<script>location.href='".$mainUrl."'</script>";
           }
           
           return $this->render("step1");
       }else if($step == 2){
           $this->checkCookie($step);
           try{
                $domain = \cpn\chanpan\classes\CNServerConfig::getDomainName();
                $dataDynamic = \backend\modules\manageproject\classes\CNCloneDb::checkDynamicDb($domain);
                $dataId=isset($dataDynamic['data_id']) ? $dataDynamic['data_id'] : '';
                $dataTctr=(new \yii\db\Query())
                        ->select('*')
                        ->from('zdata_tctr_main')
                        ->where("id=:id",[":id"=>$dataDynamic['tctr_id']])
                        ->one();
                if(!$dataTctr){
                    $sql2="REPLACE INTO zdata_tctr_main (SELECT * FROM ncrc.zdata_tctr_main WHERE id={$dataDynamic['tctr_id']})";
                    $execute2 = Yii::$app->db->createCommand($sql2)->execute();
                }               
           } catch (\yii\db\Exception $ex) {
               \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
           }
           
           return $this->render("step2");           
       }else if($step == 3){
           $this->checkCookie($step);
           return $this->render("step3");
       }else if($step == 4){
           $this->checkCookie($step);
           return $this->render("step4");
       }else if($step == 5){
           $this->checkCookie($step);
           return $this->render("step5");
       }else if($step == 6){
           $this->checkCookie($step);
           return $this->render("step6");
       }else{
           return $this->redirect(['/manageproject/step/index?step=1']);
       }
       
       
   }
  public function checkCookie($step){
       $step = isset($step) ? $step : '';
       $domain = base64_encode(\cpn\chanpan\classes\CNServerConfig::getDomainName());
       $cookieValue = (new \yii\db\Query())->select('*')->from('wizard_config')->where('domain=:domain',[':domain'=>$domain])->one();
       
       
       if(empty($cookieValue) || !in_array($step, explode(',', $cookieValue['data']))){
           return $this->redirect(['/manageproject/step/index?step=1']);
           
       }
   }
   
   
   public function actionIndexAjax(){
       $step = \backend\modules\core\classes\CoreFunc::getParams("step", 'step');
       if($step == 1){
           return $this->redirect(['/site/index']);
       }
       return $this->renderAjax("index");
   }
   public function actionGetRole(){
       return $this->renderAjax("get-role");
   }
   public function actionGetMatching(){
       return $this->renderAjax("get-matching");
   }
   public function actionGetSitecode(){
       return $this->renderAjax("get-sitecode");
   }
   
   
   public function actionSetCoreOption(){
       $option_value = isset($_GET['option_value']) ? $_GET['option_value'] :' ';
       $dataOption=['option_value'=>$option_value];
       $setCorOption = \backend\modules\core\classes\CoreFunc::updateCoreOptionValueByNameInDb("step", $dataOption);
       if($setCorOption){
           $urlModuleDefault = isset(\Yii::$app->params['url_index']) ? \Yii::$app->params['url_index'] : '/ezmodules/ezmodule/view?id=1521647584047559700&addon=0&tab=1528945511006792400';
           return $this->redirect([$urlModuleDefault]);
       }else{
           $urlModuleDefault = isset(\Yii::$app->params['url_index']) ? \Yii::$app->params['url_index'] : '/ezmodules/ezmodule/view?id=1521647584047559700&addon=0&tab=1528945511006792400';
           return $this->redirect([$urlModuleDefault]);
       }
   }
   public function actionGetRecruitMember(){
       return $this->renderAjax("get-recruit-member");
   }
   
    public function actionSchedule(){ 
       return $this->renderAjax("schedule");
   }
   public function actionVisit(){
       return $this->renderAjax("visit");
   }
   
}
