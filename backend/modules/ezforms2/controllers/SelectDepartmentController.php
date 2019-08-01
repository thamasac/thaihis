<?php

namespace backend\modules\ezforms2\controllers;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class SelectDepartmentController extends Controller{
     
      
    public static function initDepartment($model, $modelFields) {
        $id = $model[$modelFields['ezf_field_name']];
        $str = '';
        if(isset($id) && !empty($id)){
            $sitecode = \Yii::$app->user->identity->profile->sitecode;
            
            $data = (new \yii\db\Query())
                ->select(['id', 'unit_name as text'])
                ->from("zdata_working_unit")
                ->where(['id'=>$id])
                ->one();
            
             
            $str = $data ? $data['text'] : '';
        }
        
        return $str;
    }
    public function actionGetDepartment($q = null, $id = null)
    {         
        //$sitecode = \Yii::$app->user->identity->profile->sitecode;      
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }        
        $data = (new \yii\db\Query())
                ->select(['id', 'unit_name as name'])
                ->from("zdata_working_unit")
                ->where('unit_name LIKE :q',[':q'=>"%$q%"])
                ->all();        
        $i = 0;

        foreach ($data as $value) {
            $out["results"][$i] = ['id' => $value['id'], 'text' => $value["name"]];
            $i++;
        }

        return $out;
    }
    
     
}
