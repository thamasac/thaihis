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
use yii\web\NotFoundHttpException;
use appxq\sdii\utils\SDUtility;
use yii\db\Exception;
use appxq\sdii\utils\VarDumper;

class PmsResponseController extends Controller {

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex() {
        $page_from = Yii::$app->request->get('page_from');
        $ezf_id = Yii::$app->request->get('ezf_id');
        $dataid = Yii::$app->request->get('dataid');
        $task_dataid = Yii::$app->request->get('taskid');
        $taskid = Yii::$app->request->get('taskid');
        $task_id = Yii::$app->request->get('task_id');
        $ezf_tab = Yii::$app->request->get('ezf_tab');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $response_ezf_id = Yii::$app->request->get('response_ezf_id');
        $ezform_work = Yii::$app->request->get('ezform_work');
        $ezform_work_ops = Yii::$app->request->get('ezform_work_ops');
        $credit_points = Yii::$app->request->get('credit_points');
        $reward_points = Yii::$app->request->get('reward_points');
        $isreviewer = Yii::$app->request->get('isreviewer');
        $isapprover = Yii::$app->request->get('isapprover');

        $ezform_work = \appxq\sdii\utils\SDUtility::string2Array(base64_decode($ezform_work));
        if (!$ezform_work) {
            $taskData = SubjectManagementQuery::GetTableData('pms_task_target', ['dataid' => $taskid], 'one');
            $subtaskData = SubjectManagementQuery::GetTableData('pms_task_target', ['id' => $taskData['parent']], 'one');
            $ezform_work = isset($subtaskData['ezform_work']) ? \appxq\sdii\utils\SDUtility::string2Array($subtaskData['ezform_work']) : [];
            if (!$ezform_work_ops)
                $ezform_work_ops = isset($subtaskData['ezform_work_ops']) ? base64_encode($subtaskData['ezform_work_ops']) : '';
        }

        $ezform_to = [];
        $taskForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
        $taskData = SubjectManagementQuery::GetTableData($taskForm, ['id' => $taskid], 'one');
        if ($taskData['ezform_to']) {
            $ezfWork = [];
            foreach ($ezform_work as $value) {
                if ($value != $taskData['ezform_to']) {
                    $ezfWork[] = $value;
                }
            }
            $ezform_work = $ezfWork;
            $ezform_to[] = $taskData['ezform_to'];
        }

        if (!$ezform_work)
            $ezform_work = $ezform_to;
        else
            $ezform_work = array_merge_recursive($ezform_to, $ezform_work);
        $ezformList = [];
        if ($ezform_work && $ezform_work != '') {
            foreach ($ezform_work as $value) {
                $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($value);
                $ezfData = SubjectManagementQuery::GetTableData($ezform, ['pmslink' => $task_dataid], 'one');

                $ezform = (Array) $ezform->attributes;
                $ezform['dataid'] = isset($ezfData['id']) ? $ezfData['id'] : null;
                $ezform['task_dataid'] = $taskid;
                $ezformList[] = $ezform;
            }
        }

        if (!isset($page_from))
            $page_from = "other";
        if ($page_from == 'other') {
            return $this->render('index', [
                        'page_from' => $page_from,
                        'ezf_id' => $ezf_id,
                        'dataid' => $dataid,
                        'taskid' => $taskid,
                        'task_dataid' => $task_dataid,
                        'response_ezf_id' => $response_ezf_id,
                        'reloadDiv' => $reloadDiv,
                        'ezformList' => $ezformList,
                        'ezform_work_ops' => $ezform_work_ops,
                        'ezform_work' => base64_encode(\appxq\sdii\utils\SDUtility::array2String($ezform_work)),
                        'ezf_tab' => $ezf_tab,
                        'credit_points' => $credit_points,
                        'reward_points' => $reward_points,
                        'isreviewer' => $isreviewer,
                        'isapprover' => $isapprover,
            ]);
        } else {
            return $this->renderAjax('index', [
                        'page_from' => $page_from,
                        'ezf_id' => $ezf_id,
                        'dataid' => $dataid,
                        'taskid' => $taskid,
                        'task_dataid' => $task_dataid,
                        'response_ezf_id' => $response_ezf_id,
                        'reloadDiv' => $reloadDiv,
                        'ezformList' => $ezformList,
                        'ezform_work_ops' => $ezform_work_ops,
                        'ezform_work' => base64_encode(\appxq\sdii\utils\SDUtility::array2String($ezform_work)),
                        'ezf_tab' => $ezf_tab,
                        'credit_points' => $credit_points,
                        'reward_points' => $reward_points,
                        'isreviewer' => $isreviewer,
                        'isapprover' => $isapprover,
            ]);
        }
    }

    public function actionTaskSetting() {
        $taskid = Yii::$app->request->get('taskid');
        $ezf_id = Yii::$app->request->get('ezf_id');


        return $this->renderAjax('task-setting', [
                    'taskid' => $taskid,
                    'ezf_id' => $ezf_id,
        ]);
    }

    public function actionTaskResponse() {
        $taskid = Yii::$app->request->get('taskid');
        $dataid = Yii::$app->request->get('dataid');
        $task_dataid = Yii::$app->request->get('task_dataid');
        $ezf_id = Yii::$app->request->get('ezf_id');
        $task_ezf_id = Yii::$app->request->get('task_ezf_id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $ezform_work = Yii::$app->request->get('ezform_work');
        $credit_points = Yii::$app->request->get('credit_points');
        $reward_points = Yii::$app->request->get('reward_points');
        $isreviewer = Yii::$app->request->get('isreviewer');
        $isapprover = Yii::$app->request->get('isapprover');
        $response_ezf_id = \backend\modules\gantt\Module::$formsId['response_ezf_id'];
        $ezformRes = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($response_ezf_id);
        $user_id = \Yii::$app->user->id;
        
        $taskData = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', ['dataid'=>$taskid],'one');
        if($taskData){
            $approver = SDUtility::string2Array($taskData['approver']);
            $reviewer = SDUtility::string2Array($taskData['reviewer']);
            $reviewer [] = $taskData['user_create'];
            if(in_array($user_id, $approver)){
                $isapprover = '1';
            }
            
            if(in_array($user_id, $reviewer)){
                $isreviewer = '1';
            }
            
        }
        
        if($isapprover != '1'){
            $userRole= GanttQuery::getUserIdByRoles('Director');
            if(is_array($userRole) &&  in_array($user_id, $userRole)){
                $isapprover = '1';
            }
        }

        if ($dataid == null) {
            $data = SubjectManagementQuery::GetTableData($ezformRes, ['target' => $taskid], 'one');
            if ($data)
                $dataid = $data['id'];
        }

        return $this->renderAjax('task-response', [
                    'taskid' => $taskid,
                    'ezf_id' => $response_ezf_id,
                    'dataid' => $dataid,
                    'task_dataid' => $task_dataid,
                    'task_ezf_id' => $task_ezf_id,
                    'reloadDiv' => $reloadDiv,
                    'ezform_work' => $ezform_work,
                    'credit_points' => $credit_points,
                    'reward_points' => $reward_points,
                    'isreviewer' => $isreviewer,
                    'isapprover' => $isapprover,
        ]);
    }

    public function actionTaskRelated() {
        $taskid = Yii::$app->request->get('taskid');
        $dataid = Yii::$app->request->get('dataid');
        $ezf_id = Yii::$app->request->get('ezf_id');
        $response_ezf_id = Yii::$app->request->get('response_ezf_id');

        return $this->renderAjax('task-related', [
                    'taskid' => $taskid,
                    'ezf_id' => $ezf_id,
                    'dataid' => $dataid,
                    'response_ezf_id' => $response_ezf_id,
        ]);
    }

    public function actionTaskAssign() {
        $taskid = Yii::$app->request->get('taskid');
        $dataid = Yii::$app->request->get('dataid');
        $ezf_id = Yii::$app->request->get('ezf_id');
        $response_ezf_id = Yii::$app->request->get('response_ezf_id');

        $project_ezf_id = "1520711894072728800";
        $category_ezf_id = "1520711949087879800";

        return $this->renderAjax('task-assign', [
                    'taskid' => $taskid,
                    'ezf_id' => $ezf_id,
                    'dataid' => $dataid,
                    'project_ezf_id' => $project_ezf_id,
                    'category_ezf_id' => $category_ezf_id,
                    'response_ezf_id' => $response_ezf_id,
        ]);
    }

    public function actionConnectorTaskAssign() {

        $widget_id = Yii::$app->request->get('widget_id');
        $project_ezf_id = Yii::$app->request->get('project_ezf_id');
        $cate_ezf_id = Yii::$app->request->get('category_ezf_id');
        $activity_ezf_id = Yii::$app->request->get('activity_ezf_id');
        $response_ezf_id = Yii::$app->request->get('response_ezf_id');
        $taskid = Yii::$app->request->get('taskid');
        $user_id = \Yii::$app->user->id;
        
        $project_ezf_id  = \backend\modules\gantt\Module::$formsId['maintask_form'];

        $projectEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($project_ezf_id);
        $cateEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($cate_ezf_id);
        $activityEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_ezf_id);
        $responseEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($response_ezf_id);
        $taskData = SubjectManagementQuery::GetTableData($activityEzform->ezf_table, ['id' => $taskid], 'one');
        $project_id = $taskData['target'];
        $data = [];
        $cateAmt = 0;
        $status = 1;
        $sort_order = 0;
        $taskPms = [];
        $cateOrder = 1;

        $userRole = CNUser::getUserRoles();
        $assignRole = "";
        $assignUser = ' INSTR(assign_user, "' . $user_id . '")';
        if ($userRole) {
            foreach ($userRole as $key => $val) {
                $assignRole .= ' OR INSTR(assign_role, "' . $val['id'] . '") ';
            }
        }
        $taskPms = SubjectManagementQuery::GetTableData('pms_task_target', 'target="' . $project_id . '"  AND ( ' . $assignUser . $assignRole . ' OR priority=2 OR create_date="' . $user_id . '")', null, null, ['column' => 'order_node']);
        $mainEzform = SubjectManagementQuery::GetTableData($projectEzform, ['id'=>$project_id],'one');
        $i = 0;
        
        $data [$i] ['id'] = $project_id;
        $data [$i] ['ezf_id'] = $project_ezf_id;
        $data [$i] ['dataid'] = $mainEzform ['id'];
        $data [$i] ['start_date'] = null;
        $data [$i] ['end_date'] = null;
        $data [$i] ['text'] = $mainEzform ['project_name'];
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

        $links [$i] ['id'] = $i;
        $links [$i] ['source'] = $project_id;
        $links [$i] ['target'] = null;
        $links [$i] ['type'] = '3';

        $i ++;

        if ($taskPms && is_array($taskPms)) {
            $links = [];
            foreach ($taskPms as $key => $value) {
                $cateStart = "";
                $cateFinish = "";
                $cateDuration = 0;
                $cateInx = $i;
                $finishLast = "";
                $cateProgress = 0;

                $taskAmt = 0;


                $taskOrder = 1;
                if ($cateStart == '') {
                    $cateStart = $value['start_date'];
                }

                if (date('Y-m-d', strtotime($cateStart)) > date('Y-m-d', strtotime($value['start_date'])) && $value['start_date'] != '') {
                    $cateStart = $value['start_date'];
                }

                if ($cateFinish == "")
                    $cateFinish = $value['end_date'];
                if (date('Y-m-d', strtotime($cateFinish)) < date('Y-m-d', strtotime($value['end_date'])))
                    $cateFinish = $value['end_date'];

                $datetime1 = new DateTime($value['start_date']);
                $datetime2 = new DateTime($value['end_date']);
                $interval = $datetime1->diff($datetime2);
                $diffDate = $interval->format('%a');

                $progress = isset($value['progress']) ? $value['progress'] : '0';
                $actual_date = isset($value['actual_date']) ? $value['actual_date'] : null;

                $cateProgress += $progress;

                if ($value['priority'] == "3") {
                    if ($progress == '100') {
                        $status = '4';
                    } else {
                        if (date('Y-m-d', strtotime($value['end_date'])) < date('Y-m-d') && $progress < '100') {
                            $status = '3';
                        } else
                        if (date('Y-m-d', strtotime($value['start_date'])) <= date('Y-m-d') && $progress < '100') {
                            $status = '2';
                        } else {
                            $status = '1';
                        }
                    }
                } else {
                    $status = 0;
                }

                $taskData = SubjectManagementQuery::GetTableData($activityEzform, ['id' => $value['dataid']], 'one');
                $assign_user = "";
                $assign_avatar = "";
                if (isset($taskData['respons_person'])) {
                    $respons_person = \appxq\sdii\utils\SDUtility::string2Array($taskData['respons_person']);
                    if ($respons_person) {
                        $sql = "SELECT firstname, lastname,avatar_base_url,avatar_path FROM profile WHERE user_id IN(" . join($respons_person, ',') . ") ";
                        $query = Yii::$app->db->createCommand($sql)->queryAll();
                        foreach ($query as $val) {
                            if ($assign_user == "")
                                $assign_user = $val['firstname'] . ' ' . $val['lastname'];
                            else
                                $assign_user = ', ' . $val['firstname'] . ' ' . $val['lastname'];
                        }
                        $assign_avatar = $val['avatar_base_url'] . '/' . $val['avatar_path'];
                        if (!\backend\modules\thaihis\classes\ThaiHisFunc::isUrlExist($assign_avatar)) {
                            $assign_avatar = Yii::getAlias('@storageUrl/images/nouser.png');
                        }
                    }
                }

                $data[$i]['id'] = $value['id'];
                $data[$i]['ezf_id'] = $value['ezf_id'];
                $data[$i]['dataid'] = $value['dataid'];
                $data[$i]['start_date'] = $value['start_date'];
                $data[$i]['end_date'] = $value['end_date'];
                $data[$i]['actual_date'] = $actual_date;
                $data[$i]['text'] = $value['task_name'];
                $data[$i]['progress'] = $progress / 100;
                $data[$i]['progressPer'] = ($progress > 0) ? number_format($progress, 1) . " %" : "0.0%";
                $data[$i]['type_task'] = isset($value['task_type']) ? $value['task_type'] : 'task';
                $data[$i]['task_performance'] = isset($value['task_performance']) ? $value['task_performance'] : '0';
                $data[$i]['task_financial'] = isset($value['task_financial']) && $value['task_financial'] == '1' ? $value['task_financial'] : '0';
                $data[$i]['type'] = isset($value['task_type']) && $value['task_type'] == "sub-task" ? "project" : $value['task_type'];
                $data[$i]['status'] = $status;
                $data[$i]['sub_type'] = $value['sub_type'];
                $data[$i]['priority'] = $value['priority'];
                $data[$i]['sortorder'] = $value['order_node'];
                $data[$i]['sent_module_1'] = isset($value['task_financial']) ? $value['task_financial'] : '0';
                $data[$i]['sent_module_2'] = isset($value['task_type']) && $value['task_type'] == 'milestone' ? '1' : '0';
                $data[$i]['sent_module_txt1'] = 'Financial Management System';
                $data[$i]['sent_module_txt2'] = 'Timeline Milestone';
                $data[$i]['url_link'] = isset($value['url_link']) ? $value['url_link'] : null;
                $data[$i]['response_id'] = isset($value['response_id']) ? $value['response_id'] : '';
                $data[$i]['response_ezf_id'] = $responseEzform['ezf_id'];
                $data[$i]['segment'] = isset($value['task_performance']) ? $value['task_performance'] : null;
                $data[$i]['target'] = $project_id;
                $data[$i]['parent'] = isset($value['parent']) && $value['parent'] != '' ? $value['parent'] : '0';
                $data[$i]['category'] = isset($value['task_type']) && $value['task_type'] == "sub-task" ? $value['id'] : '';
                $data[$i]['order'] = $value['order_node'];
                $data[$i]['open'] = isset($value['open_node']) && $value['open_node'] == '0' ? false : true;
                $data[$i]['user_create'] = $value['user_create'];
                $data[$i]['assign_user'] = $assign_user;
                $data[$i]['assign_avatar'] = $assign_avatar;
                $dataMain = SubjectManagementQuery::GetTableData($projectEzform, ['id' => $value['share_from']], 'one');
                $data[$i]['share_from'] = isset($dataMain['project_name']) ? $dataMain['project_name'] : null;

                $links[$i]['id'] = $i;
                $links[$i]['source'] = $value['id'];
                $links[$i]['target'] = null;
                $links[$i]['type'] = '3';
                $i++;
                $taskAmt ++;
                $taskOrder ++;
            }


            $datetime1 = new DateTime($cateStart);
            $datetime2 = new DateTime($cateFinish);
            $interval = $datetime1->diff($datetime2);
            $diffDate = $interval->format('%a');
            if ($cateDuration < $diffDate)
                $cateDuration = $diffDate;

            $cateAmt ++;
            $progreeCate = 0;

            if ($cateProgress > 0)
                $progreeCate = ($cateProgress / $taskAmt);

            $data[$cateInx]['start_date'] = $cateStart;
            $data[$cateInx]['finish_date'] = $cateFinish;
            $data[$cateInx]['duration'] = $diffDate;
            $data[$cateInx]['progress'] = $progreeCate / 100;
            $data[$cateInx]['progressPer'] = (isset($progreeCate) && $progreeCate > 0) ? number_format($progreeCate, 1) . " %" : "0.0%";
            $cateOrder++;
        }

        $linkModel = new \backend\modules\gantt\models\VisitLinks();
        $queryLink = $linkModel->findAll(['widget_id' => $widget_id]);

        $links = [];
        foreach ($queryLink as $key => $val) {
            $links[$key]['id'] = $key;
            $links[$key]['source'] = $val['source'];
            $links[$key]['target'] = $val['target'];
            $links[$key]['type'] = $val['type'];
        }

        $data_all['data'] = $data;
        $data_all['links'] = $links;
        //\appxq\sdii\utils\VarDumper::dump($data_all);
        return json_encode($data_all);
    }

    public function actionUpdatePmslink() {
        $taskid = Yii::$app->request->get('taskid');
        $dataid = Yii::$app->request->get('dataid');
        $ezf_id = Yii::$app->request->get('ezf_id');

        $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
        Yii::$app->db->createCommand()->update($ezform['ezf_table'], ['pmslink' => $taskid], "id=:id", [':id' => $dataid])->execute();
    }

    public function actionViewOptional() {
        $ezf_list = Yii::$app->request->get('ezf_list');
        $showall = isset($_GET['showall']) ? $_GET['showall'] : 0;
        $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
        $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
        $target = isset($_GET['target']) ? $_GET['target'] : '';
        $task_dataid = isset($_GET['task_dataid']) ? $_GET['task_dataid'] : '';
        $filter_value = isset($_GET['filter_value']) ? $_GET['filter_value'] : '1';
        $ezf_list = \appxq\sdii\utils\SDUtility::string2Array(base64_decode($ezf_list));

        $dataForm = [];
        $dataEzForm = [];
        if ($ezf_list) {
            foreach ($ezf_list as $val) {
                $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($val);
                $dataEzForm[$val]['ezf_id'] = $val;
                $dataEzForm[$val]['ezf_name'] = $ezform['ezf_name'];
                $dataEzForm[$val]['ezf_table'] = $ezform['ezf_table'];

                if ($filter_value == '1') {
                    $data = [];
                    $data = SubjectManagementQuery::GetTableData($ezform, ['pmslink' => $task_dataid]);
                    if ($data) {
                        foreach ($data as $key => $value) {
                            $data[$key]['ezf_id'] = $val;
                            $data[$key]['ezf_name'] = $ezform['ezf_name'];
                            $data[$key]['ezf_table'] = $ezform['ezf_table'];
                            $data[$key]['field_detail'] = $ezform['field_detail'];
                        }
                        $dataForm = array_merge($dataForm, $data);
                    }
                } else {
                    $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($filter_value);
                    $data = [];
                    $data = SubjectManagementQuery::GetTableData($ezform, ['pmslink' => $task_dataid]);
                    if ($data) {
                        foreach ($data as $key => $value) {
                            $data[$key]['ezf_id'] = $filter_value;
                            $data[$key]['ezf_name'] = $ezform['ezf_name'];
                            $data[$key]['ezf_table'] = $ezform['ezf_table'];
                            $data[$key]['field_detail'] = $ezform['field_detail'];
                        }
                        $dataForm = $data;
                    }
                }
            }
        }

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $dataForm,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'attributes' => [
                    'create_date',
                    'ezf_name',
                ],
            ]
        ]);

        return $this->renderAjax('_view_optional', [
                    'ezf_list' => $ezf_list,
                    'dataForm' => $dataForm,
                    'dataEzForm' => $dataEzForm,
                    'dataProvider' => $dataProvider,
                    'addbtn' => true,
                    'reloadDiv' => $reloadDiv,
                    'modal' => $modal,
                    'task_dataid' => $task_dataid,
                    'ezform_work_ops' => base64_encode(\appxq\sdii\utils\SDUtility::array2String($ezf_list)),
                    'filter_value' => $filter_value,
        ]);
    }

    public function actionEmrOptional($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $showall = isset($_GET['showall']) ? $_GET['showall'] : 0;
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $task_dataid = isset($_GET['task_dataid']) ? $_GET['task_dataid'] : '';


            $searchModel = new EzformTarget();
            if (isset($target) && $target != '') {
                $searchModel->target_id = $target;
            }
            $searchModel->ezf_id = $ezf_id;

            $dataProvider = EzfUiFunc::modelEmrSearch($searchModel, $target, $ezf_id, Yii::$app->request->queryParams, $showall);
            $modelEzf = EzfQuery::getFormTableName($ezf_id);

            Yii::$app->session['show_varname'] = 0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();

            if (isset($searchModel->target_id) && !empty($searchModel->target_id)) {
                $target = $searchModel->target_id;
            }
            $view = $popup ? '_emr_popup' : '_emr_grid';

            return $this->renderAjax($view, [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'ezf_id' => $ezf_id,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'target' => $target,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionApproverApprove() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $task_dataid = isset($_GET['target']) ? $_GET['target'] : '';
        $user_id = Yii::$app->user->id;
        $response_ezf_id = \backend\modules\gantt\Module::$formsId['response_ezf_id'];

        $profile = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($user_id);

        $taskData = SubjectManagementQuery::GetTableData('zdata_activity', ['id' => $task_dataid], 'one');
        $mainData = SubjectManagementQuery::GetTableData('zdata_project', ['id' => $taskData['target']], 'one');
        $pmsData = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', "rstat NOT IN(0,3) AND dataid='{$task_dataid}'", 'one');
        $userList = SDUtility::string2Array($pmsData['assign_user_accept']);
        
        GanttQuery::setUpdateMainTask($taskData['target']);

        $detail_msg = "Task Item namely {$taskData['task_name']} approved by approver namely {$profile['firstname']} {$profile['lastname']}. you can see it in Main Task namely {$mainData['project_name']}";
        $detail_msg2 = "";

        $url_link = "/gantt/pms-response?page_from=other&taskid={$pmsData['dataid']}&ezf_id={$pmsData['ezf_id']}&response_ezf_id={$response_ezf_id}";

        $user_director = GanttQuery::getUserIdByRoles('Director');
        $approver = SDUtility::string2Array($pmsData['approver']);
        if(count($approver) <= 0){
            $approver = $user_director;
        }
        $user_approver = SDUtility::string2Array($pmsData['user_approver']);
        $user_approver[] = $user_id;
        $response = \Yii::$app->db->createCommand()->update('pms_task_target'
                    , ['user_approver' => SDUtility::array2String($user_approver)], "dataid='{$task_dataid}'");
                    
        if(count($user_approver) == count($approver)){
            \Yii::$app->db->createCommand()->update('pms_task_target'
                    , ['own_approved' => '1', 'task_status' => '3'], "dataid='{$task_dataid}'")->execute();
        }

        try {
            if ($response->execute()) {
                \dms\aomruk\classese\Notify::setNotify()->assign($userList)->notify($detail_msg)->url($url_link)
                        ->detail($detail_msg2)
                        ->type_link('1')->send_email(true)->send_line(true)->sendStatic();
                $result = [
                    'status' => 'success',
                    'action' => 'report',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.'),
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'action' => 'report',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Save fail!.'),
                ];
            }
        } catch (Exception $e) {
            
        }


        return $result;
    }

    public function actionGetNumberReviewing() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $task_dataid = isset($_GET['task_dataid']) ? $_GET['task_dataid'] : '';
        $user_id = Yii::$app->user->id;

        $taskData = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', ['dataid' => $task_dataid], 'one');

        $reviewer = SDUtility::string2Array($taskData['reviewer']);
        $user_review = SDUtility::string2Array($taskData['user_review']);
        if(count($reviewer) <= 0){
            $reviewer[] = $taskData['user_create'];
        }
        try {
            if ($taskData) {
                $result = [
                    'status' => 'success',
                    'action' => 'report',
                    'reviewing' => count($user_review),
                    'review_max' => count($reviewer),
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Load completed.'),
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'action' => 'report',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Load fail!.'),
                ];
            }
        } catch (Exception $e) {
            
        }

        return $result;
    }
    
    public function actionGetNumberApproving() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $task_dataid = isset($_GET['task_dataid']) ? $_GET['task_dataid'] : '';
        $user_id = Yii::$app->user->id;

        $taskData = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', ['dataid' => $task_dataid], 'one');

        $approver = SDUtility::string2Array($taskData['approver']);
        $user_approver = SDUtility::string2Array($taskData['user_approver']);
        if(count($approver) <= 0){
            $approver = GanttQuery::getUserIdByRoles('Director');
        }

        try {
            if ($taskData) {
                $result = [
                    'status' => 'success',
                    'action' => 'report',
                    'approving' => is_array($user_approver)?count($user_approver):0,
                    'approve_max' => is_array($approver)?count($approver):0,
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Load completed.'),
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'action' => 'report',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Load fail!.'),
                ];
            }
        } catch (Exception $e) {
            
        }

        return $result;
    }

    public function actionReviewingSave() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $task_dataid = isset($_GET['task_dataid']) ? $_GET['task_dataid'] : '';
        $user_id = Yii::$app->user->id;
        $response_ezf_id = \backend\modules\gantt\Module::$formsId['response_ezf_id'];
        $project_ezf_id = \backend\modules\gantt\Module::$formsId['maintask_form'];
        $mainForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($project_ezf_id);
        $taskData = SubjectManagementQuery::GetTableDataNotEzform('pms_task_target',['dataid'=>$task_dataid],'one');
        $mainData= SubjectManagementQuery::GetTableData($mainForm,['id'=>$taskData['target']],'one');
        $profile = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($user_id);
        
        GanttQuery::setUpdateMainTask($taskData['target']);
        
        $user_review = [];
        $owner = [];
        if($taskData){
            $user_review= SDUtility::string2Array($taskData['user_review']);
            $owner= SDUtility::string2Array($taskData['co_owner']);
            $owner[] = $taskData['user_create'];
        }

        $user_review[] = $user_id;
        $task_status = '2';
        if($taskData['task_status'] == '3')$task_status='3';
        $response = \Yii::$app->db->createCommand()->update('pms_task_target'
                , ['user_review' => SDUtility::array2String($user_review), 'task_status' => $task_status], "dataid='{$task_dataid}'");

        $reviewer = SDUtility::string2Array($taskData['reviewer']);
        if(count($reviewer)<= 0)
            $reviewer[] = $taskData['user_create'];
        
        $approver = SDUtility::string2Array($taskData['approver']);
        $user_director = GanttQuery::getUserIdByRoles('Director');
        
        try {
            if ($response->execute()) {
                $detail_msg = "Task Item namely {$taskData['task_name']} review by reviewer name: {$profile['firstname']} {$profile['lastname']}. you can see in Main Task namely {$mainData['project_name']}";
                $detail_msg2 = "";
                $url_link = "/gantt/pms-response?page_from=other&taskid={$taskData['dataid']}&ezf_id={$taskData['ezf_id']}&response_ezf_id={$response_ezf_id}";
                \dms\aomruk\classese\Notify::setNotify()->assign($owner)->notify($detail_msg)->url($url_link)
                        ->detail($detail_msg2)
                        ->type_link('1')->send_email(true)->send_line(true)->sendStatic();
                
                if(count($reviewer) == count($user_review)){
                    \Yii::$app->db->createCommand()->update('pms_task_target'
                    , ['user_review' => SDUtility::array2String($user_review), 'task_status' => '6'], "dataid='{$task_dataid}'")->execute();
                
                    $approver = array_merge($approver,$user_director);
                    $approver[] = $taskData['user_create'];
                    \dms\aomruk\classese\Notify::setNotify()->assign($approver)
                        ->notify("Task Item namely {$taskData['task_name']} review successfully waiting for approve from approver. you can see in Main Task namely {$mainData['project_name']}")->url($url_link)
                        ->detail("")
                        ->type_link('1')->send_email(true)->send_line(true)->sendStatic();
                }
                
                $result = [
                    'status' => 'success',
                    'action' => 'report',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.'),
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'action' => 'report',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Save fail!.'),
                ];
            }
        } catch (Exception $e) {
            
        }


        return $result;
    }
    
    public function actionUpdateResponseEzWorkbench(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $task_dataid = Yii::$app->request->get('task_dataid');
        
        $delete = new \backend\modules\ezforms2\models\QueueLog();
        if($delete->updateAll(['enabled'=>'0'],"dataid='{$task_dataid}'")){
             $result = [
                    'status' => 'success',
                    'action' => 'report',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.'),
                ];
        }else{
            $result = [
                    'status' => 'error',
                    'action' => 'report',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Save fail!.'),
                ];
        }
        
        return $result;
    }
    
    public function actionAssetFroala(){
        return $this->renderAjax('_asset_froala');
    }

}
