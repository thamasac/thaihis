<?php

namespace backend\modules\subjects\controllers;

use yii\web\Controller;
use backend\modules\gantt\models\InvProject;
use Yii;
use appxq\sdii\helpers\SDHtml;
use yii\web\Response;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfQuery;

class ClinicalTrialController extends Controller {

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $module_id = Yii::$app->request->get('module_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');
        $maintab= Yii::$app->request->get('maintab');
        $subtab= Yii::$app->request->get('subtab');


        return $this->renderAjax('index', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'options' => $options,
                    'module_id'=>$module_id,
                    'maintab'=>$maintab,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'reloadDiv' => $reloadDiv,
                    'subtab'=>$subtab,
        ]);
    }
    
    public function actionClinicalTrialTab() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $module_id = Yii::$app->request->get('module_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');
        $view = Yii::$app->request->get('view');


        return $this->renderAjax($view, [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'reloadDiv' => $reloadDiv,
                    'module_id'=>$module_id,
        ]);
    }
   
}
