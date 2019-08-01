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
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;
use backend\modules\subjects\models\VisitProcedure;
use backend\modules\gantt\Module;
use backend\modules\ezforms2\classes\EzfQuery;
use appxq\sdii\utils\VarDumper;
use appxq\sdii\utils\SDUtility;

class GanttController extends Controller {

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex() {
        $module_id = Yii::$app->request->get('module_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $schedule_id = Yii::$app->request->get('schedule_widget_id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $skin_name = Yii::$app->request->get('skin_name');
        $scale_unit = Yii::$app->request->get('scale_unit');
        $scale_step = Yii::$app->request->get('scale_step');

        return $this->renderAjax('index', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'skin_name' => $skin_name,
                    'reloadDiv' => $reloadDiv,
                    'scale_unit' => $scale_unit,
                    'scale_step' => $scale_step,
                    'module_id' => $module_id
        ]);
    }

    public function actionGanttSchedule() {
        $widget_id = Yii::$app->request->get('widget_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $skin_name = Yii::$app->request->get('skinn_ame');
        $group_id = Yii::$app->request->get('group_id');
        $unit = Yii::$app->request->get('unit');
        $step = Yii::$app->request->get('step');
        $module_id = Yii::$app->request->get('module_id');

        return $this->renderAjax('gantt-schedule', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'skin_name' => $skin_name,
                    'reloadDiv' => $reloadDiv,
                    'group_id' => $group_id,
                    'unit' => $unit,
                    'step' => $step,
                    'module_id' => $module_id
        ]);
    }

    public function actionGanttMainTask() {
        $widget_id = Yii::$app->request->get('widget_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $module_id = Yii::$app->request->get('module_id');
        $check_type = Yii::$app->request->get('check_type');
        $project_ezf_id = Yii::$app->request->get('project_ezf_id');
        $cate_ezf_id = Yii::$app->request->get('cate_ezf_id');
        $activity_ezf_id = Yii::$app->request->get('activity_ezf_id');
        $response_ezf_id = Yii::$app->request->get('response_ezf_id');
        $start_date = Yii::$app->request->get('start_date');
        $finish_date = Yii::$app->request->get('finish_date');
        $progress = Yii::$app->request->get('progress');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $project_name = Yii::$app->request->get('project_name');
        $cate_name = Yii::$app->request->get('cate_name');
        $task_name = Yii::$app->request->get('task_name');
        $skin_name = Yii::$app->request->get('skin_name');
        $other_ezforms = Yii::$app->request->get('other_ezforms');
        $calendar_widget_id = Yii::$app->request->get('calendar_widget_id');
        $schedule_widget_id = Yii::$app->request->get('schedule_widget_id');
        $pms_tab = Yii::$app->request->get('pms_tab');
        $pms_type = Yii::$app->request->get('pms_type');
        $target = Yii::$app->request->get('dataid');
        $now_date = isset($_GET ['now_date']) ? $_GET ['now_date'] : date('Y-m-d');
        $search = Yii::$app->request->get('search');


        $projectForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($project_ezf_id);
        $taskForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_ezf_id);
        $calenOption = null;
        if ($calendar_widget_id) {
            $calenWidget = SubjectManagementQuery::getWidgetById($calendar_widget_id);
            $calenOption = $calenWidget ['options'];
            $calenOption = \appxq\sdii\utils\SDUtility::string2Array($calenOption);
        }

        $startDate = date('Y-m-d H:i:s');
        $dateNow = new \DateTime($startDate);
        $dateNow = $dateNow->modify('+ 1 day');
        $dueDate = $dateNow->format('Y-m-d H:i:s');

        if (isset($target) && $target != '') {
            $pms_check = SubjectManagementQuery::GetTableData($projectForm, [
                        'id' => $target
                            ], 'one');

            if (isset($pms_check) && $pms_check ['flag_status'] == '1') {
                $_COOKIE ['project_id'] = $target;
                $modelCate = \backend\modules\patient\classes\PatientFunc::backgroundInsert($cate_ezf_id, null, $target, [
                            'cate_name' => 'New Sub-task name'
                ]);

                $modelData = null;
                $modelDataTask = null;
                $modelTarget = null;
                $modelCateTarget = null;
                $modelTaskTarget = null;
                $parentid = null;
                if (isset($modelCate ['data'])) {
                    $modelData = $modelCate ['data'];

                    $modelData ['ezf_id'] = $cate_ezf_id;
                    $modelData ['task_name'] = 'New Sub-task name';
                    $modelData ['priority'] = 2;
                    $modelData ['parent'] = $target;
                    $modelData ['open_node'] = 1;
                    $modelData ['order_node'] = GanttQuery::getNewOrderTask($target, 0);
                    $modelData ['task_type'] = 'sub-task';
                    $modelCateTarget = GanttQuery::pmsTaskTargetInsert($modelData);
                    $parentid = $modelCateTarget ['taskid'];
                }

                if (isset($modelCate ['data']) && $modelCate ['data']) {
                    $modelTask = \backend\modules\patient\classes\PatientFunc::backgroundInsert($activity_ezf_id, null, $target, [
                                'task_name' => 'New task item',
                                'category_id' => $modelData ['id'],
                                'start_date' => $startDate,
                                'finish_date' => $dueDate
                    ]);

                    if (isset($modelTask ['data'])) {
                        $dataid = isset($modelTask ['data'] ['id']) ? $modelTask ['data'] ['id'] : null;
                        $taskData = SubjectManagementQuery::GetTableData($taskForm, [
                                    'id' => $dataid
                                        ], 'one');
                        $modelDataTask = $taskData;
                        $modelDataTask ['ezf_id'] = $activity_ezf_id;
                        $modelDataTask ['end_date'] = $taskData ['finish_date'];
                        $modelDataTask ['priority'] = 3;
                        $modelDataTask ['parent'] = $parentid;
                        $modelDataTask ['open_node'] = 1;
                        $modelDataTask ['order_node'] = GanttQuery::getNewOrderTask($target, $parentid);
                        ;
                        $modelDataTask ['task_type'] = 'task';
                        $modelTaskTarget = GanttQuery::pmsTaskTargetInsert($modelDataTask);
                    }
                }
            }
        }
        if ($pms_tab == null) {
            $pms_tab = 1;
        }

        $user_id = Yii::$app->user->id;
        $user_role = \cpn\chanpan\classes\CNUser::getUserRoles();
        $manageRole = "";
        if ($user_role) {
            foreach ($user_role as $key => $val) {
                $manageRole .= ' OR INSTR(manage_roles, "' . $val ['id'] . '") > 0 ';
            }
        }
        $assignUser = ' OR (project_sharing=3 AND INSTR(assign_users, "' . $user_id . '") > 0 )';
        $manageUser = ' OR INSTR(manage_users, "' . $user_id . '") > 0 ';
        if ($search && $search != '') {
            $search = " AND project_name LIKE '%{$search}%'";
        }

        $projectData = SubjectManagementQuery::GetTableData($projectForm, "pms_type={$pms_tab} AND (user_create='{$user_id}' OR project_sharing='2' {$assignUser} {$manageUser} ) $search", 'all', null, ['column' => 'update_date', 'order' => 'desc']);

        $progress = null;
        $task_amt = null;
        $milestone_amt = null;
        $fms_amt = null;
        $shopping_amt = null;
        $approve_amt = null;
        $complete_amt = null;

        if ($projectData) {
            foreach ($projectData as $key => $val) {
                $taskProgress = 0;
                $taskAmt = 0;
                $taskData = SubjectManagementQuery::GetTableDataNotEzform("pms_task_target", " priority<>2 AND rstat NOT IN(0,3) AND target='{$val['id']}'");
                foreach ($taskData as $key2 => $val2) {
                    if ($val2 ['priority'] == '2')
                        continue;
                    // $resData = SubjectManagementQuery::GetTableData($responseForm, ['target' => $val2['id']], 'one');
                    if (isset($val2 ['progress']) && $val2 ['progress'] > 0)
                        $taskProgress += $val2 ['progress'];
                    $taskAmt += 1;
                    if (!isset($task_amt [$val ['id']]))
                        $task_amt [$val ['id']] = 0;
                    if (!isset($milestone_amt [$val ['id']]))
                        $milestone_amt [$val ['id']] = 0;
                    if (!isset($fms_amt [$val ['id']]))
                        $fms_amt [$val ['id']] = 0;
                    if (!isset($shopping_amt [$val ['id']]))
                        $shopping_amt [$val ['id']] = 0;
                    if (!isset($complete_amt [$val ['id']]))
                        $complete_amt [$val ['id']] = 0;
                    if (!isset($approve_amt [$val ['id']]))
                        $approve_amt [$val ['id']] = 0;

                    $task_amt [$val ['id']] += 1;

                    if (isset($val2 ['task_type']) && $val2 ['task_type'] == 'milestone') {
                        $milestone_amt [$val ['id']] += 1;
                    } else if (isset($val2 ['task_financial']) && $val2 ['task_financial'] == '1') {
                        $fms_amt [$val ['id']] += 1;
                    }

                    if ($val2 ['assign_user'] == '' && $val2 ['request_type'] == '1' && ($val2 ['credit_points'] == '' && $val2 ['reward_points'] == '')) {
                        $shopping_amt[$val ['id']] += 1;
                    } else if ($val2 ['approved'] == '' && ($val2 ['credit_points'] != '' || $val2 ['reward_points'] != '')) {
                        $approve_amt[$val ['id']] += 1;
                    } else if ($val2 ['assign_user'] == '' && $val2 ['approved'] == '1' && $val2 ['request_type'] == '1') {
                        $shopping_amt[$val ['id']] += 1;
                    }

                    if (isset($val2 ['task_status']) && $val2 ['task_status'] == '3') {
                        $complete_amt[$val ['id']] += 1;
                    }
                }

                if ($taskProgress > 0 && $taskAmt > 0)
                    $projectData [$key] ['progress'] = $taskProgress / $taskAmt;
                else
                    $projectData [$key] ['progress'] = $taskProgress;
            }
        }

        $eventSources = [];
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $projectData,
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        $url_curr = \cpn\chanpan\classes\CNServerConfig::getDomainName();
        $url_curr_arr = explode('.', $url_curr);
        $url_curr_pre = isset($url_curr_arr [0]) ? $url_curr_arr [0] : '';
        $projectData = \backend\modules\subjects\classes\ReportQuery::getProjectData2($url_curr_pre);
        $pms_updateid = '';
        if (isset($projectData ['id'])) {
            $checkUpdate = GanttQuery::checkPmsUpdateLog($projectData ['id']);
            if (!$checkUpdate) {
                $pms_updateid = $projectData ['id'];
            }
        }
        return $this->renderAjax('gantt-maintask', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'skin_name' => $skin_name,
                    'check_type' => $check_type,
                    'project_ezf_id' => $project_ezf_id,
                    'cate_ezf_id' => $cate_ezf_id,
                    'activity_ezf_id' => $activity_ezf_id,
                    'response_ezf_id' => $response_ezf_id,
                    'start_date' => $start_date,
                    'finish_date' => $finish_date,
                    'progress' => $progress,
                    'cate_name' => $cate_name,
                    'task_name' => $task_name,
                    'reloadDiv' => $reloadDiv,
                    'module_id' => $module_id,
                    'project_name' => $project_name,
                    'target' => $target,
                    'pms_tab' => $pms_tab,
                    'pms_type' => $pms_type,
                    'dataProvider' => $dataProvider,
                    'eventSources' => $eventSources,
                    'calenOption' => $calenOption,
                    'now_date' => $now_date,
                    'calendar_widget_id' => $calendar_widget_id,
                    'task_amt' => $task_amt,
                    'milestone_amt' => $milestone_amt,
                    'fms_amt' => $fms_amt,
                    'shopping_amt' => $shopping_amt,
                    'complete_amt' => $complete_amt,
                    'approve_amt' => $approve_amt,
                    'schedule_widget_id' => $schedule_widget_id,
                    'other_ezforms' => $other_ezforms,
                    'pms_updateid' => $pms_updateid
        ]);
    }

    public function actionGanttProject() {
        $mid = Yii::$app->request->get('id');
        $widget_id = Yii::$app->request->get('widget_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $module_id = Yii::$app->request->get('module_id');
        $check_type = Yii::$app->request->get('check_type');
        $project_ezf_id = Yii::$app->request->get('project_ezf_id');
        $cate_ezf_id = Yii::$app->request->get('cate_ezf_id');
        $activity_ezf_id = Yii::$app->request->get('activity_ezf_id');
        $response_ezf_id = Yii::$app->request->get('response_ezf_id');
        $start_date = Yii::$app->request->get('start_date');
        $finish_date = Yii::$app->request->get('finish_date');
        $progress = Yii::$app->request->get('progress');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $project_name = Yii::$app->request->get('project_name');
        $cate_name = Yii::$app->request->get('cate_name');
        $task_name = Yii::$app->request->get('task_name');
        $skin_name = Yii::$app->request->get('skin_name');
        $other_ezforms = Yii::$app->request->get('other_ezforms');
        $project_id = Yii::$app->request->get('project_id');
        $scale_unit = Yii::$app->request->get('scale_unit');
        $scale_step = Yii::$app->request->get('scale_step');
        $scale_filter = Yii::$app->request->get('scale_filter');
        $calendar_widget_id = Yii::$app->request->get('calendar_widget_id');
        $schedule_widget_id = Yii::$app->request->get('schedule_widget_id');

        $tab = Yii::$app->request->get('tab');
        $now_date = isset($_GET ['now_date']) ? $_GET ['now_date'] : date('Y-m-d');
        $calenOption = null;
        if ($calendar_widget_id) {
            $calenWidget = SubjectManagementQuery::getWidgetById($calendar_widget_id);
            $calenOption = $calenWidget ['options'];
            $calenOption = \appxq\sdii\utils\SDUtility::string2Array($calenOption);
        }

        $userid = \Yii::$app->user->id;
        if ($project_id == '') {
            $project_id = $_SESSION ['project_id'];
        }
        if ($project_id != null)
            $_SESSION ['project_id'] = $project_id;

        $values = [];
        $values ['widget_id'] = $widget_id;
        $values ['schedule_id'] = $schedule_id;
        $values ['check_type'] = $check_type;
        $values ['project_ezf_id'] = $project_ezf_id;
        $values ['cate_ezf_id'] = $cate_ezf_id;
        $values ['activity_ezf_id'] = $activity_ezf_id;
        $values ['response_ezf_id'] = $response_ezf_id;
        $values ['start_date'] = $start_date;
        $values ['finish_date'] = $finish_date;
        $values ['skin_name'] = $skin_name;
        $values ['project_id'] = $project_id;
        $values ['reloadDiv'] = $reloadDiv;
        $eventSources = [];

        return $this->renderAjax('gantt-project', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'skin_name' => $skin_name,
                    'check_type' => $check_type,
                    'project_ezf_id' => $project_ezf_id,
                    'cate_ezf_id' => $cate_ezf_id,
                    'activity_ezf_id' => $activity_ezf_id,
                    'response_ezf_id' => $response_ezf_id,
                    'start_date' => $start_date,
                    'finish_date' => $finish_date,
                    'progress' => $progress,
                    'project_name' => $project_name,
                    'cate_name' => $cate_name,
                    'task_name' => $task_name,
                    'project_id' => $project_id,
                    'reloadDiv' => $reloadDiv,
                    'values' => $values,
                    'scale_unit' => $scale_unit,
                    'scale_step' => $scale_step,
                    'scale_filter' => $scale_filter,
                    'module_id' => $module_id,
                    'calenOption' => $calenOption,
                    'eventSources' => $eventSources,
                    'schedule_widget_id' => $schedule_widget_id,
                    'tab' => $tab,
                    'now_date' => $now_date,
                    'other_ezforms' => $other_ezforms
        ]);
    }

    public function actionGenGantt() {
        $values = Yii::$app->request->get('values');
        $widget_id = Yii::$app->request->get('widget_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $module_id = Yii::$app->request->get('module_id');
        $check_type = Yii::$app->request->get('check_type');
        $project_ezf_id = Yii::$app->request->get('project_ezf_id');
        $cate_ezf_id = Yii::$app->request->get('cate_ezf_id');
        $activity_ezf_id = Yii::$app->request->get('activity_ezf_id');
        $response_ezf_id = Yii::$app->request->get('response_ezf_id');
        $start_date = Yii::$app->request->get('start_date');
        $finish_date = Yii::$app->request->get('finish_date');
        $progress = Yii::$app->request->get('progress');
        $project_name = Yii::$app->request->get('project_name');
        $cate_name = Yii::$app->request->get('cate_name');
        $task_name = Yii::$app->request->get('task_name');
        $skin_name = Yii::$app->request->get('skin_name');
        $other_ezforms = Yii::$app->request->get('other_ezforms');
        $project_id = Yii::$app->request->get('project_id');
        $scale_unit = Yii::$app->request->get('scale_unit');
        $scale_step = Yii::$app->request->get('scale_step');
        $scale_filter = Yii::$app->request->get('scale_filter');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $tab = Yii::$app->request->get('tab');
        if (!isset($project_id) && $project_id == null)
            $project_id = $values ['project_id'];
        return $this->renderAjax('gen-gantt', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'skin_name' => $skin_name,
                    'project_ezf_id' => $project_ezf_id,
                    'cate_ezf_id' => $cate_ezf_id,
                    'activity_ezf_id' => $activity_ezf_id,
                    'response_ezf_id' => $response_ezf_id,
                    'start_date' => $start_date,
                    'finish_date' => $finish_date,
                    'progress' => $progress,
                    'project_name' => $project_name,
                    'cate_name' => $cate_name,
                    'task_name' => $task_name,
                    'project_id' => $project_id,
                    'reloadDiv' => $reloadDiv,
                    'values' => $values,
                    'scale_unit' => $scale_unit,
                    'scale_step' => $scale_step,
                    'scale_filter' => $scale_filter,
                    'module_id' => $module_id,
                    'tab' => $tab,
                    'other_ezforms' => $other_ezforms
        ]);
    }

    public function actionPatient() {
        $module_id = Yii::$app->request->get('module_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $data_id = Yii::$app->request->get('data_id');
        $data_ptid = Yii::$app->request->get('data_ptid');
        $group_id = Yii::$app->request->get('group_id');
        $scale_unit = Yii::$app->request->get('scale_unit');
        $scale_step = Yii::$app->request->get('scale_step');

        $skin_name = Yii::$app->request->get('skinName');
        return $this->renderAjax('patient', [
                    'module_id' => $module_id,
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'skin_name' => $skin_name,
                    'data_id' => $data_id,
                    'data_ptid' => $data_ptid,
                    'group_id' => $group_id,
                    'scale_unit' => $scale_unit,
                    'scale_step' => $scale_step
        ]);
    }

    public function actionGanttIndividual() {
        $mid = Yii::$app->request->get('id');
        $widget_id = Yii::$app->request->get('widget_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $data_id = Yii::$app->request->get('data_id');
        $data_ptid = Yii::$app->request->get('data_ptid');
        $subject_number = Yii::$app->request->get('subject_number');
        $group_id = Yii::$app->request->get('group_id');
        $module_id = Yii::$app->request->get('module_id');

        $skin_name = Yii::$app->request->get('skinName');
        return $this->renderAjax('gantt-individual', [
                    'module_id' => $module_id,
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'skin_name' => $skin_name,
                    'data_id' => $data_id,
                    'data_ptid' => $data_ptid,
                    'subject_number' => $subject_number,
                    'group_id' => $group_id
        ]);
    }

    public function actionGanttSubjectPatient() {
        $module_id = Yii::$app->request->get('module_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $data_id = Yii::$app->request->get('data_id');
        $data_ptid = Yii::$app->request->get('data_ptid');
        $group_id = Yii::$app->request->get('group_id');
        $scale_unit = Yii::$app->request->get('scale_unit');
        $scale_step = Yii::$app->request->get('scale_step');

        $skin_name = Yii::$app->request->get('skin_name');
        if (!$skin_name)
            $skin_name = "default";
        return $this->renderAjax('gantt-subject-patient', [
                    'module_id' => $module_id,
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'skin_name' => $skin_name,
                    'data_id' => $data_id,
                    'data_ptid' => $data_ptid,
                    'group_id' => $group_id,
                    'scale_unit' => $scale_unit,
                    'scale_step' => $scale_step
        ]);
    }

    public function actionGanttSubjects() {
        $mid = Yii::$app->request->get('id');
        $widget_id = Yii::$app->request->get('widget_id');
        $schedule_id = Yii::$app->request->get('schedule_widget_id');
        $data_id = Yii::$app->request->get('data_id');
        $data_ptid = Yii::$app->request->get('data_ptid');
        $subject_number = Yii::$app->request->get('subject_number');
        $group_id = Yii::$app->request->get('group_id');
        $module_id = Yii::$app->request->get('module_id');

        $skin_name = Yii::$app->request->get('skin_name');
        if (!$skin_name)
            $skin_name = "default";
        return $this->renderAjax('gantt-subjects', [
                    'module_id' => $module_id,
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'skin_name' => $skin_name,
                    'data_id' => $data_id,
                    'data_ptid' => $data_ptid,
                    'subject_number' => $subject_number,
                    'group_id' => $group_id
        ]);
    }

    public function actionConnector() {
        $mid = isset($_GET ['id']) ? $_GET ['id'] : '';
        $mid == '' ? $mid = '1' : '';
        $widget_id = Yii::$app->request->get('widget_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $group_id = Yii::$app->request->get('group_id');

        $schedule = new \backend\modules\ezmodules\models\EzmoduleWidget ();
        $query = $schedule->findOne([
            'widget_id' => $schedule_id
        ]);
        $options = \appxq\sdii\utils\SDUtility::string2Array($query->options);
        $data = [];

        $i = 0;
        $now = date('Y-m');
        $now = $now . '-01';
        $day = 1;

        $visitModel = new \backend\modules\subjects\models\VisitSchedule ();
        // $query = new \yii\db\Query();
        // $result = $query->select('*')
        // ->from($visitModel->tableName())
        // ->where(['schedule_id' => $schedule_id])
        // ->all();

        $result = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id, $group_id);

        $visitDateArr = [];
        $links = [];
        $status = 1;
        $i = 0;
        $start_date = date('Y-m-d');
        $end_date = null;
        $plan_date = null;
        foreach ($result as $key => $value) {
            $data [$i] ['id'] = $value ['id'];

            if ($value ['id'] == '11111') {
                $start_date = date('Y-m-d');
                $visitDateArr [$value ['id']] ['plan_date'] = $start_date;
            } elseif ($value ['id'] == '22222') {
                $date = new DateTime(date('Y-m-d'));
                $date->modify('+' . $value ['plan_date'] . ' day');
                $plan_date = $date->format('Y-m-d');
                $eardate = new DateTime($plan_date);
                $eardate->modify('+' . $value ['earliest_date'] . ' day');
                $start_date = $eardate->format('Y-m-d');

                $latestdate = new DateTime($plan_date);
                $latestdate->modify('+' . $value ['latest_date'] . ' day');
                $end_date = $latestdate->format('Y-m-d');

                $visitDateArr [$value ['id']] ['plan_date'] = $plan_date;
            } else {
                if (isset($value ['visit_cal_date']) && $value ['visit_cal_date'] != '') {
                    if (isset($visitDateArr [$value ['visit_cal_date']] ['plan_date'])) {
                        $plan_date = $visitDateArr [$value ['visit_cal_date']] ['plan_date'];
                        $value ['earliest_date'] = isset($value ['earliest_date']) && $value ['earliest_date'] != '' ? $value ['earliest_date'] : 0;
                        $value ['latest_date'] = isset($value ['latest_date']) && $value ['latest_date'] != '' ? $value ['latest_date'] : 0;
                        $date = new DateTime($plan_date);
                        $date->modify('+' . $value ['plan_date'] . ' day');
                        $plan_date = $date->format('Y-m-d');

                        if ($plan_date && $plan_date != null) {
                            $eardate = new DateTime($plan_date);
                            $value ['earliest_date'] = isset($value ['earliest_date']) ? $value ['earliest_date'] : 0;
                            $eardate->modify('+' . $value ['earliest_date'] . ' day');
                            $start_date = $eardate->format('Y-m-d');

                            $latestdate = new DateTime($plan_date);
                            $latestdate->modify('+' . $value ['latest_date'] . ' day');
                            $end_date = $latestdate->format('Y-m-d');
                        }
                        $visitDateArr [$value ['id']] ['plan_date'] = $plan_date;
                    }
                }
            }
            $earliest_date = (isset($value ['earliest_date']) && $value ['earliest_date'] > 0 ? abs($value ['earliest_date']) : 0);
            $latest_date = (isset($value ['latest_date']) && $value ['latest_date'] > 0 ? $value ['latest_date'] : 0);
            $duration = ( $earliest_date + $latest_date) + 1;

            $data [$i] ['widget_id'] = $widget_id;
            $data [$i] ['schedule_id'] = $schedule_id;
            $data [$i] ['start_date'] = $start_date;
            $data [$i] ['latest_date'] = $end_date;
            $data [$i] ['earliest_date'] = $start_date;
            $data [$i] ['plan_date'] = $plan_date;
            $data [$i] ['duration'] = $duration;
            $data [$i] ['text'] = $value ['visit_name'];

            $data [$i] ['progress'] = 0;
            $data [$i] ['progress1'] = $earliest_date > 0 ? $earliest_date / $duration : 0;
            $data [$i] ['progress2'] = 1 / $duration;
            $data [$i] ['progress3'] = $latest_date > 0 ? $latest_date / $duration : 0;
            $data [$i] ['status'] = $status;
            $data [$i] ['sortorder'] = '10';
            $data [$i] ['parent'] = isset($value ['visit_parent']) ? $value ['visit_parent'] : '0';
            $data [$i] ['open'] = 1;

            $links [$i] ['id'] = $i;
            $links [$i] ['source'] = $value ['id'];
            $links [$i] ['target'] = isset($value ['visit_cal_date']) ? $value ['visit_cal_date'] : null;
            $links [$i] ['type'] = '3';

            $i ++;
        }

        $data_all ['data'] = $data;
        $data_all ['links'] = $links;

        return json_encode($data_all);
    }

    public function actionConnectorIndividual() {
        $mid = Yii::$app->request->get('id');
        $mid == '' ? $mid = '1' : '';
        $widget_id = Yii::$app->request->get('widget_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $group_id = Yii::$app->request->get('group_id');
        $data_id = Yii::$app->request->get('data_id');
        $data_ptid = Yii::$app->request->get('data_ptid');

        $schedule = new \backend\modules\ezmodules\models\EzmoduleWidget ();
        $query = $schedule->findOne([
            'widget_id' => $schedule_id
        ]);
        $options = \appxq\sdii\utils\SDUtility::string2Array($query->options);
        $data = [];

        $i = 0;
        $now = date('Y-m');
        $now = $now . '-01';
        $actual_date = "";
        $day = '1';
        $datActual = [];

        $scheduleOptions = \appxq\sdii\utils\SDUtility::string2Array(SubjectManagementQuery::getWidgetById($schedule_id) ['options']);
        $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id, $group_id);

        foreach ($visitSchedule as $key => $value) { // Form Other Visit
            $status = 1;
            $start_date = null;
            $planDate = null;
            $earDate = null;
            $latestDate = "";
            $actual_date_visit = "";
            $data [$i] ['id'] = $value ['id'];

            if (isset($value ['plan_date']))
                $start_date = $value ['plan_date'];
            if ($start_date == '') {
                $start_date = date('Y-m-d');
            }
            if (isset($value ['ezf_id']) || $value ['ezf_id'] == '')
                $value ['ezf_id'] = $visitSchedule ['11111'] ['ezf_id'];

            $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($value ['ezf_id']);
            $dataQuery = [];
            if (isset($value ['visit_name_mapping']) || $value ['visit_name_mapping'] == '')
                $value ['visit_name_mapping'] = isset($visitSchedule ['11111'] ['visit_name_mapping']) ? $visitSchedule ['11111'] ['visit_name_mapping'] : 'visit_name';

            if (isset($value ['actual_date']) || $value ['actual_date'] == '')
                $value ['actual_date'] = isset($visitSchedule ['11111'] ['actual_date']) ? $visitSchedule ['11111'] ['actual_date'] : 'date_visit';

            if (is_object($ezform))
                $dataQuery = SubjectManagementQuery::GetTableData($ezform, [
                            'target' => $data_id,
                            $value ['visit_name_mapping'] => $value ['id']
                                ], 'one');

            if ($value ['id'] == '11111') {
                $actual_date_visit = $dataQuery [$value ['actual_date']];
                $datActual [$value ['id']] = $actual_date_visit;
            } else {
                if ($value ['id'] == '22222') {
                    $actual_date = $datActual ['11111'];
                } else {
                    $actual_date = isset($datActual [$value ['visit_cal_date']]) ? $datActual [$value ['visit_cal_date']] : '';
                }

                if (isset($actual_date) && isset($value ['plan_date'])) {
                    if (isset($dataQuery [$value ['actual_date']]))
                        $actual_date_visit = $dataQuery [$value ['actual_date']];

                    $datActual [$value ['id']] = $actual_date_visit;
                    $date = new DateTime($actual_date);
                    if (isset($value ['plan_date']) && $value ['plan_date'] != '') {
                        $date->modify('+ ' . $value ['plan_date'] . ' day');
                        $planDate = $date->format('Y-m-d');
                    }

                    $peDate = new DateTime($planDate);
                    $peDate->modify('- ' . abs($value ['earliest_date']) . ' day');
                    $earDate = $peDate->format('Y-m-d');
                    $start_date = $earDate;

                    if (isset($value ['latest_date']) && $value ['latest_date'] > 0) {
                        $plDate = new DateTime($planDate);
                        $plDate->modify('+ ' . $value ['latest_date'] . ' day');
                        $latestDate = $plDate->format('Y-m-d');
                    }

                    if (date($actual_date_visit) > date($latestDate) || (date('Y-m-d') > date($latestDate) && $actual_date_visit == '')) {
                        $status = 3;
                    } else if ((date('Y-m-d') >= date($planDate) && date('Y-m-d') <= date($latestDate)) && $actual_date_visit == '') {
                        $status = 2;
                    }
                }
            }
            if ($value ['id'] == '11111') {
                $start_date = $actual_date_visit;
            }

            $earliest_date = (isset($value ['earliest_date']) && $value ['earliest_date'] > 0 ? abs($value ['earliest_date']) : 0);
            $latest_date = (isset($value ['latest_date']) && $value ['latest_date'] > 0 ? $value ['latest_date'] : 0);
            $duration = ( $earliest_date + $latest_date) + 1;

            $data [$i] ['start_date'] = $start_date;
            $data [$i] ['actual_date'] = $actual_date_visit;
            $data [$i] ['plan_date'] = $planDate;
            $data [$i] ['latest_date'] = $latestDate;
            $data [$i] ['duration'] = $duration;
            $data [$i] ['text'] = $value ['visit_name'];
            $data [$i] ['visit_id'] = $value ['id'];

            $data [$i] ['progress'] = 0;
            $data [$i] ['progress1'] = $earliest_date > 0 ? abs($value ['earliest_date']) / $duration : 0;
            $data [$i] ['progress2'] = 1 / $duration;
            $data [$i] ['progress3'] = $latest_date > 0 ? $value ['latest_date'] / $duration : 0;

            $data [$i] ['status'] = $status;
            $data [$i] ['sortorder'] = '10';
            $data [$i] ['parent'] = 0;
            $data [$i] ['ezf_id'] = $scheduleOptions ['11111'] ['main_ezf_id'];
            $data [$i] ['target'] = $data_id;
            $data [$i] ['open'] = '1';

            $links [$i] ['id'] = $i;
            $links [$i] ['source'] = $value ['id'];
            $links [$i] ['target'] = isset($value ['visit_cal_date']) ? $value ['visit_cal_date'] : null;
            $links [$i] ['type'] = '3';

            $i ++;
        }

        $data_all ['data'] = $data;
        $data_all ['links'] = $links;
        return json_encode($data_all);
    }

    public function actionConnectorSubject() {
        $mid = isset($_GET ['id']) ? $_GET ['id'] : '';
        $mid == '' ? $mid = '1' : '';
        $widget_id = Yii::$app->request->get('widget_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $group_id = Yii::$app->request->get('group_id');

        $schedule = new \backend\modules\ezmodules\models\EzmoduleWidget ();
        $query = $schedule->findOne([
            'widget_id' => $schedule_id
        ]);
        $options = \appxq\sdii\utils\SDUtility::string2Array($query->options);
        $data = [];

        $i = 0;
        $now = date('Y-m');
        $now = $now . '-01';
        $day = 1;

        $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id, $group_id);

        $visitDateArr = [];
        $links = [];
        $status = 1;
        $start_date = date('Y-m-d');
        $end_date = null;
        $plan_date = null;
        $subjectForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($options ['subject_ezf_id']);
        $subdetailForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($options ['11111'] ['main_ezf_id']);

        $columns = array_merge($options ['subject_field_display'], $options ['11111'] ['main_field_display']);
        $columns [] = $subjectForm->ezf_table . ".id";
        $columns [] = $subdetailForm->ezf_table . ".date_visit";

        $subjectData = SubjectManagementQuery::GetTableJoinData2($subjectForm, $subdetailForm, $columns, [
                    'group_name' => $group_id,
                    'visit_name' => '22222'
        ]);

        $sub_count = 0;
        $visit_count = 0;
        $visit_link = [];
        foreach ($subjectData as $keySub => $valSub) {
            $data_id = $valSub ['id'];
            $data [$i] ['id'] = $valSub ['id'];
            $data [$i] ['widget_id'] = $widget_id;
            $data [$i] ['schedule_id'] = $schedule_id;
            $data [$i] ['start_date'] = null;
            $data [$i] ['latest_date'] = null;
            $data [$i] ['earliest_date'] = null;
            $data [$i] ['plan_date'] = null;
            $data [$i] ['duration'] = null;
            $data [$i] ['text'] = "<label>{$valSub['subject_number']}</label>";
            $data [$i] ['actual_date'] = null;
            $data [$i] ['progress'] = null;
            $data [$i] ['progress1'] = null;
            $data [$i] ['progress2'] = null;
            $data [$i] ['progress3'] = null;
            $data [$i] ['status'] = null;
            $data [$i] ['actual_date'] = null;
            $data [$i] ['sortorder'] = '1';
            $data [$i] ['parent'] = 0;
            $data [$i] ['open'] = 0;
            $data [$i] ['type_task'] = 'project';
            $data [$i] ['type'] = 'project';
            $data [$i] ['priority'] = '2';

            $links [$i] ['id'] = $i;
            $links [$i] ['source'] = $valSub ['id'];
            $links [$i] ['target'] = null;
            $links [$i] ['type'] = '3';

            $i ++;

            foreach ($visitSchedule as $key => $value) { // Form Other Visit
                $status = 1;
                $start_date = null;
                $planDate = null;
                $earDate = null;
                $latestDate = "";
                $actual_date_visit = "";

                if (isset($value ['plan_date']))
                    $start_date = $value ['plan_date'];
                if ($start_date == '') {
                    $start_date = date('Y-m-d');
                }
                if (isset($value ['ezf_id']) || $value ['ezf_id'] == '')
                    $value ['ezf_id'] = $visitSchedule ['11111'] ['ezf_id'];

                $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($value ['ezf_id']);
                $dataQuery = [];
                if (isset($value ['visit_name_mapping']) || $value ['visit_name_mapping'] == '')
                    $value ['visit_name_mapping'] = isset($visitSchedule ['11111'] ['visit_name_mapping']) ? $visitSchedule ['11111'] ['visit_name_mapping'] : 'visit_name';

                if (isset($value ['actual_date']) || $value ['actual_date'] == '')
                    $value ['actual_date'] = isset($visitSchedule ['11111'] ['actual_date']) ? $visitSchedule ['11111'] ['actual_date'] : 'date_visit';

                $dataQuery = SubjectManagementQuery::GetTableData($subdetailForm, [
                            'visit_name' => $value ['id'],
                            'target' => $valSub ['id']
                                ], 'one');

                if ($value ['id'] == '11111') {
                    $actual_date_visit = isset($dataQuery [$value ['actual_date']]) ? $dataQuery [$value ['actual_date']] : null;
                    $datActual [$value ['id']] = $actual_date_visit;
                } else {
                    if ($value ['id'] == '22222') {
                        $actual_date = $datActual ['11111'];
                    } else {
                        $actual_date = isset($datActual [$value ['visit_cal_date']]) ? $datActual [$value ['visit_cal_date']] : '';
                    }

                    if (isset($actual_date) && isset($value ['plan_date'])) {
                        if (isset($dataQuery [$value ['actual_date']]))
                            $actual_date_visit = $dataQuery [$value ['actual_date']];

                        $datActual [$value ['id']] = $actual_date_visit;
                        $date = new DateTime($actual_date);
                        if (isset($value ['plan_date']) && $value ['plan_date'] != '') {
                            $date->modify('+ ' . $value ['plan_date'] . ' day');
                            $planDate = $date->format('Y-m-d');
                        }

                        $peDate = new DateTime($planDate);
                        $peDate->modify('- ' . abs($value ['earliest_date']) . ' day');
                        $earDate = $peDate->format('Y-m-d');
                        $start_date = $earDate;

                        if (isset($value ['latest_date']) && $value ['latest_date'] > 0) {
                            $plDate = new DateTime($planDate);
                            $plDate->modify('+ ' . $value ['latest_date'] . ' day');
                            $latestDate = $plDate->format('Y-m-d');
                        }

                        if (date($actual_date_visit) > date($latestDate) || (date('Y-m-d') > date($latestDate) && $actual_date_visit == '')) {
                            $status = 3;
                        } else if ((date('Y-m-d') >= date($planDate) && date('Y-m-d') <= date($latestDate)) && $actual_date_visit == '') {
                            $status = 2;
                        }
                    }
                }
                if ($value ['id'] == '11111') {
                    $start_date = $actual_date_visit;
                }

                $visit_link [$value ['id']] = $value ['id'] . $sub_count;
                $data [$i] ['id'] = $value ['id'] . $sub_count;
                $duration = (abs($value ['earliest_date']) + $value ['latest_date']) + 1;
                $data [$i] ['start_date'] = $start_date;
                $data [$i] ['actual_date'] = $actual_date_visit;
                $data [$i] ['plan_date'] = $planDate;
                $data [$i] ['latest_date'] = $latestDate;
                $data [$i] ['duration'] = $duration;
                $data [$i] ['text'] = $value ['visit_name'];
                $data [$i] ['visit_id'] = $value ['id'];
                $data [$i] ['progress'] = 0;
                $data [$i] ['progress1'] = abs($value ['earliest_date']) / $duration;
                $data [$i] ['progress2'] = 1 / $duration;
                $data [$i] ['progress3'] = $value ['latest_date'] / $duration;
                $data [$i] ['status'] = $status;
                $data [$i] ['sortorder'] = '10';
                $data [$i] ['parent'] = $data_id;
                $data [$i] ['ezf_id'] = $options ['11111'] ['main_ezf_id'];
                $data [$i] ['target'] = $data_id;
                $data [$i] ['open'] = '1';
                $data [$i] ['type_task'] = 'task';
                $data [$i] ['type'] = 'task';
                $data [$i] ['priority'] = '3';

                $links [$i] ['id'] = $i;
                $links [$i] ['source'] = $visit_link [$value ['id']];
                $links [$i] ['target'] = isset($value ['visit_cal_date']) ? $visit_link [$value ['visit_cal_date']] : null;
                $links [$i] ['type'] = '3';

                $i ++;
            }
            $sub_count ++;
        }

        $data_all ['data'] = $data;
        $data_all ['links'] = $links;

        return json_encode($data_all);
    }

    public function actionConnectorProject() {
        $widget_id = Yii::$app->request->get('widget_id');
        $project_ezf_id = Yii::$app->request->get('project_ezf_id');
        $cate_ezf_id = Yii::$app->request->get('cate_ezf_id');
        $activity_ezf_id = Yii::$app->request->get('activity_ezf_id');
        $response_ezf_id = Yii::$app->request->get('response_ezf_id');
        $start_date = Yii::$app->request->get('start_date');
        $finish_date = Yii::$app->request->get('finish_date');
        $progress = Yii::$app->request->get('progress');
        $project_name = Yii::$app->request->get('project_name');
        $cate_name = Yii::$app->request->get('cate_name');
        $task_name = Yii::$app->request->get('task_name');
        $project_id = Yii::$app->request->get('project_id');
        $scale_filter = Yii::$app->request->get('scale_filter');
        $user_id = \Yii::$app->user->id;

        $projectEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($project_ezf_id);
        $cateEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($cate_ezf_id);
        $activityEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_ezf_id);
        $responseEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($response_ezf_id);
        $data = [];
        $projectStart = "";
        $projectFinish = "";
        $projectDuration = 0;
        $projectProgress = 0;
        $cateAmt = 0;
        $status = 1;
        $sort_order = 0;

        $i = 0;

        $cateData = SubjectManagementQuery::GetTableData($cateEzform, [
                    'project_id' => $project_id
                        ], null, null, [
                    'column' => 'sort_order'
        ]);
        $taskDataNoParent = SubjectManagementQuery::GetTableData($activityEzform, "target='{$project_id}' AND ( IFNULL(category_id,'')='' OR category_id='0' )");
        $resData = SubjectManagementQuery::GetTableData($responseEzform);
        $dataResponse;
        $cateOrder = 1;
        if ($taskDataNoParent && is_array($taskDataNoParent)) {

            foreach ($taskDataNoParent as $key2 => $value2) {

                $datetime1 = new DateTime($value2 [$start_date]);
                $datetime2 = new DateTime($value2 [$finish_date]);
                $interval = $datetime1->diff($datetime2);
                $diffDate = $interval->format('%a');

                $dataResponse = GanttQuery::findArraybyFieldName($resData, $value2 ['id'], 'target');

                $progress = isset($dataResponse ['progress']) ? $dataResponse ['progress'] : '0';
                $actual_date = isset($dataResponse ['actual_date']) ? $dataResponse ['actual_date'] : null;

                $finishLast = $finish_date;

                if ($progress == '100') {
                    $status = '4';
                } else {
                    if (date('Y-m-d', strtotime($value2 [$finish_date])) < date('Y-m-d') && $progress < '100') {
                        $status = '3';
                    } else if (date('Y-m-d', strtotime($value2 [$start_date])) <= date('Y-m-d')) {
                        $status = '2';
                    } else {
                        $status = '1';
                    }
                }

                $data [$i] ['id'] = $value2 ['id'];
                $data [$i] ['ezf_id'] = $activityEzform ['ezf_id'];
                $data [$i] ['start_date'] = $value2 [$start_date];
                $data [$i] ['finish_date'] = $value2 [$finish_date];
                $data [$i] ['actual_date'] = $actual_date;
                $data [$i] ['duration'] = $diffDate;
                $data [$i] ['text'] = $value2 [$task_name];
                $data [$i] ['progress'] = $progress / 100;
                $data [$i] ['progressPer'] = ($progress > 0) ? number_format($progress, 1) . " %" : "0.0%";
                $data [$i] ['type_task'] = $value2 ['sent_module_1'] == '3' ? 'milestone' : 'task';
                $data [$i] ['type'] = $value2 ['sent_module_2'] == '1' ? 'milestone' : 'task';
                $data [$i] ['status'] = $status;
                $data [$i] ['priority'] = '3';
                $data [$i] ['sortorder'] = '10';
                $data [$i] ['sent_module_1'] = isset($value2 ['sent_module_1']) ? $value2 ['sent_module_1'] : '0';
                $data [$i] ['sent_module_2'] = isset($value2 ['sent_module_1']) ? $value2 ['sent_module_2'] : '0';
                $data [$i] ['sent_module_3'] = isset($value2 ['sent_module_1']) ? $value2 ['sent_module_3'] : '0';
                $data [$i] ['sent_module_4'] = isset($value2 ['sent_module_1']) ? $value2 ['sent_module_4'] : '0';
                $field1 = \backend\modules\ezforms2\classes\EzfQuery::getFieldByName($activityEzform ['ezf_id'], 'sent_module_1');
                $field2 = \backend\modules\ezforms2\classes\EzfQuery::getFieldByName($activityEzform ['ezf_id'], 'sent_module_2');
                $field3 = \backend\modules\ezforms2\classes\EzfQuery::getFieldByName($activityEzform ['ezf_id'], 'sent_module_3');
                $field4 = \backend\modules\ezforms2\classes\EzfQuery::getFieldByName($activityEzform ['ezf_id'], 'sent_module_4');
                $data [$i] ['sent_module_txt1'] = isset($field1 ['ezf_field_label']) ? $field1 ['ezf_field_label'] : '';
                $data [$i] ['sent_module_txt2'] = isset($field2 ['ezf_field_label']) ? $field2 ['ezf_field_label'] : '';
                $data [$i] ['sent_module_txt3'] = isset($field3 ['ezf_field_label']) ? $field3 ['ezf_field_label'] : '';
                $data [$i] ['sent_module_txt4'] = isset($field4 ['ezf_field_label']) ? $field4 ['ezf_field_label'] : '';
                $data [$i] ['url_link'] = $value2 ['url_link'];
                $data [$i] ['response_id'] = isset($dataResponse ['id']) ? $dataResponse ['id'] : '';
                $data [$i] ['response_ezf_id'] = $responseEzform ['ezf_id'];
                $data [$i] ['segment'] = $value2 ['seg_performance'];
                $data [$i] ['target'] = $project_id;
                $data [$i] ['parent'] = 0;
                $data [$i] ['category'] = '';
                $data [$i] ['order'] = GanttQuery::setNewIndexTask($value2 ['id'], $activityEzform->ezf_table, $cateOrder, $data [$i] ['parent']);
                $data [$i] ['open'] = isset($value2 ['open_node']) && $value2 ['open_node'] == '0' ? false : true;
                $data [$i] ['user_create'] = $value2 ['user_create'];
                $i ++;
                $cateOrder ++;
            }
        }

        if ($cateData && is_array($cateData)) {

            foreach ($cateData as $key => $value) {
                $cateStart = "";
                $cateFinish = "";
                $cateDuration = 0;
                $cateInx = $i;
                $finishLast = "";
                $cateProgress = 0;

                $taskAmt = 0;
                if (isset($scale_filter) && $scale_filter != null) {
                    $userRole = CNUser::getUserRoles();
                    $assignRole = null;
                    $assignUser = ' OR INSTR(respons_person, "' . $user_id . '")';
                    if ($userRole) {
                        foreach ($userRole as $key => $val) {
                            $assignRole .= ' OR INSTR(manage_roles, "' . $val ['id'] . '") ';
                        }
                    }

                    if ($scale_filter == '1') {
                        $activityData = SubjectManagementQuery::GetDataTaskResponse($activityEzform, $responseEzform, 'project_id="' . $project_id . '" AND category_id="' . $value ['id'] . '" AND ( IFNULL(ez2.actual_date,"") <> "" AND ez2.progress >= 100)', null, null, [
                                    'column' => 'sort_order'
                        ]);
                    } elseif ($scale_filter == '2') {
                        $activityData = SubjectManagementQuery::GetDataTaskResponse($activityEzform, $responseEzform, 'project_id="' . $project_id . '" AND category_id="' . $value ['id'] . '"  AND (ez2.actual_date IS NULL AND (ez2.progress < 100 OR ez2.progress IS NULL))', null, null, [
                                    'column' => 'sort_order'
                        ]);
                    } elseif ($scale_filter == '3') {
                        $activityData = SubjectManagementQuery::GetTableData($activityEzform, 'project_id="' . $project_id . '" AND category_id="' . $value ['id'] . '" AND (user_update="' . $user_id . '" ' . $assignUser . $assignRole . ')', null, null, [
                                    'column' => 'sort_order'
                        ]);
                    } else {
                        $activityData = SubjectManagementQuery::GetTableData($activityEzform, 'project_id="' . $project_id . '" AND category_id="' . $value ['id'] . '"  ', null, null, [
                                    'column' => 'sort_order'
                        ]);
                    }
                } else {
                    $activityData = SubjectManagementQuery::GetTableData($activityEzform, [
                                'project_id' => $project_id,
                                'category_id' => $value ['id']
                                    ], null, null, [
                                'column' => 'sort_order'
                    ]);
                }

                $data [$i] ['id'] = $value ['id'];
                $data [$i] ['ezf_id'] = $cateEzform ['ezf_id'];
                $data [$i] ['start_date'] = '';
                $data [$i] ['finish_date'] = '';
                $data [$i] ['actual_date'] = '';
                $data [$i] ['duration'] = 0;
                $data [$i] ['text'] = $value [$cate_name];
                $data [$i] ['progress'] = '0';
                $data [$i] ['progressPer'] = '0';
                $data [$i] ['type'] = 'project';
                $data [$i] ['type_task'] = 'category';

                $data [$i] ['status'] = '1';
                $data [$i] ['priority'] = '2';
                $data [$i] ['sortorder'] = '0';
                $data [$i] ['sent_module_1'] = '0';
                $data [$i] ['sent_module_2'] = '0';
                $data [$i] ['sent_module_3'] = '0';
                $data [$i] ['sent_module_4'] = '0';
                $data [$i] ['sent_module_txt1'] = '';
                $data [$i] ['sent_module_txt2'] = '';
                $data [$i] ['sent_module_txt3'] = '';
                $data [$i] ['sent_module_txt4'] = '';
                $data [$i] ['url_link'] = '';
                $data [$i] ['response_id'] = '';
                $data [$i] ['response_ezf_id'] = '';
                $data [$i] ['segment'] = '';
                $data [$i] ['target'] = $project_id;
                $data [$i] ['parent'] = 0; // isset($value['parent']) && $value['parent'] != '' ? $value['parent'] : $project_id;
                $data [$i] ['category'] = $value ['id'];
                $data [$i] ['order'] = GanttQuery::setNewIndexTask($value ['id'], $cateEzform->ezf_table, $cateOrder, $data [$i] ['parent']);
                $data [$i] ['open'] = isset($value ['open_node']) && $value ['open_node'] == '0' ? false : true;
                $data [$i] ['user_create'] = $value ['user_create'];

                $i ++;
                if ($activityData && is_array($activityData)) {
                    $taskOrder = 1;
                    foreach ($activityData as $key2 => $value2) {

                        if ($projectStart > $value2 [$start_date] && $value2 [$start_date] != '') {
                            $projectStart = $value2 [$start_date];
                        } else if ($projectStart == '') {
                            $projectStart = $value2 [$start_date];
                        }

                        if ($projectFinish < $value2 [$finish_date]) {
                            $projectFinish = $value2 [$finish_date];
                        }

                        if (date($cateStart) > date($value2 [$start_date]) && $value2 [$start_date] != '') {
                            $cateStart = $value2 [$start_date];
                        } else if ($cateStart == '') {
                            $cateStart = $value2 [$start_date];
                        }

                        if ($cateFinish < $value2 [$finish_date])
                            $cateFinish = $value2 [$finish_date];

                        $dataResponse = GanttQuery::findArraybyFieldName($resData, $value2 ['id'], 'target');

                        $datetime1 = new DateTime($value2 [$start_date]);
                        $datetime2 = new DateTime($value2 [$finish_date]);
                        $interval = $datetime1->diff($datetime2);
                        $diffDate = $interval->format('%a');

                        $progress = isset($dataResponse ['progress']) ? $dataResponse ['progress'] : '0';
                        $actual_date = isset($dataResponse ['actual_date']) ? $dataResponse ['actual_date'] : null;

                        $finishLast = $finish_date;
                        $cateProgress += $progress;

                        if ($progress == '100') {
                            $status = '4';
                        } else {
                            if (date('Y-m-d', strtotime($value2 [$finish_date])) < date('Y-m-d') && $progress < '100') {
                                $status = '3';
                            } else if (\Yii::$app->user->id == $value2 ['respons_person']) {
                                $status = '2';
                            } else {
                                $status = '1';
                            }
                        }

                        $data [$i] ['id'] = $value2 ['id'];
                        $data [$i] ['ezf_id'] = $activityEzform ['ezf_id'];
                        $data [$i] ['start_date'] = $value2 [$start_date];
                        $data [$i] ['finish_date'] = $value2 [$finish_date];
                        $data [$i] ['actual_date'] = $actual_date;
                        $data [$i] ['duration'] = $diffDate;
                        $data [$i] ['text'] = $value2 [$task_name];
                        $data [$i] ['progress'] = $progress / 100;
                        $data [$i] ['progressPer'] = ($progress > 0) ? number_format($progress, 1) . " %" : "0.0%";
                        $data [$i] ['type_task'] = $value2 ['sent_module_1'] == '3' ? 'milestone' : 'task';
                        $data [$i] ['type'] = $value2 ['sent_module_2'] == '1' ? 'milestone' : 'task';
                        $data [$i] ['status'] = $status;
                        $data [$i] ['priority'] = '3';
                        $data [$i] ['sortorder'] = '10';
                        $data [$i] ['sent_module_1'] = isset($value2 ['sent_module_1']) ? $value2 ['sent_module_1'] : '0';
                        $data [$i] ['sent_module_2'] = isset($value2 ['sent_module_1']) ? $value2 ['sent_module_2'] : '0';
                        $data [$i] ['sent_module_3'] = isset($value2 ['sent_module_1']) ? $value2 ['sent_module_3'] : '0';
                        $data [$i] ['sent_module_4'] = isset($value2 ['sent_module_1']) ? $value2 ['sent_module_4'] : '0';
                        $field1 = \backend\modules\ezforms2\classes\EzfQuery::getFieldByName($activityEzform ['ezf_id'], 'sent_module_1');
                        $field2 = \backend\modules\ezforms2\classes\EzfQuery::getFieldByName($activityEzform ['ezf_id'], 'sent_module_2');
                        $field3 = \backend\modules\ezforms2\classes\EzfQuery::getFieldByName($activityEzform ['ezf_id'], 'sent_module_3');
                        $field4 = \backend\modules\ezforms2\classes\EzfQuery::getFieldByName($activityEzform ['ezf_id'], 'sent_module_4');
                        $data [$i] ['sent_module_txt1'] = isset($field1 ['ezf_field_label']) ? $field1 ['ezf_field_label'] : '';
                        $data [$i] ['sent_module_txt2'] = isset($field2 ['ezf_field_label']) ? $field2 ['ezf_field_label'] : '';
                        $data [$i] ['sent_module_txt3'] = isset($field3 ['ezf_field_label']) ? $field3 ['ezf_field_label'] : '';
                        $data [$i] ['sent_module_txt4'] = isset($field4 ['ezf_field_label']) ? $field4 ['ezf_field_label'] : '';
                        $data [$i] ['url_link'] = $value2 ['url_link'];
                        $data [$i] ['response_id'] = isset($dataResponse ['id']) ? $dataResponse ['id'] : '';
                        $data [$i] ['response_ezf_id'] = $responseEzform ['ezf_id'];
                        $data [$i] ['segment'] = $value2 ['seg_performance'];
                        $data [$i] ['target'] = $project_id;
                        $data [$i] ['parent'] = isset($value2 ['parent']) && $value2 ['parent'] != '' ? $value2 ['parent'] : $value ['id'];
                        $data [$i] ['category'] = '';
                        $data [$i] ['order'] = GanttQuery::setNewIndexTask($value2 ['id'], $activityEzform->ezf_table, $taskOrder, $data [$i] ['parent']);
                        $data [$i] ['open'] = isset($value2 ['open_node']) && $value2 ['open_node'] == '0' ? false : true;
                        $data [$i] ['user_create'] = $value2 ['user_create'];
                        $i ++;
                        $taskAmt ++;
                        $taskOrder ++;
                    }
                }

                $datetime1 = new DateTime($cateStart);
                $datetime2 = new DateTime($cateFinish);
                $interval = $datetime1->diff($datetime2);
                $diffDate = $interval->format('%a');
                if ($cateDuration < $diffDate)
                    $cateDuration = $diffDate;

                if ($projectDuration < $cateDuration)
                    $projectDuration = $cateDuration;

                $cateAmt ++;
                $progreeCate = 0;

                if ($cateProgress > 0)
                    $progreeCate = ($cateProgress / $taskAmt);

                $data [$cateInx] ['start_date'] = $cateStart;
                $data [$cateInx] ['finish_date'] = $cateFinish;
                $data [$cateInx] ['duration'] = $diffDate;
                $data [$cateInx] ['progress'] = $progreeCate / 100;
                $data [$cateInx] ['progressPer'] = (isset($progreeCate) && $progreeCate > 0) ? number_format($progreeCate, 1) . " %" : "0.0%";
                $projectProgress += $progreeCate;
                $cateOrder ++;
            }
        }

        $progreesMain = 0;
        if ($projectProgress > 0)
            $progreesMain = ($projectProgress / $cateAmt);

        $linkModel = new \backend\modules\gantt\models\VisitLinks ();
        $queryLink = $linkModel->findAll([
            'widget_id' => $widget_id
        ]);

        $links = [];
        foreach ($queryLink as $key => $val) {
            $links [$key] ['id'] = $key;
            $links [$key] ['source'] = $val ['source'];
            $links [$key] ['target'] = $val ['target'];
            $links [$key] ['type'] = $val ['type'];
        }

        $data_all ['data'] = $data;
        $data_all ['links'] = $links;
        // \appxq\sdii\utils\VarDumper::dump($data_all);
        return json_encode($data_all);
    }

    public function actionConnectorPmsTarget() {
        $activity_ezf_id = Yii::$app->request->get('activity_ezf_id');
        $project_ezf_id = Yii::$app->request->get('project_ezf_id');
        $response_ezf_id = Yii::$app->request->get('response_ezf_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $start_date = Yii::$app->request->get('start_date');
        $finish_date = Yii::$app->request->get('finish_date');
        $progress = Yii::$app->request->get('progress');
        $project_name = Yii::$app->request->get('project_name');
        $cate_name = Yii::$app->request->get('cate_name');
        $task_name = Yii::$app->request->get('task_name');
        $project_id = Yii::$app->request->get('project_id');
        $scale_filter = Yii::$app->request->get('scale_filter');
        $user_filter = Yii::$app->request->get('user_filter');
        $schedule_id = Yii::$app->request->get('schedule_widget_id');

        $user_id = \Yii::$app->user->id;
        $finish_date = "end_date";

        $task_type_ezf_id = Module::$formsId ['task_item_type'];
        $task_item_form = EzfQuery::getEzformOne($task_type_ezf_id);
        $task_item_data = SubjectManagementQuery::GetTableData($task_item_form);

        $request_ezf_id = Module::$formsId ['request_task_form'];
        $request_task_form = EzfQuery::getEzformOne($request_ezf_id);

        if ($schedule_id) {
            $scheduleWidget = SubjectManagementQuery::getWidgetById($schedule_id);
            $scheduleOption = $scheduleWidget ['options'];
            $scheduleOption = \appxq\sdii\utils\SDUtility::string2Array($scheduleOption);
        }

        $mainEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($project_ezf_id);
        $activityEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_ezf_id);
        $responseEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($response_ezf_id);

        $data = [];
        $links = [];
        $cateAmt = 0;
        $status = 1;
        $sort_order = 0;

        $i = 0;

        $cateOrder = 1;

        $projectData = SubjectManagementQuery::GetTableData($mainEzform, [
                    'id' => $project_id
                        ], 'one');
        if (isset($scale_filter) && $scale_filter != null) {
            $userRole = GanttQuery::getRolesByUserId($user_id);
            $userRoleOther = GanttQuery::getRolesByUserId($user_filter);
            $assignRole = null;
            $assignRoleOther = null;
            $assignUser = ' INSTR(assign_user, "' . $user_id . '")';
            $assignUserOther = ' INSTR(assign_user, "' . $user_filter . '")';
            if ($userRole) {
                foreach ($userRole as $key => $val) {
                    $assignRole .= ' OR INSTR(assign_role, "' . $val ['id'] . '") ';
                }
            }

            if ($userRoleOther) {
                foreach ($userRoleOther as $key => $val) {
                    $assignRoleOther .= ' OR INSTR(assign_role, "' . $val ['id'] . '") ';
                }
            }

            if ($scale_filter == '1') {
                $taskPms = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', ' rstat NOT IN(0,3) AND target="' . $project_id . '" AND ( IFNULL(actual_date,"") <> "" AND progress >= 100 OR priority=2) ', null, null, [
                            'column' => 'order_node'
                ]);
            } elseif ($scale_filter == '2') {
                $taskPms = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', 'rstat NOT IN(0,3) AND target="' . $project_id . '"  AND (actual_date IS NULL AND (progress < 100 OR progress IS NULL OR priority=2))', null, null, [
                            'column' => 'order_node'
                ]);
            } elseif ($scale_filter == '3') {
                $taskPms = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', 'rstat NOT IN(0,3) AND target="' . $project_id . '"  AND ( ' . $assignUser . $assignRole . ' OR priority=2)', null, null, [
                            'column' => 'order_node'
                ]);
            } elseif ($scale_filter == '4') {
                $taskPms = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', 'rstat NOT IN(0,3) AND target="' . $project_id . '"  AND ( ' . $assignUserOther . $assignRoleOther . ' OR priority=2)', null, null, [
                            'column' => 'order_node'
                ]);
            } else if ($scale_filter == '5') {
                $taskPms = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', 'rstat NOT IN(0,3) AND target="' . $project_id . '"  AND IFNULL(assign_user,"")=""   ', null, null, [
                            'column' => 'order_node'
                ]);
            } else {
                $taskPms = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', 'rstat NOT IN(0,3) AND target="' . $project_id . '" ', null, null, [
                            'column' => 'order_node'
                ]);
            }
        } else {
            $taskPms = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', 'rstat NOT IN(0,3) AND target=' . $project_id, null, null, [
                        'column' => 'order_node'
            ]);
        }

        $data [$i] ['id'] = $project_id;
        $data [$i] ['ezf_id'] = $project_ezf_id;
        $data [$i] ['dataid'] = $projectData ['id'];
        $data [$i] ['start_date'] = null;
        $data [$i] ['end_date'] = null;
        $data [$i] ['text'] = $projectData ['project_name'];
        $data [$i] ['progress'] = 0;
        $data [$i] ['progressPer'] = "0.0%";
        $data [$i] ['type'] = 'project';
        $data [$i] ['status'] = 1;
        $data [$i] ['sub_type'] = null;
        $data [$i] ['priority'] = '1';
        $data [$i] ['sortorder'] = 0;
        $data [$i] ['target'] = $project_id;
        $data [$i] ['parent'] = 0;
        $data [$i] ['category'] = null;
        $data [$i] ['order'] = 0;
        $data [$i] ['open'] = true;
        $data [$i] ['credit_points'] = '';
        $data [$i] ['reward_points'] = '';

        $links [$i] ['id'] = $i;
        $links [$i] ['source'] = $project_id;
        $links [$i] ['target'] = null;
        $links [$i] ['type'] = '3';

        $mainProgress = 0;
        $i ++;
        $cateInx = $i;
        $subData = [];
        $taskAmt = 0;
        if ($taskPms && is_array($taskPms)) {

            $ezform_work = [];
            foreach ($taskPms as $key => $value) {
                $cateStart = "";
                $cateFinish = "";
                $cateDuration = 0;
                $finishLast = "";
                $cateProgress = 0;
                $taskOrder = 1;
                if ($cateStart == '') {
                    $cateStart = $value ['start_date'];
                }

                if (date('Y-m-d', strtotime($cateStart)) > date('Y-m-d', strtotime($value ['start_date'])) && $value ['start_date'] != '') {
                    $cateStart = $value ['start_date'];
                }

                if ($cateFinish == "")
                    $cateFinish = $value ['end_date'];
                if (date('Y-m-d', strtotime($cateFinish)) < date('Y-m-d', strtotime($value ['end_date'])))
                    $cateFinish = $value ['end_date'];

                $datetime1 = new DateTime($value ['start_date']);
                $datetime2 = new DateTime($value ['end_date']);
                $interval = $datetime1->diff($datetime2);
                $diffDate = $interval->format('%a');

                $progress = isset($value ['progress']) ? $value ['progress'] : '0';
                $actual_date = isset($value ['actual_date']) ? $value ['actual_date'] : null;

                $finishLast = $finish_date;

                if ($progress && $progress > 0)
                    $cateProgress += $progress;

                if ($value ['priority'] == "3") {
                    if ($progress == '100') {
                        $status = '4';
                    } else {
                        if (date('Y-m-d', strtotime($value ['end_date'])) < date('Y-m-d') && $progress < '100') {
                            $status = '3';
                        } else if (date('Y-m-d', strtotime($value ['start_date'])) <= date('Y-m-d') && $progress < '100') {
                            $status = '2';
                        } else {
                            $status = '1';
                        }
                    }
                } else {
                    $status = 0;
                }

                $taskData = SubjectManagementQuery::GetTableData($activityEzform, [
                            'id' => $value ['dataid']
                                ], 'one');
                $assign_user = "";
                $assign_avatar = [];
                $init_avatar = 0;
                $myassign = '0';
                $myassign_accept = '0';
                $assign_user_arr = [];
                $co_owner_user = \appxq\sdii\utils\SDUtility::string2Array($value ['co_owner']);
                $approver_user = \appxq\sdii\utils\SDUtility::string2Array($value ['approver']);
                $reviewer_user = \appxq\sdii\utils\SDUtility::string2Array($value ['reviewer']);
                if (isset($value ['assign_user'])) {
                    $assign_user_arr = \appxq\sdii\utils\SDUtility::string2Array($value ['assign_user']);
                    if (in_array($user_id, $assign_user_arr))
                        $myassign = '1';
                }
                if (isset($value ['assign_user_accept'])) {
                    $respons_person = \appxq\sdii\utils\SDUtility::string2Array($value ['assign_user_accept']);

                    if ($respons_person) {
                        if (in_array($user_id, $respons_person))
                            $myassign_accept = '1';
                        $sql = "SELECT firstname, lastname,avatar_base_url,avatar_path FROM profile WHERE user_id IN(" . join($respons_person, ',') . ") ";
                        $query = Yii::$app->db->createCommand($sql)->queryAll();
                        // \appxq\sdii\utils\VarDumper::dump($query);
                        foreach ($query as $val) {
                            if ($assign_user == "")
                                $assign_user = $val ['firstname'] . ' ' . $val ['lastname'];
                            else
                                $assign_user .= ', ' . $val ['firstname'] . ' ' . $val ['lastname'];

                            if ($init_avatar <= 4) {

                                $assign_avatar [$init_avatar] = $val ['avatar_base_url'] . '/' . $val ['avatar_path'];
                                // \appxq\sdii\utils\VarDumper::dump(Yii::getAlias('@storage/web/source/'.$val['avatar_path']));
                                if (!GanttQuery::isUrlExist(Yii::getAlias('@storage/web/source/' . $val ['avatar_path'])) || ($val ['avatar_base_url'] == null || $val ['avatar_path'] == null)) {
                                    $assign_avatar [$init_avatar] = Yii::getAlias('@storageUrl/images/nouser.png');
                                }

                                $init_avatar ++;
                            }
                        }
                    }
                    // \appxq\sdii\utils\VarDumper::dump($respons_person);
                }

                $assign_role_full = "";
                $assign_role_short = [];
                $init_role = 0;
                $respon_role = null;
                if (isset($value ['assign_role'])) {
                    // $respon_role = \appxq\sdii\utils\SDUtility::string2Array($taskData['respon_role']);
                    $respon_role = $value ['assign_role'];

                    // if(!is_array($respon_role) || count($respon_role)<=0){
                    //
                    // }

                    if ($respon_role) {
                        $whereRole = "";
                        $whereRole = $respon_role;
                        $sql = "SELECT role_name, role_detail FROM zdata_role WHERE role_name='{$whereRole}' ";
                        $query = Yii::$app->db->createCommand($sql)->queryAll();
                        // \appxq\sdii\utils\VarDumper::dump($query);
                        foreach ($query as $val) {
                            if ($assign_role_full == "")
                                $assign_role_full = $val ['role_detail'];
                            else
                                $assign_role_full .= ', ' . $val ['role_detail'];

                            $assign_role_short = $val ['role_name'];
                            // if ($init_role <= 4) {
                            // if ($assign_role_short == "")
                            // $assign_role_short = $val['role_name'] ;
                            // else
                            // $assign_role_short .= ', ' . $val['role_name'];
                            // $init_role++;
                            // }
                        }
                    }
                }

                if (isset($value ['ezform_work']) && $value ['ezform_work']) {
                    $ezform_work [$value ['id']] = $value ['ezform_work'];
                }

                $data [$i] ['id'] = $value ['id'];
                $data [$i] ['ezf_id'] = $value ['ezf_id'];
                $data [$i] ['dataid'] = $value ['dataid'];
                $data [$i] ['start_date'] = $value ['start_date'];
                $data [$i] ['end_date'] = $value ['end_date'];
                $data [$i] ['actual_date'] = $actual_date;
                $data [$i] ['text'] = isset($value ['task_name']) ? $value ['task_name'] : '';
                if ($progress && $progress > 0)
                    $data [$i] ['progress'] = $progress / 100;
                else
                    $data [$i] ['progress'] = 0;
                if ($value['priority'] == '2') {
                    $subData[$value['id']]['index'] = $i;
                }
                if ($value['priority'] == '3') {
                    if ($progress && $progress > 0) {
                        $mainProgress += $progress;
                        $cateProgress += $progress;
                    }
                    if (!isset($subData[$value['parent']]['progress']))
                        $subData[$value['parent']]['progress'] = 0;
                    if (!isset($subData[$value['parent']]['amt']))
                        $subData[$value['parent']]['amt'] = 0;

                    $subData[$value['parent']]['progress'] += $progress;
                    $subData[$value['parent']]['amt'] += 1;

                    $taskAmt ++;
                }

                $data [$i] ['progressPer'] = ($progress > 0) ? number_format($progress, 1) . " %" : "0.0%";
                $data [$i] ['type_task'] = isset($value ['task_type']) ? $value ['task_type'] : 'task';
                $data [$i] ['task_performance'] = isset($value ['task_performance']) ? $value ['task_performance'] : '0';
                $data [$i] ['task_financial'] = isset($value ['task_financial']) && $value ['task_financial'] == '1' ? $value ['task_financial'] : '0';
                $data [$i] ['type'] = isset($value ['task_type']) && $value ['task_type'] == "sub-task" ? "project" : $value ['task_type'];
                $data [$i] ['status'] = $status;
                $data [$i] ['sub_type'] = $value ['sub_type'];
                $data [$i] ['priority'] = $value ['priority'];
                $data [$i] ['sortorder'] = $value ['order_node'];
                $data [$i] ['sent_module_1'] = isset($value ['task_financial']) ? $value ['task_financial'] : '0';
                $data [$i] ['sent_module_2'] = isset($value ['task_type']) && $value ['task_type'] == 'milestone' ? '1' : '0';
                $data [$i] ['sent_module_txt1'] = 'Financial Management System';
                $data [$i] ['sent_module_txt2'] = 'Timeline Milestone';
                $data [$i] ['url_link'] = isset($value ['url_link']) ? $value ['url_link'] : null;
                $data [$i] ['response_id'] = isset($value ['response_id']) ? $value ['response_id'] : '';
                $data [$i] ['response_ezf_id'] = $responseEzform ['ezf_id'];
                $data [$i] ['segment'] = isset($value ['task_performance']) ? $value ['task_performance'] : null;
                $data [$i] ['target'] = $project_id;
                $data [$i] ['parent'] = isset($value ['parent']) && $value ['parent'] != '' && $value ['parent'] != '0' ? $value ['parent'] : $project_id;
                $data [$i] ['category'] = isset($value ['task_type']) && $value ['task_type'] == "sub-task" ? $value ['id'] : '';
                $data [$i] ['order'] = $value ['order_node'];
                $data [$i] ['request_type'] = $value ['request_type'];
                $data [$i] ['open'] = isset($value ['open_node']) && $value ['open_node'] == '0' ? false : true;
                $data [$i] ['user_create'] = $value ['user_create'];
                $data [$i] ['assign_user_state'] = is_array($assign_user_arr) && count($assign_user_arr) > 0 ? '1' : '0';
                $data [$i] ['assign_user'] = $assign_user;
                $data [$i] ['assign_avatar'] = $assign_avatar;
                $data [$i] ['assign_role_full'] = $assign_role_full;
                $data [$i] ['assign_role_short'] = $assign_role_short;

                $requestTask = SubjectManagementQuery::GetTableData('zdata_request_task', [
                            'target' => $value ['dataid'],
                            'user_create' => $user_id,
                            'status_request' => '1'
                                ], 'one');
                $data [$i] ['myrequest'] = $requestTask && is_array($requestTask) ? '1' : '0';
                $data [$i] ['myrequestid'] = $requestTask && is_array($requestTask) ? $requestTask ['id'] : null;
                $data [$i] ['myassign'] = $myassign;
                $data [$i] ['myassign_accept'] = $myassign_accept;
                $data [$i] ['approved'] = isset($value ['approved']) ? $value ['approved'] : '0';
                $data [$i] ['user_approve'] = isset($value ['user_approve']) ? $value ['user_approve'] : null;
                $data [$i] ['own_approved'] = isset($value ['own_approved']) ? $value ['own_approved'] : '0';
                $data [$i] ['user_own_approve'] = isset($value ['user_own_approve']) ? $value ['user_own_approve'] : null;
                $data [$i] ['credit_points'] = isset($value ['credit_points']) ? $value ['credit_points'] : '';
                $data [$i] ['reward_points'] = isset($value ['reward_points']) ? $value ['reward_points'] : '';
                $data [$i] ['task_item_type'] = isset($value ['task_item_type']) ? GanttQuery::findArraybyFieldName($task_item_data, $value ['task_item_type'], 'id') ['type_name'] : '';
                $data [$i] ['task_status'] = isset($value ['task_status']) ? $value ['task_status'] : '';
                $data [$i] ['request_new_amt'] = SubjectManagementQuery::GetTableDataCount($request_task_form, ['status_request' => '1', 'target' => $value ['dataid']]);
                $subTask = SubjectManagementQuery::GetTableData('pms_task_target', [
                            'id' => $data [$i] ['parent']
                                ], 'one');

                $data [$i] ['isreviewer'] = in_array($user_id, $reviewer_user) ? '1' : '0';
                $data [$i] ['isapprover'] = in_array($user_id, $approver_user) ? '1' : '0';
                $data [$i] ['isco_owner'] = in_array($user_id, $co_owner_user) ? '1' : '0';
                $data [$i] ['ezform_work'] = isset($subTask ['ezform_work']) ? base64_encode($subTask ['ezform_work']) : '';
                $data [$i] ['ezform_work_ops'] = isset($subTask ['ezform_work_ops']) ? base64_encode($subTask ['ezform_work_ops']) : '';
                $dataMain = SubjectManagementQuery::GetTableData($mainEzform, [
                            'id' => $value ['share_from']
                                ], 'one');
                $data [$i] ['share_from'] = isset($dataMain ['project_name']) ? $dataMain ['project_name'] : null;

                $links [$i] ['id'] = $i;
                $links [$i] ['source'] = $value ['id'];
                $links [$i] ['target'] = null;
                $links [$i] ['type'] = '3';
                $i ++;

                $taskOrder ++;

                if ($value ['sub_type'] == '2') { // / Task Procedure scheduler
                    $groudEzfid = isset($scheduleOption ['group_ezf_id']) ? $scheduleOption ['group_ezf_id'] : null;
                    $groupForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($groudEzfid);
                    $groupData = SubjectManagementQuery::GetTableData($groupForm);

                    if ($groupData) {
                        $group_id = null;
                        foreach ($groupData as $keyG => $valG) {
                            $group_id = $valG ['id'];
                            $data [$i] ['id'] = $valG ['id'];
                            $data [$i] ['widget_id'] = $widget_id;
                            $data [$i] ['schedule_id'] = $schedule_id;
                            $data [$i] ['start_date'] = null;
                            $data [$i] ['latest_date'] = null;
                            $data [$i] ['earliest_date'] = null;
                            $data [$i] ['plan_date'] = null;
                            $data [$i] ['duration'] = null;
                            $data [$i] ['text'] = "<label>{$valG['group_name']}</label>";
                            $data [$i] ['actual_date'] = null;
                            $data [$i] ['progress'] = null;
                            $data [$i] ['progress1'] = null;
                            $data [$i] ['progress2'] = null;
                            $data [$i] ['progress3'] = null;
                            $data [$i] ['status'] = null;
                            $data [$i] ['sub_type'] = '2';
                            $data [$i] ['actual_date'] = null;
                            $data [$i] ['sortorder'] = '1';
                            $data [$i] ['parent'] = $value ['id'];
                            $data [$i] ['open'] = 0;
                            $data [$i] ['type_task'] = 'project';
                            $data [$i] ['type'] = 'project';
                            $data [$i] ['priority'] = '3';
                            $data [$i] ['sub_type'] = '2';

                            $links [$i] ['id'] = $i;
                            $links [$i] ['source'] = $valG ['id'];
                            $links [$i] ['target'] = null;
                            $links [$i] ['type'] = '3';

                            $i ++;

                            $now = date('Y-m');
                            $now = $now . '-01';
                            $day = 1;

                            $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id, $group_id);

                            $visitDateArr = [];
                            $status = 1;
                            $start_date = date('Y-m-d');
                            $end_date = null;
                            $plan_date = null;
                            $subjectForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($scheduleOption ['subject_ezf_id']);
                            $subdetailForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($scheduleOption ['11111'] ['main_ezf_id']);

                            $columns = array_merge($scheduleOption ['subject_field_display'], $scheduleOption ['11111'] ['main_field_display']);
                            $columns [] = $subjectForm->ezf_table . ".id";
                            $columns [] = $subdetailForm->ezf_table . ".date_visit";

                            $subjectData = SubjectManagementQuery::GetTableJoinData2($subjectForm, $subdetailForm, $columns, [
                                        'group_name' => $group_id,
                                        'visit_name' => '22222'
                            ]);

                            $sub_count = 0;
                            $visit_count = 0;
                            $visit_link = [];
                            foreach ($subjectData as $keySub => $valSub) {
                                $data_id = $valSub ['id'];
                                $data [$i] ['id'] = $valSub ['id'];
                                $data [$i] ['widget_id'] = $widget_id;
                                $data [$i] ['schedule_id'] = $schedule_id;
                                $data [$i] ['start_date'] = null;
                                $data [$i] ['latest_date'] = null;
                                $data [$i] ['earliest_date'] = null;
                                $data [$i] ['plan_date'] = null;
                                $data [$i] ['duration'] = null;
                                $data [$i] ['text'] = "<label>{$valSub['subject_number']}</label>";
                                $data [$i] ['actual_date'] = null;
                                $data [$i] ['progress'] = null;
                                $data [$i] ['progress1'] = null;
                                $data [$i] ['progress2'] = null;
                                $data [$i] ['progress3'] = null;
                                $data [$i] ['status'] = null;
                                $data [$i] ['actual_date'] = null;
                                $data [$i] ['sortorder'] = '1';
                                $data [$i] ['parent'] = $group_id;
                                $data [$i] ['open'] = 0;
                                $data [$i] ['type_task'] = 'project';
                                $data [$i] ['type'] = 'project';
                                $data [$i] ['priority'] = '4';
                                $data [$i] ['sub_type'] = '2';

                                $links [$i] ['id'] = $i;
                                $links [$i] ['source'] = $valSub ['id'];
                                $links [$i] ['target'] = null;
                                $links [$i] ['type'] = '3';

                                $i ++;

                                foreach ($visitSchedule as $keyV => $valueV) { // Form Other Visit
                                    $status = 1;
                                    $start_date = null;
                                    $planDate = null;
                                    $earDate = null;
                                    $latestDate = "";
                                    $actual_date_visit = "";

                                    if (isset($valueV ['plan_date']))
                                        $start_date = $valueV ['plan_date'];
                                    if ($start_date == '') {
                                        $start_date = date('Y-m-d');
                                    }
                                    if (isset($valueV ['ezf_id']) || $valueV ['ezf_id'] == '')
                                        $valueV ['ezf_id'] = $visitSchedule ['11111'] ['ezf_id'];

                                    $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($valueV ['ezf_id']);
                                    $dataQuery = [];
                                    if (isset($valueV ['visit_name_mapping']) || $valueV ['visit_name_mapping'] == '')
                                        $valueV ['visit_name_mapping'] = isset($visitSchedule ['11111'] ['visit_name_mapping']) ? $visitSchedule ['11111'] ['visit_name_mapping'] : 'visit_name';

                                    if (isset($valueV ['actual_date']) || $valueV ['actual_date'] == '')
                                        $valueV ['actual_date'] = isset($visitSchedule ['11111'] ['actual_date']) ? $visitSchedule ['11111'] ['actual_date'] : 'date_visit';

                                    $dataQuery = SubjectManagementQuery::GetTableData($subdetailForm, [
                                                'visit_name' => $valueV ['id'],
                                                'target' => $valSub ['id']
                                                    ], 'one');
                                    $dataid_detail = $dataQuery ? $dataQuery ['id'] : '';
                                    if ($valueV ['id'] == '11111') {
                                        $actual_date_visit = isset($dataQuery [$valueV ['actual_date']]) ? $dataQuery [$valueV ['actual_date']] : null;
                                        $datActual [$valueV ['id']] = $actual_date_visit;
                                        if (date('Y-m-d') > date($latestDate) && $actual_date_visit == '') {
                                            $status = 3;
                                        } else if ((date('Y-m-d') >= date($earDate) && date('Y-m-d') <= date($latestDate)) && $actual_date_visit == '') {
                                            $status = 2;
                                        } else if (isset($actual_date_visit) && $actual_date_visit != '' && $dataid_detail != '') {
                                            $status = 4;
                                        }
                                    } else {
                                        if ($valueV ['id'] == '22222') {
                                            $actual_date = $datActual ['11111'];
                                        } else {
                                            $actual_date = isset($datActual [$valueV ['visit_cal_date']]) ? $datActual [$valueV ['visit_cal_date']] : '';
                                        }

                                        if (isset($actual_date) && isset($valueV ['plan_date'])) {
                                            if (isset($dataQuery [$valueV ['actual_date']]))
                                                $actual_date_visit = $dataQuery [$valueV ['actual_date']];

                                            $datActual [$valueV ['id']] = $actual_date_visit;
                                            $date = new DateTime($actual_date);
                                            if (isset($valueV ['plan_date']) && $valueV ['plan_date'] != '') {
                                                $date->modify('+ ' . $valueV ['plan_date'] . ' day');
                                                $planDate = $date->format('Y-m-d');
                                            }

                                            $peDate = new DateTime($planDate);
                                            $peDate->modify('- ' . abs($valueV ['earliest_date']) . ' day');
                                            $earDate = $peDate->format('Y-m-d');
                                            $start_date = $earDate;

                                            if (isset($valueV ['latest_date']) && $valueV ['latest_date'] > 0) {
                                                $plDate = new DateTime($planDate);
                                                $plDate->modify('+ ' . $valueV ['latest_date'] . ' day');
                                                $latestDate = $plDate->format('Y-m-d');
                                            }

                                            if (date('Y-m-d') > date($latestDate) && $actual_date_visit == '') {
                                                $status = 3;
                                            } else if ((date('Y-m-d') >= date($earDate) && date('Y-m-d') <= date($latestDate)) && $actual_date_visit == '') {
                                                $status = 2;
                                            } else if (isset($actual_date_visit) && $actual_date_visit != '' && $dataid_detail != '') {
                                                $status = 4;
                                            }
                                        }
                                    }
                                    if ($valueV ['id'] == '11111') {
                                        $start_date = $actual_date_visit;
                                    }

                                    $visit_link [$valueV ['id']] = $valueV ['id'] . $data_id;
                                    $data [$i] ['id'] = $valueV ['id'] . $data_id;
                                    $duration = isset($valueV['earliest_date']) && $valueV['earliest_date'] != '' ? ((abs($valueV ['earliest_date']) + $valueV ['latest_date']) + 1) : 0;
                                    $data [$i] ['start_date'] = $start_date;
                                    $data [$i] ['actual_date'] = $actual_date_visit;
                                    $data [$i] ['plan_date'] = $planDate;
                                    $data [$i] ['latest_date'] = $latestDate;
                                    $data [$i] ['duration'] = $duration;
                                    $data [$i] ['text'] = $valueV ['visit_name'];
                                    $data [$i] ['visit_id'] = $valueV ['id'];
                                    $data [$i] ['progress'] = 0;
                                    $data [$i] ['progress1'] = $duration != 0 ? abs($valueV ['earliest_date']) / $duration : 0;
                                    $data [$i] ['progress2'] = $duration != 0 ? 1 / $duration : 1;
                                    $data [$i] ['progress3'] = $duration != 0 ? $valueV ['latest_date'] / $duration : $valueV ['latest_date'];
                                    $data [$i] ['status'] = $status;
                                    $data [$i] ['sortorder'] = '10';
                                    $data [$i] ['parent'] = $data_id;
                                    $data [$i] ['ezf_id'] = $scheduleOption ['11111'] ['main_ezf_id'];
                                    $data [$i] ['target'] = $data_id;
                                    $data [$i] ['dataid'] = $dataid_detail;
                                    $data [$i] ['open'] = '1';
                                    $data [$i] ['type_task'] = 'task';
                                    $data [$i] ['type'] = 'task';
                                    $data [$i] ['priority'] = '5';
                                    $data [$i] ['sub_type'] = '2';
                                    $data [$i] ['group_id'] = $group_id;
                                    $data [$i] ['schedule_id'] = $schedule_id;

                                    $links [$i] ['id'] = $i;
                                    $links [$i] ['source'] = $visit_link [$valueV ['id']];
                                    $links [$i] ['target'] = isset($valueV ['visit_cal_date']) ? $visit_link [$valueV ['visit_cal_date']] : null;
                                    $links [$i] ['type'] = '3';

                                    $i ++;
                                }
                                $sub_count ++;
                            }
                        }
                    }
                }
            }

            foreach ($subData as $valS) {
                if (isset($valS['index'])) {
                    $progreeCate = 0;
                    if ((isset($valS['progress']) && $valS['progress'] > 0) && $valS['amt'] > 0)
                        $progreeCate = ($valS['progress'] / $valS['amt']);

                    $data [$valS['index']] ['progress'] = $progreeCate / 100;
                    $data [$valS['index']] ['progressPer'] = ($progreeCate > 0) ? number_format($progreeCate, 1) . " %" : "0.0%";
                }
            }

            $datetime1 = new DateTime($cateStart);
            $datetime2 = new DateTime($cateFinish);
            $interval = $datetime1->diff($datetime2);
            $diffDate = $interval->format('%a');
            if ($cateDuration < $diffDate)
                $cateDuration = $diffDate;

            $cateAmt ++;
            $progreeMain = 0;

            if ($mainProgress > 0)
                $progreeMain = ($mainProgress / $taskAmt);

            $data [0] ['progress'] = $progreeMain / 100;
            $cateOrder ++;
        }

        $linkModel = new \backend\modules\gantt\models\VisitLinks ();
        $queryLink = $linkModel->findAll([
            'widget_id' => $project_id
        ]);

        foreach ($queryLink as $key => $val) {
            $links [$i] ['id'] = $i;
            $links [$i] ['source'] = $val ['source'];
            $links [$i] ['target'] = $val ['target'];
            $links [$i] ['type'] = $val ['type'];
            $i ++;
        }

        $data_all ['data'] = $data;
        $data_all ['links'] = $links;
        //\appxq\sdii\utils\VarDumper::dump($data_all);
        return json_encode($data_all);
    }

    public function actionConfigGantt() {
        $widget_id = Yii::$app->request->get('widget_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $parent = Yii::$app->request->get('parent');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $require = Yii::$app->request->get('require');
        $data_id = Yii::$app->request->get('data_id');
        $id = '';
        if ($require == 'New_task') {
            $id = $parent;
        } else {
            $id = $data_id;
        }

        if ($data_id == '11111' || $data_id == '22222') {
            $modelWidget = new \backend\modules\ezmodules\models\EzmoduleWidget ();
            $model = $modelWidget->findOne([
                'widget_id' => $schedule_id
            ]);
        } else {
            $modelVisit = new \backend\modules\subjects\models\VisitProcedure ();
            $model = $modelVisit->findOne([
                'id' => $data_id
            ]);
        }

        return $this->renderAjax('config-gantt', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'model' => $model,
                    'require' => $require,
                    'reloadDiv' => $reloadDiv,
                    'data_id' => $data_id
        ]);
    }

    public function actionSaveGantt2() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $mid = Yii::$app->request->get('id');
            $mid == '' ? $mid = '1' : '';
            $visitModel = new \backend\modules\subjects\models\VisitProcedure ();
            $options = $_POST ['options'];
            $widget_id = $_POST ['widget_id'];
            $data_id = $_POST ['data_id'];
            $schedule_id = $_POST ['schedule_id'];
            $node_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $nowDate = date('Y-m-d H:i:s');
            $userid = \Yii::$app->user->id;

            try {
                if ($data_id == null) {

                    $visitModel->id = $node_id;
                    $visitModel->module_id = $mid;
                    $visitModel->widget_id = $widget_id;
                    $visitModel->schedule_id = $schedule_widget_id;
                    $visitModel->ezf_id = $options ['ezf_id'];
                    $visitModel->visit_name = $options ['visit_name'];
                    $visitModel->visit_parent = $options ['visit_parent'] == '' ? '0' : $options ['visit_parent'];
                    $visitModel->start_date = date('Y-m-d');
                    $visitModel->actual_date = $options ['actual_date'];
                    $visitModel->plan_date = $options ['plan_date'];
                    $visitModel->earliest_date = $options ['earliest_date'];
                    $visitModel->latest_date = $options ['latest_date'];
                    $visitModel->rstat = '1';
                    $visitModel->open_node = $options ['open_state'] == '' ? '1' : $options ['open_state'];
                    $visitModel->created_by = $userid . '';
                    $visitModel->created_at = $nowDate;
                    $visitModel->update_by = $userid . '';
                    $visitModel->update_at = $nowDate;

                    if ($visitModel->save()) {
                        $result = [
                            'status' => 'success',
                            'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                            'data' => $visitModel
                        ];
                        return $result;
                    } else {
                        $result = [
                            'status' => 'error',
                            'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                        ];
                        return $result;
                    }
                } else {
                    if ($data_id == '11111' || $data_id == '22222') {
                        $widgetModel = new \backend\modules\ezmodules\models\EzmoduleWidget ();
                        $query = $widgetModel->findOne([
                            'widget_id' => $schedule_id
                        ]);
                        $options_old = \appxq\sdii\utils\SDUtility::string2Array($query ['options']);

                        if ($data_id == '11111') {
                            $options_old [$data_id] ['form_name'] = $options ['visit_name'];
                            $options_old [$data_id] ['main_ezf_id'] = $options ['ezf_id'];
                            $options_old [$data_id] ['main_actual_distance'] = $options ['actual_date'];
                            $options_old [$data_id] ['main_earliest_distance'] = $options ['earliest_date'];
                            $options_old [$data_id] ['main_latest_distance'] = $options ['latest_date'];
                        } else {
                            $options_old [$data_id] ['form_name'] = $options ['visit_name'];
                            $options_old [$data_id] ['random_ezf_id'] = $options ['ezf_id'];
                            $options_old [$data_id] ['random_actual_distance'] = $options ['actual_date'];
                            $options_old [$data_id] ['random_plan_distance'] = $options ['plan_date'];
                            $options_old [$data_id] ['random_earliest_distance'] = $options ['earliest_date'];
                            $options_old [$data_id] ['random_latest_distance'] = $options ['latest_date'];
                        }
                        // \appxq\sdii\utils\VarDumper::dump($options_old);
                        $query->options = \appxq\sdii\utils\SDUtility::array2String($options_old);
                        $query->updated_at = $nowDate;

                        if ($query->update()) {
                            $result = [
                                'status' => 'success',
                                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                                'data' => $visitModel
                            ];
                            return $result;
                        } else {
                            $result = [
                                'status' => 'error',
                                'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                            ];
                            return $result;
                        }
                    } else {
                        $query = $visitModel->findOne([
                            'id' => $data_id
                        ]);

                        $query->ezf_id = $options ['ezf_id'];
                        $query->visit_name = $options ['visit_name'];
                        $query->visit_parent = $options ['visit_parent'] == '' ? '0' : $options ['visit_parent'];
                        $query->actual_date = $options ['actual_date'];
                        $query->plan_date = $options ['plan_date'];
                        $query->earliest_date = $options ['earliest_date'];
                        $query->latest_date = $options ['latest_date'];
                        $query->rstat = '1';
                        $query->open_node = $options ['open_state'] == '' ? '1' : $options ['open_state'];
                        $query->update_by = $userid . '';
                        $query->update_at = $nowDate;

                        if ($query->update()) {
                            $result = [
                                'status' => 'success',
                                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                                'data' => $visitModel
                            ];
                            return $result;
                        } else {
                            $result = [
                                'status' => 'error',
                                'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                            ];
                            return $result;
                        }
                    }
                }
            } catch (\yii\db\Exception $ex) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error base.')
                ];
                return $result;
            }
        }
    }

    public function actionUpdateGantt() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $options = \Yii::$app->request->post('options');
            $gantt_type = \Yii::$app->request->post('gantt_type');
            $activity_ezf_id = \Yii::$app->request->post('activity_ezf_id');
            $response_ezf_id = \Yii::$app->request->post('response_ezf_id');

            $optionArray = \appxq\sdii\utils\SDUtility::string2Array($options);
            $scheduleOptions = null;
            // \appxq\sdii\utils\VarDumper::dump($optionArray);
            $sceduleData = isset($optionArray ['schedule_id']) ? SubjectManagementQuery::getWidgetById($optionArray ['schedule_id']) : null;
            if ($sceduleData != null)
                $scheduleOptions = \appxq\sdii\utils\SDUtility::string2Array($sceduleData ['options']);

            if ($gantt_type == '1') { // PMS Type 1 General type 2 is Gantt for schedule
                $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformById($activity_ezf_id);
                $projectModel = new \backend\modules\ezforms2\models\TbdataAll ();
                $projectModel->setTableName($ezform ['ezf_table']);
                $query = $projectModel->findOne([
                    'id' => $optionArray ['dataid']
                ]);
                $nowDate = date('Y-m-d H:i:s');
                $userid = \Yii::$app->user->id;
                $dateSec = strtotime($optionArray ['start_date']);
                $actual_date = date('Y-m-d', $dateSec);
                $date = new DateTime($actual_date);
                // $date->modify('+ 1 day');
                $progress = $optionArray ['progress'] == '' ? 0 : $optionArray ['progress'];
                $query->start_date = $date->format('Y-m-d');
                $date->modify('+ ' . $optionArray ['duration'] . '  day');
                $query->finish_date = $date->format('Y-m-d');
                $query->user_update = $userid . '';
                $query->update_date = $nowDate;
                $modelData = (array) $query->attributes;
                $modelData ['task_type'] = $optionArray ['type'];
                $modelData ['progress'] = $progress * 100;
                $modelTarget = GanttQuery::pmsTaskTargetInsert($modelData);

                $resForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($response_ezf_id);
                $resData = SubjectManagementQuery::GetTableData($resForm, [
                            'target' => $query->id
                                ], 'one');

                if ($resData) {
                    $response = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($response_ezf_id, $resData ['id'], $query->id, [
                                'progress' => ($progress * 100)
                    ]);
                } else {
                    $response = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($response_ezf_id, null, $query->id, [
                                'progress' => ($progress * 100)
                    ]);
                    if ($response) {
                        $responseData = isset($response ['data']) ? $response ['data'] : $response->attributes;
                        $modelRes ['id'] = $responseData['target'];
                        $modelRes ['target'] = $query->target;
                        $modelRes ['response_id'] = $responseData['id'];
                        $modelRes ['progress'] = ($progress * 100);
                        $modelUpdate = GanttQuery::pmsTaskTargetInsert($modelRes);
                    }
                }

                if ($query->update()) {
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                        'data' => $query
                    ];
                    return $result;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                    ];
                    return $result;
                }
            } else {

                $curr_date = date('Y-m-d');
                $result = SubjectManagementQuery::getVisitScheduleByWidget($optionArray ['schedule_id']);
                $data = [];
                foreach ($result as $key => $value) {
                    if ($value ['id'] == '11111') {
                        $data = $value;
                    } else if ($value ['id'] == '22222') {
                        $data = $value;
                    }
                }

                $plan_date = new DateTime($optionArray ['plan_date']);
                $startDate = new DateTime($optionArray ['start_date']);
                $endDate = new DateTime($optionArray ['end_date']);
                $earliest_date = $optionArray ['earliest_date'];
                $latest_date = $optionArray ['latest_date'];

                if ($optionArray ['id'] == '11111' || $optionArray ['id'] == '22222') { // For visit Screening and Randomization
                    $plan_dis = $scheduleOptions ['22222'] ['random_plan_distance'];
                    $earliest_dis = $scheduleOptions ['22222'] ['random_earliest_distance'];
                    $latest_dis = $scheduleOptions ['22222'] ['random_latest_distance'];

                    $startDate->modify('+ 1 day');
                    $start_date = $startDate->format('Y-m-d');
                    $end_date = $endDate->format('Y-m-d');

                    if ($earliest_date != $start_date && $latest_date != $end_date) { // Moved task
                        $planDiff = date_diff(date_create($latest_date), date_create($end_date));
                        if ($planDiff->invert > 0) { // Move to left
                            $plan_dis = $plan_dis - $planDiff->days;
                        } else { // Move to left
                            $plan_dis = $plan_dis + ($planDiff->days);
                        }
                    } else { // Scale task
                        if ($earliest_date != $start_date) { // Scale earliest date
                            $earDiff = date_diff(date_create($earliest_date), date_create($start_date));
                            if ($earDiff->invert > 0) { // Move to left
                                $earliest_dis = - (abs($earliest_dis) + $earDiff->days);
                            } else { // Move to left
                                $earliest_dis = - (abs($earliest_dis) - $earDiff->days);
                            }
                        } else { // scale latest date
                            $latestDiff = date_diff(date_create($latest_date), date_create($end_date));
                            if ($latestDiff->invert > 0) { // Move to left
                                $latest_dis = $latest_dis - $latestDiff->days;
                            } else { // Move to left
                                $latest_dis = $latest_dis + $latestDiff->days;
                            }
                        }
                    }

                    $nowDate = date('Y-m-d H:i:s');
                    $userid = \Yii::$app->user->id;

                    $widgetModel = \backend\modules\ezmodules\models\EzmoduleWidget::findOne([
                                'widget_id' => $optionArray ['schedule_id']
                    ]);
                    $scheduleOptions ['22222'] ['random_earliest_distance'] = $earliest_dis . "";
                    $scheduleOptions ['22222'] ['random_latest_distance'] = $latest_dis . "";
                    $scheduleOptions ['22222'] ['random_plan_distance'] = $plan_dis . "";

                    $widgetModel->options = \appxq\sdii\utils\SDUtility::array2String($scheduleOptions);
                    if ($widgetModel->update()) {
                        $result = [
                            'status' => 'success',
                            'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                            'data' => $query
                        ];
                        return $result;
                    } else {
                        $result = [
                            'status' => 'error',
                            'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                        ];
                        return $result;
                    }
                } else {
                    $query = \backend\modules\subjects\models\VisitSchedule::findOne([
                                'id' => $optionArray ['id']
                    ]);
                    $nowDate = date('Y-m-d H:i:s');
                    $userid = \Yii::$app->user->id;

                    $plan_dis = $query->plan_date;
                    $earliest_dis = $query->earliest_date;
                    $latest_dis = $query->latest_date;

                    $startDate->modify('+ 1 day');
                    $start_date = $startDate->format('Y-m-d');
                    $end_date = $endDate->format('Y-m-d');

                    if ($earliest_date != $start_date && $latest_date != $end_date) { // Moved task
                        $planDiff = date_diff(date_create($latest_date), date_create($end_date));
                        if ($planDiff->invert > 0) { // Move to left
                            $plan_dis = $plan_dis - $planDiff->days;
                        } else { // Move to left
                            $plan_dis = $plan_dis + ($planDiff->days);
                        }
                    } else { // Scale task
                        if ($earliest_date != $start_date) { // Scale earliest date
                            $earDiff = date_diff(date_create($earliest_date), date_create($start_date));
                            if ($earDiff->invert > 0) { // Move to left
                                $earliest_dis = - (abs($earliest_dis) + $earDiff->days);
                            } else { // Move to left
                                $earliest_dis = - (abs($earliest_dis) - $earDiff->days);
                            }
                        } else { // scale latest date
                            $latestDiff = date_diff(date_create($latest_date), date_create($end_date));
                            if ($latestDiff->invert > 0) { // Move to left
                                $latest_dis = $latest_dis - $latestDiff->days;
                            } else { // Move to left
                                $latest_dis = $latest_dis + $latestDiff->days;
                            }
                        }
                    }

                    $query->progress = number_format($optionArray ['progress'], 2, '.', '') . '';
                    $query->start_date = $startDate->format('Y-m-d');
                    $query->earliest_date = $earliest_dis . "";
                    $query->latest_date = $latest_dis . "";
                    $query->plan_date = $plan_dis . "";
                    $query->update_by = $userid . '';
                    $query->update_at = $nowDate;

                    if ($query->update()) {
                        $result = [
                            'status' => 'success',
                            'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                            'data' => $query
                        ];
                        return $result;
                    } else {
                        $result = [
                            'status' => 'error',
                            'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                        ];
                        return $result;
                    }
                }
            }
        }
    }

    public function actionUpdateLink() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $mid = Yii::$app->request->get('id');
            $visitModel = new \backend\modules\gantt\models\VisitLinks ();
            $data = Yii::$app->request->post('dataList');
            $widget_id = Yii::$app->request->post('widget_id');
            $schedule_id = Yii::$app->request->post('schedule_id');
            $project_id = Yii::$app->request->post('project_id');
            $node_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $nowDate = date('Y-m-d H:i:s');
            $userid = \Yii::$app->user->id;

            $dataArray = \appxq\sdii\utils\SDUtility::string2Array($data);

            $type = 0;
            if ($dataArray ['link_from_start'] == true && $dataArray ['link_to_start'] == true) {
                $type = '1';
            } elseif ($dataArray ['link_from_start'] == true && $dataArray ['link_to_start'] == false) {
                $type = '3';
            } elseif ($dataArray ['link_from_start'] == null && $dataArray ['link_to_start'] == true) {
                $type = '0';
            } else {
                $type = '2';
            }

            $query = $visitModel->findOne([
                'source' => $dataArray ['link_source_id'],
                'target' => $dataArray ['link_target_id'],
                'type' => $type,
                'widget_id' => $project_id
            ]);

            if (!($query)) {

                $visitModel->source = $dataArray ['link_source_id'];
                $visitModel->target = $dataArray ['link_target_id'];
                $visitModel->type = $type;
                $visitModel->widget_id = $project_id;
                $visitModel->module_id = $mid;
                $visitModel->update_by = $userid . '';
                $visitModel->update_at = $nowDate;
                $visitModel->created_by = $userid . '';
                $visitModel->created_at = $nowDate;

                if ($visitModel->save()) {
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                        'data' => $query
                    ];
                    return $result;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                    ];
                    return $result;
                }
            } else {
                $query->source = $dataArray ['link_source_id'];
                $query->target = $dataArray ['link_target_id'];
                $query->type = $type;
                $query->widget_id = $project_id;
                $query->module_id = $mid;
                $query->update_by = $userid . '';
                $query->update_at = $nowDate;

                if ($query->update()) {
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                        'data' => $query
                    ];
                    return $result;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                    ];
                    return $result;
                }
            }
        }
    }

    public function actionUpdateLinkSchedule() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $mid = Yii::$app->request->get('id');
            $visitModel = new \backend\modules\gantt\models\VisitLinks ();
            $data = Yii::$app->request->post('dataList');
            $widget_id = Yii::$app->request->post('widget_id');
            $schedule_id = Yii::$app->request->post('schedule_id');
            $node_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $nowDate = date('Y-m-d H:i:s');
            $userid = \Yii::$app->user->id;
            $dataArray = \appxq\sdii\utils\SDUtility::string2Array($data);
            // \appxq\sdii\utils\VarDumper::dump($dataArray);
            $source = $dataArray ['link_source_id'];
            $target = $dataArray ['link_target_id'];

            $visitSchedule = \backend\modules\subjects\models\VisitSchedule::findOne([
                        'id' => $source
            ]);

            $type = 0;
            if ($dataArray ['link_from_start'] == true && $dataArray ['link_to_start'] == true) {
                $type = '1';
            } elseif ($dataArray ['link_from_start'] == true && $dataArray ['link_to_start'] == false) {
                $type = '3';
            } elseif ($dataArray ['link_from_start'] == null && $dataArray ['link_to_start'] == true) {
                $type = '0';
            } else {
                $type = '2';
            }

            if ($type == '3') {
                $visitSchedule->visit_cal_date = $target;
                $visitSchedule->field_cal_date = 'actual_date';
            }

            if ($visitSchedule->save()) {
                $result = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.')
                ];
                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                ];
                return $result;
            }
        }
    }

    public function actionDeleteLink() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $mid = Yii::$app->request->get('id');
            $visitModel = new \backend\modules\gantt\models\VisitLinks ();
            $data = Yii::$app->request->post('dataList');
            $widget_id = Yii::$app->request->post('widget_id');
            $schedule_widget_id = Yii::$app->request->post('schedule_widget_id');
            $dataArray = \appxq\sdii\utils\SDUtility::string2Array($data);
            $source = $dataArray ['source'];
            $target = $dataArray ['target'];
            $type = $dataArray ['type'];

            $sql = " DELETE FROM zdata_visit_links WHERE  source=:source AND target=:target AND type=:type  ";
            $result = \Yii::$app->db->createCommand($sql, [
                        ':source' => $source,
                        ':target' => $target,
                        ':type' => $type . ''
                    ])->execute();
            if ($result) {
                $result = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.')
                ];
                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                ];
                return $result;
            }
        }
    }

    public function actionDeleteLinkSchedule() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $module_id = Yii::$app->request->get('module_id');
            $data = Yii::$app->request->post('dataList');
            $widget_id = Yii::$app->request->post('widget_id');
            $schedule_id = Yii::$app->request->post('schedule_id');
            $dataArray = \appxq\sdii\utils\SDUtility::string2Array($data);
            $source = $dataArray ['source'];
            $target = $dataArray ['target'];
            $type = $dataArray ['type'];

            if ($source == '22222' || $source == '11111') {
                $scheduleWidget = \backend\modules\ezmodules\models\EzmoduleWidget::findOne([
                            'widget_id' => $schedule_id
                ]);
                $options = \appxq\sdii\utils\SDUtility::string2Array($scheduleWidget ['options']);
            } else {
                $visitSchedule = \backend\modules\subjects\models\VisitSchedule::findOne([
                            'id' => $source
                ]);
                $visitSchedule->visit_cal_date = null;
                $visitSchedule->field_cal_date = null;

                if ($visitSchedule->update()) {
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.')
                    ];
                    return $result;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                    ];
                    return $result;
                }
            }
        }
    }

    public function actionGetRefform() {
        return $this->renderAjax('_ref-form');
    }

    public function actionGetNotRefform() {
        $mid = $_GET ['id'];
        $mid == '' ? $mid = '1' : '';
        $data_node = self::GetNodeGantt($mid);
        return $this->renderAjax('_notref-form', [
                    'data_node' => $data_node
        ]);
    }

    public static function GetNodeGantt($mid) {
        $visitModel = new VisitProcedure ();
        $query = new \yii\db\Query ();
        $result = $query->select('*')->from($visitModel->tableName())->where([
                    'mid' => $mid
                ])->all();

        $data = [];
        foreach ($result as $key => $value) {
            $data [$value ['node_id']] = $value ['text'];
        }

        return $data;
    }

    public function actionGetProgress() {
        $data_id = Yii::$app->request->get('data_id');
        $result = \backend\modules\gantt\classes\GanttQuery::getProgressByTarget($data_id);
        echo $result;
    }

    public function actionCheckOwntask() {
        $data_id = Yii::$app->request->get('data_id');
        $ezf_id = Yii::$app->request->get('ezf_id');
        $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
        $result = \backend\modules\gantt\classes\GanttQuery::checkOwnData($ezform, $data_id);
        echo $result;
    }

    public function actionCopyTask() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data_id = Yii::$app->request->get('dataid');
        $activity_ezf = Yii::$app->request->get('activity_ezf');
        $cate_ezf = Yii::$app->request->get('cate_ezf');
        $project_ezf = Yii::$app->request->get('project_ezf');
        $task_type = Yii::$app->request->get('task_type');
        $schedule_id = Yii::$app->request->post('schedule_id');
        $user_id = \Yii::$app->user->id;
        $nowDate = date("Y-m-d H:i:s");
        $cate_ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($cate_ezf);
        $activity_ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_ezf);

        $new_cate_id = '';
        if ($task_type == 'main-task') {
            
        } elseif ($task_type == 'sub-task') {
            $query = SubjectManagementQuery::GetTableData('pms_task_target', [
                        'id' => $data_id
                            ], 'one');
            $target = $query ['target'];
            $text = $query ['task_name'] . " Copy";
            $parentCopy = '';
            $initdata = [
                'project_id' => $target,
                'cate_name' => $text
            ];
            $model = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($cate_ezf, null, $target, $initdata);
            if (isset($model ['data']) || isset($model->attributes)) {
                $subModel = isset($model ['data']) ? $model ['data'] : $model->attributes;
                $modelData = $query;
                $new_cate_id = $subModel['id'];
                $modelData ['id'] = $new_cate_id;
                $modelData ['ezf_id'] = $cate_ezf;
                $modelData ['task_name'] = $text;
                $modelData ['project_id'] = $target;
                $modelData ['priority'] = 2;
                $modelData ['open_node'] = 1;
                $modelData ['order_node'] = 0;
                $modelData ['sub_type'] = isset($subModel ['subtask_type']) ? $subModel ['subtask_type'] : 1;
                $modelData ['task_type'] = $task_type;
                $modelTarget = GanttQuery::pmsTaskTargetInsert($modelData);
                $parentCopy = $modelTarget ['taskid'];
            }

            $dataAct = SubjectManagementQuery::GetTableData('pms_task_target', [
                        'parent' => $data_id
            ]);
            $result = false;
            $copyModel = [];

            foreach ($dataAct as $key => $val) {
                $initdata = [
                    'task_name' => $val ['task_name']
                ];
                if (isset($val ['task_type']) && $val ['task_type'] == 'milestone') {
                    $initdata ['sent_to'] = '1';
                    $initdata ['sent_module_2'] = '1';
                }
                $target = $val ['target'];
                $queryTask = SubjectManagementQuery::GetTableData('pms_task_target', [
                            'id' => $val ['id']
                                ], 'one');

                $modelTask = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($activity_ezf, null, $target, $initdata);
                $modelData = null;
                $modelTarget = null;
                if (isset($modelTask ['data'])) {
                    $modelData = $queryTask;
                    $modelData ['id'] = $modelTask ['data'] ['id'];
                    $modelData ['ezf_id'] = $activity_ezf;
                    $modelData ['priority'] = 3;
                    $modelData ['parent'] = $parentCopy . "";
                    $modelData ['open_node'] = 1;
                    $modelData ['order_node'] = 0;
                    $modelData ['task_type'] = 'task';
                    $modelTarget = GanttQuery::pmsTaskTargetInsert($modelData);
                }

                $val ['id'] = $modelTarget ['taskid'];
                $val ['dataid'] = $modelData ['id'];
                $val ['parent'] = $parentCopy;
                $val ['start_date'] = $queryTask ['start_date'];
                $val ['finish_date'] = $queryTask ['end_date'];
                $val ['user_create'] = $user_id;
                $val ['create_date'] = $nowDate;
                $val ['user_update'] = $user_id;
                $val ['update_date'] = $nowDate;

                $copyModel [] = $val;
            }

            if (isset($model ['data'])) {
                $result = [
                    'status' => 'success',
                    'taskid' => $parentCopy,
                    'dataid' => $new_cate_id,
                    'task_name' => $query ['task_name'],
                    'taskModels' => $copyModel,
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.')
                ];
                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                ];
                return $result;
            }
        } elseif ($task_type == 'task' || $task_type == 'milestone') {
            $query = SubjectManagementQuery::GetTableData('pms_task_target', [
                        'id' => $data_id
                            ], 'one');
            $initdata = [
                'task_name' => $query ['task_name'] . " Copy"
            ];
            if ($task_type == 'milestone') {
                $initdata ['sent_to'] = '1';
                $initdata ['sent_module_2'] = '1';
            }
            $target = $query ['target'];
            $parent = $query ['parent'];

            $model = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($activity_ezf, null, $target, $initdata);

            $new_task_id = '';
            $modelData = null;
            if (isset($model ['data']) || isset($model->attributes)) {
                $taskModel = isset($model ['data']) ? $model ['data'] : $model->attributes;
                $modelData = $query;
                $new_task_id = $taskModel['id'];
                $modelData ['id'] = $new_task_id;
                $modelData ['task_name'] = $query ['task_name'] . " Copy";
                $modelData ['ezf_id'] = $activity_ezf;
                $modelData ['priority'] = 3;
                $modelData ['parent'] = $parent . "";
                $modelData ['open_node'] = 1;
                $modelData ['order_node'] = 0;
                $modelData ['task_type'] = isset($task_type) && $task_type != null ? $task_type : 'task';

                $modelTarget = GanttQuery::pmsTaskTargetInsert($modelData);

                $result = [
                    'status' => 'success',
                    'dataid' => $new_task_id,
                    'taskid' => $modelTarget ['taskid'],
                    'start_date' => $query ['start_date'],
                    'finish_date' => $query ['end_date'],
                    'task_name' => $query ['task_name'],
                    'parent' => $query ['parent'],
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.')
                ];
                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                ];
                return $result;
            }
        }
    }

    public function actionSharingTask() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data_id = Yii::$app->request->get('dataid');
        $taskid = Yii::$app->request->get('dataid');
        $activity_ezf = Yii::$app->request->get('activity_ezf');
        $cate_ezf = Yii::$app->request->get('cate_ezf');
        $project_ezf = Yii::$app->request->get('project_ezf');
        $main_from = Yii::$app->request->get('main_from');
        $task_type = Yii::$app->request->get('task_type');
        $target = Yii::$app->request->get('target');
        $parent = Yii::$app->request->get('parent');
        $schedule_id = Yii::$app->request->post('schedule_id');
        $user_id = \Yii::$app->user->id;
        $nowDate = date("Y-m-d H:i:s");

        $cate_ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($cate_ezf);
        $activity_ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_ezf);

        $new_cate_id = '';
        if ($task_type == 'main-task') {
            
        } elseif ($task_type == 'sub-task') {
            $modelTargetSub = [];
            $query = SubjectManagementQuery::GetTableData('pms_task_target', [
                        'id' => $taskid
                            ], 'one');
            $text = $query ['task_name'];
            $parentCopy = '';
            if (isset($query)) {
                $modelData = $query;
                $modelData ['id'] = $query ['dataid'];
                $modelData ['share_from'] = $modelData ['target'];
                $modelData ['target'] = $target;
                $modelTargetSub = GanttQuery::pmsTaskTargetInsert($modelData, 'insert');

                $parentCopy = isset($modelTargetSub ['taskid']) ? $modelTargetSub ['taskid'] : null;
            }

            $dataAct = SubjectManagementQuery::GetTableData('pms_task_target', [
                        'parent' => $taskid
            ]);
            $result = false;

            if (isset($modelTargetSub ['taskid'])) {
                foreach ($dataAct as $key => $val) {
                    $initdata = [
                        'task_name' => $val ['task_name']
                    ];
                    $queryTask = SubjectManagementQuery::GetTableData('pms_task_target', [
                                'id' => $val ['id']
                                    ], 'one');
                    $modelData = null;
                    $modelTarget = null;
                    if (isset($queryTask)) {
                        $modelData = $queryTask;
                        $modelData ['id'] = $val ['dataid'];
                        $modelData ['share_from'] = $main_from;
                        $modelData ['target'] = $target;
                        $modelData ['parent'] = $parentCopy . "";
                        $modelTarget = GanttQuery::pmsTaskTargetInsert($modelData, 'insert');
                    }
                }
            }

            if (isset($modelTargetSub ['taskid'])) {
                $result = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.')
                ];
                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                ];
                return $result;
            }
        } elseif ($task_type == 'task') {
            $query = SubjectManagementQuery::GetTableData('pms_task_target', [
                        'id' => $taskid
                            ], 'one');
            $new_task_id = '';
            $modelData = null;
            if ($query) {
                $modelData = $query;
                $modelData ['id'] = $query ['dataid'];
                $modelData ['target'] = $target;
                $modelData ['share_from'] = $main_from;
                $modelData ['parent'] = isset($parent) && !empty($parent) ? $parent : '0';

                $modelTarget = GanttQuery::pmsTaskTargetInsert($modelData, 'insert');

                $result = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.')
                ];
                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                ];
                return $result;
            }
        }
    }

    public function actionDragitemUpdate() {
        $target = Yii::$app->request->get('target');
        $parent = Yii::$app->request->get('parent');
        $ezf_id = Yii::$app->request->get('ezf_id');
        $type = Yii::$app->request->get('type');
        $drop_target = Yii::$app->request->get('drop_target');
        $data_task = Yii::$app->request->get('data_task');
        $data_task = base64_decode($data_task);
        $data_task = SDUtility::string2Array($data_task);

        $drop_id = explode(':', $drop_target);
        if (!isset($drop_id [1]))
            $drop_id [1] = $drop_target;
        $new_order = Yii::$app->request->get('new_order');
        $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
        $sql_update = " UPDATE pms_task_target SET order_node = CASE ";
        if (is_array($data_task)) {
            foreach ($data_task as $key => $val) {
                $sql_update .= " WHEN id='{$val['id']}' THEN '{$val['order']}' ";
            }
        }
        $sql_update .= " END WHERE parent='$parent' ";
        Yii::$app->db->createCommand($sql_update)->execute();
// 		if ($drop_target != 'next:null') {
// 			$query = new \yii\db\Query ();
// 			$query->select ( 'id,parent,order_node' )->from ( 'pms_task_target' );
// 			$query->where ( 'id IN("' . $target . '","' . $drop_id [1] . '")' );
// 			$result = $query->all ();
// 			foreach ( $result as $key => $val ) {
// 				if ($val ['id'] == $target)
// 					Yii::$app->db->createCommand ()->update ( 'pms_task_target', [ 
// 							'order_node' => $val ['order_node']
// 					], 'id=:id', [ 
// 							':id' => $drop_id [1]
// 					] )->execute ();
// 				elseif ($val ['id'] == $drop_id [1])
// 					Yii::$app->db->createCommand ()->update ( 'pms_task_target', [ 
// 							'order_node' => $val ['order_node']
// 					], 'id=:id', [ 
// 							':id' => $target
// 					] )->execute ();
// 			}
// 		} 
        // if ($type == 'task' || $type == 'milestone') {
        Yii::$app->db->createCommand()->update('pms_task_target', [
            'parent' => $parent
                ], 'id=:id', [
            ':id' => $target
        ])->execute();
        // }
    }

    public function actionGanttHvtexport() {
        $widget_id = "1520792390050287900";
        $widgetData = SubjectManagementQuery::getWidgetById($widget_id);
        $options = \appxq\sdii\utils\SDUtility::string2Array($widgetData ['options']);
        // \appxq\sdii\utils\VarDumper::dump($options);
        $project_ezf_id = $options ['project_ezf_id'];
        $cate_ezf_id = $options ['cate_ezf_id'];
        $activity_ezf_id = $options ['activity_ezf_id'];
        $start_date = $options ['start_date'];
        $finish_date = $options ['finish_date'];
        $project_name = $options ['project_name'];
        $cate_name = $options ['cate_name'];
        $task_name = $options ['task_name'];

        return $this->renderAjax('gantt-export', [
                    'project_ezf_id' => $project_ezf_id,
                    'cate_ezf_id' => $cate_ezf_id,
                    'activity_ezf_id' => $activity_ezf_id,
                    'start_date' => $start_date,
                    'finish_date' => $finish_date,
                    'project_name' => $project_name,
                    'cate_name' => $cate_name,
                    'task_name' => $task_name,
                    'widget_id' => $widget_id
        ]);
    }

    public function actionUpdateAttributeGantt() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $type = Yii::$app->request->get('type');
            $task_type = Yii::$app->request->get('task_type');
            $text = Yii::$app->request->get('text');
            $start_date = Yii::$app->request->get('start_date');
            $end_date = Yii::$app->request->get('end_date');
            $dataid = Yii::$app->request->get('dataid');
            $taskid = Yii::$app->request->get('taskid');
            $activity_ezf = Yii::$app->request->get('activity_ezf');
            $cate_ezf = Yii::$app->request->get('cate_ezf');
            $project_ezf = Yii::$app->request->get('project_ezf');

            if ($type == '2') {
                $subForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($cate_ezf);
                $modelDat = new \backend\modules\ezforms2\models\TbdataAll ();
                $modelDat->setTableName($subForm->ezf_table);
                $query = $modelDat->findOne([
                    'id' => $dataid
                ]);
                $query->cate_name = $text ? $text : $query->cate_name;
                $modelData = (array) $query->attributes;
                $modelData ['task_name'] = $query->cate_name;
                $modelData ['task_type'] = 'sub-task';

                $modelTarget = GanttQuery::pmsTaskTargetInsert($modelData);
            } else if ($type == '3') {
                $taskForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_ezf);
                $modelDat = new \backend\modules\ezforms2\models\TbdataAll ();
                $modelDat->setTableName($taskForm->ezf_table);
                $query = $modelDat->findOne([
                    'id' => $dataid
                ]);
                $query->task_name = $text ? $text : $query->task_name;
                $query->start_date = $start_date != null ? $start_date : $query->start_date;
                $query->finish_date = $end_date != null ? $end_date : $query->finish_date;
                $modelData = (array) $query->attributes;
                $modelData ['task_type'] = $task_type;

                $modelTarget = GanttQuery::pmsTaskTargetInsert($modelData);
            }

            if ($query->save()) {
                $result = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.')
                ];
                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                ];
                return $result;
            }
        }
    }

    public function actionAddNewTask() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $activity_ezf_id = Yii::$app->request->get('activity_ezf_id');
            $target = Yii::$app->request->get('target');
            $cate_id = Yii::$app->request->get('cate_id');
            $parentid = Yii::$app->request->get('parentid');
            $task_type = Yii::$app->request->get('task_type');
            $text = "New task item";
            $initdataOther = Yii::$app->request->get('initdata');
            $nowDate = date('Y-m-d H:i:s');
            //$date = new DateTime($nowDate);
            $date_mod = strtotime($nowDate . ' + 7 day');
            $end_date = date('Y-m-d H:i:s', $date_mod);
            $modelTarget = [];
            if ($task_type == 'milestone') {
                $end_date = date("Y-m-d");
                $text = "New milestone";
            }

            GanttQuery::setUpdateMainTask($target);

            $initdata = [
                'project_id' => $target,
                'task_name' => $text,
                'category_id' => $cate_id,
                'start_date' => $nowDate,
                'finish_date' => $end_date,
            ];
            if ($initdataOther != null && count($initdataOther) > 0)
                $initdata = array_merge($initdata, $initdataOther);

            $model = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($activity_ezf_id, null, $target, $initdata);
            $new_order = 0;
            if (isset($model ['data']) || isset($model->attributes)) {
                $modelData = isset($model ['data']) ? $model ['data'] : $model->attributes;
                $modelData = array_merge($modelData, $initdata);
                $modelData ['ezf_id'] = $activity_ezf_id;
                $modelData ['end_date'] = $modelData ['finish_date'];
                $modelData ['priority'] = 3;
                $modelData ['target'] = $target;
                $modelData ['parent'] = $parentid . "";
                $modelData ['open_node'] = 1;
                $modelData ['notify'] = '0';
                $modelData ['task_type'] = isset($task_type) && $task_type != null ? $task_type : 'task';
                $modelTarget = GanttQuery::pmsTaskTargetInsert($modelData);
            }

            if (isset($model ['data']) || isset($model->attributes)) {
                $result = [
                    'status' => 'success',
                    'dataid' => $modelTarget ['dataid'],
                    'taskid' => $modelTarget ['taskid'],
                    'order_node' => $modelTarget ['order_node'],
                    'start_date' => $nowDate,
                    'end_date' => $nowDate,
                    'task_name' => $text,
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.')
                ];
                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                ];
                return $result;
            }
        }
    }

    public function actionAddNewSubtask() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $cate_ezf_id = Yii::$app->request->get('cate_ezf_id');
            $target = Yii::$app->request->get('target');
            $parent = Yii::$app->request->get('parent');
            $task_type = Yii::$app->request->get('task_type');

            GanttQuery::setUpdateMainTask($target);


            $text = "New sub-task";
            $modelTarget = [];
            $initdata = [
                'project_id' => $target,
                'cate_name' => $text
            ];
            $new_order = 0;
            $model = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($cate_ezf_id, null, $target, $initdata);
            if (isset($model ['data']) || isset($model->attributes)) {
                $modelData = isset($model ['data']) ? $model ['data'] : $model->attributes;
                $modelData ['ezf_id'] = $cate_ezf_id;
                $modelData ['task_name'] = $text;
                $modelData ['project_id'] = $target;
                $modelData ['target'] = $target;
                $modelData ['priority'] = 2;
                $modelData ['open_node'] = 1;
                $modelData ['parent'] = $parent;
                $modelData ['sub_type'] = isset($model ['subtask_type']) ? $model ['subtask_type'] : 1;
                $modelData ['task_type'] = $task_type;

                $modelTarget = GanttQuery::pmsTaskTargetInsert($modelData);
            }
            if (isset($model ['data']) || isset($model->attributes)) {
                $result = [
                    'status' => 'success',
                    'dataid' => $modelTarget ['dataid'],
                    'taskid' => $modelTarget ['taskid'],
                    'order_node' => $modelTarget ['order_node'],
                    'cate_name' => $text,
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.')
                ];
                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                ];
                return $result;
            }
        }
    }

    public function actionProgressUpdate() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $models = Yii::$app->request->post('dataModels');
            $ezf_id = Yii::$app->request->post('ezf_id');
            // $models = base64_decode($models);
            // $models = \appxq\sdii\utils\SDUtility::string2Array($models);
            $response_ezf_id = \backend\modules\gantt\Module::$formsId['response_ezf_id'];
            $nowDate = date('Y-m-d H:i:s');
            $responseForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
            if ($models && count($models) > 0) {
                // $responseData = SubjectManagementQuery::GetTableData($responseForm, ['id' => $models['id']], 'one');
                $modelData = $models;
                $modelData ['response_id'] = $models ['id'];
                $modelData ['id'] = $models ['target'];

                $modelTarget = GanttQuery::pmsTaskTargetInsert($modelData, 'update');
            }

            if ($models && count($models) > 0) {
                $userResponse = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($models ['user_update']);
                $taskData = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', "rstat NOT IN(0,3) AND dataid='{$models['target']}'", 'one');
                $main_data = SubjectManagementQuery::GetTableDataNotEzform('zdata_project', "rstat NOT IN(0,3) AND id='{$taskData['target']}'", 'one');

                $detail_msg = "Task Item namely {$taskData['task_name']} progress by {$userResponse['name']}. you can see in Main Task namely is {$main_data['project_name']}";
                $detail_msg2 = " Progress percent: {$taskData['progress']} at $nowDate <br/>";
                $detail_msg2 .= $models ['response_detail'];
                $url_link = "/gantt/pms-response?page_from=other&taskid={$taskData['dataid']}&ezf_id={$taskData['ezf_id']}&response_ezf_id={$response_ezf_id}";

                $user_owner = SDUtility::string2Array($taskData['co_owner']);
                $user_owner = array_merge($user_owner, [$taskData ['user_create']]);
                \dms\aomruk\classese\Notify::setNotify()->assign($user_owner)->notify($detail_msg)->url($url_link)->detail($detail_msg2)->type_link('1')->send_email(true)->send_line(true)->sendStatic();

                if ($taskData['progress'] >= 100) {
                    $user_review = SDUtility::string2Array($taskData['reviewer']);
                    \dms\aomruk\classese\Notify::setNotify()->assign($user_review)
                            ->notify("Task Item namely {$taskData['task_name']} has been progress 100 percen waiting for review. you can see in Main Task namely is {$main_data['project_name']}")->url($url_link)
                            ->detail('')->type_link('1')->send_email(true)->send_line(true)->sendStatic();
                }
                $result = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.')
                ];
                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.')
                ];
                return $result;
            }
        }
    }

    public function actionPmsTargetUpdateTask() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $dataModels = Yii::$app->request->post('dataModels');
            $ezf_id = Yii::$app->request->post('ezf_id');
            $dataid = Yii::$app->request->post('dataid');
            $text = Yii::$app->request->post('text');
            $type = Yii::$app->request->post('type');
            $response_ezf_id = Module::$formsId['response_ezf_id'];
            $user_id = Yii::$app->user->id;
            $profile = EzfQuery::getUserProfile($user_id);

            $start_date = null;
            $user_name = $profile['firstname'] . ' ' . $profile['lastname'];
            $result = [];
            if ($type == 'task') {
                $taskEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
                $taskModel = new \backend\modules\ezforms2\models\TbdataAll ();
                $taskModel->setTableName($taskEzform->ezf_table);
                $resultTarget = $taskModel->findOne([
                    'id' => $dataid
                ]);
                $result = [];
                $mainEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne(\backend\modules\gantt\Module::$formsId ['maintask_form']);
                $main_data = [];
                if (isset($resultTarget->attributes)) {
                    $taskArr = (array) $resultTarget->attributes;
                    $modelData = $taskArr;
                    $modelData ['ezf_id'] = $ezf_id;
                    $modelData ['task_name'] = $taskArr ['task_name'];
                    $modelData ['project_id'] = $taskArr ['target'];
                    $modelData ['task_performance'] = isset($taskArr ['seg_performance']) ? $taskArr ['seg_performance'] : null;
                    $modelData ['task_type'] = 'task';
                    $start_date = $taskArr['start_date'];
                    $main_data = SubjectManagementQuery::GetTableData($mainEzform, [
                                'id' => $modelData ['target']
                                    ], 'one');

                    GanttQuery::setUpdateMainTask($modelData ['target']);

                    if (isset($resultTarget ['sent_to']) && $resultTarget ['sent_to'] == '1') {
                        if (isset($resultTarget ['sent_module_1']) && $resultTarget ['sent_module_1'] == '1') {
                            $modelData ['task_financial'] = '1';
                        } else {
                            $modelData ['task_financial'] = null;
                        }
                        if (isset($resultTarget ['sent_module_2']) && $resultTarget ['sent_module_2'] == '1') {
                            $modelData ['task_type'] = 'milestone';
                        } else {
                            $modelData ['task_type'] = 'task';
                        }
                    } else {
                        $modelData ['task_financial'] = null;
                        $modelData ['task_type'] = 'task';
                    }
                    $modelData ['url_link'] = isset($resultTarget ['url_link']) ? $resultTarget ['url_link'] : null;
                    $modelData ['assign_user'] = isset($resultTarget ['respons_person']) && $resultTarget ['respons_person'] != '' ? $resultTarget ['respons_person'] : '';
                    if ($modelData ['assign_user'] == '')
                        $modelData ['assign_user_accept'] = '';
                    $modelData ['assign_role'] = isset($resultTarget ['respon_role']) && $resultTarget ['respon_role'] != '' ? $resultTarget ['respon_role'] : '';
                    $modelData ['priority'] = 3;
                    $modelData ['open_node'] = 1;
                    $modelData ['order_node'] = 0;
                    $result = GanttQuery::pmsTaskTargetInsert($modelData);
                    $check_notify = SubjectManagementQuery::GetTableData('pms_task_target', [
                                'dataid' => $result ['dataid']
                                    ], 'one');

                    $pms_notify = SubjectManagementQuery::GetTableDataNotEzform('pms_task_notify', [
                                'dataid' => $result ['dataid'],
                                    ], 'one');

                    if (!$pms_notify || ($pms_notify && ($pms_notify['assign_notify'] == '' || $pms_notify['assign_notify'] == '0'))) {
                        /// ========================= NOTIFY ASSIGNED =========================
                        $assign_notify = SDUtility::string2Array($pms_notify['assign_user']);
                        $assign_user = SDUtility::string2Array($modelData['assign_user']);

                        if (count($assign_user) > 0) {
                            foreach ($assign_user as $valAssign) {
                                $nonify_setting = GanttQuery::getNotifySetting($valAssign);
                                $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                                $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                                $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;


                                $remind_link = "/gantt/pms-response?page_from=other&taskid={$modelData ['id']}&ezf_id={$ezf_id}&response_ezf_id={$response_ezf_id}";
                                $remind_msg = " Today is start date of Task Item namely {$modelData ['task_name']}. You can see this Task Item within the PMS Main Task name: {$main_data['project_name']} ";
                                $remind_detail = $modelData ['detail'];
                                \dms\aomruk\classese\Notify::setNotify()->assign($valAssign)->notify($remind_msg)->url($remind_link)->detail($remind_detail)
                                        ->type_link('1')->send_email($noti_email)->data_id($modelData['id'])->send_line($noti_line)->delay_date($start_date)->sendUpdate();


                                if (!in_array($valAssign, $assign_notify)) {
                                    $assign_msg = "You have been assigned task by {$user_name}. Task Item namely {$modelData ['task_name']}. You can see this Task Item within the PMS Main Task name: {$main_data['project_name']} ";
                                    $assign_detail = $modelData ['detail'];
                                    $assign_link = "/gantt/pms-response?page_from=other&taskid={$modelData ['id']}&ezf_id={$ezf_id}&response_ezf_id={$response_ezf_id}";

                                    \dms\aomruk\classese\Notify::setNotify()->assign($valAssign)->notify($assign_msg)->url($assign_link)->detail($assign_detail)
                                            ->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();
                                }
                            }
                            $assign_notify = array_merge($assign_notify, $assign_user);
                            if (!$pms_notify) {
                                Yii::$app->db->createCommand()->insert('pms_task_notify', [
                                    'dataid' => $result['dataid'],
                                    'assign_notify' => '1',
                                    'assign_user' => SDUtility::array2String($assign_notify),
                                ])->execute();
                            } else {
                                Yii::$app->db->createCommand()->update('pms_task_notify', [
                                    'assign_notify' => '1', 'assign_user' => SDUtility::array2String($assign_notify),
                                        ], "dataid='{$result['dataid']}'")->execute();
                            }
                        }
                    } else if ($pms_notify && $pms_notify['assign_notify'] == '1') {
                        
                    }

                    if (!$pms_notify || ($pms_notify && ($pms_notify['co_owner_notify'] == '' || $pms_notify['co_owner_notify'] == '0'))) {
                        /// ========================= NOTIFY CO-OWNER =========================
                        $co_owner_notify = isset($pms_notify['co_owner_user']) ? SDUtility::string2Array($pms_notify['co_owner_user']) : null;
                        $co_owner_user = isset($modelData['co_owner']) ? SDUtility::string2Array($modelData['co_owner']) : null;
                        if (count($co_owner_user) > 0) {
                            foreach ($co_owner_user as $valCo) {
                                if (!in_array($valCo, $co_owner_notify)) {
                                    $nonify_setting = GanttQuery::getNotifySetting($valCo);
                                    $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                                    $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                                    $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;

                                    $co_owner_msg = "You have been assigned as a Co-Owner of the Task Item name: {$modelData ['task_name']} by {$user_name}. You can see this Task Item within the PMS Main Task name: {$main_data['project_name']} ";
                                    $co_owner_detail = "";
                                    $co_owner_link = "/ezmodules/ezmodule/view?id=1521801182077746900&pmsid={$main_data['id']}";
                                    \dms\aomruk\classese\Notify::setNotify()->assign($valCo)->notify($co_owner_msg)->url($co_owner_link)->detail($co_owner_detail)->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();
                                }
                            }
                            $co_owner_notify = array_merge($co_owner_notify, $co_owner_user);
                            if (!$pms_notify) {
                                Yii::$app->db->createCommand()->insert('pms_task_notify', [
                                    'dataid' => $result['dataid'],
                                    'co_owner_notify' => '1',
                                    'co_owner_user' => SDUtility::array2String($co_owner_notify),
                                ])->execute();
                            } else {
                                Yii::$app->db->createCommand()->update('pms_task_notify', [
                                    'co_owner_notify' => '1', 'co_owner_user' => SDUtility::array2String($co_owner_notify),
                                        ], "dataid='{$result['dataid']}'")->execute();
                            }
                        }
                    }
                    /// ========================= NOTIFY REVIEWER =========================
                    if (!$pms_notify || ($pms_notify && ($pms_notify['reviewer_notify'] == '' || $pms_notify['reviewer_notify'] == '0'))) {
                        $reviewer_notify = isset($modelData['reviewer_user']) ? SDUtility::string2Array($modelData['reviewer_user']) : null;
                        $reviewer_user = isset($modelData['reviewer']) ? SDUtility::string2Array($modelData['reviewer']) : null;
                        if (count($reviewer_user) > 0) {
                            foreach ($reviewer_user as $valRe) {
                                if (!in_array($valRe, $reviewer_notify)) {
                                    $nonify_setting = GanttQuery::getNotifySetting($valRe);
                                    $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                                    $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                                    $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;

                                    $reviewer_msg = "You have been assigned as a Reviewer of the Task Item name: {$modelData ['task_name']} by {$user_name}. You can see it within the PMS Main Task name: {$main_data['project_name']} ";
                                    $reviewer_detail = "";
                                    $reviewer_link = "/gantt/pms-response?page_from=other&taskid={$modelData ['id']}&ezf_id={$ezf_id}&response_ezf_id={$response_ezf_id}";
                                    \dms\aomruk\classese\Notify::setNotify()->assign($valRe)->notify($reviewer_msg)->url($reviewer_link)->detail($reviewer_detail)->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();
                                }
                            }
                            $reviewer_notify = array_merge($reviewer_notify, $reviewer_user);
                            if (!$pms_notify) {
                                Yii::$app->db->createCommand()->insert('pms_task_notify', [
                                    'dataid' => $result['dataid'],
                                    'reviewer_notify' => '1',
                                    'reviewer_user' => SDUtility::array2String($reviewer_notify),
                                ])->execute();
                            } else {
                                Yii::$app->db->createCommand()->update('pms_task_notify', [
                                    'reviewer_notify' => '1', 'reviewer_user' => SDUtility::array2String($reviewer_notify),
                                        ], "dataid='{$result['dataid']}'")->execute();
                            }
                        }
                    }
                    /// ========================= NOTIFY APPROVER =========================
                    if (!$pms_notify || ($pms_notify && ($pms_notify['approver_notify'] == '' || $pms_notify['approver_notify'] == '0'))) {
                        $approver_notify = isset($modelData['approver_user']) ? SDUtility::string2Array($modelData['approver_user']) : null;
                        $approver_user = isset($modelData['approver']) ? SDUtility::string2Array($modelData['approver']) : null;
                        if (count($approver_user) > 0) {
                            foreach ($approver_user as $valApp) {
                                if (!in_array($valApp, $approver_notify)) {
                                    $nonify_setting = GanttQuery::getNotifySetting($valApp);
                                    $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                                    $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                                    $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;

                                    $approver_msg = "You have been assigned as an Approver of the Task Item name: {$modelData ['task_name']} by {$user_name}. You can see PMS Main Task name: {$main_data['project_name']} ";
                                    $approver_detail = "";
                                    $approver_link = "/gantt/pms-response?page_from=other&taskid={$modelData ['id']}&ezf_id={$ezf_id}&response_ezf_id={$response_ezf_id}";
                                    \dms\aomruk\classese\Notify::setNotify()->assign($valApp)->notify($approver_msg)->url($approver_link)->detail($approver_detail)->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();
                                }
                            }
                            $approver_notify = array_merge($approver_notify, $approver_user);
                            if (!$pms_notify) {
                                Yii::$app->db->createCommand()->insert('pms_task_notify', [
                                    'dataid' => $result['dataid'],
                                    'approver_notify' => '1',
                                    'approver_user' => SDUtility::array2String($reviewer_notify),
                                ])->execute();
                            } else {
                                Yii::$app->db->createCommand()->update('pms_task_notify', [
                                    'approver_notify' => '1', 'approver_user' => SDUtility::array2String($reviewer_notify),
                                        ], "dataid='{$result['dataid']}'")->execute();
                            }
                        }
                    }
                    //"There are new Task Items ready for reveiwing, please see it at the Main Task name: ";
                    // Check new task for notify send to all user under project =================
                    if ($result && ($modelData ['request_type'] == '1' || $modelData ['request_type'] == '2')) {

                        if ($modelData ['credit_points'] == '' && $modelData ['reward_points'] == '') {
                            $task_count = Yii::$app->db->createCommand("SELECT count(*) as amt FROM pms_task_target WHERE IFNULL(assign_user,'')='' 
                                AND IFNULL(assign_role,'')='' AND rstat NOT IN (0,3) AND target='{$main_data['id']}' AND priority='3' AND request_type IN(1,2)
                                AND  ((IFNULL(credit_points,'')='' AND IFNULL(reward_points,'')='') OR ((IFNULL(credit_points,'')<>'' OR IFNULL(reward_points,'')<>'') AND IFNULL(approved,'')='1')) ")->queryOne();
                            $task_amt = isset($task_count ['amt']) ? $task_count ['amt'] : '0';

                            if ($check_notify && ($check_notify ['notify'] == '' || $check_notify ['notify'] == '0')) {
                                $detail_msg = "There are PMS new Task Items ready for shopping at the {$main_data['project_name']}";
                                $detail_msg2 = "PMS Main Task: {$main_data['project_name']} -> Number of Task Items ready for shopping: {$task_amt} ";
                                $userList = [];
                                $url_link = "/ezmodules/ezmodule/view?id=1521801182077746900&pmsid={$main_data['id']}";
                                $userAll = Yii::$app->db->createCommand("SELECT * FROM user WHERE IFNULL(blocked_at,'')='' ")->queryAll();
                                foreach ($userAll as $val) {
                                    $userList [] = $val ['id'];
                                }

                                foreach ($userList as $valuser) {
                                    $nonify_setting = GanttQuery::getNotifySetting($valuser);
                                    $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                                    $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                                    $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;

                                    \dms\aomruk\classese\Notify::setNotify()->assign($userList)->notify($detail_msg)->url($url_link)->detail($detail_msg2)->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();
                                }
                                Yii::$app->db->createCommand()->update('pms_task_target', [
                                    'notify' => '1'
                                        ], "dataid='{$result['dataid']}'")->execute();
                            }
                        } else {
                            if ($check_notify && ($check_notify ['notify'] == '' || $check_notify ['notify'] == '0')) {
                                $task_count = Yii::$app->db->createCommand("SELECT count(*) as amt FROM pms_task_target WHERE IFNULL(assign_user,'')='' 
                                AND IFNULL(assign_role,'')='' AND rstat NOT IN (0,3) AND target='{$main_data['id']}' AND priority='3' AND request_type IN(1,2)
                                AND  ((IFNULL(credit_points,'')='' AND IFNULL(reward_points,'')='') OR ((IFNULL(credit_points,'')<>'' OR IFNULL(reward_points,'')<>'') AND IFNULL(approved,'')='1')) ")->queryOne();
                                $task_amt = isset($task_count ['amt']) ? $task_count ['amt'] : '0';
                                $detail2_msg = "There are PMS new Task Items waiting for approval at the PMS Main Task name: {$main_data['project_name']}";
                                $detail2_msg2 = "PMS Main Task name: {$main_data['project_name']} -> Number of Task Items waiting for approval: {$task_amt} ";
                                $userDirect = [];
                                $userRole = Yii::$app->db->createCommand("SELECT * FROM zdata_matching WHERE role_name='Director' ")->queryOne();
                                if ($userRole)
                                    $userDirect = \appxq\sdii\utils\SDUtility::string2Array($userRole ['user_id']);
                                $url_link = "/ezmodules/ezmodule/view?id=1521801182077746900&pmsid={$main_data['id']}";
                                \dms\aomruk\classese\Notify::setNotify()->assign($userDirect)->notify($detail2_msg)->url($url_link)->detail($detail2_msg2)->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();

                                Yii::$app->db->createCommand()->update('pms_task_target', [
                                    'notify' => '1'
                                        ], "dataid='{$result['dataid']}'")->execute();
                            }
                        }
                    }
                }
            } else if ($type == 'sub_task') {
                $subEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
                $subModel = new \backend\modules\ezforms2\models\TbdataAll ();
                $subModel->setTableName($subEzform->ezf_table);
                $resultTarget = $subModel->findOne([
                    'id' => $dataid
                ]);
                $result = [];
                if ($resultTarget) {
                    $modelData = (array) $resultTarget->attributes;
                    $modelData ['ezf_id'] = $ezf_id;
                    $modelData ['task_name'] = $resultTarget ['cate_name'];
                    $modelData ['project_id'] = $resultTarget ['target'];
                    $modelData ['task_type'] = 'sub-task';
                    $modelData ['sub_type'] = $resultTarget ['subtask_type'];
                    $modelData ['priority'] = 2;
                    $modelData ['start_date'] = null;
                    $modelData ['finish_date'] = null;
                    $modelData ['open_node'] = isset($modelData ['open_node']) && $modelData ['open_node'] == '0' ? 0 : 1;
                    $modelData ['order_node'] = 0;
                    $result = GanttQuery::pmsTaskTargetInsert($modelData);
                }
            }

            return $result;
        } else {
            
        }
    }

    public static function actionDeletePmsTask() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $ezf_id = Yii::$app->request->post('ezf_id');
            $dataid = Yii::$app->request->post('dataid');
            $parent = Yii::$app->request->post('parent');
            $task_type = Yii::$app->request->post('task_type');

            if ($task_type == '2') {
                $taskData = SubjectManagementQuery::GetTableData("pms_task_target", [
                            'dataid' => $dataid
                ]);

                foreach ($taskData as $key => $value) {
                    $delete = Yii::$app->db->createCommand()->update("pms_task_target", [
                        'rstat' => 3
                            ], 'id=:id', [
                        ':id' => $value ['id']
                    ]);
                    if ($delete->execute()) {
                        $childTask = SubjectManagementQuery::GetTableData("pms_task_target", [
                                    'parent' => $value ['id']
                        ]);
                        foreach ($childTask as $key2 => $val) {
                            $deleteTask = Yii::$app->db->createCommand()->update("pms_task_target", [
                                'rstat' => 3
                                    ], 'id=:id', [
                                ':id' => $val ['id']
                            ]);
                            $deleteTask->execute();
                        }
                        $result ['status'] = 'success';
                        $result ['message'] = 'Success completed';
                    } else {
                        $result ['status'] = 'error';
                        $result ['message'] = 'Error !';
                    }
                }

                return $result;
            } else {
                $delete = Yii::$app->db->createCommand()->update("pms_task_target", [
                    'rstat' => 3
                        ], 'dataid=:id', [
                    ':id' => $dataid
                ]);
                if ($delete->execute()) {
                    $result ['status'] = 'success';
                    $result ['message'] = 'Success completed';
                } else {
                    $result ['status'] = 'error';
                    $result ['message'] = 'Error !';
                }

                return $result;
            }
        }
    }

    public function actionCopyAndShare() {
        $activity_ezf = Yii::$app->request->get('activity_ezf');
        $cate_ezf = Yii::$app->request->get('cate_ezf');
        $main_ezf = Yii::$app->request->get('main_ezf');
        $dataid = Yii::$app->request->get('dataid');
        $main_from = Yii::$app->request->get('project_id');
        $task_type = Yii::$app->request->get('task_type');
        $ezf_id = '';
        if ($task_type == 'task') {
            $ezf_id = $activity_ezf;
        } else {
            $ezf_id = $cate_ezf;
        }

        return $this->renderAjax('_copy_share', [
                    'ezf_id' => $ezf_id,
                    'dataid' => $dataid,
                    'task_type' => $task_type,
                    'main_ezf' => $main_ezf,
                    'cate_ezf' => $cate_ezf,
                    'main_from' => $main_from
        ]);
    }

    function actionGetSubtaskSharing() {
        $out = [];

        if (isset($_POST ['depdrop_parents'])) {
            $parents = $_POST ['depdrop_parents'];
            if ($parents != null) {
                $maintask_id = $parents [0];

                $param1 = null;
                if (!empty($_POST ['depdrop_params'])) {
                    $params = $_POST ['depdrop_params'];
                    $param1 = $params [0]; // get the value of input-type-1
                }
                $cateForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($param1);
                $result = SubjectManagementQuery::GetTableData('pms_task_target', [
                            'target' => $maintask_id,
                            'priority' => '2'
                ]);
                $dataResult = [];
                $count = 0;
                foreach ($result as $key => $value) {
                    $dataResult [$count] ['id'] = $value ['id'];
                    $dataResult [$count] ['name'] = $value ['task_name'];
                    $count ++;
                }
                return Json::encode([
                            'output' => $dataResult,
                            'selected' => ''
                ]);
            }
        }
        return Json::encode([
                    'output' => '',
                    'selected' => ''
        ]);
    }

    public function actionPmsTaskForm() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $dataid = Yii::$app->request->get('dataid');
        $modal = Yii::$app->request->get('modal');
        $initdata = Yii::$app->request->get('initdata');
        $addonForm = Yii::$app->request->get('other_ezforms');
        $addonForm = base64_decode($addonForm);
        $addonForm = \appxq\sdii\utils\SDUtility::string2Array($addonForm);
        $actForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
        $dataOther [$ezf_id] ['ezf_id'] = $ezf_id;
        $dataOther [$ezf_id] ['ezf_name'] = $actForm ['ezf_name'];
        $dataOther [$ezf_id] ['dataid'] = $dataid;

        foreach ($addonForm as $key => $value) {
            $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($value);
            $dataForm = SubjectManagementQuery::GetTableData($ezform, [
                        'target' => $dataid
                            ], 'one');

            $dataOther [$value] ['ezf_id'] = $value;
            $dataOther [$value] ['ezf_name'] = $ezform ['ezf_name'];
            $dataOther [$value] ['dataid'] = isset($dataForm ['id']) ? $dataForm ['id'] : null;
        }

        return $this->renderAjax('_pms_task_form', [
                    'ezf_id' => $ezf_id,
                    'dataid' => $dataid,
                    'dataOther' => $dataOther,
                    'modal' => $modal,
                    'initdata' => $initdata
        ]);
    }

    public function actionPmsUpdateTask() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $pms_updateid = Yii::$app->request->get('pms_updateid');
            $task_ezf = Yii::$app->request->get('task_ezf');
            $sub_ezf = Yii::$app->request->get('sub_ezf');
            $main_ezf = Yii::$app->request->get('main_ezf');
            $response_ezf = Yii::$app->request->get('response_ezf');

            $mainEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($main_ezf);
            $subEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($sub_ezf);
            $taskEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($task_ezf);
            $responseEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($response_ezf);

            $delete = " DELETE FROM pms_task_target ";
            Yii::$app->db->createCommand($delete)->execute();

            $mainData = SubjectManagementQuery::GetTableData($mainEzform);
            foreach ($mainData as $key => $value) {
                $subData = SubjectManagementQuery::GetTableData($subEzform, [
                            'target' => $value ['id']
                ]);
                foreach ($subData as $key2 => $value2) {
                    $taskData = SubjectManagementQuery::GetTableData($taskEzform, [
                                'target' => $value ['id'],
                                'category_id' => $value2 ['id']
                    ]);
                    $modelData = $value2;
                    $modelData ['ezf_id'] = $sub_ezf;
                    $modelData ['task_name'] = $value2 ['cate_name'];
                    $modelData ['project_id'] = $value2 ['target'];
                    $modelData ['task_type'] = 'sub-task';
                    $modelData ['sub_type'] = isset($value2 ['subtask_type']) ? $value2 ['subtask_type'] : '1';
                    $modelData ['priority'] = 2;
                    $modelData ['start_date'] = null;
                    $modelData ['finish_date'] = null;
                    $modelData ['open_node'] = 1;
                    $modelData ['order_node'] = GanttQuery::getNewOrderTask($value ['id'], 0);
                    $modelData ['parent'] = 0;
                    $resultSub = GanttQuery::pmsTaskTargetInsert($modelData, 'insert');
                    if (isset($resultSub ['taskid'])) {
                        foreach ($taskData as $key3 => $value3) {
                            $resData = SubjectManagementQuery::GetTableData($responseEzform, [
                                        'target' => $value3 ['id']
                                            ], 'one');

                            $modelTask = $value3;
                            $modelTask ['ezf_id'] = $task_ezf;
                            $modelTask ['project_id'] = $value3 ['target'];
                            $modelTask ['task_type'] = 'task';
                            $modelTask ['priority'] = '3';
                            $modelTask ['open_node'] = 1;
                            $modelTask ['parent'] = $resultSub ['taskid'];
                            $modelTask ['order_node'] = GanttQuery::getNewOrderTask($value ['id'], $resultSub ['taskid']);
                            $modelTask ['end_date'] = $value3 ['finish_date'];
                            $modelTask ['response_id'] = isset($resData ['id']) ? $resData ['id'] : null;
                            $modelTask ['progress'] = isset($resData ['progress']) ? $resData ['progress'] : null;
                            $modelTask ['actual_date'] = isset($resData ['actual_date']) ? $resData ['actual_date'] : null;
                            if (isset($value3 ['sent_to']) && $value3 ['sent_to'] == '1') {
                                if (isset($value3 ['sent_module_1']) && $value3 ['sent_module_1'] == '1') {
                                    $modelTask ['task_financial'] = '1';
                                    $modelTask ['task_performance'] = isset($value3 ['seg_performance']) ? $value3 ['seg_performance'] : null;
                                } else if (isset($value3 ['sent_module_2']) && $value3 ['sent_module_2'] == '1') {
                                    $modelTask ['task_type'] = 'milestone';
                                }
                            }
                            $resultTask = GanttQuery::pmsTaskTargetInsert($modelTask, 'insert');
                        }
                    }
                }
            }

            $update = Yii::$app->db_main->createCommand()->insert('pms_update_log', [
                        'project_id' => $pms_updateid,
                        'update_date' => date('Y-m-d H:i:s'),
                        'version' => '1'
                    ])->execute();
            if ($update) {
                $result ['status'] = 'success';
                $result ['message'] = 'Success completed';
            } else {
                $result ['status'] = 'error';
                $result ['message'] = 'Error !';
            }
            return $result;
        }
    }

    public function actionCloneMaintask() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $project_id = Yii::$app->request->get('project_id');
            $project_ezf_id = Yii::$app->request->get('project_ezf_id');
            $cate_ezf_id = Yii::$app->request->get('subtask_ezf_id');
            $activity_ezf_id = Yii::$app->request->get('task_ezf_id');

            $mainForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($project_ezf_id);
            $subForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($cate_ezf_id);
            $taskForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_ezf_id);

            $mainData = SubjectManagementQuery::GetTableData($mainForm, [
                        'id' => $project_id
                            ], 'one');
            $newOldId = [];
            if ($mainData) {
                $initdata = $mainData;
                $initdata ['project_name'] = $mainData ['project_name'] . "-Clone";
                $newMainId = '';
                unset($initdata ['id']);
                $modelMain = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($project_ezf_id, null, null, $initdata);
                $mainDataRes = [];
                if (isset($modelMain['data']) || isset($modelMain->attributes)) {
                    $mainDataRes = isset($modelMain['data']) ? $modelMain['data'] : $modelMain->attributes;
                    $newMainId = $mainDataRes ['id'];
                    $newOldId [$mainData ['id']] = $newMainId;
                }

                $subData = SubjectManagementQuery::GetTableData($subForm, [
                            'target' => $project_id
                ]);
                $taskMData = SubjectManagementQuery::GetTableData($subForm, "target='$project_id' AND (category_id='$project_id' OR category_id='0') ");

                if ($taskMData) {
                    foreach ($taskMData as $keyT => $valT) {
                        $initdataTaskM = $valT;
                        $initdataTaskM ['target'] = $modelMain ['data'] ['id'];
                        unset($initdataTaskM ['id']);
                        unset($initdataTaskM ['update_date']);
                        unset($initdataTaskM ['update_date']);
                        unset($initdataTaskM ['respon_role']);
                        unset($initdataTaskM ['respons_person']);

                        $dataTaskM = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($activity_ezf_id, null, $newMainId, $initdataTask);
                        if (isset($dataTaskM['data']) || isset($dataTaskM->attributes)) {
                            $dataTask = isset($dataTaskM['data']) ? $dataTaskM['data'] : $dataTaskM->attributes;
                            $newOldId [$valT ['id']] = $dataTask['id'];
                        }
                        // $taskArr = SubjectManagementQuery::GetTableData($taskForm, ['id'=>$modelMain['data']['id']], 'one');
                    }
                }

                if ($subData) {
                    foreach ($subData as $key => $value) {
                        $initdataSub = $value;
                        $initdataSub ['target'] = $modelMain ['data'] ['id'];
                        unset($initdataSub ['id']);
                        unset($initdataSub ['create_date']);
                        unset($initdataSub ['update_date']);
                        $modelSub = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($cate_ezf_id, null, $newMainId, $initdataSub);
                        if (isset($modelSub['data']) || isset($modelSub->attributes)) {
                            $subDataArr = isset($modelSub['data']) ? $modelSub['data'] : $modelSub->attributes;
                            $newOldId [$value ['id']] = $subDataArr['id'];
                        }

                        if ($modelSub) {
                            $taskData = SubjectManagementQuery::GetTableData($taskForm, [
                                        'target' => $project_id,
                                        'category_id' => $value ['id']
                            ]);

                            if ($taskData) {
                                foreach ($taskData as $keyT => $valT) {
                                    $initdataTask = $valT;
                                    $initdataTask ['target'] = $modelMain ['data'] ['id'];
                                    unset($initdataTask ['id']);
                                    unset($initdataTask ['update_date']);
                                    unset($initdataTask ['update_date']);
                                    unset($initdataTask ['respon_role']);
                                    unset($initdataTask ['respons_person']);

                                    $dataTaskRes = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($activity_ezf_id, null, $newMainId, $initdataTask);
                                    if (isset($dataTaskRes['data']) || isset($dataTaskRes->attributes)) {
                                        $dataTask = isset($dataTaskRes['data']) ? $dataTaskRes['data'] : $dataTaskRes->attributes;
                                        $newOldId [$valT ['id']] = $dataTask['id'];
                                    }
                                }
                            }
                        }
                    }
                }

                $dataSubPms = SubjectManagementQuery::GetTableData('pms_task_target', [
                            'target' => $project_id
                                ], 'all', null, [
                            'column' => 'priority',
                            'order' => 'asc'
                ]);

                if ($dataSubPms) {
                    foreach ($dataSubPms as $value) {
                        $newId = \appxq\sdii\utils\SDUtility::getMillisecTime();
                        $newOldId [$value ['id']] = $newId;
                        $value ['id'] = $newId;
                        $value ['dataid'] = isset($newOldId [$value ['dataid']]) && $newOldId [$value ['dataid']] != null ? $newOldId [$value ['dataid']] : $value ['dataid'];
                        $value ['target'] = $modelMain ['data'] ['id'];
                        $value ['parent'] = $value ['parent'] != '0' ? $newOldId [$value ['parent']] : '0';
                        $value ['progress'] = null;
                        $value ['actual_date'] = null;
                        $value ['response_id'] = null;
                        $value ['assign_user'] = null;
                        $value ['assign_role'] = null;
                        $value ['create_date'] = date('Y-m-d H:i:s');
                        $value ['update_date'] = date('Y-m-d H:i:s');
                        \Yii::$app->db->createCommand()->insert('pms_task_target', $value)->execute();
                    }
                }
                if ($modelMain) {
                    $result ['status'] = 'success';
                    $result ['message'] = 'Save complete';
                } else {
                    $result ['status'] = 'error';
                    $result ['message'] = 'Error main task';
                }
                return $result;
            }
        }
    }

    public function actionExportMaintask() {
        $project_id = Yii::$app->request->get('project_id');
        $project_ezf_id = Yii::$app->request->get('project_ezf_id');
        $cate_ezf_id = Yii::$app->request->get('cate_ezf_id');
        $activity_ezf_id = Yii::$app->request->get('activity_ezf_id');
        $response_ezf_id = Yii::$app->request->get('response_ezf_id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');

        $mainForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($project_ezf_id);
        $subForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($cate_ezf_id);
        $taskForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_ezf_id);
        $resForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($response_ezf_id);

        $queryMain = new \backend\modules\ezforms2\models\TbdataAll ();
        $queryMain->setTableName($mainForm ['ezf_table']);
        $mainData = $queryMain->find()->where([
                    'id' => $project_id
                ])->all();
        $mainHead = $mainData [0]->attributes;

        $querySub = new \backend\modules\ezforms2\models\TbdataAll ();
        $querySub->setTableName($subForm ['ezf_table']);
        $subData = $querySub->find()->where("target='$project_id' AND rstat NOT IN(0,3)")->all();
        $subHead = $subData [0]->attributes;

        $queryTask = new \backend\modules\ezforms2\models\TbdataAll ();
        $queryTask->setTableName($taskForm ['ezf_table']);
        $taskData = $queryTask->find()->where("target='$project_id' AND rstat NOT IN(0,3)")->all();
        $taskHead = $taskData [0]->attributes;

        $taskAllId = [];
        foreach ($taskData as $val) {
            $taskAllId [] = $val ['id'];
        }

        $queryRes = new \backend\modules\ezforms2\models\TbdataAll ();
        $queryRes->setTableName($resForm ['ezf_table']);
        $resData = $queryRes->find()->where("target IN (" . join($taskAllId, ',') . ") AND rstat NOT IN(0,3)")->all();
        $resHead = $resData [0]->attributes;

        $queryPms = new \backend\modules\gantt\models\PmsTaskTarget ();
        $pmsData = $queryPms->find()->where("target='$project_id' AND rstat NOT IN(0,3)")->all();
        $pmsHead = $pmsData [0]->attributes;

        $dataMulti = [
            'sheet_maintask' => $mainData,
            'sheet_subtask' => $subData,
            'sheet_task' => $taskData,
            'sheet_response' => $resData,
            'sheet_pms_target' => $pmsData
        ];
        $headers = [
            'sheet_maintask' => $mainHead,
            'sheet_subtask' => $subHead,
            'sheet_task' => $taskHead,
            'sheet_response' => $resHead,
            'sheet_pms_target' => $pmsHead
        ];

        $fileName = \backend\modules\subjects\classes\ExportexcelFunc::ExportExcelMultiSh('PMS-' . $mainHead ['project_name'], $headers, $dataMulti);
        $this->redirect(Yii::getAlias('@web/print/') . $fileName);
    }

    public function actionRestoreModal() {
        $project_ezf_id = Yii::$app->request->get('project_ezf_id');
        $subtask_ezf_id = Yii::$app->request->get('subtask_ezf_id');
        $task_ezf_id = Yii::$app->request->get('task_ezf_id');
        $response_ezf_id = Yii::$app->request->get('response_ezf_id');

        return $this->renderAjax('gantt-maintask-restore', [
                    'project_ezf_id' => $project_ezf_id,
                    'subtask_ezf_id' => $subtask_ezf_id,
                    'task_ezf_id' => $task_ezf_id,
                    'response_ezf_id' => $response_ezf_id
        ]);
    }

    public function actionRestoreMaintask() {
        $project_ezf_id = Yii::$app->request->post('project_ezf_id');
        $subtask_ezf_id = Yii::$app->request->post('subtask_ezf_id');
        $task_ezf_id = Yii::$app->request->post('task_ezf_id');
        $response_ezf_id = Yii::$app->request->post('response_ezf_id');

        $excel_path = $_FILES ['excel_file'];
        $user_id = \Yii::$app->user->identity->id;
        $sitecode = \Yii::$app->user->identity->profile->sitecode;
        $header = true;
        $filePath = $excel_path ['tmp_name'];
        $arrList = array();
        $sum = [];
        if (isset($excel_path ['name']) && $excel_path ['name'] != '') {
            ini_set('max_execution_time', 0);
            set_time_limit(0);
            ini_set('memory_limit', '256M');

            $excel_file = UploadedFile::getInstanceByName('excel_file');

            if ($excel_file) {
                $arrList = \moonland\phpexcel\Excel::import($excel_file->tempName, [
                            'setFirstRecordAsKeys' => true,
                            'setIndexSheetByName' => true
                                // 'getOnlySheet' => 'sheet1',
                ]);
            }
        }
        $response ['success'] = 0;
        $response ['fail'] = 0;

        if ($arrList) {
            $nowDate = date('Y-m-d H:i:s');
            $data = [];
            if (!isset($arrList ['sheet_maintask'] [0])) {
                return "<code>Ops! Content file is not match</code> ";
            }
            $mainArray = $arrList ['sheet_maintask'] [0];
            $subArray = $arrList ['sheet_subtask'];
            $taskArray = $arrList ['sheet_task'];
            $resArray = $arrList ['sheet_response'];
            $pmsArray = $arrList ['sheet_pms_target'];

            $newOldId = [];
            $newMainId = null;
            $oldMainId = $mainArray ['id'];
            $mainArray ['project_name'] = $mainArray ['project_name'] . "-Backup";
            $mainArray ['sitecode'] = $sitecode;
            $mainArray ['hsitecode'] = $sitecode;
            $mainArray ['xsourcex'] = $sitecode;
            $mainArray ['rstat'] = '1';
            $mainArray ['created_at'] = $nowDate;
            $mainArray ['created_by'] = $user_id . '';
            $mainArray ['update_at'] = $nowDate;
            $mainArray ['update_by'] = $user_id . '';
            $newOldId [$oldMainId] = '';
            unset($mainArray ['id']);
            $resMain = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($project_ezf_id, null, null, $mainArray);

            if (isset($resMain ['data']) || isset($resMain->attributes)) {
                $mainData = isset($resMain ['data']) ? $resMain ['data'] : $resMain->attributes;
                $newMainId = $mainData['id'];
                $newOldId [$oldMainId] = $newMainId;
            }

            foreach ($subArray as $val) {
                $subOldId = $val ['id'];
                $val ['target'] = $newMainId;
                $val ['ptid'] = $newMainId;
                $val ['project_id'] = $newMainId;
                $val ['sitecode'] = $sitecode;
                $val ['hsitecode'] = $sitecode;
                $val ['xsourcex'] = $sitecode;
                $val ['rstat'] = '1';
                $val ['created_at'] = $nowDate;
                $val ['created_by'] = $user_id . '';
                $val ['update_at'] = $nowDate;
                $val ['update_by'] = $user_id . '';
                $newOldId [$subOldId] = '';
                unset($val ['id']);
                $resSub = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($subtask_ezf_id, null, $newMainId, $val);
                if (isset($resSub ['data']) || isset($resSub->attributes)) {
                    $subData = isset($resSub ['data']) ? $resSub ['data'] : $resSub->attributes;
                    $subNewId = $subData['id'];
                    $newOldId [$subOldId] = $subNewId;
                }


                // =========================================
            }
            if ($taskArray) {
                foreach ($taskArray as $valT) {
                    $taskOldId = $valT ['id'];
                    $valT ['target'] = $newMainId;
                    $valT ['ptid'] = $newMainId;
                    $valT ['project_id'] = $newMainId;
                    $valT ['category_id'] = $valT ['category_id'] != '0' ? $newOldId [$valT ['category_id']] : '0';
                    $valT ['sitecode'] = $sitecode;
                    $valT ['rstat'] = '1';
                    $valT ['created_at'] = $nowDate;
                    $valT ['created_by'] = $user_id . '';
                    $valT ['update_at'] = $nowDate;
                    $valT ['update_by'] = $user_id . '';
                    $newOldId [$taskOldId] = '';
                    unset($valT ['id']);

                    $resTask = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($task_ezf_id, null, $newMainId, $valT);
                    if (isset($resTask ['data']) || isset($resTask->attributes)) {
                        $taskData = isset($resTask ['data']) ? $resTask ['data'] : $resTask->attributes;
                        $taskNewId = $taskData ['id'];
                        $newOldId [$taskOldId] = $taskNewId;
                    }
                }
            }
            if ($resArray) {
                $resOldId = '';
                foreach ($resArray as $valR) {
                    $resOldId = $valR ['id'];
                    $valR ['target'] = $newOldId [$valR ['target']];
                    $valR ['sitecode'] = $sitecode;
                    $valR ['rstat'] = '1';
                    $valR ['created_at'] = $nowDate;
                    $valR ['created_by'] = $user_id . '';
                    $valR ['update_at'] = $nowDate;
                    $valR ['update_by'] = $user_id . '';
                    unset($valR ['id']);
                    $newOldId [$resOldId] = '';
                    $dataRes = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($response_ezf_id, null, $valR ['target'], $valR);
                    if (isset($dataRes ['data']) || isset($dataRes->attributes)) {
                        $resData = isset($dataRes ['data']) ? $dataRes ['data'] : $dataRes->attributes;
                        $resNewId = $resData ['id'];
                        $newOldId [$resOldId] = $resNewId;
                    }
                }
            }
            if ($pmsArray) {
                foreach ($pmsArray as $value) {
                    $newId = \appxq\sdii\utils\SDUtility::getMillisecTime();
                    $newOldId [$value ['id']] = $newId;
                    $value ['id'] = $newId;
                    $value ['dataid'] = isset($newOldId [$value ['dataid']]) && $newOldId [$value ['dataid']] != null ? $newOldId [$value ['dataid']] : $value ['dataid'];
                    $value ['response_id'] = isset($newOldId [$value ['response_id']]) && $newOldId [$value ['response_id']] != null ? $newOldId [$value ['response_id']] : $value ['response_id'];
                    $value ['target'] = $newMainId;
                    $value ['parent'] = $value ['parent'] != '0' ? $newOldId [$value ['parent']] : '0';
                    \Yii::$app->db->createCommand()->insert('pms_task_target', $value)->execute();
                }
            }
        }

        return "success";
    }

    public function actionBackupMaintask() {
        if (Yii::$app->getRequest()->isAjax) {
            $project_id = Yii::$app->request->get('project_id');
            $project_ezf_id = Yii::$app->request->get('project_ezf_id');
            $cate_ezf_id = Yii::$app->request->get('subtask_ezf_id');
            $activity_ezf_id = Yii::$app->request->get('task_ezf_id');
            $response_ezf_id = Yii::$app->request->get('response_ezf_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');

            // === Export Data ===
            $url = \yii\helpers\Url::to([
                        '/gantt/gantt/export-maintask',
                        'reloadDiv' => $reloadDiv,
                        'project_id' => $project_id,
                        'project_ezf_id' => $project_ezf_id,
                        'cate_ezf_id' => $cate_ezf_id,
                        'activity_ezf_id' => $activity_ezf_id,
                        'response_ezf_id' => $response_ezf_id
            ]);

            $protocol = ((!empty($_SERVER ['HTTPS']) && $_SERVER ['HTTPS'] != 'off') || $_SERVER ['SERVER_PORT'] == 443) ? "https://" : "http://";
            $html = '<iframe src="' . $protocol . getenv('HTTP_HOST') . $url . '" width="100%" height="200px" />';

            $result = [
                'status' => 'success',
                'action' => 'report',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Load completed.'),
                'html' => $html
            ];

            return \yii\helpers\Json::encode($result);
        } else {
            throw new \yii\web\NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionRequestTaskSave() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $task_dataid = Yii::$app->request->get('task_dataid');
        $task_owner = Yii::$app->request->get('task_owner');
        $task_ezf_id = Yii::$app->request->get('task_ezf_id');
        $module_id = Yii::$app->request->get('module_id');
        $target = Yii::$app->request->get('target');
        $user_id = Yii::$app->user->id;

        $initdata = [
            'respons_person' => SDUtility::array2String([$user_id]),
        ];

        $profile = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($user_id);
        $taskUpdate = \Yii::$app->db->createCommand()->update('zdata_activity', $initdata, "id='{$task_dataid}'")->execute();
        $mainData = SubjectManagementQuery::GetTableData('zdata_project', [
                    'id' => $target
                        ], 'one');
        $pmsData = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', "rstat NOT IN(0,3) AND dataid='{$task_dataid}'", 'one');
        $now = date('Y-m-d H:i:s');
        $response = null;
        $url_link = "/ezmodules/ezmodule/view?id={$module_id}&pmsid={$target}";
        $detail = "Task {$pmsData['task_name']} in the {$mainData['project_name']} is currently processing by {$profile['name']}  at $now ";
        if ($taskUpdate) {
            $response = \Yii::$app->db->createCommand()->update('pms_task_target', [
                'assign_user' => \appxq\sdii\utils\SDUtility::array2String([
                    $user_id
                ]),
                'assign_user_accept' => \appxq\sdii\utils\SDUtility::array2String([
                    $user_id
                ])
                    ], "dataid='$task_dataid'");
            if (!is_array($task_owner)) {
                $nonify_setting = GanttQuery::getNotifySetting($task_owner);
                $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;

                \dms\aomruk\classese\Notify::setNotify()->assign($task_owner)->notify($detail)->url($url_link)->detail('')->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();
            } else {
                foreach ($task_owner as $valown) {
                    $nonify_setting = GanttQuery::getNotifySetting($valown);
                    $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                    $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                    $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;

                    \dms\aomruk\classese\Notify::setNotify()->assign($valown)->notify($detail)->url($url_link)->detail('')->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();
                }
            }
        }

        if ($response->execute()) {
            $result = [
                'status' => 'success',
                'action' => 'report',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.')
            ];
        } else {
            $result = [
                'status' => 'error',
                'action' => 'report',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Save fail!.')
            ];
        }

        return $result;
    }

    public function actionRequestApproveSave() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $task_dataid = Yii::$app->request->get('task_dataid');
        $dataid = Yii::$app->request->get('dataid');
        $task_owner = Yii::$app->request->get('task_owner');
        $task_ezf_id = Yii::$app->request->get('task_ezf_id');
        $module_id = Yii::$app->request->get('module_id');
        $target = Yii::$app->request->get('target');
        $user_id = Yii::$app->user->id;
        $request_ezf = \backend\modules\gantt\Module::$formsId ['request_task_form'];

        $reqData = SubjectManagementQuery::GetTableData('zdata_request_task', [
                    'id' => $dataid
                        ], 'one');
        $user_request = $reqData ['user_create'];

        $initdata = [
            'respons_person' => SDUtility::array2String([$user_request]),
        ];
        $initdata2 = [
            'status_request' => '2'
        ];
        $profile = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($user_id);

        $taskUpdate = \Yii::$app->db->createCommand()->update('zdata_activity', $initdata, "id='{$task_dataid}'")->execute();
        $reqUpdate = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($request_ezf, $dataid, $task_dataid, $initdata2);
        if (isset($reqUpdate ['data'] ['id'])) {
            \Yii::$app->db->createCommand()->update('zdata_request_task', [
                'status_request' => '3'
                    ], "id<>'{$dataid}'")->execute();
        }
        $taskData = SubjectManagementQuery::GetTableData('zdata_activity', [
                    'id' => $task_dataid
                        ], 'one');
        $mainData = SubjectManagementQuery::GetTableData('zdata_project', [
                    'id' => $target
                        ], 'one');
        $pmsData = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', "rstat NOT IN(0,3) AND dataid='{$taskData['id']}'", 'one');

        $now = date('Y-m-d H:i:s');
        $response = null;
        $url_link = "/ezmodules/ezmodule/view?id={$module_id}&pmsid={$target}";
        $detail = "Task {$taskData['task_name']} in the {$mainData['project_name']} is already approved  at $now ";
        if ($taskUpdate) {
            $response = \Yii::$app->db->createCommand()->update('pms_task_target', [
                'assign_user' => \appxq\sdii\utils\SDUtility::array2String([
                    $user_request
                ]),
                'assign_user_accept' => \appxq\sdii\utils\SDUtility::array2String([
                    $user_request
                ])
                    ], "dataid='$task_dataid'");
            
             $nonify_setting = GanttQuery::getNotifySetting($user_id);
                    $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                    $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                    $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;
            \dms\aomruk\classese\Notify::setNotify()->assign($user_id)->notify($detail)->url($url_link)->detail('')->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();
        }

        if ($response->execute()) {
            $result = [
                'status' => 'success',
                'action' => 'report',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.')
            ];
        } else {
            $result = [
                'status' => 'error',
                'action' => 'report',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Save fail!.')
            ];
        }

        return $result;
    }

    public function actionRequestTaskSend() {
        $modelData = Yii::$app->request->get('dataModels');
        $ezf_id = Yii::$app->request->get('ezf_id');
        $target = Yii::$app->request->get('target');
        $user_id = Yii::$app->user->id;

        $now = date('Y-m-d H:i:s');
        $profile = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($user_id);
        $taskData = SubjectManagementQuery::GetTableData('zdata_activity', [
                    'id' => $target
                        ], 'one');

        $mainData = SubjectManagementQuery::GetTableData('zdata_project', [
                    'id' => $taskData['target']
                        ], 'one');
        $url_link = "/ezmodules/ezmodule/view?id=1521801182077746900&pmsid={$mainData['target']}";
        $detail = "Task {$taskData['task_name']} in the {$mainData['project_name']} has been voluntarily requested by {$profile['firstname']} {$profile['lastname']}  at $now ";
        $request_detail = $modelData['request_detail'];
        if ($taskData) {
            
            $nonify_setting = GanttQuery::getNotifySetting($taskData ['user_create']);
                    $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                    $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                    $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;
            \dms\aomruk\classese\Notify::setNotify()->assign($taskData ['user_create'])->notify($detail)->url($url_link)->detail($request_detail)->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();
        }
    }

    public function actionRequestList() {
        $task_dataid = Yii::$app->request->get('task_dataid');
        $taskid = Yii::$app->request->get('taskid');
        $request_ezf_id = Yii::$app->request->get('request_ezf_id');
        $module_id = Yii::$app->request->get('module_id');
        $project_id = Yii::$app->request->get('project_id');
        $task_ezf_id = Yii::$app->request->get('task_ezf_id');

        return $this->renderAjax('_request_list', [
                    'request_ezf_id' => $request_ezf_id,
                    'task_dataid' => $task_dataid,
                    'taskid' => $taskid,
                    'task_ezf_id' => $task_ezf_id,
                    'project_id' => $project_id,
                    'module_id' => $module_id
        ]);
    }

    public function actionRequestGrid() {
        $task_dataid = Yii::$app->request->get('task_dataid');
        $taskid = Yii::$app->request->get('taskid');
        $request_ezf_id = Yii::$app->request->get('request_ezf_id');
        $module_id = Yii::$app->request->get('module_id');
        $project_id = Yii::$app->request->get('project_id');
        $task_ezf_id = Yii::$app->request->get('task_ezf_id');
        $request_form = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne(\backend\modules\gantt\Module::$formsId ['request_task_form']);
        $query = SubjectManagementQuery::GetTableData($request_form, [
                    'target' => $task_dataid
                        ], 'all', null, [
                    'column' => 'create_date',
                    'order' => 'desc'
        ]);

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        return $this->renderAjax('_request_list_grid', [
                    'request_ezf_id' => $request_ezf_id,
                    'dataProvider' => $dataProvider,
                    'task_dataid' => $task_dataid,
                    'taskid' => $taskid,
                    'task_ezf_id' => $task_ezf_id,
                    'project_id' => $project_id,
                    'module_id' => $module_id
        ]);
    }

    public function actionRejectRequestSave() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $dataid = Yii::$app->request->get('dataid');
        $ezf_id = Yii::$app->request->get('ezf_id');
        $target = Yii::$app->request->get('target');
        $user_id = Yii::$app->user->id;
        $request_ezf = \backend\modules\gantt\Module::$formsId ['request_task_form'];
        $requestData = SubjectManagementQuery::GetTableData('zdata_request_task', [
                    'id' => $target
                        ], 'one');
        $taskData = SubjectManagementQuery::GetTableData('zdata_activity', [
                    'id' => $requestData ['target']
                        ], 'one');
        $mainData = SubjectManagementQuery::GetTableData('zdata_project', [
                    'id' => $taskData ['target']
                        ], 'one');
        $initdata = [
            'status_request' => '3'
        ];
        $requestUpdate = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($request_ezf, $target, null, $initdata);

        $now = date('Y-m-d H:i:s');
        $response = null;
        // $url_link = "/ezmodules/ezmodule/view?id={$module_id}&pmsid={$target}";
        $detail = "Task Item namely {$taskData['task_name']} under Main Task {$mainData['project_name']} is reject request  at $now ";
        if (isset($requestUpdate ['data'] ['id'])) {
            $result = [
                'status' => 'success',
                'action' => 'report',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.')
            ];
            
            $nonify_setting = GanttQuery::getNotifySetting($taskData ['user_create']);
                    $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                    $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                    $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;
            \dms\aomruk\classese\Notify::setNotify()->assign($requestData ['user_create'])->notify($detail)->detail('')->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();
        } else {
            $result = [
                'status' => 'error',
                'action' => 'report',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Save fail!.')
            ];
        }

        return $result;
    }

    public static function actionApproveTaskDirector() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $task_dataid = Yii::$app->request->get('task_dataid');
        $taskid = Yii::$app->request->get('taskid');
        $task_ezf_id = Yii::$app->request->get('task_ezf_id');
        $target = Yii::$app->request->get('target');
        $module_id = Yii::$app->request->get('module_id');
        $user_id = Yii::$app->user->id;

        $profile = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($user_id);
        $taskData = SubjectManagementQuery::GetTableDataNotEzform('zdata_activity', " id='$task_dataid'", 'one');
        $mainData = SubjectManagementQuery::GetTableDataNotEzform('zdata_project', [
                    'id' => $taskData ['target']
                        ], 'one');
        // $pmsData = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', "rstat NOT IN(0,3) AND dataid='{$task_dataid}'", 'one');

        $detail_msg = "Task name {$taskData['task_name']} approved by director name {$profile['firstname']} {$profile['lastname']} you can see in PMS project name is {$mainData['project_name']}";
        $detail_msg2 = "";
        $userList = [];
        $url_link = "/ezmodules/ezmodule/view?id=1521801182077746900&pmsid={$mainData['id']}";

        \dms\aomruk\classese\Notify::setNotify()->assign($taskData ['user_create'])->notify($detail_msg)->url($url_link)->detail($detail_msg2)->type_link('1')->send_email(true)->send_line(true)->sendStatic();

        $response = Yii::$app->db->createCommand()->update('pms_task_target', [
            'approved' => '1',
            'user_approve' => $user_id
                ], "dataid='{$task_dataid}'");

        if ($response->execute()) {
            if ($taskData['assign_user_accept'] == '' && $taskData['request_type'] != '0') {
                $task_count = Yii::$app->db->createCommand("SELECT count(*) as amt FROM pms_task_target WHERE IFNULL(assign_user,'')='' AND IFNULL(assign_role,'')='' AND rstat NOT IN (0,3) AND target='{$mainData['id']}' AND priority='3' ")->queryOne();
                $task_amt = isset($task_count ['amt']) ? $task_count ['amt'] : '0';
                $detail_msg = "Have the new Task Item ready for shopping you can see in Main Task namely {$mainData['project_name']}";
                $detail_msg2 = "Main Task namely {$mainData['project_name']} has been task ready to shopping {$task_amt} ";
                $userList = [];
                $url_link = "/ezmodules/ezmodule/view?id=1521801182077746900&pmsid={$mainData['id']}";
                $userAll = Yii::$app->db->createCommand("SELECT * FROM user WHERE IFNULL(blocked_at,'')='' ")->queryAll();
                foreach ($userAll as $val) {
                    $userList [] = $val ['id'];
                }

                \dms\aomruk\classese\Notify::setNotify()->assign($userList)->notify($detail_msg)->url($url_link)->detail($detail_msg2)->type_link('1')->send_email(true)->send_line(true)->sendStatic();
            }

            $result = [
                'status' => 'success',
                'action' => 'report',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.')
            ];
        } else {
            $result = [
                'status' => 'error',
                'action' => 'report',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Save fail!.')
            ];
        }

        return $result;
    }

    public static function actionDeclineTask() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $modelData = Yii::$app->request->post('dataModels');
        $ezf_id = Yii::$app->request->post('ezf_id');
        $user_id = Yii::$app->user->id;

        $profile = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($user_id);
        $taskData = SubjectManagementQuery::GetTableDataNotEzform('zdata_activity', [
                    'id' => $modelData ['target']
                        ], 'one');
        $mainData = SubjectManagementQuery::GetTableDataNotEzform('zdata_project', [
                    'id' => $taskData ['target']
                        ], 'one');
        $pmsData = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', "rstat NOT IN(0,3) AND dataid='{$taskData['id']}'", 'one');

        $detail_msg = "The Task Item namely {$taskData['task_name']} assigned to {$profile['firstname']} {$profile['lastname']} has been declined. This task is under the PMS Main Task name: {$mainData['project_name']}";
        $detail_msg2 = $modelData ['decline_detail'];

        $url_link = "/ezmodules/ezmodule/view?id=1521801182077746900&pmsid={$mainData['id']}";
        $assignUser = \appxq\sdii\utils\SDUtility::string2Array($pmsData ['assign_user']);
        if ($assignUser && is_array($assignUser)) {
            $assignUser = array_diff($assignUser, [$user_id]);
        }
        if ($modelData && $modelData ['task_refer'] == '1') {
            $referUser = $modelData ['refer_user'];
            $profileRefer = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($referUser);
            $detail_msg = "The Task Item namely {$taskData['task_name']} assigned to {$profile['firstname']} {$profile['lastname']} has been referred to {$profileRefer['firstname']} {$profileRefer['lastname']}. This task is under the Main Task name: {$mainData['project_name']}";
            $refer_msg = "The Task Item namely {$taskData['task_name']} has been referred by {$profile['firstname']} {$profile['lastname']} to you. This task is under the PMS Main Task name: {$mainData['project_name']} ";

            $assignUser [] = $referUser;

            \dms\aomruk\classese\Notify::setNotify()->assign($referUser)->notify($refer_msg)->url($url_link)->type_link('1')->send_email(true)->send_line(true)->sendStatic();
        }

        \Yii::$app->db->createCommand()->update('pms_task_target', [
            'assign_user' => \appxq\sdii\utils\SDUtility::array2String($assignUser)
                ], "dataid='{$taskData['id']}'")->execute();
        $response = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($pmsData ['ezf_id'], $pmsData ['dataid'], null, [
                    'assign_user' => \appxq\sdii\utils\SDUtility::array2String($assignUser)
        ]);

        $nonify_setting = GanttQuery::getNotifySetting($taskData ['user_create']);
                    $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                    $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                    $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;
        \dms\aomruk\classese\Notify::setNotify()->assign($taskData ['user_create'])->notify($detail_msg)->url($url_link)->detail($detail_msg2)->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();

        if ($response ['data'] ['id']) {
            $result = [
                'status' => 'success',
                'action' => 'report',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.')
            ];
        } else {
            $result = [
                'status' => 'error',
                'action' => 'report',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Save fail!.')
            ];
        }

        return $result;
    }

    public static function actionReferTask() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $modelData = Yii::$app->request->post('dataModels');
        $ezf_id = Yii::$app->request->post('ezf_id');
        $user_id = Yii::$app->user->id;

        $profile = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($user_id);
        $taskData = SubjectManagementQuery::GetTableData('zdata_activity', [
                    'id' => $modelData ['target']
                        ], 'one');
        $mainData = SubjectManagementQuery::GetTableData('zdata_project', [
                    'id' => $taskData ['target']
                        ], 'one');

        $referUser = $modelData ['refer_user'];
        $profileRefer = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($referUser);

        $pmsData = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', "rstat NOT IN(0,3) AND dataid='{$taskData['id']}'", 'one');

        $detail_msg = "The Task Item namely {$taskData['task_name']} has been referred from {$profile['firstname']} {$profile['lastname']} to {$profileRefer['firstname']} {$profileRefer['lastname']}. This task is under the PMS Main Task name: {$mainData['project_name']}";

        $refer_msg = "The Task Item namely {$taskData['task_name']} has been referred by {$profile['firstname']} {$profile['lastname']} to you. This task is under the PMS Main Task name: {$mainData['project_name']} ";
        $refer_msg2 = $modelData ['refer_detail'];

        $url_link = "/ezmodules/ezmodule/view?id=1521801182077746900&pmsid={$mainData['id']}";

        $assignUser = \appxq\sdii\utils\SDUtility::string2Array($pmsData ['assign_user']);
        $assignUserAcc = \appxq\sdii\utils\SDUtility::string2Array($pmsData ['assign_user_accept']);

        if ($assignUser && is_array($assignUser)) {
            $assignUser = array_diff($assignUser, [$user_id]);
        }

        $assignUser [] = $referUser;

        if ($assignUserAcc && is_array($assignUserAcc)) {
            $assignUserAcc = array_diff($assignUserAcc, [$user_id]);
        }

        \dms\aomruk\classese\Notify::setNotify()->assign($referUser)->notify($refer_msg)->detail($refer_msg2)->url($url_link)->type_link('1')->send_email(true)->send_line(true)->sendStatic();

        \Yii::$app->db->createCommand()->update('pms_task_target', [
            'assign_user' => \appxq\sdii\utils\SDUtility::array2String($assignUser),
            'assign_user_accept' => \appxq\sdii\utils\SDUtility::array2String($assignUserAcc),
            'task_status' => ''
                ], "dataid='{$taskData['id']}'")->execute();
        $response = \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($pmsData ['ezf_id'], $pmsData ['dataid'], null, [
                    'assign_user' => \appxq\sdii\utils\SDUtility::array2String($assignUser),
        ]);

        $nonify_setting = GanttQuery::getNotifySetting($taskData ['user_create']);
                    $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                    $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                    $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;
        \dms\aomruk\classese\Notify::setNotify()->assign($taskData ['user_create'])->notify($detail_msg)->url($url_link)->detail("")->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();

        if ($response ['data'] ['id']) {
            $result = [
                'status' => 'success',
                'action' => 'report',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.')
            ];
        } else {
            $result = [
                'status' => 'error',
                'action' => 'report',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Save fail!.')
            ];
        }

        return $result;
    }

    public static function actionAcceptTask() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $task_dataid = Yii::$app->request->get('task_dataid');
        $taskid = Yii::$app->request->get('taskid');
        $task_ezf_id = Yii::$app->request->get('task_ezf_id');
        $target = Yii::$app->request->get('target');
        $module_id = Yii::$app->request->get('module_id');
        $user_id = Yii::$app->user->id;

        $profile = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($user_id);
        $taskData = SubjectManagementQuery::GetTableDataNotEzform('zdata_activity', [
                    'id' => $task_dataid
                        ], 'one');
        $mainData = SubjectManagementQuery::GetTableDataNotEzform('zdata_project', [
                    'id' => $taskData ['target']
                        ], 'one');
        $pmsData = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', "rstat NOT IN(0,3) AND id='{$taskid}'", 'one');

        $detail_msg = "The Task Item namely {$taskData['task_name']} has been accepted by {$profile['firstname']} {$profile['lastname']}. This task is under the PMS Main Task name: {$mainData['project_name']}";
        $detail_msg2 = "";

        $url_link = "/ezmodules/ezmodule/view?id=1521801182077746900&pmsid={$mainData['id']}";

        $nonify_setting = GanttQuery::getNotifySetting($taskData ['user_create']);
                    $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                    $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                    $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;
        \dms\aomruk\classese\Notify::setNotify()->assign($taskData ['user_create'])->notify($detail_msg)->url($url_link)->detail($detail_msg2)->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();

        $assignUser = \appxq\sdii\utils\SDUtility::string2Array($pmsData ['assign_user_accept']);
        $assignUser [] = $user_id;

        if (isset($pmsData['task_status']) && $pmsData['task_status'] == '' || $pmsData['task_status'] == '5' || $pmsData['task_status'] == '4') {
            $task_status = '1';
        } else {
            $task_status = $pmsData['task_status'];
        }
        $response = \Yii::$app->db->createCommand()->update('pms_task_target', [
            'assign_user_accept' => \appxq\sdii\utils\SDUtility::array2String($assignUser),
            'task_status' => $task_status
                ], "dataid='{$task_dataid}'");

        if ($response->execute()) {
            $result = [
                'status' => 'success',
                'action' => 'report',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.')
            ];
        } else {
            $result = [
                'status' => 'error',
                'action' => 'report',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Save fail!.')
            ];
        }

        return $result;
    }

    public static function actionTerminateTask() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $task_dataid = Yii::$app->request->get('task_dataid');
        $taskid = Yii::$app->request->get('taskid');
        $task_ezf_id = Yii::$app->request->get('task_ezf_id');
        $target = Yii::$app->request->get('target');
        $module_id = Yii::$app->request->get('module_id');
        $user_id = Yii::$app->user->id;

        $profile = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($user_id);
        $taskData = SubjectManagementQuery::GetTableData('zdata_activity', [
                    'id' => $task_dataid
                        ], 'one');
        $mainData = SubjectManagementQuery::GetTableData('zdata_project', [
                    'id' => $taskData ['target']
                        ], 'one');
        $pmsData = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', "rstat NOT IN(0,3) AND id='{$taskid}'", 'one');

        $detail_msg = "The Task Item namely {$taskData['task_name']} has been abandoned prematurely by {$profile['firstname']} {$profile['lastname']}. This task is under the PMS Main Task name: {$mainData['project_name']}";
        $detail_msg2 = "";

        $url_link = "/ezmodules/ezmodule/view?id=1521801182077746900&pmsid={$mainData['id']}";

        $nonify_setting = GanttQuery::getNotifySetting($taskData ['user_create']);
                    $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                    $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                    $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;
        \dms\aomruk\classese\Notify::setNotify()->assign($taskData ['user_create'])->notify($detail_msg)->url($url_link)->detail($detail_msg2)->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();

        $assignUser = \appxq\sdii\utils\SDUtility::string2Array($pmsData ['assign_user']);
        $assignUserAcc = \appxq\sdii\utils\SDUtility::string2Array($pmsData ['assign_user_accept']);
        $user_terminate = \appxq\sdii\utils\SDUtility::string2Array($pmsData ['user_terminate']);
        if ($assignUser && is_array($assignUser)) {
            $assignUser = array_diff($assignUser, [$user_id]);
        }

        if ($assignUserAcc && is_array($assignUserAcc)) {
            $assignUserAcc = array_diff($assignUserAcc, [$user_id]);
        }

        $user_terminate [] = $user_id;

        $response = \Yii::$app->db->createCommand()->update('pms_task_target', [
            'assign_user_accept' => \appxq\sdii\utils\SDUtility::array2String($assignUserAcc),
            'assign_user' => \appxq\sdii\utils\SDUtility::array2String($assignUser),
            'user_terminate' => \appxq\sdii\utils\SDUtility::array2String($user_terminate),
            'task_status' => '4'
                ], "dataid='{$task_dataid}'");
        \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($pmsData ['ezf_id'], $pmsData ['dataid'], null, [
            'assign_user' => \appxq\sdii\utils\SDUtility::array2String($assignUser),
        ]);

        if ($response->execute()) {
            $result = [
                'status' => 'success',
                'action' => 'report',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.')
            ];
        } else {
            $result = [
                'status' => 'error',
                'action' => 'report',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Save fail!.')
            ];
        }

        return $result;
    }

    public static function actionPendingTask() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $task_dataid = Yii::$app->request->get('task_dataid');
        $taskid = Yii::$app->request->get('taskid');
        $task_ezf_id = Yii::$app->request->get('task_ezf_id');
        $target = Yii::$app->request->get('target');
        $module_id = Yii::$app->request->get('module_id');
        $user_id = Yii::$app->user->id;

        $profile = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($user_id);
        $taskData = SubjectManagementQuery::GetTableData('zdata_activity', [
                    'id' => $task_dataid
                        ], 'one');
        $mainData = SubjectManagementQuery::GetTableData('zdata_project', [
                    'id' => $taskData ['target']
                        ], 'one');
        $pmsData = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', "rstat NOT IN(0,3) AND id='{$taskid}'", 'one');

        $detail_msg = "The Task Item namely {$taskData['task_name']} assigned to {$profile['firstname']} {$profile['lastname']} has been pending. This task is under the PMS Main Task name: {$mainData['project_name']}";
        $detail_msg2 = "";

        $url_link = "/ezmodules/ezmodule/view?id=1521801182077746900&pmsid={$mainData['id']}";

        $nonify_setting = GanttQuery::getNotifySetting($taskData ['user_create']);
                    $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                    $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                    $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;
        \dms\aomruk\classese\Notify::setNotify()->assign($taskData ['user_create'])->notify($detail_msg)->url($url_link)->detail($detail_msg2)->type_link('1')->send_email($noti_email)->send_line($noti_line)->sendStatic();

        $user_pending = \appxq\sdii\utils\SDUtility::string2Array($pmsData ['user_pending']);
        $response = \Yii::$app->db->createCommand()->update('pms_task_target', [
            'task_status' => '5',
            'user_pending' => \appxq\sdii\utils\SDUtility::array2String($user_pending)
                ], "dataid='{$task_dataid}'");

        if ($response->execute()) {
            $result = [
                'status' => 'success',
                'action' => 'report',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.')
            ];
        } else {
            $result = [
                'status' => 'error',
                'action' => 'report',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Save fail!.')
            ];
        }

        return $result;
    }

    public static function actionGetHeaderAmount() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $project_id = Yii::$app->request->get('project_id');
        $user_id = Yii::$app->user->id;

        $sql = " SELECT COUNT(DISTINCT IF(INSTR(assign_user,'{$user_id}') AND INSTR(assign_user_accept,'{$user_id}')<=0 ,id,null)) as 'not_accept'
				,  COUNT(DISTINCT IF( INSTR(assign_user_accept,'{$user_id}')>0 ,id,null)) as 'mytask' 
				,  COUNT(DISTINCT IF(IFNULL(approved,'')='' AND (IFNULL(credit_points,'')<>'' OR IFNULL(reward_points,'')<>''),id,null)) as 'wait_approve'
				,  COUNT(DISTINCT IF((IFNULL(approved,'')='1' AND IFNULL(assign_user,'')='') OR (IFNULL(credit_points,'')='' AND IFNULL(reward_points,'')='') AND request_type IN(1,2) AND IFNULL(assign_user,'')='' ,id,null)) as 'shopping'
				FROM pms_task_target WHERE rstat NOT IN(0,3) AND target='$project_id' AND priority='3'";
        $query = \Yii::$app->db->createCommand($sql)->queryOne();

        $result = [
            'not_accept_amt' => isset($query['not_accept']) ? $query['not_accept'] : 0,
            'mytask_amt' => isset($query['not_accept']) ? $query['mytask'] : 0,
            'wait_approve_amt' => isset($query['not_accept']) ? $query['wait_approve'] : 0,
            'shopping_amt' => isset($query['not_accept']) ? $query['shopping'] : 0,
        ];

        return $result;
    }

    public static function actionSetHeadOptions() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $project_id = Yii::$app->request->get('project_id');
        $ischeked = Yii::$app->request->get('ischeck');
        $column_id = Yii::$app->request->get('column_id');
        $user_id = Yii::$app->user->id;
        $pmsOption = GanttQuery::getOptionsPMS($project_id);
        $col_show = $pmsOption['column_show'];

        if ($ischeked == 'true') {
            $col_show = array_diff($col_show, [$column_id]);
        } else {
            $col_show[] = $column_id;
        }

        $newOps = SDUtility::array2String(['column_show' => $col_show]);
        $query = \Yii::$app->db->createCommand("REPLACE INTO pms_options (pmsid,user_id,column_ops) VALUES('{$project_id}','{$user_id}','{$newOps}')")->execute();

        return true;
    }

    public function actionNotifySetting() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $noti_sys = Yii::$app->request->post('noti_sys');
        $noti_email = Yii::$app->request->post('noti_email');
        $noti_line = Yii::$app->request->post('noti_line');
        $user_id = Yii::$app->user->id;
        $now = date('Y-m-d H:i:s');

        $noti_sys = $noti_sys == 'true' ? 1 : 0;
        $noti_email = $noti_email == 'true' ? 1 : 0;
        $noti_line = $noti_line == 'true' ? 1 : 0;

        $sql = " REPLACE INTO pms_notify_setting (user_id,noti_sys,noti_email,noti_line,update_date) VALUE(:user_id,:noti_sys,:noti_email,:noti_line,:update_date)";
        $response = Yii::$app->db->createCommand($sql, [':user_id' => $user_id, ':noti_sys' => $noti_sys, ':noti_email' => $noti_email, ':noti_line' => $noti_line, ':update_date' => $now])->execute();

        if ($response) {
            $result = [
                'status' => 'success',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.')
            ];
        } else {
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Save fail!.')
            ];
        }

        return $result;
    }

}
