<?php
 
namespace backend\modules\manageproject\controllers;
use Yii;
use yii\web\Controller;
use backend\modules\manageproject\classes\CNSettingProjectFunc;
class MonitorProjectController extends Controller{
    public function beforeAction($action)
    {

        if($action->id =='index' || $action->id =='my-project')
        {
            if(!Yii::$app->user->can('administrator')){
                return $this->redirect(['/site/index']);
            }
            return parent::beforeAction($action);
        }
        return true;
    }
    //put your code here
    public function actionIndex(){      
      return $this->render('index');  
    }
    public function actionMyProject(){
        
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        if($search != ''){
            $projectAll = CNSettingProjectFunc::getProjectAll($search);
        }else{
            $projectAll = CNSettingProjectFunc::getProjectAll();
        }
//        \appxq\sdii\utils\VarDumper::dump($projectAll);
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $projectAll,
            'sort' => [
                'attributes' => ['useTemplate','rstat','projectname', 'projectacronym', 'projecticon','projurl','projdomain','pi_name','create_date'],
            ],
            'key' => 'id',
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        return $this->renderAjax('my-project',[
           'dataProvider'=>$dataProvider 
        ]);
    }
    public function actionClone(){
        $dataid = isset($_POST['id']) ? $_POST['id'] : '';
        $projectName = isset($_POST['projname']) ? $_POST['projname'] : '';
        
        $myproject= CNSettingProjectFunc::MyProjectByidNoUser($dataid);    //get zdata_create_project    
        
        /**
         * 
         */
        $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        if(!empty($myproject)){
            $columns=$myproject[0];            
            $dbdynamic = CNSettingProjectFunc::getDynamicDbByDataId($columns['id']); //get dynamic db
            
            if(!empty($dbdynamic)){
                $columns['id']=$id;
                $dbdynamic2 = $dbdynamic;
                unset($dbdynamic2['id']); 
                $dbdynamic2['url']="{$columns['projectacronym']}_{$columns['id']}.{$columns['projdomain']}";
                $dbdynamic2['data_id'] = $columns['id'];
                $defaultDb = isset(\Yii::$app->params['default_db']) ? \Yii::$app->params['default_db'] : 'ncrc_';
                $dbdynamic2['config_db']= "{$defaultDb}".$columns['projectacronym'].$id;
                $dbdynamic2['dbname']= $dbdynamic2['config_db'];
                $dbdynamic2['project_template'] = $dbdynamic['config_db'];
                $dbdynamic2['user_create'] = \cpn\chanpan\classes\CNUser::getUserId();
                \common\modules\user\classes\CNDynamicDb::saveByDefaultTable($dbdynamic2); //save dynamic db
                
//                print_r($dbdynamic2);exit();
                if(!empty($projectName)){
                    $columns['projectname']=$projectName;
                }
                $columns['user_create']= \cpn\chanpan\classes\CNUser::getUserId();
                $columns['user_update']= \cpn\chanpan\classes\CNUser::getUserId();                
                $columns['ptid']=$id;
                $columns['projectacronym']="{$columns['projectacronym']}_{$columns['id']}";
                $columns['target']=$id;
                $columns['projurl'] .= '_'.$columns['id'];
                $columns['useTemplate'] = $dbdynamic['config_db'];
                $create = CNSettingProjectFunc::create($columns); //save zdata_create_project
                if($create){
                    return \backend\modules\manageproject\classes\CNMessage::getSuccessObj('success', $columns);
                }else{
                    return \backend\modules\manageproject\classes\CNMessage::getError("error");
                }
            }
        }
        
    }
    
   
}
