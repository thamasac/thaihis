<?php

namespace backend\modules\gantt\controllers;

use yii\web\Controller;
use backend\modules\gantt\models\InvProject;
use Yii;
use backend\modules\subjects\models\VisitProcedure;
class DefaultController extends Controller
{
    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }    
    public function actionIndex()
    {
        $mid = Yii::$app->request->get('mid');
        $project = InvProject::findOne(['id'=>$mid]);
        return $this->renderAjax('index',[
            'project'   =>  $project,
        ]);
    }
    public function actionConnector()
    {
        $mid = $_GET['module_id'];
        $mid == '' ? $mid='1' : '';
        
        $visitModel = new VisitProcedure();
        $query = new \yii\db\Query();
        $result = $query->select('*')
                ->from($visitModel->tableName())
                ->where(['mid' => $mid])
                ->all();
        
        $data = [];
        foreach ($result as $key=>$value){
            $data[$key]['id'] = $value['node_id'];
            $data[$key]['start_date'] = $value['start_date'];
            $data[$key]['duration'] = $value['duration'];
            $data[$key]['text'] = $value['text'];
            $data[$key]['progress'] = $value['progress'];
            $data[$key]['sortorder'] = $value['sortorder'];
            $data[$key]['parent'] = $value['parent_id'];
            $data[$key]['open'] = $value['open_state'];

        }
        
        $data_all['data'] = $data;
        $data_all['collections']['links'] = [];
        return json_encode($data_all);
        //return $this->renderAjax('json');
    }
}
