<?php

namespace backend\modules\gantt\controllers;

use yii\web\Controller;
use backend\modules\gantt\models\InvProject;
use Yii;
use appxq\sdii\helpers\SDHtml;
use yii\web\Response;
use DateTime;
use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\helpers\Json;
use cpn\chanpan\classes\CNUser;
use backend\modules\gantt\classes\GanttQuery;

class PmsWorkbrenchController extends Controller {

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex() {
        $pms_widget_id = Yii::$app->request->get('pms_widget_id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $widget_id = Yii::$app->request->get('widget_id');
        $pmsWidget = SubjectManagementQuery::getWidgetById($pms_widget_id);
        $pmsOptions = \appxq\sdii\utils\SDUtility::string2Array($pmsWidget['options']);
        $project_ezf_id = isset($pmsOptions['project_ezf_id']) ? $pmsOptions['project_ezf_id'] : \backend\modules\gantt\Module::$formsId["maintask_form"];
        $activity_ezf_id = isset($pmsOptions['activity_ezf_id']) ? $pmsOptions['activity_ezf_id'] : \backend\modules\gantt\Module::$formsId["task_ezf_id"];
        $user_id = \Yii::$app->user->id;

        $project_form = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($project_ezf_id);
        $activity_form = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_ezf_id);
        
        $condition = " OR (INSTR(assign_users, '{$user_id}') )";
        $projectData = SubjectManagementQuery::GetTableData($project_form, " (project_sharing='1' AND create_date='$user_id') OR project_sharing='2' " . $condition);

        $dataMyTask = [];
        $moduleList = [];
        $userRole = CNUser::getUserRoles();
        $assignRole = "";
        $assignRole2 = "";
        $assignUser = ' INSTR(assign_user, "' . $user_id . '")';
        $assignUser2 = ' INSTR(respons_person, "' . $user_id . '")';
        if ($userRole) {
            foreach ($userRole as $key => $val) {
                $assignRole .= ' OR INSTR(assign_role, "' . $val['id'] . '") ';
                $assignRole2 .= ' OR INSTR(respon_role, "' . $val['id'] . '") ';
            }
        }
        if ($projectData) {
            foreach ($projectData as $value) {
                $condTask = "";
                $dataTask = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', " rstat NOT IN(0,3) AND target='{$value['id']}' AND priority=3 AND ( $assignUser $assignRole) AND (IFNULL(progress,0)<100) ");
                $activityData = SubjectManagementQuery::GetTableData($activity_form," target='{$value['id']}'  AND ( $assignUser2 $assignRole2) ");
                
                if ($dataTask) {
                    foreach ($dataTask as $valTask) {
                        $valTask['main_task_name'] = $value['project_name'];
                        $dataMyTask[] = $valTask;
                        $dataFinded = GanttQuery::findArraybyFieldName($activityData, $valTask['dataid'], 'id');
                        if($dataFinded){
                            if(isset($dataFinded['ezmodule_to']) && $dataFinded['ezmodule_to'] != '')
                                if(!GanttQuery::findArraybyFieldName($moduleList, $dataFinded['ezmodule_to'], 'ezmodule_to')) 
                                    $moduleList[] = $dataFinded['ezmodule_to'];
                        }
                    }
                }
            }
        }
        $dataModule = [];
        if(count($moduleList) > 0){
            $dataModule = Yii::$app->db->createCommand(
                    "SELECT * FROM ezmodule WHERE ezm_id IN (". join($moduleList, ',').")"
                    )->queryAll();
        }
        
//        $moduleProvider = new \yii\data\ArrayDataProvider([
//            'allModels' => $dataModule,
//            'sort' => [
//                'attributes' => ['ezm_name'],
//            ],
//            'pagination' => [
//                'pageSize' => 20,
//            ],
//        ]);

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $dataMyTask,
            'sort' => [
                'attributes' => ['task_name', 'start_date', 'end_date'],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->renderAjax('index', [
                    'project_ezf_id' => $project_ezf_id,
                    'reloadDiv' => $reloadDiv,
                    'pmsOptions' => $pmsOptions,
                    'dataProvider' => $dataProvider,
                    'dataModule'=>$dataModule,
        ]);
    }

    public function actionGridPmsTask() {
        
    }

}
