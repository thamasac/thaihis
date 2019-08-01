<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\controllers;
use backend\models\Eztours;
/**
 * Description of EztourController
 *
 * @author chanpan
 */
use Yii;
class EztourController extends \yii\web\Controller{
    //put your code here
    public function actionGetTour(){
        $widgetId = \Yii::$app->request->post('widget_id', '');
        $eztour = \backend\models\Eztours::find()->where(['widget_id' => $widgetId])->all();
        return $this->renderAjax('get-tour',['eztour'=>$eztour]);
    }
    public function actionClone(){ 
        $widgetId = \Yii::$app->request->post('widget_id', '');
        $optionsClone = [
            'element'=>'#demo',
            'title'=>'ปุ่มสำหรับสร้างโครงการ',
            'content'=>'ปุ่มสำหรับสร้างโครงการ',
            'placement'=> 'auto',
            'smartPlacement'=> true, 
        ];
        $model = new Eztours();
        $model->widget_id = $widgetId;
        $model->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        $model->element = $optionsClone['element'];
        $model->title = $optionsClone['element'];
        $model->content = $optionsClone['content'];
        $model->placement = $optionsClone['placement'];
        $model->smartPlacement = $optionsClone['smartPlacement'];
        if($model->save()){
            return \backend\modules\manageproject\classes\CNMessage::getSuccess('Success', $model);
        }else{
            return \backend\modules\manageproject\classes\CNMessage::getError('Error', $model->errors);
        }
        
        //return $this->renderAjax('clone', ['options'=>$optionsClone]);
    }
    public function actionGetForm(){
        $widgetId = \Yii::$app->request->post('widget_id', '');
        $model = Eztours::find()->where(['widget_id'=>$widgetId])->orderBy(['widget_id'=>SORT_DESC])->all();
        return $this->renderAjax('get-form', ['model'=>$model]);
    }
    public function actionUpdateTour(){
        $data = \Yii::$app->request->post('data', '');
        
        
        $model = Eztours::find()->where(['id'=>$data['id']])->one();
        $model[$data['name']] = $data['value'];
        if($model->save()){
            return \backend\modules\manageproject\classes\CNMessage::getSuccess('Success', $model);
        }else{
            return \backend\modules\manageproject\classes\CNMessage::getError('Error', $model->errors);
        }
        \appxq\sdii\utils\VarDumper::dump($model);
//        return $this->renderAjax('get-form', ['model'=>$model]);
    }
    
    public function actionDeleteTour(){
        $id = \Yii::$app->request->post('id', '');
        $model = Eztours::findOne($id);
        if($model->delete()){
            return \backend\modules\manageproject\classes\CNMessage::getSuccess('Success', $model);
        }else{
            return \backend\modules\manageproject\classes\CNMessage::getError('Error', $model->errors);
        }
        
    }
    
}
