<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\gantt\classes;

use Yii;
use backend\modules\subjects\classes\SubjectManagementQuery;
use cpn\chanpan\classes\CNUser;
use appxq\sdii\utils\SDUtility;
use appxq\sdii\utils\VarDumper;
use backend\modules\ezforms2\classes\EzfFunc;
use yii\db\Exception;

/**
 * Description of GanttQuery
 *
 * @author Admin
 */
class GanttQuery {

    public static function getProgressByTarget($data_id) {
        $sql = " SELECT SUM(percomp) as progress FROM zdata_activity_response WHERE target='$data_id'";
        $result = \Yii::$app->db->createCommand($sql)->queryOne();
        return $result['progress'];
    }

    public static function checkOwnData($ezform, $dataid) {
        $user_id = \Yii::$app->user->id;
        $userRole = CNUser::getUserRoles();
        $manageRole = "";
        $manageUser = ' OR INSTR(manage_users, \'"' . $user_id . '"\') > 0';
        if ($userRole) {
            foreach ($userRole as $key => $val) {
                $manageRole .= ' OR INSTR(manage_roles, \'"' . $val['id'] . '"\') > 0 ';
            }
        }
        $query = new \yii\db\Query();
        $query->select('*')
                ->from($ezform->ezf_table)
                ->where('id=:id AND (user_create=:user_create ' . $manageUser . $manageRole . ')', [':user_create' => $user_id, 'id' => $dataid]);
        $result = $query->one();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public static function getMyCreatedData($dataid) {
        $user_id = \Yii::$app->user->id;
        $query = new \yii\db\Query();
        $query->select('*')
                ->from('pms_task_target')
                ->where('rstat NOT IN(0,3) AND dataid=:dataid AND user_create=:user_create', [':dataid' => $dataid, ':user_create' => $user_id]);
        $result = $query->one();

        return $result;
    }

    public static function getMytask($ezform, $project_id) {
        $user_id = \Yii::$app->user->id;
        $assignRole = "";
        $manageUser = "";
        $userRole = CNUser::getUserRoles();
        $assignUser = ' INSTR(assign_user,' . $user_id . ')';
        if ($userRole) {
            foreach ($userRole as $key => $val) {
                $assignRole .= ' OR INSTR(assign_role, ' . $val['id'] . ')';
            }
        }
        $query = new \yii\db\Query();
        $query->select('id')
                ->from('pms_task_target')
                ->where('rstat NOT IN(0,3) AND target=:target AND (' . $assignUser . '' . $assignRole . ' )', [':target' => $project_id]);
        $result = $query->all();
        $data = [];
        foreach ($result as $key => $val) {
            $data[] = $val['id'];
        }

        return json_encode($data);
    }

    public static function getMySubtask($ezform, $project_id) {
        $user_id = \Yii::$app->user->id;

        $query = new \yii\db\Query();
        $query->select('id')
                ->from($ezform->ezf_table)
                ->where('rstat NOT IN(0,3) AND target=:target AND (user_create=:user_create )', [':user_create' => $user_id, ':target' => $project_id]);
        $result = $query->all();
        $data = [];
        foreach ($result as $key => $val) {
            $data[] = $val['id'];
        }

        return json_encode($data);
    }

    public static function getPMSMainTask($ezform = null) {
        if ($ezform == null) {
            $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne('1520711894072728800');
        }
        $user_id = \Yii::$app->user->id;
        $query = new \yii\db\Query();
        $query->select('*')
                ->from($ezform->ezf_table)
                ->where('rstat NOT IN(0,3)');
        $result = $query->all();

        return $result;
    }

    public static function getPMSSubTask($target, $ezform = null) {
        if ($ezform == null) {
            $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne('1520711949087879800');
        }
        $user_id = \Yii::$app->user->id;
        $query = new \yii\db\Query();
        $query->select('*')
                ->from($ezform->ezf_table)
                ->where('rstat NOT IN(0,3) AND target=:target', [':target' => $target]);
        $result = $query->all();

        return $result;
    }

    public static function getPMSTask($target, $ezform = null) {
        if ($ezform == null) {
            $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne('1520742721018881300');
        }
        $user_id = \Yii::$app->user->id;
        $query = new \yii\db\Query();
        $query->select('*')
                ->from($ezform->ezf_table)
                ->where('rstat NOT IN(0,3) AND category_id=:category_id', [':category_id' => $target]);
        $result = $query->all();

        return $result;
    }

    public static function updateTaskCompletion($dataid, $task, $actual_field = null, $maxValue = null, $performanceValue = null) {
        $ezf_id = '1520742721018881300';
        $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
        $todate = date('Y-m-d h:i:s');
        $set = ['progress' => $task];
        if ($task == $maxValue) {
            $set = ['progress' => $task, $actual_field => $todate];
        }

        $check = self::checkPermissionTask($dataid);

        if ($check) {
            $update = \Yii::$app->db->createCommand()
                    ->update($ezform->ezf_table, $set, 'id=:id', [':id' => $dataid]);
            if ($update->execute()) {
                return 'success';
            } else {
                return 'fail';
            }
        }
    }

    public static function checkPermissionTask($dataid) {
        $user_id = Yii::$app->user->id;
        $activity_form = "1520742721018881300";
        $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_form);
        $userRole = CNUser::getUserRoles();
        $assignRole = "";
        $assignUser = ' OR INSTR(RIGHT(LEFT(respons_person,LENGTH(respons_person)-1), LENGTH(LEFT(respons_person,LENGTH(respons_person)-1))-1), "' . $user_id . '") > 0';
        if ($userRole) {
            foreach ($userRole as $key => $val) {
                $assignRole .= ' OR INSTR(RIGHT(LEFT(respon_role,LENGTH(respon_role)-1), LENGTH(LEFT(respon_role,LENGTH(respon_role)-1))-1), "' . $val['id'] . '") > 0 ';
            }
        }
        $query = new \yii\db\Query();
        $query->select('id, task_name')
                ->from($ezform->ezf_table)
                ->where(" id={$dataid} AND (user_create='{$user_id}' OR user_update='{$user_id}' $assignUser $assignRole )");
        $result = $query->one();
        return $result;
    }

    public static function getTaskStatus($task_id) {
        $activity_form = "1520742721018881300";
        $response_form = "1520742721018881300";
        $where = '';
        if (isset($task_id)) {
            $where = "and dataid = '$task_id'";
        }
        $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_form);
        $sql = " SELECT 
            task_name,
            IF(actual_date IS NOT NULL AND progress>=100,1,0) AND task_status='3' AS completed,
            task_performance as segment,
            actual_date,
            DATEDIFF(end_date,start_date) as date_per,DATEDIFF(actual_date,start_date) AS actual_diff, 
            (DATEDIFF(end_date,start_date)/task_performance) AS seg_perday , 
            CEIL(DATEDIFF(actual_date,start_date)/(DATEDIFF(end_date,start_date)/task_performance)) as actual_seg
            FROM pms_task_target 
            WHERE rstat NOT IN(0,3) $where";
        $query = \Yii::$app->db->createCommand($sql);
        $result = $query->queryOne();
        return $result;
    }

    public static function setNewIndexTask($dataid, $table, $order, $parent = null) {
        Yii::$app->db->createCommand()->update($table, ['sort_order' => $order], 'id=:id', [':id' => $dataid])->execute();

        return $order;
    }

    public static function getTaskSentModule($sent_module) {
        $activity_form = "1520742721018881300";
        $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_form);

        $query = SubjectManagementQuery::GetTableQuery($ezform, ['sent_module_' . $sent_module => '1']);
        return $query;
    }

    public static function findArraybyFieldName($array, $index, $field_name, $type = 'one') {
        $result = null;
        if ($array) {
            foreach ($array as $key => $val) {
                if ($val[$field_name] == $index) {
                    if ($type == 'all')
                        $result[] = $val;
                    else
                        $result = $val;
                }
            }
        }

        return $result;
    }

    public static function pmsTaskTargetInsert($model = null, $action = null) {
        $dataid = $model['id'];
        $target = $model['target'];
        $dataModels = $model;
        $taskModel = new \backend\modules\gantt\models\PmsTaskTarget();
        $parent = isset($dataModels['parent']) ? $dataModels['parent'] : '0';
        $ezf_id = isset($dataModels['ezf_id']) ? $dataModels['ezf_id'] : null;
        $user_id = Yii::$app->user->id;
        $nowDate = date('Y-m-d H:i:s');
        //\appxq\sdii\utils\VarDumper::dump($dataid);
        $resultTarget = null;
        if ($dataid) {
            $resultTarget = $taskModel->findAll(['dataid' => $dataid]);
        }

        if ($resultTarget && ($action == null || $action == 'update')) {
            $updateParams = [];

            if (isset($dataModels['start_date']) && !empty($dataModels['start_date'])) {
                $updateParams['start_date'] = $dataModels['start_date'];
            }
            if (isset($dataModels['finish_date']) && !empty($dataModels['finish_date'])) {
                $updateParams['end_date'] = $dataModels['finish_date'];
            }
            if (isset($dataModels['task_name']) && !empty($dataModels['task_name'])) {
                $updateParams['task_name'] = $dataModels['task_name'];
            }
            if (isset($dataModels['task_type']) && !empty($dataModels['task_type'])) {
                $updateParams['task_type'] = $dataModels['task_type'];
            }

            if (isset($dataModels['progress']) && !empty($dataModels['progress'])) {
                $updateParams['progress'] = $dataModels['progress'];
            }
            if (isset($dataModels['actual_date']) && !empty($dataModels['actual_date'])) {
                $updateParams['actual_date'] = $dataModels['actual_date'];
            }

            if ((isset($dataModels['progress']) && $dataModels['progress'] >= 100) && (isset($dataModels['actual_date']) && !empty($dataModels['actual_date']))) {

                $checkTask = SubjectManagementQuery::GetTableDataNotEzform("pms_task_target", ['dataid' => $dataid], 'one');
                if ($checkTask && $checkTask['own_approved'] == '1') {
                    $updateParams['task_status'] = '3';
                } else {
                    $updateParams['task_status'] = '2';
                }
            } else if ((isset($dataModels['progress']) && $dataModels['progress'] < 100) && $dataModels['progress'] != '') {
                if ($dataModels['progress'] != '') {
                    $updateParams['task_status'] = '1';
                } else {
                    $updateParams['task_status'] = '0';
                }
            }

            if (isset($dataModels['response_id']) && !empty($dataModels['response_id'])) {
                $updateParams['response_id'] = $dataModels['response_id'];
            }
            if (isset($dataModels['sub_type']) && !empty($dataModels['sub_type'])) {
                $updateParams['sub_type'] = $dataModels['sub_type'];
            }
            if (isset($dataModels['task_performance'])) {
                $updateParams['task_performance'] = $dataModels['task_performance'];
            }
            if (isset($dataModels['task_financial'])) {
                $updateParams['task_financial'] = $dataModels['task_financial'];
            }
            if (isset($dataModels['url_link']) && !empty($dataModels['url_link'])) {
                $updateParams['url_link'] = $dataModels['url_link'];
            }

            if (isset($dataModels['assign_user'])) {
                $updateParams['assign_user'] = $dataModels['assign_user'];
            }

            if (isset($dataModels['assign_user_accept'])) {
                $updateParams['assign_user_accept'] = $dataModels['assign_user_accept'];
            }

            if (isset($dataModels['assign_role'])) {
                $updateParams['assign_role'] = $dataModels['assign_role'];
            }

            if (isset($dataModels['update_date']) && !empty($dataModels['update_date'])) {
                $updateParams['update_date'] = $nowDate;
            }

            if (isset($dataModels['request_type']) && !empty($dataModels['request_type'])) {
                $updateParams['request_type'] = $dataModels['request_type'];
            }
            if (isset($dataModels['credit_points']) && !empty($dataModels['credit_points'])) {
                $updateParams['credit_points'] = $dataModels['credit_points'];
            }
            if (isset($dataModels['reward_points']) && !empty($dataModels['reward_points'])) {
                $updateParams['reward_points'] = $dataModels['reward_points'];
            }
            if (isset($dataModels['task_item_type']) && !empty($dataModels['task_item_type'])) {
                $updateParams['task_item_type'] = $dataModels['task_item_type'];
            }
            if (isset($dataModels['request_type']) && !empty($dataModels['request_type'])) {
                $updateParams['request_type'] = $dataModels['request_type'];
            }
            if (isset($dataModels['co_owner']) && !empty($dataModels['co_owner'])) {
                $updateParams['co_owner'] = $dataModels['co_owner'];
            }
            if (isset($dataModels['reviewer']) && !empty($dataModels['reviewer'])) {
                $updateParams['reviewer'] = $dataModels['reviewer'];
            }
            if (isset($dataModels['approver']) && !empty($dataModels['approver'])) {
                $updateParams['approver'] = $dataModels['approver'];
            }
            if (isset($dataModels['ezmodule_to']) && !empty($dataModels['ezmodule_to'])) {
                $updateParams['ezmodule_work'] = $dataModels['ezmodule_to'];
            }
            if (isset($dataModels['ezform_work'])) {
                $updateParams['ezform_work'] = $dataModels['ezform_work'];
                $formList = SDUtility::string2Array($dataModels['ezform_work']);
                foreach ($formList as $val) {
                    $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($val);
                    Yii::$app->db->createCommand(" ALTER TABLE {$ezform->ezf_table} ADD COLUMN IF NOT EXISTS `pmslink` VARCHAR(150) DEFAULT NULL; ")->execute();
                }
            }

            if (isset($dataModels['ezform_work_ops'])) {
                $updateParams['ezform_work_ops'] = $dataModels['ezform_work_ops'];
                $formList = SDUtility::string2Array($dataModels['ezform_work_ops']);
                foreach ($formList as $val) {
                    $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($val);
                    Yii::$app->db->createCommand(" ALTER TABLE {$ezform->ezf_table} ADD COLUMN IF NOT EXISTS `pmslink` VARCHAR(150) DEFAULT NULL; ")->execute();
                }
            }

            if (isset($dataModels['open_node'])) {
                $updateParams['open_node'] = $dataModels['open_node'];
            }


            $execute = Yii::$app->db->createCommand()->update($taskModel->tableName(), $updateParams, 'dataid = :dataid', [':dataid' => $dataid]);

            try {
                if ($execute->execute()) {
                    $result['dataid'] = $dataid;
                    $result['status'] = 'success';
                    $result['message'] = 'Success completed';
                } else {
                    $result['status'] = 'error';
                    $result['message'] = 'Error !';
                }
            } catch (\yii\db\Exception $e) {
                return $e;
            }
        } else {

            $taskModel = new \backend\modules\gantt\models\PmsTaskTarget();
            $parent = isset($dataModels['parent']) ? $dataModels['parent'] : 0;
            $dataModels['order_node'] = GanttQuery::getNewOrderTask($dataModels['target'], $parent);
            $order_node = $dataModels['order_node'];
            $id = SDUtility::getMillisecTime();
//            $taskModel->id = $id;
//            $taskModel->dataid = $dataid . "";
//            $taskModel->ezf_id = $ezf_id;
            $keyVal = [
                'id' => $id,
                'dataid' => $dataid,
                'ezf_id' => $ezf_id,
                'task_name' => $dataModels['task_name'],
                'task_type' => $dataModels['task_type'],
                'user_update' => $user_id,
                'update_date' => $nowDate,
                'user_create' => $user_id,
                'create_date' => $nowDate,
                'target' => isset($dataModels['target']) ? $dataModels['target'] : null,
                'priority' => isset($dataModels['target']) ? $dataModels['priority'] : null,
                'open_node' => isset($dataModels['open_node']) ? $dataModels['open_node'] : null,
                'order_node' => isset($dataModels['order_node']) ? $dataModels['order_node'] : null,
                'sitecode' => isset($dataModels['order_node']) ? isset($dataModels['xsourcex']) ? $dataModels['xsourcex'] : $dataModels['sitecode'] : null,
                'rstat' => 1,
            ];
            if (isset($dataModels['share_from'])) {
                $keyVal['share_from'] = $dataModels['share_from'];
            }
            if ($dataModels['priority'] == '3') {
//                $taskModel->assign_role = isset($dataModels['respon_role']) ? SDUtility::array2String($dataModels['respon_role']) : null;
//                $taskModel->assign_user = isset($dataModels['respons_person']) ? SDUtility::array2String($dataModels['respons_person']) : null;
//                $taskModel->start_date = isset($dataModels['start_date']) ? $dataModels['start_date'] : null;
//                $taskModel->end_date = isset($dataModels['finish_date']) ? $dataModels['finish_date'] : null;
//                $taskModel->parent = $parent;

                $keyVal['assign_role'] = isset($dataModels['respon_role']) ? SDUtility::array2String($dataModels['respon_role']) : null;
                $keyVal['assign_user'] = isset($dataModels['assign_user']) ? SDUtility::array2String($dataModels['assign_user']) : null;
                $keyVal['start_date'] = isset($dataModels['start_date']) ? $dataModels['start_date'] : null;
                $keyVal['end_date'] = isset($dataModels['end_date']) ? $dataModels['end_date'] : null;
                $keyVal['progress'] = isset($dataModels['progress']) ? $dataModels['progress'] : null;
                $keyVal['actual_date'] = isset($dataModels['actual_date']) ? $dataModels['actual_date'] : null;
                $keyVal['response_id'] = isset($dataModels['response_id']) ? $dataModels['response_id'] : null;
                $keyVal['task_performance'] = isset($dataModels['task_performance']) ? $dataModels['task_performance'] : null;
                $keyVal['task_financial'] = isset($dataModels['task_financial']) ? $dataModels['task_financial'] : null;
                $keyVal['ezform_work'] = isset($dataModels['ezform_work']) ? $dataModels['ezform_work'] : '';
                $keyVal['request_type'] = isset($dataModels['request_type']) ? $dataModels['request_type'] : '';
                $keyVal['parent'] = $parent;
            } else {
                $keyVal['parent'] = $parent;
                $keyVal['sub_type'] = isset($dataModels['sub_type']) ? $dataModels['sub_type'] : 0;
                $keyVal['ezform_work'] = isset($dataModels['ezform_work']) ? $dataModels['ezform_work'] : '';
                //$taskModel->parent = '0';
            }

//            $taskModel->user_create = $user_id . "";
//            $taskModel->create_date = $nowDate;
//            $taskModel->user_update = $user_id . "";
//            $taskModel->update_date = $nowDate;
//            $taskModel->task_name = $dataModels['task_name'];
//            $taskModel->target = $dataModels['target'] . "";
//            $taskModel->priority = $dataModels['priority'];
//            $taskModel->open_node = $dataModels['open_node'];
//            $taskModel->order_node = $dataModels['order_node'];
//            $taskModel->task_type = $dataModels['task_type'];
//            $taskModel->sitecode = isset($dataModels['sitecode']) ? $dataModels['sitecode'] : null;
//            $taskModel->rstat = 1;



            $execute = Yii::$app->db->createCommand()->insert($taskModel->tableName(), $keyVal);

            try {
                if ($execute->execute()) {
                    $result['status'] = 'success';
                    $result['taskid'] = $id;
                    $result['dataid'] = $dataid;
                    $result['order_node'] = $order_node;
                    $result['message'] = 'Success completed';
                } else {
                    $result['status'] = 'error';
                    $result['message'] = 'Error !';
                }
            } catch (\yii\db\Exception $e) {
                return $e;
            }
        }

        return $result;
    }

    public static function getNewOrderTask($target, $parent) {
        $sql = " SELECT MAX(order_node) as 'maxOrder' FROM pms_task_target WHERE target=:target AND parent=:parent ";
        $query = Yii::$app->db->createCommand($sql, [':target' => $target, ':parent' => $parent])->queryOne();
        $newOrder = 0;
        if (isset($query['maxOrder'])) {
            $newOrder = $query['maxOrder'] + 1;
        }

        return $newOrder;
    }

    public static function checkPmsUpdateLog($dataid) {
        $query = Yii::$app->db_main->createCommand("SELECT * FROM pms_update_log WHERE project_id=:dataid AND version='1' ", [
                    ':dataid' => $dataid,
                ])->queryOne();

        return $query;
    }

    public static function getProjectDataProvider() {
        
    }

    public static function isUrlExist($url) {
        //\appxq\sdii\utils\VarDumper::dump($url);
        $ch = file_exists($url);
        if ($ch) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    public static function task2PmsTaskTaget($modelTask, $ezf_id, $parentid, $target = null) {
        $modelDataTask = $modelTask;
        $modelDataTask['ezf_id'] = $ezf_id;
        $modelDataTask['project_id'] = $target == null ? $modelTask['target'] : $target;
        $modelDataTask['task_performance'] = isset($modelTask['seg_performance']) ? $modelTask['seg_performance'] : null;
        $modelDataTask['task_type'] = 'task';

        if (isset($modelTask['sent_to']) && $modelTask['sent_to'] == '1') {
            if (isset($modelTask['sent_module_1']) && $modelTask['sent_module_1'] == '1') {
                $modelDataTask['task_financial'] = '1';
            } else {
                $modelDataTask['task_financial'] = null;
            }
            if (isset($modelTask['sent_module_2']) && $modelTask['sent_module_2'] == '1') {
                $modelDataTask['task_type'] = 'milestone';
            } else {
                $modelDataTask['task_type'] = 'task';
            }
        } else {
            $modelDataTask['task_financial'] = null;
            $modelDataTask['task_type'] = 'task';
        }
        $modelDataTask['url_link'] = isset($modelTask['url_link']) ? $modelTask['url_link'] : null;
        $modelDataTask['assign_user'] = isset($modelTask['respons_person']) ? $modelTask['respons_person'] : null;
        $modelDataTask['assign_role'] = isset($modelTask['respon_role']) ? $modelTask['respon_role'] : null;
        $modelDataTask['priority'] = 3;
        $modelDataTask['open_node'] = 1;
        $modelDataTask['order_node'] = 0;
        $modelDataTask['parent'] = $parentid;

        return self::pmsTaskTargetInsert($modelDataTask);
    }

    public static function subtask2PmsTaskTaget($model, $ezf_id, $target = null) {
        $modelData = $model;
        $modelData['ezf_id'] = $ezf_id;
        $modelData['task_name'] = $modelData['cate_name'];
        $modelData['project_id'] = $target == null ? $modelData['target'] : $target;
        $modelData['task_type'] = 'sub-task';
        $modelData['sub_type'] = $modelData['subtask_type'];
        $modelData['priority'] = 2;
        $modelData['start_date'] = null;
        $modelData['finish_date'] = null;
        $modelData['open_node'] = isset($modelData['open_node']) && $modelData['open_node'] == '0' ? 0 : 1;
        $modelData['order_node'] = 0;
        $modelData['parent'] = 0;
        $insertSub = self::pmsTaskTargetInsert($modelData);
        return $insertSub;
    }

    public static function getRoleNameByUserId($user_id) {
        try {
            $matching = (new \yii\db\Query())->select('zdata_role.id,zdata_role.role_name,zdata_role.role_detail')->from('zdata_matching')->innerJoin('zdata_role', "zdata_matching.role_name=zdata_role.role_name")
                            ->where("INSTR(user_id,'{$user_id}')")->andWhere('zdata_role.rstat not in(0,3)')->all();
            $role_list = [];
            if ($matching) {

                foreach ($matching as $k => $m) {
                    $role_list[$m['role_name']] = $m['role_detail'] . "({$m['role_name']})";
                }
                return $role_list;
            }
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return self::response_status(false, 'not found data', []);
        }
    }

    public static function getAllUser() {

        $result = Yii::$app->db->createCommand("SELECT user_id, CONCAT(firstname,' ',lastname) as `name` FROM profile   ")->queryAll();
        return $result;
    }

    public static function getRolesByUserId($userId) {
        try {
            return Yii::$app->db->createCommand("SELECT id,role_name,role_start,role_stop,expire_status FROM zdata_matching WHERE rstat NOT IN (0,3) AND INSTR(user_id,:user_id) ", [":user_id" => $userId])->queryAll();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getUserIdByRoles($role_name) {
        try {
            $result = Yii::$app->db->createCommand("SELECT id,role_name,user_id,role_start,role_stop,expire_status FROM zdata_matching WHERE rstat NOT IN (0,3) AND role_name=:role_name ", [":role_name" => $role_name])->queryOne();
            $userList = null;
            if ($result)
                $userList = SDUtility::string2Array($result['user_id']);

            return $userList;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getOptionsPMS($pmsid) {
        $user_id = \Yii::$app->user->id;
        try {
            $response = \Yii::$app->db->createCommand("SELECT * FROM pms_options WHERE pmsid=:pmsid AND user_id=:user_id ", [":pmsid" => $pmsid, ':user_id' => $user_id])->queryOne();

            if ($response) {
                return SDUtility::string2Array($response['column_ops']);
            } else {
                $default = ['column_show' => ['duration', 'priority', 'financial', 'ezf_id']];
                return $default;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function setUpdateMainTask($project_id) {
        $main_table = \backend\modules\gantt\Module::$formsTable['maintask_form'];
        try {
            $now = date('Y-m-d H:i:s');
            \Yii::$app->db->createCommand()->update($main_table, ['update_date' => $now], "id='{$project_id}'")->execute();
        } catch (\yii\db\Exception $e) {
            
        }
    }

    public static function getNotifySetting($user_id) {
        //$user_id = Yii::$app->user->id;
        $now = date('Y-m-d H:i:s');
        if (!is_array($user_id)) {
            $result = Yii::$app->db->createCommand("SELECT * FROM pms_notify_setting WHERE user_id=:user_id", [':user_id' => $user_id])->queryOne();
            if (!$result) {
                $sql = " REPLACE INTO pms_notify_setting (user_id,noti_sys,noti_email,noti_line,update_date) VALUE(:user_id,:noti_sys,:noti_email,:noti_line,:update_date)";
                Yii::$app->db->createCommand($sql, [':user_id' => $user_id, ':noti_sys' => '1', ':noti_email' => '1', ':noti_line' => '1', ':update_date' => $now])->execute();
                $result = Yii::$app->db->createCommand("SELECT * FROM pms_notify_setting WHERE user_id=:user_id", [':user_id' => $user_id])->queryOne();
            }
        } else {
            
        }

        return $result;
    }

}
