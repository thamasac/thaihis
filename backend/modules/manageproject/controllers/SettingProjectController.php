<?php
 
namespace backend\modules\manageproject\controllers;
use yii\db\Exception;
use yii\web\Controller;
use Yii;
use backend\modules\manageproject\classes\CNDatabaseFunc2;
class SettingProjectController extends Controller{
   public function actionIndex(){
       \backend\modules\manageproject\classes\CNFunc::addLog('View Manage the Created Projects');
       return $this->render("index");
   } 
   public function actionMyProject(){
 
       if(\Yii::$app->request->isAjax){
           $data = \backend\modules\manageproject\classes\CNSettingProjectFunc::MyProject();
            $dataProvider = new \yii\data\ArrayDataProvider([
                 'allModels' => $data,
                 'sort' => [
                     'attributes' => ['projectacronym', 'projurl', 'projdomain','useTemplate','pi_name','rstat','create_date','create_by'],
                 ],
                 'pagination' => [
                     'pageSize' => 100,
                 ],
           ]);
           return $this->renderAjax("my-project",[
               'dataProvider'=>$dataProvider
           ]); 
       }
   }
   public function actionRestore(){
        if(\Yii::$app->request->isAjax){            
           $id = \Yii::$app->request->post('id', '');
           return \backend\modules\manageproject\classes\CNSettingProjectFunc::RestoreMyProject($id);
       }   
   }
   public function actionDelete(){
       if(\Yii::$app->request->isAjax){
           $id = \Yii::$app->request->post('id', '');
           return \backend\modules\manageproject\classes\CNSettingProjectFunc::DeleteMyProject($id);
       }       
   }
   
   public function actionRepair(){
       
       $dataid                      = isset($_GET['id']) ? $_GET['id'] : '';
       $dataProject                 = \backend\modules\manageproject\classes\CNSettingProjectFunc::MyProjectByid2($dataid);
       if(!empty($dataid)){
          $myProject                = \common\modules\user\classes\CNDynamicDb::getDataById($dataProject[0]['id']);
         
          if(empty($myProject)){  
              \common\modules\user\classes\CNDynamicDb::save($dataProject[0]);              
              $myProject                = \common\modules\user\classes\CNDynamicDb::getDataById($dataProject[0]['id']);
            //ไม่มีข้อมูลใน dbDynamic          
          } 
           
          if(!empty($myProject)){               
              $template             = ($myProject['project_template'] != '') ? $myProject['project_template'] : 'ncrc';
              $dbClone              = $myProject['config_db'];               
              $databaseAll          = CNDatabaseFunc2::getDatabase();
              if(!in_array($dbClone, $databaseAll)){
                  
                 //ถ้าไม่พบ ฐานข้อมูล
                 CNDatabaseFunc2::cloneRepair($dataProject[0],$myProject);
                 return \backend\modules\manageproject\classes\CNMessage::getSuccess("Repair success");
              }else{
                  
                  //ถ้ามีฐานข้อมูล
                  $tables           = CNDatabaseFunc2::getTableAll($template);
                  $dataTb           = "Tables_in_{$template}";
                  
                  foreach($tables as $key=>$table){
                     $tbname        = $table[$dataTb];                     
                     $table_struc   = CNDatabaseFunc2::getTableByName($template, $tbname);                      
                     $addTable      = CNDatabaseFunc2::updateFields($dbClone, $table_struc, $tbname); 
                  }  
              }
              return \backend\modules\manageproject\classes\CNMessage::getSuccess("Update Project: {$dataProject[0]['projectname']} Successfully");
          }else{
              return \backend\modules\manageproject\classes\CNMessage::getError("error");
          }
       }
        
   }
   public function actionDestroy(){
       $post = \Yii::$app->request->post();
       $dataid = isset($post['id']) ? $post['id'] : '';        
       $destroy=\cpn\chanpan\classes\utils\CNProject::desTroyProjectById($dataid);       
       if($destroy){
           return \backend\modules\manageproject\classes\CNMessage::getSuccess("Permanently Delete Successfully");
       }else{
              return \backend\modules\manageproject\classes\CNMessage::getError("error");
          }
       
        
   }

}
