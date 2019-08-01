<?php

namespace backend\modules\update_project\controllers;

use yii\web\Controller;

/**
 * Default controller for the `update_project` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        if(\Yii::$app->user->can('administrator') && \Yii::$app->user->id == 1){
          $dataProvider = $this->getLogUpdate();
          return $this->render('index', ['dataProvider'=>$dataProvider]);  
        }else{
            throw new \yii\web\NotFoundHttpException('You are not allowed to access this section.'); 
        }
        
    }
    public function getLogUpdate(){
        $data = \backend\modules\manageproject\classes\CNUpdateCommandFunc::getLogNotUpdateAll();
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $data,
            'sort' => [
                //'attributes' => ['message', 'user_id', 'status','date', 'sql_command'],
            ],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        return $dataProvider;
        
    }
    public function actionDeleteLog(){
        $id = \Yii::$app->request->post('id', '');
        $delete = \backend\modules\manageproject\classes\CNUpdateCommandFunc::deleteLogByid($id);
        if($delete){
            return \backend\modules\manageproject\classes\CNMessage::getSuccess("Success");
        }
    }

    public function actionRunCommand()
    {
        
        if(!empty($_POST)){
            try{
                $dbName = Yii::$app->params['model_dynamic']['config_db'];
                $sitecode = \common\modules\user\classes\CNSitecode::getSiteCodeCurrent();
                $data = (new \yii\db\Query())
                        ->select('*')
                        ->from('zdata_update_project')
                        ->where('rstat = 1 AND sitecode=:sitecode',[":sitecode"=>$sitecode])
                        ->all();
                //\appxq\sdii\utils\VarDumper::dump($data);
                foreach($data as $k=>$d){
                    \Yii::$app->db->createCommand($d['sql_command'])->execute();
                }
                return \backend\modules\manageproject\classes\CNMessage::getSuccess('Update success');
            } catch (\yii\db\Exception $ex) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
                return \backend\modules\manageproject\classes\CNMessage::getError($ex->getMessage());
            }
        }        
        
    }
    
    public function actionRunCommandOne()
    {
        if(!empty($_POST)){
            try{
                 $cmd = isset($_POST['cmd']) ? $_POST['cmd'] : '';
                \Yii::$app->db->createCommand($cmd)->execute();
                return \backend\modules\manageproject\classes\CNMessage::getSuccess('Update success');
                
            } catch (\yii\db\Exception $ex) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
                return \backend\modules\manageproject\classes\CNMessage::getError($ex->getMessage());
            }
        }        
        
    }
}
