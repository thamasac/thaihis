<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\subjects\classes;

use Yii;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\models\Ezform;
use yii\db\Exception;

/**
 * Description of SubjectManagementQuery
 *
 * @author Admin
 */
class SubjectManagementQuery {

    // put your code here
    public static function GetTableData($ezform, $where = null, $type = null, $limit = null, $order = null, $group = null) {
        if (isset($ezform->ezf_table) || isset($ezform ['ezf_table']))
            $table = isset($ezform->ezf_table) ? $ezform->ezf_table : $ezform ['ezf_table'];
        else
            $table = $ezform;

        $query = new \yii\db\Query ();
        $query->select('*')->from($table);

        if ($where != null)
            $query->where($where);
        else
            $query->where('1=1');

        if (isset($ezform->ezf_table)) {
            if ($ezform ['public_listview'] == 2) {
                $query->andWhere('xsourcex = :site', [
                    ':site' => Yii::$app->user->identity->profile->sitecode
                ]);
            }

            if ($ezform ['public_listview'] == 3) {
                $query->andWhere('xdepartmentx = :unit', [
                    ':unit' => Yii::$app->user->identity->profile->department
                ]);
            }

            if ($ezform ['public_listview'] == 0) {
                $query->andWhere("user_create=:created_by", [
                    ':created_by' => Yii::$app->user->id
                ]);
            }
        } else {
            $query->andWhere('sitecode = :site', [
                ':site' => Yii::$app->user->identity->profile->sitecode
            ]);
        }

        $query->andWhere(" rstat NOT IN('0','3') ");

        if ($group != null)
            $query->groupBy($group);

        if ($order != null) {
            $orderby = isset($order ['order']) ? $order ['order'] : '';
            $query->orderBy($order ['column'] . ' ' . $orderby);
        }
         //\appxq\sdii\utils\VarDumper::dump($query->createCommand()->sql,0);
        if ($limit != null)
            $query->limit($limit);

        $result = null;

        try {
            if ($type == 'one') {
                $result = $query->one();
            } else {
                $result = $query->all();
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }

        return $result;
    }

    public static function GetTableDataCount($ezform, $where = null) {
        if (isset($ezform->ezf_table) || isset($ezform ['ezf_table']))
            $table = isset($ezform->ezf_table) ? $ezform->ezf_table : $ezform ['ezf_table'];
        else
            $table = $ezform;

        $query = new \yii\db\Query ();
        $query->select('count(*) as amt')->from($table);

        if ($where != null)
            $query->where($where);
        else
            $query->where('1=1');

        if (isset($ezform->ezf_table)) {
            if ($ezform ['public_listview'] == 2) {
                $query->andWhere('xsourcex = :site', [
                    ':site' => Yii::$app->user->identity->profile->sitecode
                ]);
            }

            if ($ezform ['public_listview'] == 3) {
                $query->andWhere('xdepartmentx = :unit', [
                    ':unit' => Yii::$app->user->identity->profile->department
                ]);
            }

            if ($ezform ['public_listview'] == 0) {
                $query->andWhere("user_create=:created_by", [
                    ':created_by' => Yii::$app->user->id
                ]);
            }
        } else {
            $query->andWhere('sitecode = :site', [
                ':site' => Yii::$app->user->identity->profile->sitecode
            ]);
        }

        $query->andWhere(" rstat NOT IN('0','3') ");
        $result = null;
        try {
            $result = $query->one();
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }

        return $result ['amt'];
    }

    public static function GetTableDataNotEzform($table, $where = null, $type = null, $limit = null, $order = null, $group = null) {
        $query = new \yii\db\Query ();
        $query->select('*')->distinct()->from($table);

        if ($where != null)
            $query->where($where);
        else
            $query->where('1=1');

        if ($group != null)
            $query->groupBy($group);

        if ($order != null) {
            $orderby = isset($order ['order']) ? $order ['order'] : '';
            $query->orderBy($order ['column'] . ' ' . $orderby);
        }

        if ($limit != null)
            $query->limit($limit);

        $result = null;

        try {
            if ($type == 'one') {
                $result = $query->one();
            } else {
                $result = $query->all();
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }

        return $result;
    }

    public static function GetTableCountNotEzform($table, $where = null, $group = null) {
        $query = new \yii\db\Query ();
        $query->select('COUNT(id) as amt')->distinct()->from($table);

        if ($where != null)
            $query->where($where);
        else
            $query->where('1=1');

        if ($group != null)
            $query->groupBy($group);

        $result = null;

        try {
            $result = $query->one();
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }

        return $result;
    }

    public static function GetTableQuery($ezform, $where = null, $type = null, $limit = null) {
        if (isset($ezform->ezf_table))
            $table = $ezform->ezf_table;
        else
            $table = $ezform;
        $query = new \yii\db\Query ();
        $query->select('*')->from($table);

        if ($where != null)
            $query->where($where);
        else
            $query->where('1=1');

        if (isset($ezform->ezf_table)) {
            if ($ezform ['public_listview'] == 2) {
                $query->andWhere('xsourcex = :site', [
                    ':site' => Yii::$app->user->identity->profile->sitecode
                ]);
            }

            if ($ezform ['public_listview'] == 3) {
                $query->andWhere('xdepartmentx = :unit', [
                    ':unit' => Yii::$app->user->identity->profile->department
                ]);
            }

            if ($ezform ['public_listview'] == 0) {
                $query->andWhere("user_create=:created_by", [
                    ':created_by' => Yii::$app->user->id
                ]);
            }
        } else {
            $query->andWhere('sitecode = :site', [
                ':site' => Yii::$app->user->identity->profile->sitecode
            ]);
        }

        $query->andWhere(" rstat NOT IN(0,3) ");
        $query->orderBy('create_date DESC');
        if ($limit != null)
            $query->limit($limit);

        try {
            if ($type == 'one') {
                $query->one();
            } else {
                $query->all();
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }

        return $query;
    }

    public static function GetDataTaskResponse($ezform, $ezform2, $where) {
        $sql = "SELECT * FROM " . $ezform->ezf_table . " ez1 LEFT JOIN (SELECT target,actual_date, progress, MAX(update_date) FROM " . $ezform2->ezf_table . " GROUP BY target ) as ez2 ON ez1.id=ez2.target 
            WHERE $where ";
        $params = [];

        if ($ezform ['public_listview'] == 2) {
            $sql .= " AND ez1.xsourcex = :site ";
            $params [':site'] = Yii::$app->user->identity->profile->sitecode;
        }

        if ($ezform ['public_listview'] == 3) {
            $sql .= " AND ez1.xdepartmentx = :unit ";
            $params [':unit'] = Yii::$app->user->identity->profile->department;
        }

        if ($ezform ['public_listview'] == 0) {
            $sql .= " AND ez1.user_create=:created_by ";
            $params [':created_by'] = Yii::$app->user->id;
        }

        $sql .= " AND ez1.rstat NOT IN(0,3) ";
        $sql .= " GROUP BY ez1.id ";

        $result = Yii::$app->db->createCommand($sql, $params)->queryAll();
        return $result;
    }

    public static function GetTableJoinData($ezform, $ezform2, $where = null, $type = null, $limit = null, $offset = null, $custom_column = null) {
        if ($custom_column == null) {
            $custom_column = [
                $ezform->ezf_table . '.*',
                $ezform2->ezf_table . '.*'
            ];
        } else {
            array_push($custom_column, $ezform->ezf_table . '.*', $ezform2->ezf_table . '.*');
        }
        $query = new \yii\db\Query ();
        $query->select($custom_column)->from($ezform->ezf_table)->rightJoin($ezform2->ezf_table, $ezform->ezf_table . '.id=' . $ezform2->ezf_table . '.target');

        if ($where != null)
            $query->where($where);
        else
            $query->where('1=1');

        if ($ezform ['public_listview'] == 2) {
            $query->andWhere($ezform->ezf_table . '.xsourcex = :site', [
                ':site' => Yii::$app->user->identity->profile->sitecode
            ]);
        }

        if ($ezform ['public_listview'] == 3) {
            $query->andWhere($ezform->ezf_table . '.xdepartmentx = :unit', [
                ':unit' => Yii::$app->user->identity->profile->department
            ]);
        }

        if ($ezform ['public_listview'] == 0) {
            $query->andWhere($ezform->ezf_table . ".user_create=:created_by", [
                ':created_by' => Yii::$app->user->id
            ]);
        }

        $query->andWhere($ezform->ezf_table . ".rstat NOT IN(0,3) ");
        $query->andWhere($ezform2->ezf_table . ".rstat NOT IN(0,3) ");
        $query->groupBy($ezform->ezf_table . '.target');
        if ($limit != null)
            $query->limit($limit);

        if ($offset != null)
            $query->offset($offset);
        // \appxq\sdii\utils\VarDumper::dump($query->createCommand()->sql);
        $result = false;
        try {
            if ($type == 'one') {
                $result = $query->one();
            } else {
                $result = $query->all();
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }

        return $result;
    }

    public static function GetTableJoinDataCount($ezform, $ezform2, $where = null) {
        $query = new \yii\db\Query ();
        $query->select("count(DISTINCT {$ezform->ezf_table}.target) as amt")->from($ezform->ezf_table)->rightJoin($ezform2->ezf_table, $ezform->ezf_table . '.id=' . $ezform2->ezf_table . '.target');

        if ($where != null)
            $query->where($where);
        else
            $query->where('1=1');

        if ($ezform ['public_listview'] == 2) {
            $query->andWhere($ezform->ezf_table . '.xsourcex = :site', [
                ':site' => Yii::$app->user->identity->profile->sitecode
            ]);
        }

        if ($ezform ['public_listview'] == 3) {
            $query->andWhere($ezform->ezf_table . '.xdepartmentx = :unit', [
                ':unit' => Yii::$app->user->identity->profile->department
            ]);
        }

        if ($ezform ['public_listview'] == 0) {
            $query->andWhere($ezform->ezf_table . ".user_create=:created_by", [
                ':created_by' => Yii::$app->user->id
            ]);
        }

        $query->andWhere($ezform->ezf_table . ".rstat NOT IN(0,3) ");
        $query->andWhere($ezform2->ezf_table . ".rstat NOT IN(0,3) ");
        // $query->groupBy($ezform->ezf_table . '.target');

        $result = null;
        try {
            $result = $query->one();
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }

        return isset($result ['amt']) ? $result ['amt'] : '0';
    }

    public static function GetTableLeftJoinData($ezform, $ezform2, $where = null, $type = null, $limit = null, $offset = null) {
        $query = new \yii\db\Query ();
        $query->select($ezform->ezf_table . '.*,' . $ezform2->ezf_table . '.*')->from($ezform->ezf_table)->leftJoin($ezform2->ezf_table, $ezform->ezf_table . '.id=' . $ezform2->ezf_table . '.target');

        if ($where != null)
            $query->where($where);
        else
            $query->where('1=1');

        if ($ezform ['public_listview'] == 2) {
            $query->andWhere($ezform->ezf_table . '.xsourcex = :site', [
                ':site' => Yii::$app->user->identity->profile->sitecode
            ]);
        }

        if ($ezform ['public_listview'] == 3) {
            $query->andWhere($ezform->ezf_table . '.xdepartmentx = :unit', [
                ':unit' => Yii::$app->user->identity->profile->department
            ]);
        }

        if ($ezform ['public_listview'] == 0) {
            $query->andWhere($ezform->ezf_table . ".user_create=:created_by", [
                ':created_by' => Yii::$app->user->id
            ]);
        }

        $query->andWhere($ezform->ezf_table . ".rstat NOT IN(0,3) ");
        $query->andWhere($ezform2->ezf_table . ".rstat NOT IN(0,3) ");
        $query->groupBy($ezform->ezf_table . '.target');
        if ($limit != null)
            $query->limit($limit);

        if ($offset != null)
            $query->offset($offset);

        $result = false;
        try {
            if ($type == 'one') {
                $result = $query->one();
            } else {
                $result = $query->all();
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }

        return $result;
    }

    public static function GetTableLeftJoinDataCount($ezform, $ezform2, $where = null) {
        $query = new \yii\db\Query ();
        $query->select("count(DISTINCT {$ezform->ezf_table}.target) as amt")->from($ezform->ezf_table)->leftJoin($ezform2->ezf_table, $ezform->ezf_table . '.id=' . $ezform2->ezf_table . '.target');

        if ($where != null)
            $query->where($where);
        else
            $query->where('1=1');

        if ($ezform ['public_listview'] == 2) {
            $query->andWhere($ezform->ezf_table . '.xsourcex = :site', [
                ':site' => Yii::$app->user->identity->profile->sitecode
            ]);
        }

        if ($ezform ['public_listview'] == 3) {
            $query->andWhere($ezform->ezf_table . '.xdepartmentx = :unit', [
                ':unit' => Yii::$app->user->identity->profile->department
            ]);
        }

        if ($ezform ['public_listview'] == 0) {
            $query->andWhere($ezform->ezf_table . ".user_create=:created_by", [
                ':created_by' => Yii::$app->user->id
            ]);
        }

        $query->andWhere($ezform->ezf_table . ".rstat NOT IN(0,3) ");
        $query->andWhere($ezform2->ezf_table . ".rstat NOT IN(0,3) ");
        // $query->groupBy($ezform->ezf_table . '.target');

        $result = null;
        try {
            $result = $query->one();
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }

        return isset($result ['amt']) ? $result ['amt'] : '0';
    }

    public static function GetTableJoinData2($ezform, $ezform2, $columns, $where = null, $type = null, $limit = null, $offset = null) {
        $query = new \yii\db\Query ();
        $query->select($columns)->distinct(true)->from($ezform->ezf_table)->innerJoin($ezform2->ezf_table, $ezform->ezf_table . '.id=' . $ezform2->ezf_table . '.target');

        if ($where != null)
            $query->where($where);
        else
            $query->where('1=1');

        if ($ezform ['public_listview'] == 2) {
            $query->andWhere($ezform->ezf_table . '.xsourcex = :site', [
                ':site' => Yii::$app->user->identity->profile->sitecode
            ]);
        }

        if ($ezform ['public_listview'] == 3) {
            $query->andWhere($ezform->ezf_table . '.xdepartmentx = :unit', [
                ':unit' => Yii::$app->user->identity->profile->department
            ]);
        }

        if ($ezform ['public_listview'] == 0) {
            $query->andWhere($ezform->ezf_table . ".user_create=:created_by", [
                ':created_by' => Yii::$app->user->id
            ]);
        }

        $query->andWhere($ezform->ezf_table . ".rstat NOT IN(0,3) ");
        $query->andWhere($ezform2->ezf_table . ".rstat NOT IN(0,3) ");
        $query->groupBy($ezform->ezf_table . '.target');
        if ($limit != null)
            $query->limit($limit);

        if ($offset != null)
            $query->offset($offset);

        $result = false;
        try {
            if ($type == 'one') {
                $result = $query->one();
            } else {
                $result = $query->all();
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }

        return $result;
    }

    public static function GetTableActivity($ezform, $table2, $tb2_column = null, $where = null, $having = null, $type = null, $limit = null, $sys = null) {
        $query = new \yii\db\Query ();
        if (isset($ezform->ezf_table)) {
            $table = $ezform->ezf_table;
        }

        $select = "";
        foreach ($tb2_column as $key => $value) {
            $select .= ',(SELECT ' . $value . ' FROM `' . $table2 . '` WHERE target=' . $table . '.id AND rstat NOT IN(0,3) ORDER BY `create_date` DESC LIMIT 1) as ' . $value;
        }

        $query->select($table . '.*
                ' . $select)->from($table)->leftJoin($table2, $table . ".id=" . $table2 . ".target");
        $query->where('1=1');

        if ($where != null)
            $query->andWhere($where);

        if ($ezform ['public_listview'] == 2) {
            $query->andWhere($table . '.xsourcex = :site', [
                ':site' => Yii::$app->user->identity->profile->sitecode
            ]);
        }

        if ($ezform ['public_listview'] == 3) {
            $query->andWhere($table . '.xdepartmentx = :unit', [
                ':unit' => Yii::$app->user->identity->profile->department
            ]);
        }

        if ($ezform ['public_listview'] == 0) {
            $query->andWhere($table . ".user_create=:created_by", [
                ':created_by' => Yii::$app->user->id
            ]);
        }

        $query->andWhere($table . ".rstat NOT IN(0,3) ");
        if ($limit != null)
            $query->limit($limit);

        $query->groupBy($table . ".id");
        if ($having != null)
            $query->having($having);

        if ($type == 'one') {
            $result = $query->one();
        } else {
            $result = $query->all();
        }

        return $query;
    }

    public static function GetScheduleActivity($ezform, $table2, $tb2_column = null, $where = null, $offset = null, $limit = null, $type = null) {
        $query = new \yii\db\Query ();
        $table = $ezform->ezf_table;

        $query->select([
            $table . '.*',
            $table2 . '.*',
            " (SELECT subject_no FROM {$table2} WHERE target={$table}.id AND IFNULL(subject_no,'')<>'' LIMIT 1) AS subject_no "
        ])->from($table)->leftJoin('(SELECT * FROM ' . $table2 . ' sd WHERE sd.id = (SELECT id FROM ' . $table2 . '  WHERE sd.target= ' . $table2 . '.target AND ' . $where . '  ORDER BY date_visit DESC LIMIT 1)) ' . $table2, $table . '.id=' . $table2 . '.target');
        if ($where != null)
            $query->where($where);
        else
            $query->where('1=1');

        if ($ezform ['public_listview'] == 2) {
            $query->andWhere($table . '.xsourcex = :site', [
                ':site' => Yii::$app->user->identity->profile->sitecode
            ]);
        }

        if ($ezform ['public_listview'] == 3) {
            $query->andWhere($table . '.xdepartmentx = :unit', [
                ':unit' => Yii::$app->user->identity->profile->department
            ]);
        }

        if ($ezform ['public_listview'] == 0) {
            $query->andWhere($table . ".user_create=:created_by", [
                ':created_by' => Yii::$app->user->id
            ]);
        }

        $query->andWhere($table . ".rstat NOT IN(0,3) ");
        if ($limit != null)
            $query->limit($limit);

        if ($offset != null)
            $query->offset($offset);

        $query->groupBy($table . '.id');

        if ($type == 'one') {
            $result = $query->one();
        } else {
            $result = $query->all();
        }

        return $result;
    }

    public static function GetActivityAllVisit($ezform, $table2, $tb2_column = null, $where = null, $limit = null, $type = null) {
        $query = new \yii\db\Query ();
        $table = $ezform->ezf_table;

        $query->select($table . '.*,' . $table2 . '.*')->from($table)->leftJoin($table2, $table . '.id=' . $table2 . '.target');
        if ($where != null)
            $query->where($where);
        else
            $query->where('1=1');

        if ($ezform ['public_listview'] == 2) {
            $query->andWhere($table . '.xsourcex = :site', [
                ':site' => Yii::$app->user->identity->profile->sitecode
            ]);
        }

        if ($ezform ['public_listview'] == 3) {
            $query->andWhere($table . '.xdepartmentx = :unit', [
                ':unit' => Yii::$app->user->identity->profile->department
            ]);
        }

        if ($ezform ['public_listview'] == 0) {
            $query->andWhere($table . ".user_create=:created_by", [
                ':created_by' => Yii::$app->user->id
            ]);
        }

        $query->andWhere($table . ".rstat NOT IN(0,3) ");
        if ($limit != null)
            $query->limit($limit);

        $query->groupBy($table . '.id');

        if ($type == 'one') {
            $result = $query->one();
        } else {
            $result = $query->all();
        }

        return $result;
    }

    public static function GetTargetById($ezform, $id) {
        $query = new \yii\db\Query ();
        $result = $query->select('*')->from($ezform->ezf_table)->where('target=:target', [
                    ':target' => $id
                ])->one();
        return $result;
    }

    public static function getWidgetByUserId($user_id = null) {
        $query = new \yii\db\Query ();
        $resule = $query->select('*')->from('ezmodule_widget')->where('enable=:enable', [
            ':enable' => '1'
                ]);
        if ($user_id != null)
            $query->andWhere([
                'created_by' => $user_id
            ]);

        $resule = $query->all();
        return $resule;
    }

    public static function getWidgetByModule($ezm_id, $widget_id = null) {
        $query = new \yii\db\Query ();
        $query->select('*')->from('ezmodule_widget')->where('enable=:enable', [
            ':enable' => '1'
        ])->andWhere('ezm_id=:mid', [
            ':mid' => $ezm_id
        ]);

        if ($widget_id) {
            $query->andWhere('widget_id<>:widget_id', [
                ':widget_id' => $widget_id
            ]);
        }
        $result = $query->all();
        return $result;
    }

    public static function getWidgetById($widget_id) {
        $query = new \yii\db\Query ();
        $resule = $query->select('*')->from('ezmodule_widget')->where('widget_id=:widget_id', [
                    ':widget_id' => $widget_id
                ])->
                // ->andWhere('ezm_id=:mid',[':mid'=>$mid])
                one();
        return $resule;
    }

    public static function getVisitScheduleByWidget($widget_id, $group_id = null, $where = null, $visit_id = null) {
        $ezform = [];
        // if ($group_id == null) {
        // $group_id = $_SESSION['group_id'];
        // }
        $query = new \yii\db\Query ();
        $query->select('zdata_visit_schedule.*')->distinct()->from('zdata_visit_schedule')
                ->innerJoin('zdata_subject_group', 'zdata_subject_group.id=zdata_visit_schedule.group_name OR zdata_visit_schedule.group_name=0')
                ->where('schedule_id=:schedule_id AND zdata_visit_schedule.sitecode=:sitecode', [
            ':schedule_id' => $widget_id,
            ':sitecode' => Yii::$app->user->identity->profile->sitecode
        ])->andWhere('zdata_subject_group.rstat NOT IN(0,3)');

        if ($group_id != null)
            $query->andWhere('zdata_visit_schedule.group_name=:group_id OR zdata_visit_schedule.group_name=0', [
                ':group_id' => $group_id
            ]);

        if ($where != null) {
            $query->andWhere($where);
        }

        $query->andWhere('zdata_visit_schedule.rstat NOT IN(0,3)');
        $widget_ref = SubjectManagementQuery::getWidgetById($widget_id);
        $dataVisit = \appxq\sdii\utils\SDUtility::string2Array($widget_ref ['options']);
        $query->orderBy('id');
        $result = $query->all();
        $dataResult = [];
        $count = 0;

        foreach ($dataVisit as $key => $value) {
            if (isset($value ['enable_visit']) && $value ['enable_visit'] == '1') {
                if ($key == '11111') {
                    $dataResult [$key] ['id'] = $key;
                    $dataResult [$key] ['ezf_id'] = $value ['main_ezf_id'];
                    $dataResult [$key] ['visit_name'] = $value ['form_name'];
                    $dataResult [$key] ['visit_name_mapping'] = $value ['main_visit_name'];
                    $dataResult [$key] ['actual_date'] = $value ['main_actual_date'];
                    $dataResult [$key] ['earliest_date'] = $value ['main_earliest_distance'];
                    $dataResult [$key] ['latest_date'] = isset($value ['main_latest_distance']) ? $value ['main_latest_distance'] : '';
                    $dataResult [$key] ['form_list'] = isset($value ['form_list']) ? $value ['form_list'] : '';
                    $dataResult [$key] ['group_name'] = isset($value ['group_name']) ? $value ['group_name'] : '';

                    $count ++;
                } elseif ($key == '22222') {
                    $dataResult [$key] ['id'] = $key;
                    $dataResult [$key] ['ezf_id'] = $value ['random_ezf_id'];
                    $dataResult [$key] ['visit_name'] = $value ['form_name'];
                    $dataResult [$key] ['visit_name_mapping'] = $value ['random_visit_name'];
                    $dataResult [$key] ['actual_date'] = $value ['random_actual_date'];
                    $dataResult [$key] ['plan_date'] = $value ['random_plan_distance'];
                    $dataResult [$key] ['earliest_date'] = $value ['random_earliest_distance'];
                    $dataResult [$key] ['latest_date'] = $value ['random_latest_distance'];
                    $dataResult [$key] ['visit_cal_date'] = '11111';
                    $dataResult [$key] ['field_cal_date'] = 'actual_date';
                    $dataResult [$key] ['form_list'] = isset($value ['form_list']) ? $value ['form_list'] : '';
                    $dataResult [$key] ['group_name'] = isset($value ['group_name']) ? $value ['group_name'] : '';

                    $count ++;
                }
            }
        }

        foreach ($result as $key => $value) {
            $dataResult [$value ['id']] ['id'] = $value ['id'];
            $dataResult [$value ['id']] ['ezf_id'] = $value ['ezf_id'];
            $dataResult [$value ['id']] ['visit_name'] = $value ['visit_name'];
            $dataResult [$value ['id']] ['visit_name_mapping'] = $value ['visit_name_mapping'];
            $dataResult [$value ['id']] ['actual_date'] = $value ['actual_date'];
            $dataResult [$value ['id']] ['plan_date'] = $value ['plan_date'];
            $dataResult [$value ['id']] ['earliest_date'] = $value ['earliest_date'];
            $dataResult [$value ['id']] ['latest_date'] = $value ['latest_date'];
            $dataResult [$value ['id']] ['visit_cal_date'] = $value ['visit_cal_date'];
            $dataResult [$value ['id']] ['field_cal_date'] = $value ['field_cal_date'];
            $dataResult [$value ['id']] ['form_list'] = $value ['form_list'];
            $dataResult [$value ['id']] ['group_name'] = $value ['group_name'];

            $count ++;
        }

        return $dataResult;
    }
    
    public static function getVisitScheduleByWidget2($widget_id, $group_id = null, $where = null, $visit_id = null) {
        $ezform = [];
        // if ($group_id == null) {
        // $group_id = $_SESSION['group_id'];
        // }
        $query = new \yii\db\Query ();
        $query->select('zdata_visit_schedule.*')->distinct()->from('zdata_visit_schedule')
                ->innerJoin('zdata_subject_group', 'zdata_subject_group.id=zdata_visit_schedule.group_name OR zdata_visit_schedule.group_name=0')
                ->where('schedule_id=:schedule_id AND zdata_visit_schedule.sitecode=:sitecode', [
            ':schedule_id' => $widget_id,
            ':sitecode' => Yii::$app->user->identity->profile->sitecode
        ])->andWhere('zdata_subject_group.rstat NOT IN(0,3)');

        if ($group_id != null)
            $query->andWhere('zdata_visit_schedule.group_name=:group_id OR zdata_visit_schedule.group_name=0', [
                ':group_id' => $group_id
            ]);

        if ($where != null) {
            $query->andWhere($where);
        }

        $query->andWhere('zdata_visit_schedule.rstat NOT IN(0,3)');
        $widget_ref = SubjectManagementQuery::getWidgetById($widget_id);
        $dataVisit = \appxq\sdii\utils\SDUtility::string2Array($widget_ref ['options']);
        $query->orderBy('id');
        $result = $query->all();
        $dataResult = [];
        $count = 0;

        foreach ($dataVisit as $key => $value) {
            if (isset($value ['enable_visit']) && $value ['enable_visit'] == '1') {
                if ($key == '11111') {
                    $dataResult [$key] ['id'] = $key;
                    $dataResult [$key] ['ezf_id'] = $value ['main_ezf_id'];
                    $dataResult [$key] ['visit_name'] = $value ['form_name'];
                    $dataResult [$key] ['visit_name_mapping'] = $value ['main_visit_name'];
                    $dataResult [$key] ['actual_date'] = $value ['main_actual_date'];
                    $dataResult [$key] ['earliest_date'] = $value ['main_earliest_distance'];
                    $dataResult [$key] ['latest_date'] = isset($value ['main_latest_distance']) ? $value ['main_latest_distance'] : '';
                    $dataResult [$key] ['form_list'] = isset($value ['form_list']) ? $value ['form_list'] : '';
                    $dataResult [$key] ['group_name'] = isset($value ['group_name']) ? $value ['group_name'] : '';

                    $count ++;
                } elseif ($key == '22222') {
                    $dataResult [$key] ['id'] = $key;
                    $dataResult [$key] ['ezf_id'] = $value ['random_ezf_id'];
                    $dataResult [$key] ['visit_name'] = $value ['form_name'];
                    $dataResult [$key] ['visit_name_mapping'] = $value ['random_visit_name'];
                    $dataResult [$key] ['actual_date'] = $value ['random_actual_date'];
                    $dataResult [$key] ['plan_date'] = $value ['random_plan_distance'];
                    $dataResult [$key] ['earliest_date'] = $value ['random_earliest_distance'];
                    $dataResult [$key] ['latest_date'] = $value ['random_latest_distance'];
                    $dataResult [$key] ['visit_cal_date'] = '11111';
                    $dataResult [$key] ['field_cal_date'] = 'actual_date';
                    $dataResult [$key] ['form_list'] = isset($value ['form_list']) ? $value ['form_list'] : '';
                    $dataResult [$key] ['group_name'] = isset($value ['group_name']) ? $value ['group_name'] : '';

                    $count ++;
                }
            }
        }

        foreach ($result as $key => $value) {
            $dataResult [$value ['id']] ['id'] = $value ['id'];
            $dataResult [$value ['id']] ['ezf_id'] = $value ['ezf_id'];
            $dataResult [$value ['id']] ['visit_name'] = $value ['visit_name'];
            $dataResult [$value ['id']] ['visit_name_mapping'] = $value ['visit_name_mapping'];
            $dataResult [$value ['id']] ['actual_date'] = $value ['actual_date'];
            $dataResult [$value ['id']] ['plan_date'] = $value ['plan_date'];
            $dataResult [$value ['id']] ['earliest_date'] = $value ['earliest_date'];
            $dataResult [$value ['id']] ['latest_date'] = $value ['latest_date'];
            $dataResult [$value ['id']] ['visit_cal_date'] = $value ['visit_cal_date'];
            $dataResult [$value ['id']] ['field_cal_date'] = $value ['field_cal_date'];
            $dataResult [$value ['id']] ['form_list'] = $value ['form_list'];
            $dataResult [$value ['id']] ['group_name'] = $value ['group_name'];

            $count ++;
        }

        return \appxq\sdii\utils\SDUtility::array2String($dataResult);
    }

    public static function getVisitScheduleById($widget_id, $visit_id) {
        $ezform = [];

        $query = new \yii\db\Query ();
        $query->select('*')->from('zdata_visit_schedule')->where('schedule_id=:schedule_id', [
            ':schedule_id' => $widget_id
        ]);

        $query->andWhere('visit_name=:visit_name', [
            ':visit_name' => $visit_id
        ]);

        $widget_ref = SubjectManagementQuery::getWidgetById($widget_id);
        $dataVisit = \appxq\sdii\utils\SDUtility::string2Array($widget_ref ['options']);
        $result = $query->one();
        $dataResult = $result;
        $count = 0;

        // \appxq\sdii\utils\VarDumper::dump($dataVisit);
        if (isset($dataVisit ['11111']) && $visit_id == '11111') {
            $value = $dataVisit ['11111'];
            $dataResult ['id'] = '11111';
            $dataResult ['ezf_id'] = $value ['main_ezf_id'];
            $dataResult ['visit_name'] = $value ['form_name'];
            $dataResult ['visit_name_mapping'] = isset($value ['main_visit_name']) ? $value ['main_visit_name'] : null;
            $dataResult ['actual_date'] = isset($value ['main_actual_date']) ? $value ['main_actual_date'] : null;
            $dataResult ['earliest_date'] = isset($value ['main_earliest_distance']) ? $value ['main_earliest_distance'] : null;
            $dataResult ['latest_date'] = isset($value ['main_latest_distance']) ? $value ['main_latest_distance'] : null;
            $dataResult ['form_list'] = isset($value ['form_list']) ? $value ['form_list'] : null;

            $count ++;
        } elseif (isset($dataVisit ['22222']) && $visit_id == '22222') {
            $value = $dataVisit ['22222'];
            $dataResult ['id'] = '22222';
            $dataResult ['ezf_id'] = $value ['random_ezf_id'];
            $dataResult ['visit_name'] = $value ['form_name'];
            $dataResult ['visit_name_mapping'] = isset($value ['random_visit_name']) ? $value ['random_visit_name'] : null;
            $dataResult ['actual_date'] = isset($value ['random_actual_date']) ? $value ['random_actual_date'] : null;
            $dataResult ['plan_date'] = isset($value ['random_plan_distance']) ? $value ['random_plan_distance'] : null;
            $dataResult ['earliest_date'] = isset($value ['random_earliest_distance']) ? $value ['random_earliest_distance'] : null;
            $dataResult ['latest_date'] = isset($value ['random_latest_distance']) ? $value ['random_latest_distance'] : null;
            $dataResult ['form_list'] = isset($value ['form_list']) ? $value ['form_list'] : null;

            $count ++;
        }

        return $dataResult;
    }

    public static function getVisitScheduleByFunc($widget_id, $group_id = null, $where = null, $visit_id = null, $ezf_id = null) {
        $ezform = [];
        if ($ezf_id != null) {
            $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
        }
        if ($group_id == null) {
            $group_id = isset($_SESSION ['group_id']) ? $_SESSION ['group_id'] : '';
        }
        $query = new \yii\db\Query ();
        $query->select('*')->from('zdata_visit_schedule')->where('schedule_id=:schedule_id AND sitecode=:sitecode', [
            ':schedule_id' => $widget_id,
            ':sitecode' => Yii::$app->user->identity->profile->sitecode
        ]);

        if ($group_id != null) {
            $query->andWhere('group_name=:group_id', [
                ':group_id' => $group_id
            ]);
        }

        if ($where != null) {
            $query->andWhere($where);
        }

        if ($ezf_id != null) {
            if ($ezform ['public_listview'] == 2) {
                $query->andWhere('sitecode = :site', [
                    ':site' => Yii::$app->user->identity->profile->sitecode
                ]);
            }

            if ($ezform ['public_listview'] == 0) {
                $query->andWhere("created_by=:created_by", [
                    ':created_by' => Yii::$app->user->id
                ]);
            }
        }

        $query->andWhere('rstat NOT IN(0,3)');
        $widget_ref = SubjectManagementQuery::getWidgetById($widget_id);
        $dataVisit = \appxq\sdii\utils\SDUtility::string2Array($widget_ref ['options']);
        $result = $query->all();
        $dataResult = [];
        $count = 0;

        foreach ($dataVisit as $key => $value) {
            if ($key == '11111') {
                $dataResult [$key] = $value ['form_name'];
                $count ++;
            } elseif ($key == '22222') {
                $dataResult [$key] = $value ['form_name'];
                ;
                $count ++;
            }
        }
        foreach ($result as $key => $value) {
            $dataResult [$value ['id']] = $value ['visit_name'];
            $count ++;
        }

        return $dataResult;
    }

    public static function getVisitScheduleByInput($widget_id, $group_id = null, $where = null, $visit_id = null) {
        $ezform = [];
        if (isset($ezf_id) && $ezf_id != null) {
            $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
        }
        if ($group_id == null) {
            $group_id = isset($_SESSION ['group_id']) ? $_SESSION ['group_id'] : null;
        }
        $query = new \yii\db\Query ();
        $query->select('zdata_visit_schedule.*')->distinct()->from('zdata_visit_schedule')->innerJoin('zdata_subject_group', 'zdata_subject_group.id=zdata_visit_schedule.group_name')->where('schedule_id=:schedule_id AND zdata_visit_schedule.sitecode=:sitecode', [
            ':schedule_id' => $widget_id,
            ':sitecode' => Yii::$app->user->identity->profile->sitecode
        ])->andWhere('zdata_subject_group.rstat NOT IN(0,3)');

        $query->andWhere('zdata_visit_schedule.group_name=:group_id OR zdata_visit_schedule.group_name=0', [
            ':group_id' => $group_id
        ]);

        if (isset($where) && $where != null) {
            $query->andWhere($where);
        }

        if (isset($ezf_id) && $ezf_id != null) {
            if ($ezform ['public_listview'] == 2) {
                $query->andWhere('sitecode = :site', [
                    ':site' => Yii::$app->user->identity->profile->sitecode
                ]);
            }

            if ($ezform ['public_listview'] == 0) {
                $query->andWhere("created_by=:created_by", [
                    ':created_by' => Yii::$app->user->id
                ]);
            }
        }
        $query->andWhere('zdata_visit_schedule.rstat NOT IN(0,3)');
        $widget_ref = SubjectManagementQuery::getWidgetById($widget_id);
        $dataVisit = \appxq\sdii\utils\SDUtility::string2Array($widget_ref ['options']);
        $result = $query->all();
        $dataResult = [];
        $count = 0;

        foreach ($dataVisit as $key => $value) {
            if ($key == '11111') {
                if (isset($value ['enable_visit']) && $value ['enable_visit'] == '1') {
                    $dataResult [$count] ['id'] = $key;
                    $dataResult [$count] ['name'] = $value ['form_name'];
                    $count ++;
                }
            } elseif ($key == '22222') {
                if (isset($value ['enable_visit']) && $value ['enable_visit'] == '1') {
                    $dataResult [$count] ['id'] = $key;
                    $dataResult [$count] ['name'] = $value ['form_name'];
                    $count ++;
                }
            }
        }
        foreach ($result as $key => $value) {
            $dataResult [$count] ['id'] = [
                $value ['id']
            ];
            $dataResult [$count] ['name'] = [
                $value ['visit_name']
            ];
            $count ++;
        }

        return $dataResult;
    }

    public static function getVisitScheduleByEzf($widget_id, $group_id = null, $ezf_id = null) {
        $ezform = [];
        if ($ezf_id != null) {
            $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
        }
        if ($group_id == null) {
            $group_id = $_SESSION ['group_id'];
        }
        $query = new \yii\db\Query ();
        $query->select('*')->from('zdata_visit_schedule')->where('schedule_id=:schedule_id', [
            ':schedule_id' => $widget_id
        ]);
        if ($group_id != null) {
            $query->andWhere('group_name=:group_id', [
                ':group_id' => $group_id
            ]);
        }

        if ($ezf_id != null) {
            if ($ezform ['public_listview'] == 2) {
                $query->andWhere('sitecode = :site', [
                    ':site' => Yii::$app->user->identity->profile->sitecode
                ]);
            }

            if ($ezform ['public_listview'] == 0) {
                $query->andWhere("created_by=:created_by", [
                    ':created_by' => Yii::$app->user->id
                ]);
            }
        }

        $widget_ref = SubjectManagementQuery::getWidgetById($widget_id);
        $dataVisit = \appxq\sdii\utils\SDUtility::string2Array($widget_ref ['options']);
        $result = $query->all();
        $dataResult = [];
        $count = 0;

        foreach ($dataVisit as $key => $value) {
            if ($key == '11111') {
                $dataResult [$count] ['id'] = $key;
                $dataResult [$count] ['ezf_id'] = $value ['main_ezf_id'];
                $dataResult [$count] ['visit_name'] = $value ['form_name'];
                $dataResult [$count] ['visit_name_mapping'] = $value ['main_visit_name'];
                $dataResult [$count] ['actual_date'] = $value ['main_actual_date'];
                $dataResult [$count] ['earliest_date'] = $value ['main_earliest_distance'];
                $dataResult [$count] ['latest_date'] = $value ['main_latest_distance'];

                $count ++;
            } elseif ($key == '22222') {
                $dataResult [$count] ['id'] = $key;
                $dataResult [$count] ['ezf_id'] = $value ['random_ezf_id'];
                $dataResult [$count] ['visit_name'] = $value ['form_name'];
                $dataResult [$count] ['visit_name_mapping'] = $value ['random_visit_name'];
                $dataResult [$count] ['actual_date'] = $value ['random_actual_date'];
                $dataResult [$count] ['plan_date'] = $value ['random_plan_distance'];
                $dataResult [$count] ['earliest_date'] = $value ['random_earliest_distance'];
                $dataResult [$count] ['latest_date'] = $value ['random_latest_distance'];

                $count ++;
            }
        }
        foreach ($result as $key => $value) {
            $dataResult [$count] ['id'] = $value ['id'];
            $dataResult [$count] ['ezf_id'] = $value ['ezf_id'];
            $dataResult [$count] ['visit_name'] = $value ['visit_name'];
            $dataResult [$count] ['visit_name_mapping'] = $value ['visit_name_mapping'];
            $dataResult [$count] ['actual_date'] = $value ['actual_date'];
            $dataResult [$count] ['plan_date'] = $value ['plan_date'];
            $dataResult [$count] ['earliest_date'] = $value ['earliest_date'];
            $dataResult [$count] ['latest_date'] = $value ['latest_date'];
            $dataResult [$count] ['visit_cal_date'] = $value ['visit_cal_date'];
            $dataResult [$count] ['field_cal_date'] = $value ['field_cal_date'];

            $count ++;
        }

        return json_encode($dataResult);
    }

    public static function getGroupScheduleByWidget($widget_id, $ezform_group = null, $field = null) {
        $ezform = [];

        $widget_ref = SubjectManagementQuery::getWidgetById($widget_id);
        $dataSchedule = \appxq\sdii\utils\SDUtility::string2Array($widget_ref ['options']);
        if ($ezform_group == null) {
            $ezform_group = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($dataSchedule ['group_ezf_id']);
        }

        if ($field == null) {
            $field = $dataSchedule ['group_field'];
        }
        $query = new \yii\db\Query ();
        $query->select('*')->from($ezform_group->ezf_table)->where("IFNULL({$field},'')<>'' ");

        $query->andWhere('rstat NOT IN(0,3)');

        if ($ezform_group != null) {
            if ($ezform_group ['public_listview'] == 2) {
                $query->andWhere('sitecode = :site', [
                    ':site' => Yii::$app->user->identity->profile->sitecode
                ]);
            }

            if ($ezform_group ['public_listview'] == 0) {
                $query->andWhere("user_create=:created_by", [
                    ':created_by' => Yii::$app->user->id
                ]);
            }
        }

        $dataResult = [];
        $result = $query->all();
        $dataResult [0] ['id'] = '1';
        $dataResult [0] ['group_name'] = $dataSchedule ['group_name'];

        foreach ($result as $key => $value) {
            $dataResult [$key + 1] ['id'] = $value ['id'];
            $dataResult [$key + 1] ['group_name'] = $value [$field];
        }
        return $dataResult;
    }

    public static function getGroupScheduleByFunc($widget_id, $ezform_group = null, $field = null) {
        $ezform = [];
        $dataResult = [];

        $widget_ref = SubjectManagementQuery::getWidgetById($widget_id);
        $dataSchedule = \appxq\sdii\utils\SDUtility::string2Array($widget_ref ['options']);
        if ($ezform_group == null && $dataSchedule) {
            $ezform_group = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($dataSchedule ['group_ezf_id']);
        }

        if ($field == null && $dataSchedule) {
            $field = $dataSchedule ['group_field'];
        }
        if ($ezform_group) {
            $query = new \yii\db\Query ();
            $query->select('*')->from($ezform_group->ezf_table)->where($field . '<> "" ');

            $query->andWhere('rstat NOT IN(0,3)');

            if ($ezform_group != null) {
                if ($ezform_group ['public_listview'] == 2) {
                    $query->andWhere('sitecode = :site', [
                        ':site' => Yii::$app->user->identity->profile->sitecode
                    ]);
                }

                if ($ezform_group ['public_listview'] == 0) {
                    $query->andWhere("user_create=:created_by", [
                        ':created_by' => Yii::$app->user->id
                    ]);
                }
            }

            $result = $query->all();
            $dataResult [0] = "All Group";
            // $dataResult[1] = $dataSchedule['group_name'];

            foreach ($result as $key => $value) {
                $dataResult [$value ['id']] = $value [$field];
            }
        }
        return $dataResult;
    }

    public static function getVisitScheduleBySite($sitecode = null, $ezf_id = null) {
        $ezform = [];
        if ($ezf_id != null) {
            $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
        }
        $query = new \yii\db\Query ();
        $query->select('*')->from('zdata_visit_schedule');

        $query->andWhere('sitecode = :site', [
            ':site' => Yii::$app->user->identity->profile->sitecode
        ]);
        $data = $query->all();
        return ArrayHelper::map($data, 'visit_name', 'visit_name');
        ;
    }

    public static function getSubjectProcedureByName($procedureName, $group_name = null, $visit_id = null, $type = null) {
        $query = new \yii\db\Query ();
        $query->select('*')->from('zdata_visit_procedure')->where('procedure_name=:procedure_name', [
            ':procedure_name' => $procedureName
        ]);
        if ($group_name != null) {
            $query->andWhere('group_name=:group_name', [
                ':group_name' => $group_name
            ]);
        }
        if ($visit_id != null) {
            $query->andWhere('visit_name=:visit_name', [
                ':visit_name' => $visit_id
            ]);
        }

        $resule = $query->all();
        return $resule;
    }

    public static function getSubjectProcedureByVisit($visitName, $widget_id, $group_name = null, $type = null) {
        $procedure_widget_ref = SubjectManagementQuery::getWidgetById($widget_id);
        $procedure_data = \appxq\sdii\utils\SDUtility::string2Array($procedure_widget_ref ['options']);
        $procedureForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($procedure_data ['procedure_ezf_id']);
        $table2 = "";
        if (isset($procedureForm) && $procedureForm != '') {
            $table2 = $procedureForm->ezf_table;
        }
        $query = new \yii\db\Query ();
        $query->select('zdata_visit_procedure.*')->from('zdata_visit_procedure')->where("1=1");
        if ($table2 !== '') {
            $query->innerJoin($table2, "zdata_visit_procedure.target=" . $table2 . ".id");
            $query->andWhere($table2 . '.rstat NOT IN(0,3)');
        }
        if ($group_name != null) {
            $query->andWhere('zdata_visit_procedure.group_name=:group_name', [
                ':group_name' => $group_name
            ]);
        }

        $query->andWhere('zdata_visit_procedure.group_name<>"" OR zdata_visit_procedure.group_name IS NOT NULL');
        $query->andWhere('visit_name=:visit_name', [
                    ':visit_name' => $visitName
                ])->
                // ->andWhere('module_id=:module_id',[':module_id'=>$mid])
                andWhere('widget_id=:widget_id', [
                    ':widget_id' => $widget_id
                ])->all();

        $resule = $query->all();
        return $resule;
    }

    public static function getSubjectProcedureById($subjectId) {
        $query = new \yii\db\Query ();
        $resule = $query->select('*')->from('zdata_visit_procedure')->where('id=:id', [
                    ':id' => $subjectId
                ])->
                // ->andWhere('ezm_id=:mid',[':mid'=>$mid])
                one();
        return $resule;
    }

    public static function getProcedureBudgetApprovedBySubject($budget_form, $target, $visit_name = null, $group_id = null) {
        $table_budget = "zdata_budget_procedure";
        if (isset($budget_form->ezf_table)) {
            $table_budget = $budget_form->ezf_table;
        }
        $sql = "SELECT tb_pro.*, sva.procedure_name as pro_approved ,sva.approved_date FROM  
            (SELECT DISTINCT vp.visit_name,vp.procedure_name, budget, bp.financial_type ,bp.xsourcex,bp.xdepartmentx,bp.user_create
            FROM ( SELECT zvp.group_name,zvp.procedure_name,zvp.visit_name,'' as budget_id FROM zdata_visit_procedure zvp WHERE visit_name='$visit_name' 
                   UNION
                   SELECT '$group_id' as group_name, sap.procedure_name, sap.visit_name,budget_id 
                   FROM  subject_additional_payment sap WHERE visit_name='$visit_name' AND subject_target_id='$target' ) vp
            INNER JOIN  " . $table_budget . " bp 
            ON vp.procedure_name=bp.pro_name 
            WHERE vp.group_name='$group_id' AND bp.rstat NOT IN(0,3)
            AND vp.visit_name = '$visit_name' ) tb_pro
            LEFT JOIN (SELECT * FROM subject_visit_approved WHERE subject_target_id='$target' )sva
            ON sva.procedure_name=tb_pro.procedure_name AND sva.visit_name=tb_pro.visit_name  
            INNER JOIN zdata_visit_schedule zvs ON tb_pro.visit_name=zvs.id ";

        $param = [];
        if ($budget_form ['public_listview'] == 2) {
            $sql .= " AND tb_pro.xsourcex = '" . Yii::$app->user->identity->profile->sitecode . "' ";
            $param [':site'] = Yii::$app->user->identity->profile->sitecode;
        }

        if ($budget_form ['public_listview'] == 3) {
            $sql .= " AND tb_pro.xdepartmentx = '" . Yii::$app->user->identity->profile->department . "' ";
            $param [':unit'] = Yii::$app->user->identity->profile->department;
        }

        if ($budget_form ['public_listview'] == 0) {
            $sql .= " AND tb_pro.user_create='" . Yii::$app->user->id . "' ";
            $param [':created_by'] = Yii::$app->user->id;
        }
        $result = \Yii::$app->db->createCommand($sql)->queryAll();

        $data_budget = SubjectManagementQuery::GetTableData($budget_form, "enable_visit IS NOT NULL AND (group_name='$group_id' OR group_name='0')");

        foreach ($data_budget as $key => $val) {
            $enableVisit = \appxq\sdii\utils\SDUtility::string2Array($val ['enable_visit']);
            if (in_array($visit_name, $enableVisit)) {
                $checkApproved = SubjectManagementQuery::GetTableData('subject_visit_approved', [
                            'procedure_name' => $val ['pro_name'],
                            'visit_name' => $visit_name,
                            'subject_target_id' => $target
                                ], 'one');
                $val ['pro_approved'] = '';
                if ($checkApproved)
                    $val ['pro_approved'] = $val ['pro_name'];

                $result [] = $val;
            }
        }

        return $result;
    }

    public static function getSumBudgetApprovedRevenue($study_form, $where = null) {
        $table_budget = "zdata_study_payment";
        if (isset($study_form->ezf_table)) {
            $table_budget = $study_form->ezf_table;
        }
        $sql = " SELECT SUM(amount) as sum_amount FROM $table_budget WHERE rstat NOT IN (0,3) ";

        $param = [];

        if ($study_form ['public_listview'] == 2) {
            $sql .= " AND xsourcex = '" . Yii::$app->user->identity->profile->sitecode . "' ";
            $param [':site'] = Yii::$app->user->identity->profile->sitecode;
        }

        if ($study_form ['public_listview'] == 3) {
            $sql .= " AND xdepartmentx = '" . Yii::$app->user->identity->profile->department . "' ";
            $param [':unit'] = Yii::$app->user->identity->profile->department;
        }

        if ($study_form ['public_listview'] == 0) {
            $sql .= " AND user_create='" . Yii::$app->user->id . "' ";
            $param [':created_by'] = Yii::$app->user->id;
        }
        if ($where != null)
            $sql .= " AND " . $where;

        $result = \Yii::$app->db->createCommand($sql)->queryOne();

        return $result;
    }

    public static function getSumBudgetApprovedExpense($budget_form, $expense_section, $sumColum, $where = null, $visit_name = null, $group_id = null) {
        $table_budget = "zdata_budget_procedure";
        if (isset($budget_form->ezf_table)) {
            $table_budget = $budget_form->ezf_table;
        }
        if ($sumColum == 'budget') {
            $sumColum = $sumColum . "-(hos_fee+crc_fee+profesional_fees)";
        }
        $sql = "SELECT SUM($sumColum) as sum_budget FROM 
                (SELECT DISTINCT tb_pro.visit_name, tb_pro.procedure_name, sva.subject_target_id,tb_pro.budget,tb_pro.hos_fee,tb_pro.crc_fee,tb_pro.profesional_fees FROM
                (SELECT DISTINCT vp.visit_name,vp.procedure_name, budget,hos_fee,crc_fee, profesional_fees, financial_type ,bp.xsourcex,bp.xdepartmentx,bp.user_create
                FROM 
                    ( SELECT zvp.procedure_name,zvp.visit_name FROM zdata_visit_procedure zvp 
                    UNION
                    SELECT sap.procedure_name, sap.visit_name 
                    FROM subject_additional_payment sap ) vp
                INNER JOIN zdata_budget_procedure bp ON vp.procedure_name=bp.pro_name 
                WHERE bp.rstat NOT IN('0','3') $expense_section ) tb_pro
                INNER JOIN (SELECT sa.* FROM subject_visit_approved sa INNER JOIN zdata_subject_detail sd ON sa.subject_target_id=sd.target WHERE 1=1 $where )sva
                ON sva.procedure_name=tb_pro.procedure_name AND sva.visit_name=tb_pro.visit_name ";

        $param = [];
        if ($budget_form ['public_listview'] == 2) {
            $sql .= " AND tb_pro.xsourcex = '" . Yii::$app->user->identity->profile->sitecode . "' ";
            $param [':site'] = Yii::$app->user->identity->profile->sitecode;
        }

        if ($budget_form ['public_listview'] == 3) {
            $sql .= " AND tb_pro.xdepartmentx = '" . Yii::$app->user->identity->profile->department . "' ";
            $param [':unit'] = Yii::$app->user->identity->profile->department;
        }

        if ($budget_form ['public_listview'] == 0) {
            $sql .= " AND tb_pro.user_create='" . Yii::$app->user->id . "' ";
            $param [':created_by'] = Yii::$app->user->id;
        }

        $sql .= " ) as finaly_table ";
        $result = \Yii::$app->db->createCommand($sql)->queryOne();

        return $result;
    }

    public static function getGroupByTarget($detail_form, $target) {
        $query = new \yii\db\Query ();
        $query->select('group_name')->from($detail_form->ezf_table)->where([
            'target' => $target,
            'visit_name' => '22222'
        ]);

        if ($detail_form ['public_listview'] == 2) {
            $query->andWhere('xsourcex = :site', [
                ':site' => Yii::$app->user->identity->profile->sitecode
            ]);
        }

        if ($detail_form ['public_listview'] == 3) {
            $query->andWhere('xdepartmentx = :unit', [
                ':unit' => Yii::$app->user->identity->profile->department
            ]);
        }

        if ($detail_form ['public_listview'] == 0) {
            $query->andWhere("user_create=:created_by", [
                ':created_by' => Yii::$app->user->id
            ]);
        }

        $query->andWhere(" rstat NOT IN(0,3) ");

        $result = $query->one();
        return $result;
    }

    public static function getInvoiceSubjectReport($budget_form, $group_id) {
        $table_budget = "zdata_budget_procedure";
        if (isset($budget_form->ezf_table)) {
            $table_budget = $budget_form->ezf_table;
        }
        $sql = "SELECT sva.subject_target_id,sva.visit_name, SUM(budget) as sum_budget,SUM(IF(sva.procedure_name IS NOT NULL,1,0))as approved_pro FROM 
                (SELECT DISTINCT vp.visit_name,vp.procedure_name, budget, financial_type ,bp.xsourcex,bp.xdepartmentx,bp.user_create
                FROM 
                        ( SELECT zvp.group_name,zvp.procedure_name,zvp.visit_name,'' as budget_id 
                            FROM zdata_visit_procedure zvp
                        UNION
                        SELECT '$group_id' as group_name, sap.procedure_name, sap.visit_name,budget_id 
                        FROM subject_additional_payment sap  ) vp
                INNER JOIN zdata_budget_procedure bp 
                ON vp.procedure_name=bp.pro_name 
                WHERE vp.group_name='$group_id' AND bp.rstat NOT IN('0','3')  ) tb_pro
                LEFT JOIN (SELECT * FROM subject_visit_approved WHERE approved_date IS NOT NULL )sva
                ON sva.procedure_name=tb_pro.procedure_name AND sva.visit_name=tb_pro.visit_name WHERE sva.subject_target_id IS NOT NULL  ";

        $param = [];
        if ($budget_form ['public_listview'] == 2) {
            $sql .= " AND tb_pro.xsourcex = '" . Yii::$app->user->identity->profile->sitecode . "' ";
            $param [':site'] = Yii::$app->user->identity->profile->sitecode;
        }

        if ($budget_form ['public_listview'] == 3) {
            $sql .= " AND tb_pro.xdepartmentx = '" . Yii::$app->user->identity->profile->department . "' ";
            $param [':unit'] = Yii::$app->user->identity->profile->department;
        }

        if ($budget_form ['public_listview'] == 0) {
            $sql .= " AND tb_pro.user_create='" . Yii::$app->user->id . "' ";
            $param [':created_by'] = Yii::$app->user->id;
        }

        $sql .= "GROUP BY sva.subject_target_id,tb_pro.visit_name
                ORDER BY sva.subject_target_id,sva.approved_date ";
        $result = \Yii::$app->db->createCommand($sql)->queryAll();
        return $result;
    }

    public static function convertDate($date, $type = null) {
        $inx = 1;
        if ($type == 'full') {
            $inx = 0;
        }
        if (isset($date) && $date != '') {
            $month = [];
            $month [0] = [
                \Yii::t('subjects', 'January'),
                \Yii::t('subjects', 'Jan')
            ];
            $month [1] = [
                \Yii::t('subjects', 'February'),
                \Yii::t('subjects', 'Feb')
            ];
            $month [2] = [
                \Yii::t('subjects', 'March'),
                \Yii::t('subjects', 'Mar')
            ];
            $month [3] = [
                \Yii::t('subjects', 'April'),
                \Yii::t('subjects', 'Apr')
            ];
            $month [4] = [
                \Yii::t('subjects', 'May'),
                \Yii::t('subjects', 'Ma')
            ];
            $month [5] = [
                \Yii::t('subjects', 'June'),
                \Yii::t('subjects', 'Jun')
            ];
            $month [6] = [
                \Yii::t('subjects', 'July'),
                \Yii::t('subjects', 'Jul')
            ];
            $month [7] = [
                \Yii::t('subjects', 'August'),
                \Yii::t('subjects', 'Aug')
            ];
            $month [8] = [
                \Yii::t('subjects', 'September'),
                \Yii::t('subjects', 'Sep')
            ];
            $month [9] = [
                \Yii::t('subjects', 'October'),
                \Yii::t('subjects', 'Oct')
            ];
            $month [10] = [
                \Yii::t('subjects', 'November'),
                \Yii::t('subjects', 'Nov')
            ];
            $month [11] = [
                \Yii::t('subjects', 'December'),
                \Yii::t('subjects', 'Dec')
            ];

            $dateStamp = strtotime($date);
            $y = date('Y', $dateStamp);
            $m = date('m', $dateStamp);
            $d = date('d', $dateStamp);

            $convert = $d . ' ' . $month [((int) $m - 1)] [$inx] . ' ' . ($y + (int) Yii::t('subjects', 'Y'));
        } else {
            $convert = '';
        }
        return $convert;
    }

    public static function getGroupNameById($ezform, $target) {
        $table = "zdata_subject_group";
        if (isset($ezform->ezf_table)) {
            $table = $ezform->ezf_table;
        }
        $query = new \yii\db\Query ();
        $query->select('*')->from($table)->where([
            'id' => $target
        ])->andWhere("rstat NOT IN(0,3)");

        $result = $query->one();
        return $result;
    }

    public static function getVisitProcedure($ezform_budget, $visit_id, $group_id = null, $schedule_id = null) {
        $sitecode = \Yii::$app->user->identity->profile->sitecode;
        $where = "";
        $where2 = "";
        $where3 = "";
        // if($group_id == null){
        // $group_id = $this->getGroupScheduleByWidget($schedule_id);
        // }
        if ($group_id != null) {
            $where = " AND zvp.group_name='$group_id' ";
            $where2 = " AND (zbp.group_name='$group_id' OR zbp.group_name='0') ";
            $where3 = " AND (zb.group_name='$group_id' OR zb.group_name='0') ";
        }
        $sql = "SELECT zvp.visit_name,zvp.procedure_name,zvp.group_name
            FROM zdata_visit_procedure zvp
            INNER JOIN zdata_procedure zp ON zvp.procedure_name=zp.id
            INNER JOIN " . $ezform_budget->ezf_table . " zbp ON zvp.procedure_name=zbp.pro_name
            WHERE (zvp.visit_name IN (SELECT id FROM zdata_visit_schedule WHERE rstat NOT IN (0,3)) 
            OR (zvp.visit_name='11111' OR  zvp.visit_name='22222')) 
            AND zvp.visit_name='$visit_id' $where AND zvp.sitecode='$sitecode'  AND zp.rstat NOT IN(0,3)
            AND zbp.rstat NOT IN(0,3) AND zbp.sitecode='$sitecode' $where2
                
                ";
        if ($ezform_budget ['public_listview'] == 2) {
            $sql .= " AND zbp.xsourcex =" . Yii::$app->user->identity->profile->sitecode;
        }

        if ($ezform_budget ['public_listview'] == 3) {
            $sql .= " AND zbp.xdepartmentx =" . Yii::$app->user->identity->profile->department;
        }

        if ($ezform_budget ['public_listview'] == 0) {
            $sql .= " AND zbp.user_create =" . Yii::$app->user->id;
        }

        $sql .= " GROUP BY zvp.group_name ,zvp.visit_name, zvp.procedure_name ORDER BY zvp.procedure_name ";
        $result = \Yii::$app->db->createCommand($sql)->queryAll();

        $sql_budget = "SELECT zb.*,zb.pro_name as procedure_name FROM " . $ezform_budget->ezf_table . " zb 
                INNER JOIN zdata_procedure zp
                ON zb.pro_name=zp.id WHERE zb.enable_visit IS NOT NULL $where3 AND zp.rstat NOT IN(0,3) AND zb.rstat NOT IN(0,3)  ";

        if ($ezform_budget ['public_listview'] == 2) {
            $sql_budget .= " AND zb.xsourcex ='" . Yii::$app->user->identity->profile->sitecode . "'";
        }

        if ($ezform_budget ['public_listview'] == 3) {
            $sql_budget .= " AND zb.xdepartmentx ='" . Yii::$app->user->identity->profile->department . "'";
        }

        if ($ezform_budget ['public_listview'] == 0) {
            $sql_budget .= " AND zb.user_create ='" . Yii::$app->user->id . "'";
        }
        $sql_budget .= " GROUP BY zb.group_name,zb.pro_name ";
        $data_budget = Yii::$app->db->createCommand($sql_budget)->queryAll();

        $data = [];
        foreach ($data_budget as $key => $val) {
            $enableVisit = \appxq\sdii\utils\SDUtility::string2Array($val ['enable_visit']);
            if (in_array($visit_id, $enableVisit)) {

                $checkSChedule = SubjectManagementQuery::GetTableData('zdata_visit_schedule', [
                            'id' => $visit_id
                                ], 'one');

                if ($checkSChedule || $visit_id == '11111' || $visit_id == '22222')
                    $result [] = $val;
            }
        }

        return $result;
    }

    public static function getVisitBudgetProcedure($ezform_budget, $columns = null) {
        $sitecode = \Yii::$app->user->identity->profile->sitecode;
        $sql = "SELECT DISTINCT {$ezform_budget->ezf_table}.id," . join($columns, ',') . ",enable_visit  FROM(
            SELECT zvp.id ,zvp.procedure_name
            FROM zdata_visit_procedure zvp
            INNER JOIN zdata_procedure zp ON zvp.procedure_name=zp.id
            WHERE (zvp.visit_name IN (SELECT id FROM zdata_visit_schedule WHERE rstat NOT IN (0,3)) 
            OR (zvp.visit_name='11111' OR  zvp.visit_name='22222')) 
            AND zvp.sitecode='$sitecode'  AND zp.rstat NOT IN(0,3)
                GROUP BY zvp.group_name ,zvp.visit_name, zvp.procedure_name ORDER BY zvp.procedure_name) table1
            INNER JOIN " . $ezform_budget->ezf_table . "  ON table1.procedure_name=" . $ezform_budget->ezf_table . ".pro_name 
            WHERE " . $ezform_budget->ezf_table . ".rstat NOT IN(0,3) 
            ";
        if ($ezform_budget ['public_listview'] == 2) {
            $sql .= " AND " . $ezform_budget->ezf_table . ".xsourcex ='" . Yii::$app->user->identity->profile->sitecode . "'";
        }

        if ($ezform_budget ['public_listview'] == 3) {
            $sql .= " AND " . $ezform_budget->ezf_table . ".xdepartmentx ='" . Yii::$app->user->identity->profile->department . "'";
        }

        if ($ezform_budget ['public_listview'] == 0) {
            $sql .= " AND " . $ezform_budget->ezf_table . ".user_create ='" . Yii::$app->user->id . "'";
        }

        $sql .= " ORDER BY " . $ezform_budget->ezf_table . ".create_date DESC ";

        $result = \Yii::$app->db->createCommand($sql)->queryAll();

        $sql_budget = "SELECT " . $ezform_budget->ezf_table . ".id, " . join($columns, ',') . ",enable_visit FROM " . $ezform_budget->ezf_table . " 
                INNER JOIN zdata_procedure zp
                ON " . $ezform_budget->ezf_table . ".pro_name=zp.id WHERE enable_visit<>'' AND enable_visit IS NOT NULL AND zp.rstat NOT IN(0,3) 
                AND " . $ezform_budget->ezf_table . ".rstat NOT IN(0,3) ";
        if ($ezform_budget ['public_listview'] == 2) {
            $sql_budget .= " AND " . $ezform_budget->ezf_table . ".xsourcex ='" . Yii::$app->user->identity->profile->sitecode . "'";
        }

        if ($ezform_budget ['public_listview'] == 3) {
            $sql_budget .= " AND " . $ezform_budget->ezf_table . ".xdepartmentx ='" . Yii::$app->user->identity->profile->department . "'";
        }

        if ($ezform_budget ['public_listview'] == 0) {
            $sql_budget .= " AND " . $ezform_budget->ezf_table . ".user_create ='" . Yii::$app->user->id . "'";
        }

        $sql_budget .= " GROUP BY " . $ezform_budget->ezf_table . ".group_name," . $ezform_budget->ezf_table . ".pro_name ";
        $data_budget = Yii::$app->db->createCommand($sql_budget)->queryAll();

        foreach ($data_budget as $key => $val) {
            $result [] = $val;
        }

        return $result;
    }

    public static function getVisitProcedureApproved($visit_id, $group_id = null, $target = null) {
        $sitecode = \Yii::$app->user->identity->profile->sitecode;
        $where = "";
        $where2 = "";
        if ($group_id != null) {
            $where = " AND (group_name='$group_id' OR group_name='0') ";
            $where2 = " AND zvp.group_name='$group_id' ";
        }
        $sql = " SELECT DISTINCT va.id,va.visit_name,va.procedure_name,va.subject_target_id FROM subject_visit_approved va 
            INNER JOIN zdata_visit_procedure zvp ON  va.procedure_name=zvp.procedure_name AND va.visit_name=zvp.visit_name
            INNER JOIN zdata_procedure zp ON zvp.procedure_name=zp.id
            WHERE va.procedure_name IN(SELECT pro_name FROM zdata_budget_procedure WHERE 1=1  $where AND sitecode='$sitecode' AND rstat NOT IN(0,3)) 
            AND zp.rstat NOT IN(0,3) AND va.visit_name='$visit_id' AND va.sitecode='$sitecode'  $where2";
        if ($target != null) {
            $sql .= " AND va.subject_target_id= '$target'";
        }

        $result = \Yii::$app->db->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getVisitProcedureAddition($visit_id, $group_id, $target = null) {
        $sitecode = \Yii::$app->user->identity->profile->sitecode;
        $where = "";
        if ($group_id != null) {
            $where = " AND (zbp.group_name='$group_id' ) ";
        }
        $sql = " SELECT DISTINCT ap.id,ap.visit_name,ap.procedure_name,ap.subject_target_id FROM subject_additional_payment ap 
            INNER JOIN zdata_budget_procedure zbp ON  ap.procedure_name=zbp.pro_name
            WHERE ap.visit_name='$visit_id' AND ap.sitecode='$sitecode' $where AND zbp.rstat NOT IN(0,3)";
        if ($target != null) {
            $sql .= " AND ap.subject_target_id= '$target'";
        }

        $result = \Yii::$app->db->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getEzformAll($ezf_id) {
        $model = Ezform::find()->where('ezf_id<>:ezf_id', [
                    ':ezf_id' => $ezf_id
                ])->andWhere('ezform.status = :status', [
                    ':status' => 1
                ])->all();
        return $model;
    }

    public static function getMenuContent($ezf_table, $id, $where) {
        $model_menu = new \backend\modules\ezforms2\models\TbdataAll ();
        $model_menu->setTableName($ezf_table);

        $model_menu = $model_menu->find()->where("rstat not in(0,3) AND id=:id", [
                    ':id' => $id
                ])->andWhere($where)->one();

        return $model_menu;
    }

    public static function getContentForm($ezf_table, $id) {
        $model_menu = new \backend\modules\ezforms2\models\TbdataAll ();
        $model_menu->setTableName($ezf_table);

        $model_menu = $model_menu->find()->where("rstat not in(0,3) AND id=:id", [
                    ':id' => $id
                ])->one();

        return $model_menu;
    }

    public static function getMenu($ezf_table, $parent = '0', $sitecode) {
        $model_menu = new \backend\modules\ezforms2\models\TbdataAll ();
        $model_menu->setTableName($ezf_table);

        $whereStr = 'AND sitecode=:sitecode AND (menu_parent is null OR menu_parent = 0)';
        $params = [
            ':sitecode' => $sitecode
        ];
        if ($parent > 0) {
            $whereStr = ' AND sitecode=:sitecode AND menu_parent = :parent';
            $params = [
                ':sitecode' => $sitecode,
                ':parent' => $parent . ''
            ];
        }

        $model_menu = $model_menu->find()->where("rstat not in(0,3) $whereStr", $params)->orderBy('menu_order')->all();

        return $model_menu;
    }

    public static function importVisitScheduleSave($data) {
        $response ['success'] = 0;
        $response ['fail'] = 0;
        foreach ($data as $key => $val) {
            $visitModel = new \backend\modules\subjects\models\VisitSchedule ();
            $visitModel->attributes = $val;

            try {
                if ($visitModel->save()) {
                    $response ['success'] += 1;
                } else {
                    $response ['fail'] += 1;
                }
            } catch (yii\db\Exception $e) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            }
        }

        return $response;
    }

    public static function getEzformsSubjectProfile2($q = null) {
        $profile_ezf = "1521941065093085100";
        $params = [];
        $model1 = Ezform::find()->select([
                    Ezform::tableName() . '.ezf_id',
                    'ezf_name'
                ])->distinct()->innerJoin('ezform_fields', 'ezform_fields.ezf_id=' . Ezform::tableName() . '.ezf_id')->where('status =1')->andWhere(Ezform::tableName() . '.created_by=:user_id  ', [
                    ':user_id' => Yii::$app->user->id
                ])->andWhere([
                    'ezf_crf' => '1'
                ])->andWhere([
                    'ezform_fields.ref_ezf_id' => $profile_ezf
                ])->orderBy(Ezform::tableName() . '.created_at DESC')->all();

        $model2 = Ezform::find()->select([
                    Ezform::tableName() . '.ezf_id',
                    'ezf_name'
                ])->distinct()->innerJoin('ezform_fields', 'ezform_fields.ezf_id=' . Ezform::tableName() . '.ezf_id')->where('status =1')->andWhere('shared = 0 AND (' . Ezform::tableName() . '.ezf_id in (SELECT ' . Ezform::tableName() . '.ezf_id FROM ezform_co_dev WHERE user_co = :user_id AND ezf_id<>ezform.ezf_id)) ', [
                    ':user_id' => Yii::$app->user->id
                ])->andWhere([
                    'ezf_crf' => '1'
                ])->andWhere([
                    'ezform_fields.ref_ezf_id' => $profile_ezf
                ])->orderBy(Ezform::tableName() . '.created_at DESC')->all();

        $model3 = Ezform::find()->select([
                    Ezform::tableName() . '.ezf_id',
                    'ezf_name'
                ])->distinct()->innerJoin('ezform_fields', 'ezform_fields.ezf_id=' . Ezform::tableName() . '.ezf_id')->where('status =1')->andWhere('(shared = 1 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared = 2 AND ' . Ezform::tableName() . '.ezf_id in (SELECT ezf_id FROM ezform_assign WHERE user_id = :user_id AND ezf_id<>ezform.ezf_id)))', [
                    ':user_id' => Yii::$app->user->id,
                    ':xsourcex' => Yii::$app->user->identity->profile->sitecode
                ])->andWhere([
                    'ezf_crf' => '1'
                ])->andWhere([
                    'ezform_fields.ref_ezf_id' => $profile_ezf
                ])->orderBy(Ezform::tableName() . '.created_at DESC')->all();

        $model4 = Ezform::find()->select([
                    Ezform::tableName() . '.ezf_id',
                    'ezf_name'
                ])->distinct()->innerJoin('ezform_fields', 'ezform_fields.ezf_id=' . Ezform::tableName() . '.ezf_id')->where('status =1')->andWhere('public_listview=1 ')->andWhere([
                    'ezf_crf' => '1'
                ])->andWhere([
                    'ezform_fields.ref_ezf_id' => $profile_ezf
                ])->orderBy(Ezform::tableName() . '.created_at DESC')->all();

        $out = [];
        $i = 0;
        $out ["results"] [$i] = [
            'text' => 'My own'
        ];
        $out ["results"] [$i] ['children'] = [];
        $arrayMapp = [];
        foreach ($model1 as $value) {
            $out ["results"] [$i] ['children'] [] = [
                'id' => $value ['ezf_id'],
                'text' => $value ["ezf_name"]
            ];
        }

        $i ++;
        $out ["results"] [$i] = [
            'text' => 'Co-creator'
        ];
        $out ["results"] [$i] ['children'] = [];
        foreach ($model2 as $value) {
            $out ["results"] [$i] ['children'] [] = [
                'id' => $value ['ezf_id'],
                'text' => $value ["ezf_name"]
            ];
        }

        $i ++;
        $out ["results"] [$i] = [
            'text' => 'Assigned to me'
        ];
        $out ["results"] [$i] ['children'] = [];
        foreach ($model3 as $value) {
            $out ["results"] [$i] ['children'] [] = [
                'id' => $value ['ezf_id'],
                'text' => $value ["ezf_name"]
            ];
        }

        $i ++;
        $out ["results"] [$i] = [
            'text' => 'Public'
        ];
        $out ["results"] [$i] ['children'] = [];
        foreach ($model4 as $value) {
            $out ["results"] [$i] ['children'] [] = [
                'id' => $value ['ezf_id'],
                'text' => $value ["ezf_name"]
            ];
        }

        return json_encode($out);
        // return ArrayHelper::map($out, 'ezf_id', 'ezf_name');
    }

    public static function getEzformsSubjectProfile($params = []) {
        $profile_ezf = "1521941065093085100";
        $model = Ezform::find()->select([
                    Ezform::tableName() . '.ezf_id',
                    'ezf_name'
                ])->distinct()->innerJoin('ezform_fields', 'ezform_fields.ezf_id=' . Ezform::tableName() . '.ezf_id')->where('status =1')->andWhere('(' . Ezform::tableName() . '.created_by=:user_id ) OR public_listview=1 ', [
                    ':user_id' => Yii::$app->user->id
                ])->andWhere([
                    'ezf_crf' => '1'
                ])->andWhere([
                    'ezform_fields.ref_ezf_id' => $profile_ezf
                ])->orderBy(Ezform::tableName() . '.created_at DESC');
        $items = $model->all();

        return ArrayHelper::map($items, 'ezf_id', 'ezf_name');
    }

    public static function getAllRoles() {
        try {
            return Yii::$app->db->createCommand("SELECT id,CONCAT(role_detail,'(',role_name, ')') as role_name FROM zdata_role WHERE rstat NOT IN (0,3) ")->queryAll();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getFirstGroup($ezf_id) {
        $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
        $groupData = self::GetTableData($ezform, null, 'one');

        return $groupData;
    }

}
