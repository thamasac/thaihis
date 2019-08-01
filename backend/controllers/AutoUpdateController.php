<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\controllers;

/**
 * Description of AutoUpdateController
 *
 * @author chanpan
 */
class AutoUpdateController extends \yii\web\Controller{
    //put your code here
    public function actionIndex(){
        $roles = \Yii::$app->authManager->getRolesByUser(\Yii::$app->user->id);
        $roles = array_keys($roles);
        if(in_array('admin', $roles)){
            \appxq\sdii\utils\VarDumper::dump('Admin');
        }
        return $this->render('index');
    }
    public function actionCreate(){
        $data = \Yii::$app->request->post('data');
        //\appxq\sdii\utils\VarDumper::dump(\Yii::$app->db->dsn);
        foreach($data as $d){
            $model = new \app\models\UpdateCommands();
            $model->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $model->sql_command = $d['sql_command'];
            $model->data_id = $d['id'];
            $model->create_by = \cpn\chanpan\classes\CNUser::getUserId();
            $model->create_date = date('Y-m-d H:i:s'); 
            try{
                \Yii::$app->db->createCommand($d['sql_command'])->execute();
                $model->rstat = 1;
            } catch (\yii\db\Exception $ex) {
                $model->rstat = 2;
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            }
            if(!$model->save()){
                return \backend\modules\manageproject\classes\CNMessage::getError("Error ".\appxq\sdii\utils\SDUtility::array2String($model->errors));
            }
        }
        return \backend\modules\manageproject\classes\CNMessage::getSuccess("Success");
        
    }
    public function actionGetCurrent(){
        $model = \app\models\UpdateCommands::find()->orderBy(['id'=>SORT_DESC])->one();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if($model){
            $model['id'] = "{$model['id']}";
            $model['data_id'] = "{$model['data_id']}";
            return \backend\modules\manageproject\classes\CNMessage::getSuccess("Success", $model);
        }else{
            return \backend\modules\manageproject\classes\CNMessage::getError("Error");
        }
    }
    
    
    ///
    
    public function actionGetSiteCode($q = '', $id = ''){
//        if(\Yii::$app->user->id == '1'){
//            \appxq\sdii\utils\VarDumper::dump(\Yii::$app->session['knsitecode']);
//        }
        try{
             \Yii::$app->db_main->createCommand("UPDATE {$dbName}.`profile` SET sitecode=:sitecode WHERE user_id = :user_id",[
                ':sitecode'=>\Yii::$app->session['knsitecode'],
                ':user_id'=> \Yii::$app->user->id    
            ])->execute();
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }
       
        try {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $out = ['results' => ['id' => '', 'text' => '']];
            if ($id == null) {
                $sql="
                    SELECT u.id, concat(p.`firstname`,' ',p.`lastname`) as name FROM user as u INNER JOIN profile as p ON u.id=p.id
                    WHERE concat(p.firstname,' ',p.lastname) LIKE :name OR u.email LIKE :email AND blocked_at is null AND confirmed_at is not null
                ";
                $params = [':name'=>"%{$q}%" , ":email"=>"%{$q}%"];
                $data_all = \Yii::$app->db->createCommand($sql,$params)->queryAll();
                
                $data_all = (new \yii\db\Query())->select('*')
                        ->from('all_hospital_thai')
                        ->where('code LIKE :code OR name LIKE :name',[
                            ':code'=>"%$q%",
                            ':name'=>"%$q%"
                        ])->limit(100)->all(); 
                $data = [];
                foreach ($data_all as $k => $c) {
                    $data[$k] = ['id' => $c['code'], 'text' => $c['name']];
                }
                $out['results'] = array_values($data);
            } else {
                $data_one = (new \yii\db\Query())->select('*')
                        ->from('all_hospital_thai')
                        ->where(['code'=>$id])->one(); 
                $out = ['id' => $data_one['code'], 'text' => $data_one['name']];
            }
            return $out;
        } catch (\yii\db\Exception $ex) {
            return false;
        }
    }
}
