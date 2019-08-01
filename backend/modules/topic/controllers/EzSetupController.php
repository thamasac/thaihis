<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\topic\controllers;
use Yii;
use backend\modules\topic\models\Ezsetup;
use yii\web\Controller;
class EzSetupController extends Controller{
    //put your code here
    public function actionIndex(){
        return $this->render('index');
    }
    public function actionGetEzSetup(){
        $model = Ezsetup::find()->orderBy(['forder'=>SORT_ASC])->all();
        $id = Yii::$app->request->get('id', '');
        return $this->renderAjax('get-ez-setup', ['model'=>$model, 'id'=>$id]);
    }
    public function actionUpdate(){
        $id = Yii::$app->request->post('id', '');
        $value = Yii::$app->request->post('value', '');
        
        $model = Ezsetup::findOne($id);
        $model->action_taken = $value;
        $model->status = $value;
        if($model->save()){
            return \cpn\chanpan\classes\CNResponse::getSuccess('Success');
        }else{
            return \cpn\chanpan\classes\CNResponse::getError("error", $model->errors);
        }
        //\appxq\sdii\utils\VarDumper::dump($_POST);
    }
    public function actionUpdateByParent(){
        $parent_id = Yii::$app->request->post('parent_id', '');
        $id = Yii::$app->request->post('id', '');
        $value = Yii::$app->request->post('value', '');
        $model = Ezsetup::find()->where(['parent_id'=>$parent_id])->andWhere('id <> :id',[':id'=>$id])->all();
        foreach($model as $k=>$v){
            if($v['status'] != ''){
                $v->action_taken = $value;
                $v->status = $value;
                $v->save();
            }
             
        }
        return \cpn\chanpan\classes\CNResponse::getSuccess('Success');
        //\appxq\sdii\utils\VarDumper::dump($_POST);
    }
    public function actionUpdateForm(){
        $id = Yii::$app->request->get('id', '');
        $widget_id = Yii::$app->request->get('widget_id', '');
        $model = Ezsetup::findOne($id);
       // \appxq\sdii\utils\VarDumper::dump($id);
        if($model->load(Yii::$app->request->post())){
            $steps = $_POST['Ezsetup']['steps'];
            $link = $_POST['Ezsetup']['link'];
            
            $model->steps = $steps;
            $model->link = $link;
            if($model->save()){
                return \cpn\chanpan\classes\CNResponse::getSuccess('Success');
            }else{
                return \cpn\chanpan\classes\CNResponse::getError("error", $model->errors);
            }
        }
        return $this->renderAjax('update-form', ['model'=>$model, 'widget_id'=>$widget_id]);
    }
}
