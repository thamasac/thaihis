<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\ezforms2\controllers;

use yii\web\Controller;
use yii\helpers\Json;
use Yii;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use yii\web\Response;

/**
 * Description of TaskCompletionController
 *
 * @author Admin
 */
class TaskCompletionController extends Controller {

    //put your code here
    public function actionGetSubtask() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $target = Yii::$app->request->get('target');
        $params = Yii::$app->request->post('depdrop_all_params');

        $out = [];
        $dataResult = [];
        $selected = '';
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if ($parents != null) {
                $maintask_id = $parents[0];
                $count = 0;
                $result = \backend\modules\gantt\classes\GanttQuery::getPMSSubTask($maintask_id);
                foreach ($result as $key => $value) {
                    if ($params['value_subtask'] == $value['id']) {
                        $selected = $count;
                    }
                    $dataResult[$count]['id'] = [$value['id']];
                    $dataResult[$count]['name'] = [$value['cate_name']];
                    $count ++;
                }
                $out = $dataResult;
                return Json::encode(['output' => $out, 'selected' => $params['value_subtask']]);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionGetTask() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $target = Yii::$app->request->get('target');
        $params = Yii::$app->request->post('depdrop_all_params');

        $out = [];
        $dataResult = [];
        $selected = '';
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if ($parents != null) {
                $subtask_id = $parents[0];
                $count = 0;
                $result = \backend\modules\gantt\classes\GanttQuery::getPMSTask($subtask_id);
                $task_list = \appxq\sdii\utils\SDUtility::string2Array($params['value_task']);
                foreach ($result as $key => $value) {
                    $dataResult[$count]['id'] = [$value['id']];
                    $dataResult[$count]['name'] = [$value['task_name']];
                    $count ++;
                }
                $out = $dataResult;
                return Json::encode(['output' => $out, 'selected' => $task_list]);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionSelectTask() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $field_name = Yii::$app->request->get('field_name');
        $user_id = Yii::$app->user->id;
        $ezfield = \backend\modules\ezforms2\classes\EzfQuery::getFieldByName($ezf_id, $field_name);

        $activity_form = "1520742721018881300";
        $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_form);

        $ezfield_data = \appxq\sdii\utils\SDUtility::string2Array($ezfield['ezf_field_data']);
        $tasks = isset($ezfield_data['task']) ? $ezfield_data['task'] : [];
        $task_str = join($tasks, ',');
        $assignRole = "";
        $userRole = \cpn\chanpan\classes\CNUser::getUserRoles();
        $assignUser = ' OR INSTR(respons_person, \'"' . $user_id . '"\') > 0';
        if ($userRole) {
            foreach ($userRole as $key => $val) {
                $assignRole .= ' OR INSTR(respon_role, \'"' . $val['id'] . '"\') > 0 ';
            }
        }
        $result = null;
        if (isset($task_str) && $task_str != '') {
            $query = new \yii\db\Query();
            $query->select('id, task_name')
                    ->from($ezform->ezf_table)
                    ->where(" id IN({$task_str}) AND (user_create='{$user_id}' OR user_update='{$user_id}' $assignUser $assignRole) ");
            $result = $query->all();
        }
        return $this->renderAjax('_view', ['result' => $result,]);
    }

    public function actionSelectTaskRelated() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $dataid = Yii::$app->request->get('dataid');
        $field_name = Yii::$app->request->get('field_name');
        $user_id = Yii::$app->user->id;
        $ezfield = \backend\modules\ezforms2\classes\EzfQuery::getFieldByName($ezf_id, $field_name);

        $task_ezf_id = "1520742721018881300";
        $maintask_ezf_id = "1520711894072728800";
        $subtask_ezf_id = "1520711949087879800";
        $ezformTask = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($task_ezf_id);
        $ezformSubTask = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($subtask_ezf_id);
        $ezformMainTask = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($maintask_ezf_id);

        $ezfield_data = \appxq\sdii\utils\SDUtility::string2Array($ezfield['ezf_field_data']);

        $main_task = isset($ezfield_data['main_task']) ? $ezfield_data['main_task'] : null;
        $sub_task = isset($ezfield_data['main_task']) ? $ezfield_data['sub_task'] : null;
        $tasks = isset($ezfield_data['task']) ? $ezfield_data['task'] : null;

        $assignRole = "";
        $userRole = \cpn\chanpan\classes\CNUser::getUserRoles();
        $assignUser = ' OR INSTR(respons_person, \'"' . $user_id . '"\') > 0';
        if ($userRole) {
            foreach ($userRole as $key => $val) {
                $assignRole .= ' OR INSTR(respon_role, \'"' . $val['id'] . '"\') > 0 ';
            }
        }

        $result = null;
        $dataAll = [];
        $dataSubtask = null;
        if ($sub_task == null) {
            $dataSubtask = \backend\modules\subjects\classes\SubjectManagementQuery::GetTableData($ezformSubTask, ['target' => $main_task]);
            if ($dataSubtask) {
                $taskAll = [];
                foreach ($dataSubtask as $key => $val) {
                    $dataAll[$val['id']]['id'] = $val['id'];
                    $dataAll[$val['id']]['sub_name'] = $val['cate_name'];
                    $query = new \yii\db\Query();
                    $query->select('id, task_name')
                            ->from($ezformTask->ezf_table)
                            ->where(" target='{$main_task}' AND category_id='{$val['id']}' AND (user_create='{$user_id}' OR user_update='{$user_id}' $assignUser $assignRole) ");
                    $result = $query->all();
                    foreach ($result as $key2 => $val2) {
                        $taskAll[$val2['id']]['id'] = $val2['id'];
                        $taskAll[$val2['id']]['task_name'] = $val2['task_name'];
                    }

                    $dataAll[$val['id']]['taskall'] = $taskAll;
                }
            }
        } else {
            if ($tasks != null) {
                $task_str = join($tasks, ',');
                $dataSubtask = \backend\modules\subjects\classes\SubjectManagementQuery::GetTableData($ezformSubTask, ['id' => $sub_task], 'one');
                $dataAll[$sub_task]['id'] = $sub_task;
                $dataAll[$sub_task]['sub_name'] = $dataSubtask['cate_name'];
                $taskAll = [];
                $query = new \yii\db\Query();
                $query->select('id, task_name')
                        ->from($ezformTask->ezf_table)
                        ->where(" target='{$main_task}' AND category_id='{$sub_task}' AND id IN($task_str) AND (user_create='{$user_id}' OR user_update='{$user_id}' $assignUser $assignRole) ");
                $result = $query->all();
                foreach ($result as $key2 => $val2) {
                    $taskAll[$val2['id']]['id'] = $val2['id'];
                    $taskAll[$val2['id']]['task_name'] = $val2['task_name'];
                }
                $dataAll[$sub_task]['taskall'] = $taskAll;
            } else {
                $dataSubtask = \backend\modules\subjects\classes\SubjectManagementQuery::GetTableData($ezformSubTask, ['id' => $sub_task], 'one');
                $dataAll[$sub_task]['id'] = $sub_task;
                $dataAll[$sub_task]['sub_name'] = $dataSubtask['cate_name'];
                $taskAll = [];
                $query = new \yii\db\Query();
                $query->select('id, task_name')
                        ->from($ezformTask->ezf_table)
                        ->where(" target='{$main_task}' AND category_id='{$sub_task}' AND (user_create='{$user_id}' OR user_update='{$user_id}' $assignUser $assignRole) ");
                $result = $query->all();
                foreach ($result as $key2 => $val2) {
                    $taskAll[$val2['id']]['id'] = $val2['id'];
                    $taskAll[$val2['id']]['task_name'] = $val2['task_name'];
                }
                $dataAll[$sub_task]['taskall'] = $taskAll;
            }
        }

        return $this->renderAjax('_view-related', ['dataAll' => $dataAll, 'main_task' => $main_task, 'ezf_id' => $ezf_id, 'dataid' => $dataid,]);
    }

    public function actionSaveTaskRelated() {
        //$checkitemAll = \Yii::$app->request->post('checkbox');
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $maintask = \Yii::$app->request->get('maintask');
        $subtask = \Yii::$app->request->get('subtask');
        $taskid = \Yii::$app->request->get('taskid');
        $dataid = \Yii::$app->request->get('dataid');
        $ezf_id = \Yii::$app->request->get('ezf_id');
        $status = \Yii::$app->request->get('checked');
        $sitecode = Yii::$app->user->identity->profile->sitecode;
        $userid = Yii::$app->user->id;
        $nowDate = date('Y-m-d h:i:s');

        if ($status == 'true') {
            $command = Yii::$app->db->createCommand("INSERT INTO task_related (id,maintask,subtask,task_id,dataid,ezf_id,rstat,sitecode,xsourcex,user_create,create_date,user_update,update_date)
                                      VALUES(:id,:maintask,:subtask,:task_id,:dataid,:ezf_id,:rstat,:sitecode,:xsourcex,:user_create,:create_date,:user_update,:update_date)", [
                ':id' => SDUtility::getMillisecTime(),
                ':maintask' => $maintask,
                ':subtask' => $subtask,
                ':task_id' => $taskid,
                ':dataid' => $dataid,
                ':ezf_id' => $ezf_id,
                ':rstat' => '1',
                ':sitecode' => $sitecode,
                ':xsourcex' => $sitecode,
                ':user_create' => $userid,
                ':create_date' => $nowDate,
                ':user_update' => $userid,
                ':update_date' => $nowDate,
            ]);
            if ($command->execute()) {
                return [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                    'data' => [],
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error..'),
                ];
            }
        } else {
            $command = Yii::$app->db->createCommand()->delete('task_related', 'maintask=:maintask AND subtask=:subtask AND task_id=:task_id AND dataid=:dataid', [':maintask' => $maintask, ':subtask' => $subtask, ':task_id' => $taskid, ':dataid' => $dataid]);
            if ($command->execute()) {
                return [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                    'data' => [],
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error..'),
                ];
            }
        }
    }

}
