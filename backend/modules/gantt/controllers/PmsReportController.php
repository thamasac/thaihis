<?php

namespace backend\modules\gantt\controllers;

use yii\web\Controller;
use backend\modules\gantt\models\InvProject;
use Yii;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\data\ActiveDataProvider;

class PmsReportController extends Controller {

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex() {
        $module_id = Yii::$app->request->get('module_id');
        $project_id = Yii::$app->request->get('project_id');
        $maintask_ezf_id = isset($_GET['maintask_ezf_id']) ? $_GET['maintask_ezf_id'] : '';
        $subtask_ezf_id = isset($_GET['subtask_ezf_id']) ? $_GET['subtask_ezf_id'] : '';
        $task_ezf_id = isset($_GET['task_ezf_id']) ? $_GET['task_ezf_id'] : '';
        $response_ezf_id = isset($_GET['response_ezf_id']) ? $_GET['response_ezf_id'] : '';
        $response_actual_field = isset($_GET['actual_field']) ? $_GET['actual_field'] : '';
        $start_date = Yii::$app->request->get('start_date');
        $finish_date = Yii::$app->request->get('finish_date');
        $progress = Yii::$app->request->get('progress');
        $project_name = Yii::$app->request->get('project_name');
        $cate_name = Yii::$app->request->get('cate_name');
        $task_name = Yii::$app->request->get('task_name');

        return $this->renderAjax('index', [
                    'project_id' => $project_id,
                    'maintask_ezf_id' => $maintask_ezf_id,
                    'subtask_ezf_id' => $subtask_ezf_id,
                    'task_ezf_id' => $task_ezf_id,
                    'response_ezf_id' => $response_ezf_id,
                    'response_actual_field' => $response_actual_field,
                    'start_date' => $start_date,
                    'finish_date' => $finish_date,
                    'progress' => $progress,
                    'project_name' => $project_name,
                    'cate_name' => $cate_name,
                    'task_name' => $task_name,
                    'module_id' => $module_id,
        ]);
    }

    public function actionPmsReportStatus() {
        $module_id = Yii::$app->request->get('module_id');
        $project_id = Yii::$app->request->get('project_id');
        $maintask_ezf_id = isset($_GET['maintask_ezf_id']) ? $_GET['maintask_ezf_id'] : '';
        $subtask_ezf_id = isset($_GET['subtask_ezf_id']) ? $_GET['subtask_ezf_id'] : '';
        $task_ezf_id = isset($_GET['task_ezf_id']) ? $_GET['task_ezf_id'] : '';
        $response_ezf_id = isset($_GET['response_ezf_id']) ? $_GET['response_ezf_id'] : '';
        $response_actual_field = isset($_GET['response_actual_field']) ? $_GET['response_actual_field'] : '';
        $start_date = Yii::$app->request->get('start_date');
        $finish_date = Yii::$app->request->get('finish_date');
        $progress = Yii::$app->request->get('progress');
        $project_name = Yii::$app->request->get('project_name');
        $cate_name = Yii::$app->request->get('cate_name');
        $task_name = Yii::$app->request->get('task_name');

        $ezformTask = EzfQuery::getEzformOne($task_ezf_id);
        $ezformRes = EzfQuery::getEzformOne($response_ezf_id);
        $ezformMain = EzfQuery::getEzformOne($maintask_ezf_id);

        $taskAll = 0;
        $taskCompleted = 0;
        $sum_percen = 0;
        $sum_progress = 0;

        $mainData = SubjectManagementQuery::GetTableData($ezformMain, ['id' => $project_id], 'one');
        $taskData = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target',"target='$project_id' AND priority='3' AND rstat NOT IN(0,3)");
        $taskAll = count($taskData);
        $pms_start_date = null;
        $pms_end_date = null;

        $ownPms;
        if (isset($mainData['user_create']))
            $ownPms = EzfQuery::getUserProfile($mainData['user_create']);

        if ($taskData) {
            foreach ($taskData as $key => $value) {
                if ($pms_start_date == null)
                    $pms_start_date = $value['start_date'];
                if ($pms_end_date == null)
                    $pms_end_date = $value['end_date'];

                if (date('Y-m-d', strtotime($pms_start_date)) > date('Y-m-d', strtotime($value['start_date']))) {
                    $pms_start_date = $value['start_date'];
                }
                if (date('Y-m-d', strtotime($pms_end_date)) < date('Y-m-d', strtotime($value['end_date']))) {
                    $pms_end_date = $value['end_date'];
                }

                $sum_progress += isset($value['progress']) ? $value['progress'] : 0;
                if ($value['progress'] >= 100 && (isset($value['actual_date']) && $value['actual_date'] != null)) {
                    $taskCompleted += 1;
                }
            }
        }

        $sum_percen = ($sum_progress / $taskAll);

        return $this->renderAjax('_report-status', [
                    'taskAll' => $taskAll,
                    'sum_percen' => $sum_percen,
                    'taskCompleted' => $taskCompleted,
                    'ownPms' => $ownPms,
                    'pms_start_date' => $pms_start_date,
                    'pms_end_date' => $pms_end_date,
        ]);
    }

    public function actionPmsReport() {
        $module_id = Yii::$app->request->get('module_id');
        $project_id = Yii::$app->request->get('project_id');
        $maintask_ezf_id = isset($_GET['maintask_ezf_id']) ? $_GET['maintask_ezf_id'] : '';
        $subtask_ezf_id = isset($_GET['subtask_ezf_id']) ? $_GET['subtask_ezf_id'] : '';
        $task_ezf_id = isset($_GET['task_ezf_id']) ? $_GET['task_ezf_id'] : '';
        $response_ezf_id = isset($_GET['response_ezf_id']) ? $_GET['response_ezf_id'] : '';
        $response_actual_field = isset($_GET['response_actual_field']) ? $_GET['response_actual_field'] : '';
        $start_date = Yii::$app->request->get('start_date');
        $finish_date = Yii::$app->request->get('finish_date');
        $progress = Yii::$app->request->get('progress');
        $project_name = Yii::$app->request->get('project_name');
        $cate_name = Yii::$app->request->get('cate_name');
        $task_name = Yii::$app->request->get('task_name');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $sub_filter = Yii::$app->request->get('sub_filter');

        $ezformTask = EzfQuery::getEzformOne($task_ezf_id);
        $ezformResponse = EzfQuery::getEzformOne($response_ezf_id);

        $filter_task = [];
        $filter_task = "target='$project_id' AND priority='3' AND rstat NOT IN(0,3)";
        if($sub_filter){
            $filter_task = "target='$project_id' AND priority='2' AND rstat NOT IN(0,3) AND parent='$sub_filter'"; 
        }

        $taskQuery = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', $filter_task);
        $subtaskQuery = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target',"target='$project_id' AND priority='2' AND rstat NOT IN(0,3)");
        $subtask_list = [];
        foreach ($subtaskQuery as $value){
            if($value['priority'] == '2'){
                $subtask_list[]=$value;
            }
        }

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $taskQuery,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort'=>[
                'attributes' => [
                    'task_name',
                    'start_date',
                    'end_date',
                    'actual_date',
                    'assign_user',
                    'progress',
                ],
                //'defaultOrder' => ['task_name'=>SORT_ASC],
            ]
        ]);
        
//        $dataProvider->setSort([
//            'defaultOrder' => ['task_name'=>SORT_ASC],
//        ]);	

        return $this->renderAjax('_report-grid', [
                    'dataProvider' => $dataProvider,
                    'ezformResponse' => $ezformResponse,
                    'ezformTask' => $ezformTask,
                    'reloadDiv' => $reloadDiv,
                    'progress' => $progress,
                    'subtask_list'=>$subtask_list,
                    'sub_filter'=>$sub_filter,
        ]);
    }

    public function actionPmsReportUser() {
        $module_id = Yii::$app->request->get('module_id');
        $project_id = Yii::$app->request->get('project_id');
        $maintask_ezf_id = isset($_GET['maintask_ezf_id']) ? $_GET['maintask_ezf_id'] : '';
        $subtask_ezf_id = isset($_GET['subtask_ezf_id']) ? $_GET['subtask_ezf_id'] : '';
        $task_ezf_id = isset($_GET['task_ezf_id']) ? $_GET['task_ezf_id'] : '';
        $response_ezf_id = isset($_GET['response_ezf_id']) ? $_GET['response_ezf_id'] : '';
        $response_actual_field = isset($_GET['response_actual_field']) ? $_GET['response_actual_field'] : '';
        $start_date = Yii::$app->request->get('start_date');
        $finish_date = Yii::$app->request->get('finish_date');
        $progress = Yii::$app->request->get('progress');
        $project_name = Yii::$app->request->get('project_name');
        $cate_name = Yii::$app->request->get('cate_name');
        $task_name = Yii::$app->request->get('task_name');
        $reloadDiv = Yii::$app->request->get('reloadDiv');

        $ezformTask = EzfQuery::getEzformOne($task_ezf_id);
        $ezformResponse = EzfQuery::getEzformOne($response_ezf_id);
        $url_curr = \cpn\chanpan\classes\CNServerConfig::getDomainName();
        $dataUserPMS = [];

        $userList = \backend\modules\subjects\classes\ReportQuery::getUserList();
        foreach ($userList as $key => $val) {

            $dataUserPMS[$key]['id'] = $val['id'];
            $dataUserPMS[$key]['firstname'] = $val['firstname'];
            $dataUserPMS[$key]['lastname'] = $val['lastname'];
            $dataUserPMS[$key]['fullname'] = $val['firstname'].' '.$val['lastname'];
            $dataUserPMS[$key]['avatar_path'] = $val['avatar_path'];
            $dataUserPMS[$key]['avatar_base_url'] = $val['avatar_base_url'];
            $dataUserPMS[$key]['task_amt'] = 0;
            $dataUserPMS[$key]['task_completed'] = 0;
            $dataUserPMS[$key]['task_overdue'] = 0;
            $dataUserPMS[$key][$progress] = 0;


            $taskData = SubjectManagementQuery::GetTableData('pms_task_target', "target='{$project_id}' AND ((INSTR(assign_user,'{$val['id']}') AND INSTR(assign_user_accept,'{$val['id']}')) OR (user_create='{$val['id']}' AND IFNULL(assign_user,'')='') )");
            if ($taskData) {
                $task_amt = 0;
                $review_amt = 0;
                $approve_amt = 0;
                $task_complete = 0;
                $task_overdue = 0;
                $sum_progress = 0;
                $sum_credit_points = 0;
                $sum_reward_points = 0;
                foreach ($taskData as $key2 => $val2) {
                    if ($val2['priority'] == '3') {
                        $user_review = \appxq\sdii\utils\SDUtility::string2Array($val2['user_review']);
                        $user_approve = \appxq\sdii\utils\SDUtility::string2Array($val2['user_approver']);
                        
                        if(in_array($val['id'], $user_approve)){
                            $approve_amt += 1;
                        }
                        if(in_array($val['id'], $user_review)){
                            $review_amt += 1;
                        }
                        if ($val2['approved'] == '1') {
                            $sum_credit_points += $val2['credit_points'];
                            $sum_reward_points += $val2['reward_points'];
                        }
                        $task_amt += 1;
                        $sum_progress += isset($val2['progress']) ? $val2['progress'] : '0';

                        if (isset($val2['progress']) && $val2['progress'] >= '100' && isset($val2['actual_date']) && $val2['actual_date'] != null) {
                            $task_complete += 1;
                        } else {
                            if (isset($val2['end_date']) && date('Y-m-d', strtotime($val2['end_date'])) < date('Y-m-d')) {
                                $task_overdue += 1;
                            }
                        }
                    }
                }

                $dataUserPMS[$key]['credit_points'] = $sum_credit_points;
                $dataUserPMS[$key]['reward_points'] =$sum_reward_points ;
                $dataUserPMS[$key]['progress'] = $sum_progress / $task_amt;
                $dataUserPMS[$key]['assigned_task'] = $task_amt;
                $dataUserPMS[$key]['task_completed'] = $task_complete;
                $dataUserPMS[$key]['task_overdue'] = $task_overdue;
                $dataUserPMS[$key]['review_amt'] = $review_amt;
                $dataUserPMS[$key]['approve_amt'] = $approve_amt;
            }
        }

        usort($dataUserPMS, function($x, $y) {
            if ($x['task_amt'] == $y['task_amt'])
                return 0;

            if ($x['task_amt'] > $y['task_amt'])
                return -1;
            else
                return 1;
        });

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $dataUserPMS,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort'=>[
                'attributes' => [
                    'fullname',
                    'credit_points',
                    'reward_points',
                    'assigned_task',
                ],
                'defaultOrder' => ['fullname'=>SORT_ASC],
            ]
        ]);

        return $this->renderAjax('_report-grid-user', [
                    'dataProvider' => $dataProvider,
                    'ezformResponse' => $ezformResponse,
                    'progress' => $progress,
                    'reloadDiv' => $reloadDiv,
        ]);
    }
    
    public function actionMyreport() {
        $user_id = Yii::$app->user->id;
        $taskData = SubjectManagementQuery::GetTableData('pms_task_target', " priority='3' AND ((INSTR(assign_user,'{$user_id}') AND INSTR(assign_user_accept,'{$user_id}')) OR (user_create='{$user_id}' AND IFNULL(assign_user,'')='') )", 'all', null, ['column' => 'target', 'order' => 'desc']);
        $taskAmt = 0;
        $taskOngoing = 0;
        $taskReviewing = 0;
        $taskApproving = 0;
        $taskDone = 0;
        $taskOverdue = 0;
        $taskViewAmt = 0;
        $taskViewDone = 0;
        $taskApprove = 0;
        $taskApproved = 0;
        $creditPoints = 0;
        $rewardPoints = 0;
        foreach ($taskData as $key2 => $val2) {

            $taskAmt += 1;
            if ($val2['task_status'] == '1') {
                $taskOngoing += 1;
                if (date('Y-m-d', strtotime($val2['end_date'])) < date('Y-m-d')) {
                    $taskOverdue += 1;
                }
            } else if ($val2['task_status'] == '2') {
                $taskReviewing += 1;
            } else if ($val2['task_status'] == '3') {
                $taskDone += 1;
                if ($val2['approved'] == '1') {
                    $creditPoints += $val2['credit_points'];
                    $rewardPoints += $val2['reward_points'];
                }
            } else if ($val2['task_status'] == '6') {
                $taskApproving += 1;
            } else {
                if (date('Y-m-d', strtotime($val2['end_date'])) < date('Y-m-d')) {
                    $taskOverdue += 1;
                }
            }

            $user_review = \appxq\sdii\utils\SDUtility::string2Array($val2['user_review']);
            $user_approve = \appxq\sdii\utils\SDUtility::string2Array($val2['user_approver']);
            $reviewer = \appxq\sdii\utils\SDUtility::string2Array($val2['reviewer']);
            $approver = \appxq\sdii\utils\SDUtility::string2Array($val2['approver']);
            if (in_array($user_id, $reviewer)) {
                $taskViewAmt += 1;
            }
            if (in_array($user_id, $approver)) {
                $taskApprove += 1;
            }

            if (in_array($user_id, $user_approve)) {
                $taskViewDone += 1;
            }
            if (in_array($user_id, $user_review)) {
                $taskApproved += 1;
            }
        }

        return $this->renderAjax('_myreport', [
                    'taskAmt' => $taskAmt,
                    'taskOngoing' => $taskOngoing,
                    'taskReviewing' => $taskReviewing,
                    'taskApproving' => $taskApproving,
                    'taskDone' => $taskDone,
                    'taskOverdue' => $taskOverdue,
                    'taskViewAmt' => $taskViewAmt,
                    'taskViewDone' => $taskViewDone,
                    'taskApprove' => $taskApprove,
                    'taskApproved' => $taskApproved,
                    'creditPoints' => $creditPoints,
                    'rewardPoints' => $rewardPoints,
        ]);
    }

    public function actionMyreportGrid() {
        $user_id = Yii::$app->user->id;
        $keystore = Yii::$app->request->get('keystore');
        $taskData = false;
        if ($keystore == 'all_tasks') {
            $where = " priority='3' AND ((INSTR(assign_user,'{$user_id}') AND INSTR(assign_user_accept,'{$user_id}')) 
                OR (user_create='{$user_id}' AND IFNULL(assign_user,'')='') )";
            $taskData = SubjectManagementQuery::GetTableData('pms_task_target', $where, 'all', 50, ['column' => 'target', 'order' => 'desc']);
        } else if ($keystore == 'in_progress') {
            $where = " priority='3' AND task_status='1' AND ((INSTR(assign_user,'{$user_id}') AND INSTR(assign_user_accept,'{$user_id}')) 
                    OR (user_create='{$user_id}' AND IFNULL(assign_user,'')='') )";
            $taskData = SubjectManagementQuery::GetTableData('pms_task_target', $where, 'all', 50, ['column' => 'target', 'order' => 'desc']);
        } else if ($keystore == 'completed') {
            $where = " priority='3' AND task_status='3' AND ((INSTR(assign_user,'{$user_id}') AND INSTR(assign_user_accept,'{$user_id}')) 
                    OR (user_create='{$user_id}' AND IFNULL(assign_user,'')='') )";
            $taskData = SubjectManagementQuery::GetTableData('pms_task_target', $where, 'all', 50, ['column' => 'target', 'order' => 'desc']);
        } else if ($keystore == 'overdue') {
            $where = " priority='3' AND (task_status='1' OR IFNULL(task_status,'')='') AND DATE(end_date)<CURDATE() AND ((INSTR(assign_user,'{$user_id}') AND INSTR(assign_user_accept,'{$user_id}')) 
                    OR (user_create='{$user_id}' AND IFNULL(assign_user,'')='') )";
            $taskData = SubjectManagementQuery::GetTableData('pms_task_target', $where, 'all', 50, ['column' => 'target', 'order' => 'desc']);
        } else if ($keystore == 'reviewing') {
            $where = " priority='3' AND task_status='2' AND ((INSTR(assign_user,'{$user_id}') AND INSTR(assign_user_accept,'{$user_id}')) 
                    OR (user_create='{$user_id}' AND IFNULL(assign_user,'')='') )";
            $taskData = SubjectManagementQuery::GetTableData('pms_task_target', $where, 'all', 50, ['column' => 'target', 'order' => 'desc']);
        } else if ($keystore == 'approving') {
            $where = " priority='3' AND task_status='6' AND ((INSTR(assign_user,'{$user_id}') AND INSTR(assign_user_accept,'{$user_id}')) 
                    OR (user_create='{$user_id}' AND IFNULL(assign_user,'')='') )";
            $taskData = SubjectManagementQuery::GetTableData('pms_task_target', $where, 'all', 50, ['column' => 'target', 'order' => 'desc']);
        } else if ($keystore == 'myreview') {
            $where = " priority='3' AND (INSTR(user_review,'{$user_id}') ";
            $taskData = SubjectManagementQuery::GetTableData('pms_task_target', $where, 'all', 50, ['column' => 'target', 'order' => 'desc']);
        } else if ($keystore == 'myapproved') {
            $where = " priority='3' AND (INSTR(user_approver,'{$user_id}') ";
            $taskData = SubjectManagementQuery::GetTableData('pms_task_target', $where, 'all', 50, ['column' => 'target', 'order' => 'desc']);
        } else if ($keystore == 'waiting_review') {
            $where = " priority='3' AND (INSTR(reviewer,'{$user_id}') ";
            $taskData = SubjectManagementQuery::GetTableData('pms_task_target', $where, 'all', 50, ['column' => 'target', 'order' => 'desc']);
        } else if ($keystore == 'waiting_approve') {
            $where = " priority='3' AND (INSTR(approver,'{$user_id}') ";
            $taskData = SubjectManagementQuery::GetTableData('pms_task_target', $where, 'all', 50, ['column' => 'target', 'order' => 'desc']);
        }

        $dataUserPMS = [];
        if ($taskData) {
            
            foreach ($taskData as $key2 => $val2) {
                $dataUserPMS[$val2['target']][$val2['parent']][$key2]['task_name'] = $val2['task_name'];
                $dataUserPMS[$val2['target']][$val2['parent']][$key2]['start_date'] = $val2['start_date'];
                $dataUserPMS[$val2['target']][$val2['parent']][$key2]['end_date'] = $val2['end_date'];
                $dataUserPMS[$val2['target']][$val2['parent']][$key2]['progress'] = $val2['progress'];
                $dataUserPMS[$val2['target']][$val2['parent']][$key2]['task_status'] = $val2['task_status'];
                $dataUserPMS[$val2['target']][$val2['parent']][$key2]['task_owner'] = $val2['user_create'];
                $dataUserPMS[$val2['target']][$val2['parent']][$key2]['credit_point'] = $val2['credit_points'];
                $dataUserPMS[$val2['target']][$val2['parent']][$key2]['reward_point'] = $val2['reward_points'];
            }
        }

//        usort($dataUserPMS, function($x, $y) {
//            if ($x['task_amt'] == $y['task_amt'])
//                return 0;
//
//            if ($x['task_amt'] > $y['task_amt'])
//                return -1;
//            else
//                return 1;
//        });

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $dataUserPMS,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => [
                    'task_name',
                    'start_date',
                    'end_date',
                    'progress',
                ],
            //'defaultOrder' => ['fullname' => SORT_ASC],
            ]
        ]);
        return $this->renderAjax('_myreport_grid', [
                    'dataUserPMS' => $dataUserPMS,
        ]);
    }

    public function actionReportOverall() {
        $module_id = Yii::$app->request->get('module_id');
        $project_id = Yii::$app->request->get('project_id');
        $maintask_ezf_id = isset($_GET['maintask_ezf_id']) ? $_GET['maintask_ezf_id'] : '';
        $subtask_ezf_id = isset($_GET['subtask_ezf_id']) ? $_GET['subtask_ezf_id'] : '';
        $task_ezf_id = isset($_GET['task_ezf_id']) ? $_GET['task_ezf_id'] : '';
        $response_ezf_id = isset($_GET['response_ezf_id']) ? $_GET['response_ezf_id'] : '';
        $response_actual_field = isset($_GET['response_actual_field']) ? $_GET['response_actual_field'] : '';
        $start_date = Yii::$app->request->get('start_date');
        $finish_date = Yii::$app->request->get('finish_date');
        $progress = Yii::$app->request->get('progress');
        $project_name = Yii::$app->request->get('project_name');
        $cate_name = Yii::$app->request->get('cate_name');
        $task_name = Yii::$app->request->get('task_name');
        $reloadDiv = Yii::$app->request->get('reloadDiv');

        $ezformTask = EzfQuery::getEzformOne($task_ezf_id);
        $ezformResponse = EzfQuery::getEzformOne($response_ezf_id);
        $url_curr = \cpn\chanpan\classes\CNServerConfig::getDomainName();
        $dataUserPMS = [];

        $userList = \backend\modules\subjects\classes\ReportQuery::getUserList();
        foreach ($userList as $key => $val) {

            $dataUserPMS[$key]['id'] = $val['id'];
            $dataUserPMS[$key]['firstname'] = $val['firstname'];
            $dataUserPMS[$key]['lastname'] = $val['lastname'];
            $dataUserPMS[$key]['fullname'] = $val['firstname'] . ' ' . $val['lastname'];
            $dataUserPMS[$key]['avatar_path'] = $val['avatar_path'];
            $dataUserPMS[$key]['avatar_base_url'] = $val['avatar_base_url'];
            $dataUserPMS[$key]['task_amt'] = 0;
            $dataUserPMS[$key]['task_completed'] = 0;
            $dataUserPMS[$key]['task_overdue'] = 0;
            $dataUserPMS[$key]['progress'] = 0;

            $taskData = SubjectManagementQuery::GetTableData('pms_task_target', " ((INSTR(assign_user,'{$val['id']}') AND INSTR(assign_user_accept,'{$val['id']}')) OR (user_create='{$val['id']}' AND IFNULL(assign_user,'')='') )");

            if ($taskData) {
                
                $task_amt = 0;
                $review_amt = 0;
                $approve_amt = 0;
                $task_complete = 0;
                $task_overdue = 0;
                $sum_progress = 0;
                $sum_credit_points = 0;
                $sum_reward_points = 0;
                foreach ($taskData as $key2 => $val2) {
                    if ($val2['priority'] == '3') {
                        $user_review = \appxq\sdii\utils\SDUtility::string2Array($val2['user_review']);
                        $user_approve = \appxq\sdii\utils\SDUtility::string2Array($val2['user_approver']);

                        if (in_array($val['id'], $user_approve)) {
                            $approve_amt += 1;
                        }
                        if (in_array($val['id'], $user_review)) {
                            $review_amt += 1;
                        }

                        if ($val2['approved'] == '1') {
                            $sum_credit_points += $val2['credit_points'];
                            $sum_reward_points += $val2['reward_points'];
                        }
                        $task_amt += 1;
                        $sum_progress += isset($val2['progress']) ? $val2['progress'] : '0';

                        if (isset($val2['progress']) && $val2['progress'] >= '100' && isset($val2['actual_date']) && $val2['actual_date'] != null) {
                            $task_complete += 1;
                        } else {
                            if (isset($val2['end_date']) && date('Y-m-d', strtotime($val2['end_date'])) < date('Y-m-d')) {
                                $task_overdue += 1;
                            }
                        }
                    }
                    $dataUserPMS[$key]['target'] = $val2['target'];
                    $dataUserPMS[$key]['credit_points'] = $sum_credit_points;
                    $dataUserPMS[$key]['reward_points'] = $sum_reward_points;
                    $dataUserPMS[$key]['progress'] = $sum_progress > 0?($sum_progress / $task_amt):0;
                    $dataUserPMS[$key]['assigned_task'] = $task_amt;
                    $dataUserPMS[$key]['task_completed'] = $task_complete;
                    $dataUserPMS[$key]['task_overdue'] = $task_overdue;
                    $dataUserPMS[$key]['review_amt'] = $review_amt;
                    $dataUserPMS[$key]['approve_amt'] = $approve_amt;
                }
            }
        }

        usort($dataUserPMS, function($x, $y) {
            if ($x['task_amt'] == $y['task_amt'])
                return 0;

            if ($x['task_amt'] > $y['task_amt'])
                return -1;
            else
                return 1;
        });

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $dataUserPMS,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'attributes' => [
                    'fullname',
                    'credit_points',
                    'reward_points',
                    'assigned_task',
                ],
                'defaultOrder' => ['assigned_task' => SORT_DESC,],
            ]
        ]);

        return $this->renderAjax('_report_overall', [
                    'dataProvider' => $dataProvider,
        ]);
    }

}
