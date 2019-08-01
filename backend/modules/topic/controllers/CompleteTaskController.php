<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\topic\controllers;
use Yii;
/**
 * Description of CompleteTaskController
 *
 * @author chanpan
 */
class CompleteTaskController extends \yii\web\Controller{
    //put your code here
    public function actionIndex(){
        $widget_id = Yii::$app->request->get('id', '');
        $query = \backend\modules\topic\models\CompleteTask::find()
                ->where(['widget_id'=>$widget_id])
                ->andWhere('rstat not in(0,3)');
        
        if(!empty($_POST)){
            $widget_id = Yii::$app->request->post('id', '');
            $txtsearch = isset($_POST['txtsearch']) ? $_POST['txtsearch'] : '';
            $dropsearch =  isset($_POST['dropsearch']) ? $_POST['dropsearch'] : '';
            $query = $query->orWhere('((header_text LIKE :t1 OR content LIKE :t2) AND (status_complete LIKE :t3) AND widget_id = :t4)',[
                ':t1'=>"%{$txtsearch}%",
                ':t2'=>"%{$txtsearch}%",
                ':t3'=>"%{$dropsearch}%",
                ':t4'=>$widget_id
            ]);
            //\appxq\sdii\utils\VarDumper::dump($query->all());
            
            
        }
        $data = $query->orderBy(['forder'=>SORT_ASC])->all();
        
        $provider = new \yii\data\ArrayDataProvider([
            'allModels'=>$data,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $this->renderAjax('index', ['dataProvider'=>$provider, 'widget_id'=>$widget_id]);
    }
    public function actionCreate(){
        $ezf_id = '1542120564041895900';//Yii::$app->request->get('ezf_id', '');
        $widget_id = Yii::$app->request->get('id', '');
        $data['widget_id']=$widget_id;
        $data['header_text']=$widget_id;
        $data['content']=$widget_id;
        $data['rstat']=1;
        $data['forder'] = '1';
        $model = \backend\modules\patient\classes\PatientFunc::backgroundInsert($ezf_id, '', '', $data);
        if($model){
            return \cpn\chanpan\classes\CNResponse::getSuccess('Success', $model);
        }else{
            return \cpn\chanpan\classes\CNResponse::getError('Error');
        }
    }
    public function actionDetail(){
        $id = Yii::$app->request->get('id', '');
        $data = \backend\modules\topic\models\CompleteTask::find()
                ->where(['id'=>$id])
                ->andWhere('rstat not in(0,3)')->one();
        return $this->renderAjax('detail', ['data'=>$data]);
    }
    public function actionSaveForder() {
        $data = Yii::$app->request->post('data' , '');
        $data = explode(',', $data);
       
        $defaultOrder = 10; 
        foreach($data as $id){
            $model = \backend\modules\topic\models\CompleteTask::findOne($id);  
            $model->forder = $defaultOrder;
            $defaultOrder += 10;
            if(!$model->save()){
//                return \cpn\chanpan\classes\CNResponse::getError('erro', $model->errors);
//                \appxq\sdii\utils\VarDumper::dump($model->errors);
            }
         }
         return \cpn\chanpan\classes\CNResponse::getSuccess("success");
    }  
    public function actionDelete() {
        $id = Yii::$app->request->post('id' , '');
        $model = \backend\modules\topic\models\CompleteTask::findOne($id);
        $model->rstat = 3;
        if($model->save()){
            return \cpn\chanpan\classes\CNResponse::getSuccess('Complete');
        }else{
            return \cpn\chanpan\classes\CNResponse::getError('Error');
        }
        
    }
    public function actionDone() {
        $id = Yii::$app->request->post('id' , '');
        $status = Yii::$app->request->post('status' , ''); 
        $model = \backend\modules\topic\models\CompleteTask::findOne($id);
        $model->status_complete = "{$status}";
        if($model->save()){
            return \cpn\chanpan\classes\CNResponse::getSuccess('Complete');
        }else{
            return \cpn\chanpan\classes\CNResponse::getError('Error');
        }
        
    }
}
