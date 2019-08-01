<?php

namespace backend\modules\manageproject\controllers;
use backend\modules\manageproject\models\CreateProject;
use yii\db\Query;
use yii\web\Controller;

class TemplateController extends Controller
{
    public function actionIndex(){
       return $this->renderAjax('index');       
    }
    public function actionGetTemplate(){   
        $scratch = \backend\modules\core\classes\CoreFunc::getParams('scratch', 'search');
        $scratch = ($scratch != "") ? $scratch : '1531376278057471800';
        $template=(new Query())
           ->select('*')
           ->from('zdata_create_project_template')
           ->where('id <> :id AND rstat not in(0,3)', [':id'=>$scratch])
           ->all();
        foreach($template as $key=>$value){
           $template[$key]['mode']='assign';
        }  
        if(empty($template)){
            return \cpn\chanpan\classes\CNResponse::notFoundAlert();
        }
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels'=>$template,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]); 
          
        return $this->renderAjax("get-template",[
            'template'=>$template,
            'dataProvider'=>$dataProvider,
            'status'=>'assign'
        ]);
    }
    public function actionGetUserTemplate(){ 
       $scratch = \backend\modules\core\classes\CoreFunc::getParams('scratch', 'search');
       $scratch = ($scratch != "") ? $scratch : '1531376278057471800';
        $template=(new Query())
           ->select('*')
           ->from('zdata_create_project_template')
           ->where('id <> :id AND rstat not in(0,3)', [':id'=>$scratch])
           ->all(); 
        foreach($template as $key=>$value){
           $template[$key]['mode']='assign';
        }  
        if(empty($template)){
            return \cpn\chanpan\classes\CNResponse::notFoundAlert();
        }
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels'=>$template,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]); 
          
        return $this->renderAjax("get-template",[
            'template'=>$template,
            'dataProvider'=>$dataProvider,
            'status'=>'assign'
        ]);
    }
    public function actionGetFormCreate(){
       $id = isset($_GET['id']) ? $_GET['id'] : '';
       $data=(new Query())
           ->select('*')
           ->from('zdata_create_project_template')
           ->where('id=:id',[':id'=>$id])
           ->one(); 
       $study = \backend\modules\core\classes\CoreFunc::getParams('study_design', 'study');
       $study = \appxq\sdii\utils\SDUtility::string2Array($study);
       $studydesign = '';
       if(isset($data['studydesign'])){
           //\appxq\sdii\utils\VarDumper::dump($study);
           $studydesign = isset($study[$data['studydesign']])?$study[$data['studydesign']]:''; 
       } 
              
  
       return $this->renderAjax('get-form-create', [
           'data'=>$data,
           'studydesign'=>$studydesign
       ]);
    }
    
    public function actionGetCloneFormCreate(){
        \backend\modules\manageproject\classes\CNFunc::addLog("View Project Clone data-id = {$id}");
       $id = isset($_GET['id']) ? $_GET['id'] : '';       
       $data=(new Query())
           ->select('*')
           ->from('zdata_create_project')
           ->where('id=:id',[':id'=>$id])
           ->one(\Yii::$app->db_main);
       //\appxq\sdii\utils\VarDumper::dump($data);
       $study = \backend\modules\core\classes\CoreFunc::getParams('study_design', 'study');
       $study = \appxq\sdii\utils\SDUtility::string2Array($study);
       
       $studydesign = '';
       if(!empty($study)){
           $studydesign = $study[$data['studydesign']];
       }
       
       return $this->renderAjax('get-clone-form-create', [
        'data'=>$data  ,
        'studydesign'=>$studydesign
       ]);
    }
}
   