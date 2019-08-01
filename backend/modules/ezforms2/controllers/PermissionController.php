<?php

namespace backend\modules\ezforms2\controllers;


use Yii;
class PermissionController extends \yii\web\Controller{
    
    public function actionIndex(){
        $value = isset($_GET["value"]) ? $_GET["value"] : '';
        $module_id = isset($_GET["module_id"]) ? $_GET["module_id"] : '';
  
        return $this->renderAjax("index",[
            'value'=>$value,
            'module_id'=>$module_id
        ]);
    }
    public function actionSavePermission(){
        $model = new \backend\modules\ezforms2\models\TbPermission();
        
        $model->user_id = '';
        $model->ezf_id = '';
        $model->module_id = '';
        $model->permission = '';
        $model->permission2 = '';
        $model->create_at = Date('Y-m-d H:i:s');
        
        $model->save();
    }
}
