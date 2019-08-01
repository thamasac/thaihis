<?php

namespace backend\modules\thaihis\classes;

use backend\modules\ezforms2\models\Ezform;
use backend\modules\ezforms2\models\EzformFields;
use Yii;
use backend\modules\ezforms2\classes\EzfQuery;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class ThaiHisQuery {

    public static function getEzformRef($ezf_id) {
        $model = Ezform::find()
                ->innerJoin('ezform_fields', 'ezform_fields.ezf_id = ezform.ezf_id')
                ->where('ezform.ezf_id<>:ezf_id AND ezform.status = 1', [':ezf_id' => $ezf_id,])
                ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND ezform.xsourcex=:xsourcex) OR (shared = 2 AND ezform.ezf_id in (SELECT ezf_id FROM ezform_assign WHERE user_id = :user_id AND ezf_id<>ezform.ezf_id)) OR (shared = 0 AND (ezform.created_by=:user_id OR ezform.ezf_id in (SELECT ezf_id FROM ezform_co_dev WHERE user_co = :user_id AND ezf_id<>ezform.ezf_id)))', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->andWhere('ezf_field_type=79')
                ->orderBy('ezform.created_at DESC')
                ->all();
        return $model;
    }

    public static function getEzformRef2($ezf_id) {
        $model = Ezform::find()
                ->innerJoin('ezform_fields', 'ezform_fields.ezf_id = ezform.ezf_id')
                ->where('ezform.ezf_id<>:ezf_id AND ezform.status = 1', [':ezf_id' => $ezf_id,])
                ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND ezform.xsourcex=:xsourcex) OR (shared = 2 AND ezform.ezf_id in (SELECT ezf_id FROM ezform_assign WHERE user_id = :user_id AND ezf_id<>ezform.ezf_id)) OR (shared = 0 AND (ezform.created_by=:user_id OR ezform.ezf_id in (SELECT ezf_id FROM ezform_co_dev WHERE user_co = :user_id AND ezf_id<>ezform.ezf_id)))', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->andWhere('(ezf_field_type=79 OR ezf_field_type=80)')
                ->orderBy('ezform.created_at DESC')
                ->all();
        return $model;
    }

    public static function getEzformRefAll2($ezf_id, $value_ref = null) {
        //\appxq\sdii\utils\VarDumper::dump($value_ref);
        $ref_ezf_list = null;
        if (is_array($value_ref)) {
            $ref_ezf_list = join($value_ref, ',');
        }
        $model1 = new \yii\db\Query();
        $model1->select('ezf.ezf_id as `id`,ezf.ezf_name as `name`')
                ->from('ezform_fields ezff')
                ->innerJoin('ezform ezf', '(ezf.ezf_id=ezff.ezf_id OR ezf.ezf_id=ezff.ref_ezf_id)');

        if ($ref_ezf_list) {
            $model1->where('(ezff.ezf_id=:ezf_id OR ezff.ezf_id IN(' . $ref_ezf_list . ')) ', [':ezf_id' => $ezf_id]);
        } else {
            $model1->where(' ezff.ezf_id=:ezf_id  ', [':ezf_id' => $ezf_id]);
        }

        $model1->andWhere('ezff.ezf_field_type=79 OR ezff.ezf_field_type=80')
                ->andWhere('ezf.status=1');

        $model2 = new \yii\db\Query();

        $model2->select('ezf.ezf_id as `id`,ezf.ezf_name as `name`')
                ->from('ezform_fields ezff')
                ->innerJoin('ezform ezf', '(ezf.ezf_id=ezff.ezf_id OR ezf.ezf_id=ezff.ref_ezf_id)');
        if ($ref_ezf_list) {
            $model2->where('(ezff.ref_ezf_id=:ezf_id OR ezff.ref_ezf_id IN(' . $ref_ezf_list . ')) ', [':ezf_id' => $ezf_id]);
        } else {
            $model2->where(' ezff.ref_ezf_id=:ezf_id ', [':ezf_id' => $ezf_id]);
        }
        $model2->andWhere('ezff.ezf_field_type=79 OR ezff.ezf_field_type=80')
                ->andWhere('ezf.status=1');

        $result = $model1->union($model2->createCommand()->rawSql);
        return $result->all();
    }

    public static function getEzformRefAll($ezf_id) {
        $model1 = new \yii\db\Query();
        $model1->select('ezf.ezf_id as `id`,ezf.ezf_name as `name`')
                ->from('ezform_fields ezff')
                ->innerJoin('ezform ezf', 'ezf.ezf_id=ezff.ezf_id')
                ->where(['ezff.ref_ezf_id' => $ezf_id])
                ->andWhere('ezff.ezf_field_type=79 ');

        $model2 = new \yii\db\Query();
        $model2->select('ezf.ezf_id as `id`,ezf.ezf_name as `name`')
                ->from('ezform_fields ezff')
                ->innerJoin('ezform ezf', 'ezf.ezf_id=ezff.ref_ezf_id')
                ->where(['ezff.ezf_id' => $ezf_id])
                ->andWhere('ezff.ezf_field_type=79 ');

        $result = $model1->union($model2->createCommand()->rawSql);

        return $result->all();
    }

    public static function getFields($ezf_id) {
        $sql = "SELECT ezf_field_id AS `id`, IFNULL(concat(ezf_field_name, ' (', ezf_field_label, ')'),ezf_field_name) AS`name`
                ,ezf_version, ezf_field_name, ezf_field_label 
                FROM `ezform_fields` 
                WHERE `ezf_id` = :ezf_id AND table_field_type not in('none')
                #AND ezf_field_type <> 'all'
                ORDER BY ezf_version, ezf_field_order";

        return Yii::$app->db->createCommand($sql, [':ezf_id' => $ezf_id])->queryAll();
    }

    public static function getFields2($ezf_id, $type = null) {
        $sql = "SELECT concat(ezf.ezf_table,'.',ezff.ezf_field_name) AS `id`, concat(ezf_field_name, ' (',ezf_field_label ,' : ', ezf.ezf_name, ')') AS`name`
                ,ezff.ezf_version, ezf_field_name, ezf_field_label 
                FROM `ezform_fields` ezff INNER JOIN ezform ezf ON ezff.ezf_id=ezf.ezf_id
                WHERE ezff.`ezf_id` = :ezf_id AND ezff.table_field_type not in('none','field')";
        if ($type) {
            $sql .= "AND ezff.ezf_field_type = {$type}";
        }
        $sql .= "#AND ezff.ezf_version <> 'all'
                ORDER BY ezff.ezf_version, ezff.ezf_field_order";

        return Yii::$app->db->createCommand($sql, [':ezf_id' => $ezf_id])->queryAll();
    }

    public static function getEzformRefList($ezf_id) {
        $model = Ezform::find()
                ->innerJoin('ezform_fields', 'ezform_fields.ezf_id = ezform.ezf_id')
                ->where('status = 1 AND ezf_field_type<>0 AND (ezform_fields.ref_ezf_id=:ezf_id OR ezform.ezf_id=:ezf_id)', [':ezf_id' => $ezf_id])
                ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND ezform.xsourcex=:xsourcex) OR (shared = 2 AND ezform.ezf_id in (SELECT ezf_id FROM ezform_assign WHERE user_id = :user_id AND ezf_id<>ezform.ezf_id)) OR (shared = 0 AND (ezform.created_by=:user_id OR ezform.ezf_id in (SELECT ezf_id FROM ezform_co_dev WHERE user_co = :user_id AND ezf_id<>ezform.ezf_id)))', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->groupBy('ezform.ezf_id')
                ->all();
        return $model;
    }

    public static function getEzformFieldsByName($fields, $ezf_id) {
        $modelFields = EzformFields::find()
                ->select('ezf.ezf_table ,ezff.*,')
                ->alias('ezff')
                ->innerJoin('ezform AS ezf', 'ezf.ezf_id=ezff.ezf_id')
                ->where("`status`='1'")
                ->andWhere(['ezf.ezf_id' => $ezf_id])
                ->andWhere(['in', 'ezff.ezf_field_name', $fields])->asArray()
                ->orderBy('ezf.ezf_table')
                ->all();

        return $modelFields;
    }

    public static function getEzformFieldsById2($fields, $ezf_id) {
        $modelFields = EzformFields::find()
                ->select('ezf.ezf_table ,ezff.*,')
                ->alias('ezff')
                ->innerJoin('ezform AS ezf', 'ezf.ezf_id=ezff.ezf_id')
                ->where("`status`='1'")
                ->andWhere(['ezf.ezf_id' => $ezf_id])
                ->andWhere(['in', 'ezff.ezf_field_id', $fields])->asArray()
                ->orderBy('ezf.ezf_table')
                ->all();

        return $modelFields;
    }

    public static function getEzformFields2($fields, $ezf_id) {
        $modelFields = EzformFields::find()
                ->select('ezf.ezf_table,ref.ref_field_id AS join_field_id,ref.ezf_field_name AS ref_field,ezff.*,')
                ->alias('ezff')
                ->innerJoin('ezform AS ezf', 'ezf.ezf_id=ezff.ezf_id')
                ->leftjoin('ezform_fields AS ref', "ref.ezf_id=ezf.ezf_id AND (ref.ezf_field_type='79') AND ref.ezf_id<>'{$ezf_id}'")
                ->where("`status`='1'")
                ->andWhere(['in', 'ezff.ezf_field_id', $fields])->asArray()
                ->orderBy('ezf.ezf_table')
                ->all();
//        \appxq\sdii\utils\VarDumper::dump($modelFields->createCommand()->rawSql);
        return $modelFields;
    }

    public static function getEzformFields3($ref_fields_id, $ezf_id, $ref_forms = null, $left_forms = null) {
        $modelFields = EzformFields::find()
                ->select("ezf.ezf_table,CONCAT('') as ref_ezf_table,ref.ref_field_id AS join_field_id,ref.ezf_field_name AS ref_field, CONCAT('no') AS field_to_join,CONCAT('no') AS type_to_join,ezff.*,")
                ->alias('ezff')
                ->innerJoin('ezform AS ezf', 'ezf.ezf_id=ezff.ezf_id ')
                ->leftjoin('ezform_fields AS ref', "ref.ezf_id=ezf.ezf_id AND (ref.ezf_field_type='79' OR ref.ezf_field_type='80') AND ref.ezf_id<>'{$ezf_id}'")
                ->where("`status`='1'")
                ->andWhere(['in', 'ezff.ezf_field_id', $ref_fields_id])->asArray()
                ->groupBy('ezff.ezf_field_id')
                ->orderBy('ezf.ezf_table');

        $modelFields2 = EzformFields::find()
                ->select("ezf.ezf_table,ezf2.ezf_table as ref_ezf_table,ezff.ref_field_id AS join_field_id,ezff.ezf_field_name AS ref_field,CONCAT('yes') AS field_to_join,CONCAT('dynamic') AS type_to_join,ezff.*,")
                ->alias('ezff')
                ->innerJoin('ezform AS ezf', 'ezf.ezf_id=ezff.ref_ezf_id')
                ->innerJoin('ezform AS ezf2', 'ezf2.ezf_id=ezff.ezf_id')
                ->where("ezf.`status`='1'")
                ->andWhere("(ezff.ezf_field_type='79' OR ezff.ezf_field_type='80')")
                ->groupBy('ezff.ezf_field_id');

        $ref_forms[] = $ezf_id;
        $modelFields2->andWhere(['in', 'ezf.ezf_id', $ref_forms])->asArray();
        $modelFields2->andWhere(['in', 'ezf2.ezf_id', $ref_forms])->asArray();
        $modelFields2->orderBy('ezff.ezf_field_type');
        if ($left_forms) {
            $modelFields3 = EzformFields::find()
                    ->select("ezf.ezf_table,ezf2.ezf_table as ref_ezf_table,ezff.ref_field_id AS join_field_id,ezff.ezf_field_name AS ref_field,CONCAT('yes') AS field_to_join,CONCAT('left_join') AS type_to_join,ezff.*,")
                    ->alias('ezff')
                    ->innerJoin('ezform AS ezf', 'ezf.ezf_id=ezff.ref_ezf_id')
                    ->innerJoin('ezform AS ezf2', 'ezf2.ezf_id=ezff.ezf_id')
                    ->where("ezf.`status`='1'")
                    ->andWhere("(ezff.ezf_field_type='79' OR ezff.ezf_field_type='80')");
            $left_forms[] = $ezf_id;
            $modelFields3->andWhere(['in', 'ezf.ezf_id', $ref_forms])->asArray();
            $modelFields3->andWhere(['in', 'ezf2.ezf_id', $left_forms])->asArray();

            //\appxq\sdii\utils\VarDumper::dump($modelFields->union($modelFields2->createCommand()->rawSql)->union($modelFields3->createCommand()->rawSql)->createCommand()->rawSql);
            $result = $modelFields->union($modelFields2->createCommand()->rawSql)->union($modelFields3->createCommand()->rawSql)->createCommand()->queryAll();
        } else {
//            \appxq\sdii\utils\VarDumper::dump($modelFields->union($modelFields2->createCommand()->rawSql)->createCommand()->rawSql);
            $result = $modelFields->union($modelFields2->createCommand()->rawSql)->createCommand()->queryAll();
        }

        return $result;
    }

    public static function getEzfieldById($ref_fields_id) {
        $modelFields = EzformFields::find()
                        ->where(['in', 'ezf_field_id', $ref_fields_id])->asArray();

        $result = $modelFields->all();
        return $result;
    }

    public static function getEzformFieldsById($ref_ezf_id) {
        $modelFields = EzformFields::find()
                ->alias('ezff')
                ->select('CASE WHEN `ezf`.`ezf_table` IS NULL THEN (SELECT ezf_table FROM ezform WHERE ezf_id=ezff.ezf_id) ELSE `ezf`.`ezf_table` END AS ezf_table,`ezff`.`ref_field_id` AS `ref_field`,`ezff`.`ezf_field_name` AS `join_field_id`,`ezff`.*')
                ->leftjoin('ezform AS ezf', 'ezf.ezf_id=ezff.ref_ezf_id AND `status`=1')
                ->where(['in', 'ezff.ezf_field_id', $ref_ezf_id])->asArray()
                ->all();
//        \appxq\sdii\utils\VarDumper::dump($modelFields->createCommand()->rawSql);
        return $modelFields;
    }

    public static function getModelFields($ezf_id, $ezf_version, $columns) {

        $modelFields = EzformFields::find()
                ->select('*')
                ->alias('ezff')
                ->innerJoin('ezform AS ezf', 'ezf.ezf_id=ezff.ezf_id')
                ->where("`status`='1'")
                ->andWhere(['ezff.ezf_id' => $ezf_id, 'ezff.ezf_version' => $ezf_version])
                ->andWhere(['in', 'ezf_field_name', $columns])->asArray()
                ->all();

        return $modelFields;
    }

    public static function calAge($bdate) {
        //$age = date_diff(date('Y-m-d'), $bdate);
        $diff = abs(strtotime(date('Y-m-d')) - strtotime($bdate));
        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        return $years;
    }

    public static function getCurrentVisit($target, $ezf_id = null, $ezf_id2 = null) {
        $ezform = null;
        $ezform2 = null;
        $table1 = 'zdata_visit';
        $table2 = 'zdata_visit_tran';
        if ($ezf_id) {
            $ezform = EzfQuery::getEzformOne($ezf_id);
            $table1 = $ezform['ezf_table'];
        }
        if ($ezf_id2) {
            $ezform2 = EzfQuery::getEzformOne($ezf_id2);
            $table2 = $ezform2['ezf_table'];
        }

        $nowDate = date('Y-m-d');

        $where = "{$table1}.target='" . $target . "' AND DATE({$table1}.visit_date)='" . $nowDate . "' AND {$table1}.visit_status=1";
        $columns = "{$table1}.*,{$table2}.id as visit_tran_id";
        //filter leftjoin By Oak        
        $addonLeft = " AND visit_tran_status='1'";
        if (Yii::$app->user->can('doctor')) {
            $user_id = Yii::$app->user->identity->profile->user_id;
            $addonLeft .= " AND visit_tran_doctor='{$user_id}'";
        } else {
            $addonLeft .= " AND IFNULL(visit_tran_doctor,'') = ''";
        }
        $dept = Yii::$app->user->identity->profile->department;
        $addonLeft .= " AND visit_tran_dept='{$dept}'";
        $result = self::getTableLeftJoinTarget($ezform, $table2, $columns, $where, $addonLeft, 'one');
        return $result;
    }

    public static function getTableLeftJoinTarget($ezform, $table2, $columns, $where = null, $addonLeft = null, $type = null, $offset = null, $limit = null) {
        $query = new \yii\db\Query();
        $dept = Yii::$app->user->identity->profile->department;

        $table = isset($ezform['ezf_table']) ? $ezform['ezf_table'] : $ezform['ezf_table'];
        $query->select($columns)
                ->from($table)
                ->leftJoin($table2, "{$table}.id={$table2}.target  $addonLeft");
        if ($where != null)
            $query->where($where);
        else
            $query->where('1=1');

//        if ($ezform['public_listview'] == 2) {
//            $query->andWhere($table . '.xsourcex = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
//        }
//
//        if ($ezform['public_listview'] == 3) {
//            $query->andWhere($table . '.xdepartmentx = :unit', [':unit' => $dept]);
//        }
//
//        if ($ezform['public_listview'] == 0) {
//            $query->andWhere($table . ".user_create=:created_by", [':created_by' => Yii::$app->user->id]);
//        }

        $query->andWhere($table . ".rstat NOT IN(0,3) ");
        if ($limit != null)
            $query->limit($limit);

        if ($offset != null)
            $query->offset($offset);

        $query->groupBy($table . '.id');
//        \appxq\sdii\utils\VarDumper::dump($query->createCommand()->rawSql, 1, 0);
        if ($type == 'one') {
            $result = $query->one();
        } else {
            $result = $query->all();
        }

        return $result;
    }

    public static function getTableData($ezform, $where = null, $type = null, $limit = null, $order = null, $group = null) {
        if (isset($ezform->ezf_table) || isset($ezform['ezf_table']))
            $table = isset($ezform->ezf_table) ? $ezform->ezf_table : $ezform['ezf_table'];
        else
            $table = $ezform;

        $query = new \yii\db\Query();
        $query->select('*')
                ->from($table);

        if ($where != null)
            $query->where($where);
        else
            $query->where('1=1');

        if (isset($ezform->ezf_table)) {
            if ($ezform['public_listview'] == 2) {
                $query->andWhere('xsourcex = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
            }

            if ($ezform['public_listview'] == 3) {
                $query->andWhere('xdepartmentx = :unit', [':unit' => Yii::$app->user->identity->profile->department]);
            }

            if ($ezform['public_listview'] == 0) {
                $query->andWhere("user_create=:created_by", [':created_by' => Yii::$app->user->id]);
            }
        } else {
            $query->andWhere('sitecode = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
        }

        $query->andWhere(" rstat NOT IN(0,3) ");

        if ($group != null)
            $query->groupBy($group);

        if ($order != null) {
            $orderby = isset($order['order']) ? $order['order'] : '';
            $query->orderBy($order['column'] . ' ' . $orderby);
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

    public static function getPtRightLast($pt_id) {
        $query = new \yii\db\Query();
        $data = $query->select('right_hos_main,right_refer_no,right_refer_end,right_prove_no,zpr.right_code
,right_refer_start,right_sub_code,right_status,right_flag,right_prove_end,right_hos_refer,right_project_id
,note_detail,zpr.id AS right_id,zr.right_name')
                        ->from('zdata_patientright zpr')
                        ->innerJoin('zdata_right zr', 'zr.right_code=zpr.right_code')
                        ->where('zpr.rstat=1 AND zpr.ptid=:pt_id', [':pt_id' => $pt_id])
                        ->orderBy('zpr.create_date DESC')->one();

        return $data;
    }

    public static function getDepartmentFull($user_id) {
        $data = (new \yii\db\Query())
                        ->select(["user_id", "CONCAT(firstname,' ',lastname) AS fullname", "p.sitecode", "zs.site_detail AS site_name"
                            , "zwu.unit_name", "zot.order_type_code", "zot.order_type_name", "unit_code", "zwu.id AS unit_id"])
                        ->from("profile p")
                        ->innerJoin('zdata_sitecode zs', 'zs.site_name=p.sitecode')
                        ->innerJoin('zdata_working_unit zwu', 'zwu.id=p.department')
                        ->leftJoin('zdata_order_type zot', 'zot.id=zwu.unit_order_type')
                        ->where(['p.user_id' => $user_id])->one();
        return $data;
    }

    public static function getSex($visit_id) {
        $data = (new \yii\db\Query())
                        ->select(["pt_sex"])
                        ->from("zdata_visit zv")
                        ->innerJoin('zdata_patientprofile zpf', 'zpf.id=zv.ptid')
                        ->where(['zv.id' => $visit_id])->one();
        return $data;
    }

    public static function getPtProfile($pt_id) {
        $data = (new \yii\db\Query())
                        ->select(["zpp.id AS pt_id", "zpp.pt_cid", "zpp.pt_hn", "zp.prefix_name", "zpp.pt_firstname", "zpp.pt_lastname", "zpp.pt_moi"
                            , "concat(zp.prefix_name_cid,zpp.pt_firstname,' ',zpp.pt_lastname) AS fullname", "zpp.pt_pic"
                            , "zpp.pt_bdate", "zpp.pt_address", "zpp.pt_moi", "zpp.pt_addr_tumbon", "zpp.pt_addr_amphur", "zpp.pt_addr_province", "zpp.pt_addr_zipcode"
                            , "zpp.pt_status", "zpp.pt_sex", "zpp.xsourcex", "zph.id AS pt_ht_id", "zph.pt_disease_status", "zph.pt_disease_detail"
                            , "zph.pt_drug_status", "zph.pt_drug_list", "zph.pt_drug_action", "zpp.pt_email", "zpp.pt_vip"])
                        ->from("zdata_patientprofile zpp")
                        ->innerJoin('zdata_prefix zp', 'zp.prefix_id = zpp.pt_prefix_id')
                        ->leftJoin('zdata_patienthistory zph', "zpp.id = zph.ptid AND zph.rstat <> '0'")
                        ->where("zpp.rstat <> '0' AND zpp.id=:pt_id", ['pt_id' => $pt_id])->one();
        return $data;
    }

    public static function getQueryGroupConcat($ezf_id, $target, $column) {
        $ezform = EzfQuery::getEzformOne($ezf_id);
        $query = new \yii\db\Query();
        $query->select(['id', $column])
                ->from($ezform['ezf_table'])
                ->where(['target' => $target])
                ->andWhere('rstat NOT IN(0,3)')
                ->orderBy('create_date desc');

        return $query->all();
    }

    public static function getDynamicQuery($fields, $forms, $ezform, $conditions = null, $summarys = null, $image_field = null, $customSelect = null, $modelFilter = null, $group_field = null, $left_forms = null, $sort_order = null, $selects = null, $pageAmt = 25, $subquery = null) {

        $ezf_table = isset($ezform->ezf_table) ? $ezform->ezf_table : 'zdata_pism_item';
        $ezf_id = isset($ezform->ezf_id) ? $ezform->ezf_id : '1515588745039739100';
        if ($image_field)
            $fields[] = $image_field;
        //$dataProvider = \backend\modules\pis\classes\PisFunc::getOrderTran($visit_id, ''); // ค้นหา Order จากทุก Doctor_Id
        $modelFields = ThaiHisQuery::getEzformFields3($fields, $ezform['ezf_id'], $forms, $left_forms);
        $modelDyn = ThaiHisFunc::setDynamicModel($modelFields, $ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);

        $searchModel = new \backend\modules\ezforms2\models\TbdataAll();
        $searchModel->setTableName($ezf_table);

        $ezformParent = Null;
        $targetField = EzfQuery::getTargetOne($ezf_id);
        if (isset($targetField)) {
            $ezformParent = EzfQuery::getEzformById($targetField->ref_ezf_id);
        }

        if (isset($selects)) {
            foreach ($selects as $key => $val) {
                $valCus = $val['field'];
                if (isset($val['field']) && $val['field'] == '') {
                    $valCus = isset($val['custom_val']) ? $val['custom_val'] : null;
                }
                $alias = isset($val['alias_name']) ? $val['alias_name'] : 'alias' . $key;
                $customSelect[] = "{$valCus} as {$alias}";
            }
        }

        if (is_array($conditions)) {
            foreach ($conditions as $val) {
                $modelFilter[] = $val['field'] . $val['operator'] . $val['compare'];
            }
        }

        if (is_array($summarys)) {
            foreach ($summarys as $val) {
                $customSelect[] = 'SUM(' . $val['field'] . ') as ' . $val['alias_name'];
            }
        }

        $data = ThaiHisFunc::modelSearchAll2($searchModel, $ezform, $targetField, $ezformParent, $modelFields, $modelFilter, null, $customSelect, $group_field, false, $sort_order, null, $subquery);

        if ($data)
            $modelDyn->attributes = $data[0];

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => $pageAmt,
            ],
        ]);

        $responseQuery = [
            'modelDynamic' => $modelDyn,
            'data' => $data,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'modelFields' => $modelFields,
        ];

        return $responseQuery;
    }

    public static function getMedicalDoctor($visitid) {
        $query = Yii::$app->db->createCommand("SELECT visit_doctor_concat('{$visitid}') as 'doctor_concat';")->queryOne();
        return $query;
    }

    public static function getDepartmentByName($q = null, $sht = null) {
        $sql = "SELECT zwu.id AS code,unit_name AS name,order_type_code AS type,zwu.unit_code_old,zwu.unit_code
                FROM zdata_working_unit zwu
                INNER JOIN zdata_order_type zot ON(zot.id=unit_order_type)
                WHERE zwu.rstat=1 AND CONCAT(unit_name,unit_code_old,unit_code) LIKE :q AND order_type_code LIKE :type";
        return Yii::$app->db->createCommand($sql, [':q' => "%$q%", ':type' => "$sht%"])->queryAll();
    }

    public static function getDeptMapForm($dept_id) {
        $sql = "SELECT ezf1.ezf_id AS ezf_id_tk,ezf1.ezf_table AS ezf_table_tk
                ,ezf2.ezf_id AS ezf_id_pe,ezf2.ezf_table AS ezf_table_pe
                FROM zdata_dept_map_form zdmf
                INNER JOIN ezform ezf1 ON(ezf1.ezf_id=zdmf.dept_form_nu)
                INNER JOIN ezform ezf2 ON(ezf2.ezf_id=zdmf.dept_form_doc)
                WHERE zdmf.rstat NOT IN(0,3) AND dept_form_deptid = :dept";

        return Yii::$app->db->createCommand($sql, [':dept' => $dept_id])->queryOne();
    }

    public static function getEventStopCustom($start, $end) {
        $sarry = date_parse($start);
        $earry = date_parse($end);

        $sdate = "{$sarry['year']}-{$sarry['month']}-{$sarry['day']}";
        $edate = "{$earry['year']}-{$earry['month']}-{$earry['day']}";

        $sql = "SELECT id, CONCAT(lpad(hyear,4,0), '-', lpad(hmonth,2,0), '-', lpad(hday,2,0)) AS ddate, user_create,
		hname
		FROM zdata_holiday WHERE rstat<>3 AND rstat<>0 AND hstatus=0 AND CONCAT(lpad(hyear,4,0), '-', lpad(hmonth,2,0), '-', lpad(hday,2,0)) BETWEEN DATE(:start) AND DATE(:end)
		";

        return Yii::$app->db->createCommand($sql, [':start' => $sdate, ':end' => $edate])->queryAll();
    }

    public static function getEzfNameByTarget($ezf_id) {
        $sql = "SELECT ez.ezf_id,ez.ezf_name,ez.ezf_table
                FROM ezform ez
                INNER JOIN ezform_fields ezf ON(ezf.ezf_id=ez.ezf_id)
                WHERE ezf.ezf_field_type='79' AND ezf.ref_ezf_id=:ezf_id
                AND ez.ezf_id NOT IN('1503589101005614900','1513343009045814500','1520997145096407200')
                ";

        return Yii::$app->db->createCommand($sql, [':ezf_id' => $ezf_id])->queryAll();
    }

}
