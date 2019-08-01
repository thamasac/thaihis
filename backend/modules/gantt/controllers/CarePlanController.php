<?php

namespace backend\modules\gantt\controllers;

use yii\web\Controller;
use backend\modules\gantt\models\InvProject;
use Yii;
class DefaultController extends Controller
{
    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }    
    public function actionIndex()
    {
        $module_id = Yii::$app->request->get('module_id');
        return $this->renderAjax('index',[
            'module_id'=>$module_id,
        ]);
    }
   
    
    public function actionViewProcedure(){
        return $this->renderAjax('_view-procedure',[
        ]);
    }
    
    public function actionViewProcess(){
        
    }
}
