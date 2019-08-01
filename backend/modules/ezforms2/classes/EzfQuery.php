<?php

namespace backend\modules\ezforms2\classes;

use Yii;
use backend\modules\ezforms2\models\Ezform;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\models\EzformVersion;

/**
 * OvccaQuery class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class EzfQuery {

    public static function getIntUserAll() {
        $sitecode = Yii::$app->user->identity->profile->sitecode;

        $sql = "SELECT user_id as id, 
		    CONCAT(firstname, ' ', lastname) AS text
		FROM profile
                WHERE sitecode=:sitecode
		";

        return Yii::$app->db->createCommand($sql, [':sitecode' => $sitecode])->queryAll();
    }

    public static function getInputv2Hide() {
        $sql = "SELECT *
		FROM ezform_input
		WHERE input_version='v2' AND input_active=1 AND ( ISNULL(input_category) OR input_category>0 )
		ORDER BY input_category, input_name
		";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getInputv2All() {
        $sql = "SELECT *
		FROM ezform_input
		WHERE input_version='v2' AND input_active=1
		ORDER BY input_category, input_name
		";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getFieldsCountById($id) {
        $sql = "SELECT MAX(ezf_field_order)+1 AS num
		FROM ezform_fields
		WHERE ezf_id=:id AND ezf_field_ref IS NULL AND ezf_field_type<>0
		";
        $order = Yii::$app->db->createCommand($sql, [':id' => $id])->queryScalar();
        return isset($order) ? $order : 1;
    }

    public static function getEzformById($id) {

        $sql = "SELECT ezf_id, ezf_version, ezf_name, ezf_table, field_detail, unique_record FROM ezform WHERE ezf_id = :id";

        return Yii::$app->db->createCommand($sql, [':id' => $id])->queryOne();
    }

    public static function getConditionFieldsName($field, $cond) {
        $sql = "SELECT $field
		FROM ezform_fields 
		WHERE ezform_fields.ezf_field_id in($cond) ";

        if ($cond != '') {
            $data = Yii::$app->db->createCommand($sql)->queryAll();
            if ($data) {
                return $data;
            }
        }
        return [];
    }

    public static function deleteFieldOther($ezf_field_id) {
        $sql = "DELETE FROM `ezform_fields` WHERE `ezf_field_ref` = :id ";

        return Yii::$app->db->createCommand($sql, [':id' => $ezf_field_id])->execute();
    }

    public static function deleteChoice($ezf_field_id) {
        $sql = "DELETE FROM `ezform_choice` WHERE `ezf_field_id` = :id ";

        return Yii::$app->db->createCommand($sql, [':id' => $ezf_field_id])->execute();
    }
    
    public static function deleteAutonum($ezf_field_id) {
        $sql = "DELETE FROM `ezform_autonum` WHERE `ezf_field_id` = :id ";

        return Yii::$app->db->createCommand($sql, [':id' => $ezf_field_id])->execute();
    }

    public static function getCondition($ezf_id, $ezf_field_name) {
        $sql = "SELECT *
		FROM ezform_condition
		WHERE ezform_condition.ezf_id = :ezf_id AND ezform_condition.ezf_field_name = :ezf_field_name
		ORDER BY ezform_condition.cond_id;";

        return Yii::$app->db->createCommand($sql, [':ezf_id' => $ezf_id, ':ezf_field_name' => $ezf_field_name])->queryAll();
    }

    public static function getEzformReportById($id) {

        $sql = "SELECT ezform.ezf_id, ezform.ezf_name, ezf_table, comp_id_target, field_detail, unique_record , ezform_config.*
		FROM ezform INNER JOIN ezform_config ON ezform_config.ezf_id = ezform.ezf_id
		WHERE ezform.ezf_id = :id AND ezform_config.config_type = 'report' ";

        return Yii::$app->db->createCommand($sql, [':id' => $id])->queryOne();
    }

    public static function getEzformReportByIdAll($id) {

        $sql = "SELECT ezform_config.*
		FROM ezform_config
		WHERE ezform_config.ezf_id = :id AND ezform_config.config_type = 'report' ";

        return Yii::$app->db->createCommand($sql, [':id' => $id])->queryAll();
    }

    public static function getEzformReportByUser($user_id, $ezf_id) {

        $sql = "SELECT ezform_config.*
		FROM ezform_report INNER JOIN ezform_config ON ezform_config.config_id = ezform_report.config_id
		WHERE ezform_report.ezf_id = :id AND ezform_report.user_id = :user_id ";

        return Yii::$app->db->createCommand($sql, [':id' => $ezf_id, ':user_id' => $user_id])->queryAll();
    }

    public static function getProvince() {
        $sql = "SELECT `PROVINCE_ID`, `PROVINCE_CODE`,`PROVINCE_NAME` FROM `const_province`";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getFormTableName($ezf_id) {
        $ezform = Ezform::find()
                ->select('ezf_id, ezf_version, ezf_table, ezf_name, ezf_icon, unique_record')
                ->where(['ezf_id' => $ezf_id])
                ->one();
        return $ezform;
    }

    public static function getDynamicFormById($table, $id) {
        $sql = "SELECT *
		FROM $table
		WHERE id = :id ";

        return Yii::$app->db->createCommand($sql, [':id' => $id])->queryOne();
    }

    public static function showColumn($table) {

        $sql = "SHOW COLUMNS FROM $table;";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function copyTable($tableNew, $tableCopy) {
        $sql = "CREATE TABLE $tableNew LIKE $tableCopy";

        return Yii::$app->db->createCommand($sql)->execute();
    }

    public static function dropTable($table) {
        $sql = "DROP TABLE `$table`";

        return Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * Get all Ezform except self
     * @param $ezf_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getEzformAll($ezf_id) {
        $model = Ezform::find()->where('ezf_id<>:ezf_id', [':ezf_id' => $ezf_id])
                ->andWhere('ezform.status = :status', [':status' => 1])
                ->all();
        return $model;
    }

    public static function getEzformCoDevCRF() {
        $model = Ezform::find()
                ->where('status = 1 AND ezf_crf = 1')
                ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id)  OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->orderBy('created_at DESC')
                ->all();
        return $model;
    }

    public static function getEzformCoDevDb2() {
        $model = Ezform::find()
                ->where('status = 1 AND ezf_db2 = 1')
                ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id) OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->orderBy('created_at DESC')
                ->all();
        return $model;
    }

    public static function getEzformCoDevAll($ref_ezf_id='') {
        $model = Ezform::find()
                ->where('status = 1')
                ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id) OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->orderBy('created_at DESC');
        
        if(!empty($ref_ezf_id)){
            $model->orWhere('ezf_id=:dataid', [':dataid'=>$ref_ezf_id]);
        }
              
        return $model->all();
    }

    public static function getEzformCoDev($ezf_id, $ref_ezf_id='') {
        $model = Ezform::find()
                ->where('ezf_id<>:ezf_id AND status = 1', [':ezf_id' => $ezf_id])
                ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id) OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->orderBy('created_at DESC');
        
        if(!empty($ref_ezf_id)){
            $model->orWhere('ezform.ezf_id=:dataid', [':dataid'=>$ref_ezf_id]);
        }
              
        return $model->all();
    }

    public static function getEzformRef($ezf_id, $ezf_parent, $ref_ezf_id='') {
        $model = Ezform::find()
                ->innerJoin('ezform_fields', 'ezform_fields.ezf_id = ezform.ezf_id')
                ->where('ezform.ezf_id<>:ezf_id AND (ezform.ezf_id=:ezf_parent OR ezform_fields.parent_ezf_id=:ezf_parent) AND ezform.status = 1', [':ezf_id' => $ezf_id, ':ezf_parent' => $ezf_parent])
                ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id) OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->orderBy('ezform.created_at DESC');
        
        if(!empty($ref_ezf_id)){
            $model->orWhere('ezform.ezf_id=:dataid', [':dataid'=>$ref_ezf_id]);
        }
              
        return $model->all();
    }
    
    public static function getEzformDepend($ezf_id, $ref_id, $ref_ezf_id='') {
        $model = Ezform::find()
                ->innerJoin('ezform_fields', 'ezform_fields.ezf_id = ezform.ezf_id')
                ->where('(ezform_fields.ref_ezf_id=:ref_ezf_id) AND ezform.status = 1 AND ezf_target = 1', [':ref_ezf_id' => $ezf_ref])
                ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id) OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->orderBy('ezform.created_at DESC');
                
        if(!empty($ref_ezf_id)){
            $model->orWhere('ezform.ezf_id=:dataid', [':dataid'=>$ref_ezf_id]);
        }
              
        return $model->all();
    }

    public static function getEzformSubRef($ezf_ref, $ref_ezf_id='') {
        $model = Ezform::find()
                ->innerJoin('ezform_fields', 'ezform_fields.ezf_id = ezform.ezf_id')
                ->where('(ezform_fields.ref_ezf_id=:ref_ezf_id) AND ezform.status = 1 AND ezf_target = 1', [':ref_ezf_id' => $ezf_ref])
                ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id) OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->orderBy('ezform.created_at DESC');
        
        if(!empty($ref_ezf_id)){
            $model->orWhere('ezform.ezf_id=:dataid', [':dataid'=>$ref_ezf_id]);
        }
              
        return $model->all();
    }

    public static function getFieldsRef($ezf_id) {
        $sql = "SELECT ezf_field_name AS `id`, concat(ezf_field_name, ' (', ezf_field_label, ')') AS`name` FROM `ezform_fields` WHERE `ezf_id` = :id AND `ezf_field_type` = '904' AND table_field_type not in('none','field') ORDER BY ezf_field_order";
        $data = Yii::$app->db->createCommand($sql, [':id' => $ezf_id])->queryAll();
        return $data;
    }

    /**
     * @param $ezf_id
     * @return array|null|\yii\db\ActiveRecord|Ezform
     */
    public static function getEzformCoDevOne($ezf_id) {
        $model = Ezform::find()->where('ezf_id=:ezf_id AND status = 1', [':ezf_id' => $ezf_id]);

        if (!Yii::$app->user->can('administrator')) {
            $model->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id) OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode]);
        }

        $model->one();

        return $model;
    }

    /**
     * @param $ezf_id
     * @return array|null|\yii\db\ActiveRecord|Ezform
     */
    public static function getEzformOne($ezf_id) {
        $model = Ezform::find()->where('ezf_id=:ezf_id AND status = 1', [':ezf_id' => $ezf_id])->one();

        return $model;
    }
    
    public static function getTargetOneEzform($ezf_id) {
        
        $sql = "SELECT
            `ezform_fields`.*,
            `ezform`.`ezf_version`,
            `ezform`.`ezf_name`,
            `ezform`.`ezf_detail`,
            `ezform`.`xsourcex`,
            `ezform`.`ezf_table`,
            `ezform`.`status`,
            `ezform`.`shared`,
            `ezform`.`public_listview`,
            `ezform`.`public_edit`,
            `ezform`.`public_delete`,
            `ezform`.`co_dev`,
            `ezform`.`assign`,
            `ezform`.`category_id`,
            `ezform`.`field_detail`,
            `ezform`.`ezf_sql`,
            `ezform`.`ezf_js`,
            `ezform`.`ezf_error`,
            `ezform`.`query_tools`,
            `ezform`.`unique_record`,
            `ezform`.`consult_tools`,
            `ezform`.`consult_users`,
            `ezform`.`consult_telegram`,
            `ezform`.`ezf_options`
            FROM `ezform`
            INNER JOIN `ezform_fields` ON `ezform`.`ezf_id` = `ezform_fields`.`ezf_id`
            WHERE `ezform`.ezf_id = :ezf_id AND ezf_target=1
            ";

        return Yii::$app->db->createCommand($sql, [':ezf_id' => $ezf_id])->queryOne();
    }
    
    public static function getEzformWithField($ezf_field_id) {
        $sql = "SELECT
            `ezform_fields`.*,
            `ezform`.`ezf_version`,
            `ezform`.`ezf_name`,
            `ezform`.`ezf_detail`,
            `ezform`.`xsourcex`,
            `ezform`.`ezf_table`,
            `ezform`.`status`,
            `ezform`.`shared`,
            `ezform`.`public_listview`,
            `ezform`.`public_edit`,
            `ezform`.`public_delete`,
            `ezform`.`co_dev`,
            `ezform`.`assign`,
            `ezform`.`category_id`,
            `ezform`.`field_detail`,
            `ezform`.`ezf_sql`,
            `ezform`.`ezf_js`,
            `ezform`.`ezf_error`,
            `ezform`.`query_tools`,
            `ezform`.`unique_record`,
            `ezform`.`consult_tools`,
            `ezform`.`consult_users`,
            `ezform`.`consult_telegram`,
            `ezform`.`ezf_options`
            FROM `ezform`
            INNER JOIN `ezform_fields` ON `ezform`.`ezf_id` = `ezform_fields`.`ezf_id`
            WHERE ezf_field_id=:ezf_field_id
            ";

        return Yii::$app->db->createCommand($sql, [':ezf_field_id' => $ezf_field_id])->queryOne();
    }

    public static function getEzformTargetField($ezf_field_id) {
        $sql = "SELECT
            `ezform_fields`.*,
            `ezform`.`ezf_version`,
            `ezform`.`ezf_name`,
            `ezform`.`ezf_detail`,
            `ezform`.`xsourcex`,
            `ezform`.`ezf_table`,
            `ezform`.`status`,
            `ezform`.`shared`,
            `ezform`.`public_listview`,
            `ezform`.`public_edit`,
            `ezform`.`public_delete`,
            `ezform`.`co_dev`,
            `ezform`.`assign`,
            `ezform`.`category_id`,
            `ezform`.`field_detail`,
            `ezform`.`ezf_sql`,
            `ezform`.`ezf_js`,
            `ezform`.`ezf_error`,
            `ezform`.`query_tools`,
            `ezform`.`unique_record`,
            `ezform`.`consult_tools`,
            `ezform`.`consult_users`,
            `ezform`.`consult_telegram`,
            `ezform`.`created_by` as user_by,
            `ezform`.`ezf_options`
            FROM `ezform`
            INNER JOIN `ezform_fields` ON `ezform`.`ezf_id` = `ezform_fields`.`ref_ezf_id`
            WHERE ezf_field_id=:ezf_field_id
            ";

        return Yii::$app->db->createCommand($sql, [':ezf_field_id' => $ezf_field_id])->queryOne();
    }

    public static function getPtid($table, $target) {
        try {
            $data = Yii::$app->db->createCommand("SELECT ptid, sitecode, ptcode, ptcodefull FROM `" . $table . "` WHERE id = :id;", [':id' => $target])->queryOne();
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            $data = [];
        }
        return $data;
    }

    public static function findTable($table) {
        $sql = "SELECT TABLE_NAME FROM information_schema.TABLES 
            WHERE TABLE_NAME = :table";

        return Yii::$app->db->createCommand($sql, [':table' => $table])->queryOne();
    }

    public static function getRightEzform($ezf_id, $user_id) {
        $sql = "SELECT
            (SELECT COUNT(created_by) FROM ezform WHERE ezf_id = :ezf_id AND created_by=:user_id) AS ezform,
            (SELECT COUNT(user_co) FROM ezform_co_dev WHERE ezf_id = :ezf_id AND user_co=:user_id) AS codev,
            (SELECT COUNT(user_id) FROM ezform_assign WHERE ezf_id = :ezf_id AND user_id=:user_id) AS assign,
            (SELECT shared FROM ezform WHERE ezf_id = :ezf_id) AS shared";

        return Yii::$app->db->createCommand($sql, [':ezf_id' => $ezf_id, ':user_id' => $user_id])->queryOne();
    }

    public static function insertEzformCoDev($ezf_id, $user_id) {
        $sql = "INSERT IGNORE ezform_co_dev VALUES(:id,:user_id,1)";
        return Yii::$app->db->createCommand($sql, [':id' => $ezf_id, ':user_id' => $user_id])->execute();
    }

    public static function insertEzformAssign($ezf_id, $user_id) {
        $sql = "INSERT IGNORE ezform_assign VALUES(:id,:user_id,1)";
        return Yii::$app->db->createCommand($sql, [':id' => $ezf_id, ':user_id' => $user_id])->execute();
    }
    
    public static function insertEzformRole($ezf_id, $role) {
        $sql = "INSERT IGNORE ezform_role VALUES(:id,:role,1)";
        return Yii::$app->db->createCommand($sql, [':id' => $ezf_id, ':role' => $role])->execute();
    }

    public static function getTarget($table, $id) {
        if ($id == '') {
            return false;
        }

        $sql = "SELECT *
		FROM $table
                WHERE id = :id AND rstat not in(0, 3)
		";

        return Yii::$app->db->createCommand($sql, [':id' => $id])->queryOne();
    }

    public static function getTargetNotRstat($table, $id) {
        if ($id == '') {
            return false;
        }

        $sql = "SELECT *
		FROM $table
                WHERE id = :id AND rstat <> 3
		";

        return Yii::$app->db->createCommand($sql, [':id' => $id])->queryOne();
    }

    public static function getMaxCodeBySitecode($table, $hsitecode) {
        $maxcode = Yii::$app->db->createCommand("SELECT MAX(CAST(hptcode AS UNSIGNED)) AS ptcode FROM $table WHERE xsourcex = :hsitecode ORDER BY id DESC ", [':hsitecode' => $hsitecode])->queryScalar();
        $hptcode = str_pad($maxcode + 1, 5, '0', STR_PAD_LEFT);
        return $hptcode;
    }

    public static function getMaxCode($table) {
        $maxcode = Yii::$app->db->createCommand("SELECT MAX(CAST(hptcode AS UNSIGNED)) AS ptcode FROM $table ORDER BY id DESC ")->queryScalar();
        $hptcode = str_pad($maxcode + 1, 5, '0', STR_PAD_LEFT);
        return $hptcode;
    }

    public static function getEzfSpecial($ezf_id) {
        $modelFields = EzformFields::find()
                ->where('ezf_id = :ezf_id AND ezf_special=1', [':ezf_id' => $ezf_id])
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();
        return $modelFields;
    }

    public static function checkCidAll($table, $field, $cid) {
        $sql = "SELECT * FROM $table WHERE rstat not in(0,3) AND $field = :cid";
        return Yii::$app->db->createCommand($sql, [':cid' => $cid])->queryAll();
    }

    public static function getFieldById($ezf_field_id) {
        $modelFields = EzformFields::find()
                ->where('ezf_field_id = :ezf_field_id', [':ezf_field_id' => $ezf_field_id])
                ->one();
        return $modelFields;
    }

    public static function getFieldChildrenById($ezf_id, $ezf_field_id) {
        $sql = "SELECT
                    ezf_field_id,
                    ezf_id,
                    ezf_field_name,
                    ezf_field_type,
                    ezf_field_options,
                    ezf_field_ref
                FROM
                `ezform_fields`
                WHERE `ezf_id`= :ezf_id AND
                `ezf_field_ref`= :ezf_field_id";

        return Yii::$app->db->createCommand($sql, [':ezf_id' => $ezf_id, ':ezf_field_id' => $ezf_field_id])->queryAll();
    }

    public static function getRefFieldById($ezf_field_id) {
        $sql = "SELECT
                `ezform`.`ezf_id`,
                `ezform`.`ezf_name`,
                `ezform`.`ezf_table`,
                `ezform_fields`.*
                FROM
                `ezform`
                INNER JOIN `ezform_fields`
                ON `ezform`.`ezf_id` = `ezform_fields`.`ref_ezf_id`
                WHERE
                `ezform_fields`.`ezf_field_id`= :ezf_field_id";

        return Yii::$app->db->createCommand($sql, [':ezf_field_id' => $ezf_field_id])->queryOne();
    }

    public static function getRefFieldByName($ezf_id, $varname) {
        $sql = "SELECT
                `ezform`.`ezf_id`,
                `ezform`.`ezf_name`,
                `ezform`.`ezf_table`,
                `ezform_fields`.`ref_ezf_id`,
                `ezform_fields`.`ref_field_id`
                FROM
                `ezform`
                INNER JOIN `ezform_fields`
                ON `ezform`.`ezf_id` = `ezform_fields`.`ref_ezf_id`
                INNER JOIN `ezform_fields` AS `efa1`
                ON `ezform`.`ezf_id` = `efa1`.`ezf_id`
                WHERE
                `ezform_fields`.`ref_ezf_id` = :ezf_id AND `ezform_fields`.`ref_field_id`=:ezf_field_name AND `efa1`.`ezf_field_name`= :ezf_field_name";

        return Yii::$app->db->createCommand($sql, [':ezf_id' => $ezf_id, ':ezf_field_name' => $varname])->queryOne();
    }

    public static function getFieldByNameVersion($ezf_id, $varname, $v) {
        $modelFields = EzformFields::find()
                ->where('ezf_id = :ezf_id AND ezf_field_name=:ezf_field_name AND ezf_version=:v', [':ezf_id' => $ezf_id, ':ezf_field_name' => $varname, ':v' => $v])
                ->one();
        return $modelFields;
    }

    public static function getFieldByName($ezf_id, $varname) {
        $modelFields = EzformFields::find()
                ->where('ezf_id = :ezf_id AND ezf_field_name=:ezf_field_name', [':ezf_id' => $ezf_id, ':ezf_field_name' => $varname])
                ->one();
        return $modelFields;
    }

    public static function getFieldDependOne($ezf_id, $dependField) {
        
        $modelFields = EzformFields::find()
                ->where('ezf_id = :ezf_id AND ezf_field_name=:ezf_field_name', [':ezf_id' => $ezf_id, ':ezf_field_name'=>$dependField])
                ->one();
        return $modelFields;
    }
    
    public static function getTargetOne($ezf_id) {
        $modelFields = EzformFields::find()
                ->where('ezf_id = :ezf_id AND ezf_target=1', [':ezf_id' => $ezf_id])
                ->one();
        return $modelFields;
    }

    public static function findSpecialOne($ezf_id) {
        $modelFields = EzformFields::find()
                ->select('ef2.*')
                ->innerJoin('ezform_fields ef2', 'ezform_fields.parent_ezf_id=ef2.ezf_id')
                ->where('ezform_fields.ezf_id = :ezf_id AND ef2.ezf_special=1', [':ezf_id' => $ezf_id])
                ->one();
        return $modelFields;
    }

    public static function getSpecialOne($ezf_id) {
        $modelFields = EzformFields::find()
                ->where('ezf_id = :ezf_id AND ezf_special=1', [':ezf_id' => $ezf_id])
                ->one();
        return $modelFields;
    }

    public static function getEventFields($ezf_id) {
        $modelFields = EzformFields::find()
                ->where('ezf_id = :ezf_id AND (ezf_special=1 OR ezf_target=1)', [':ezf_id' => $ezf_id])
                ->all();
        return $modelFields;
    }
    
    public static function getJoinFieldsAll($ezf_id) {
        $modelFields = EzformFields::find()
                ->where('parent_ezf_id = :ezf_id AND ezf_target=1', [':ezf_id' => $ezf_id])
                ->all();
        return $modelFields;
    }

    public static function checkEventFields($ezf_id, $ezf_field_id) {
        $modelFields = EzformFields::find()
                ->where('ezf_id = :ezf_id AND ezf_field_id <> :ezf_field_id AND (ezf_special=1 OR ezf_target=1)', [':ezf_id' => $ezf_id, ':ezf_field_id' => $ezf_field_id])
                ->one();
        return $modelFields;
    }

    public static function setParentFields($ezf_id, $parentId) {
        $countLv = 0;
        $modelFields = EzformFields::find()
                ->where('`ezf_target`=1 AND `ref_ezf_id`=:ezf_id', [':ezf_id' => $ezf_id])
                ->one();

        if ($modelFields) {
            $modelEzf = EzfQuery::getEzformOne($ezf_id);
            $dataEzf = $modelEzf->attributes;

            $modelFields->parent_ezf_id = $parentId;
            $modelFields->ref_form = \backend\modules\ezbuilder\classes\EzBuilderFunc::setRefForm($modelFields);
            if ($modelFields->save()) {
                \backend\modules\ezbuilder\classes\EzBuilderFunc::updateRefForm($modelFields, $dataEzf);
            }
            $countLv++;

            self::setParentFields($modelFields['ezf_id'], $parentId);
        }

        return $countLv;
    }

    public static function getDepartmentByName($q = null, $sht = null) {
        $sql = "SELECT zwu.id AS code,unit_name AS name,order_type_code AS type
                FROM zdata_working_unit zwu
                INNER JOIN zdata_order_type zot ON(zot.id=unit_order_type)
                WHERE zwu.rstat=1 AND unit_name LIKE :q AND order_type_code LIKE :type";
        return Yii::$app->db->createCommand($sql, [':q' => "%$q%", ':type' => "$sht%"])->queryAll();
    }

    public static function getDepartmentByCode($q) {
        $sql = "SELECT zwu.id AS code,unit_name AS name,order_type_code AS type
                FROM zdata_working_unit zwu
                INNER JOIN zdata_order_type zot ON(zot.id=unit_order_type)
                WHERE zwu.rstat=1 AND unit_code LIKE :q";
        return Yii::$app->db->createCommand($sql, [':q' => "%$q%"])->queryOne();
    }

    public static function getDepartmentByID($q) {
        $sql = "SELECT zwu.id AS code,unit_name AS name,order_type_code AS type
                FROM zdata_working_unit zwu
                INNER JOIN zdata_order_type zot ON(zot.id=unit_order_type)
                WHERE zwu.rstat=1 AND zwu.id LIKE :q";
        return Yii::$app->db->createCommand($sql, [':q' => "%$q%"])->queryOne();
    }

    public static function getRefFields($ezf_field_ref) {
        $sql = "SELECT
            `ezform`.`ezf_name`,
            `ezform`.`ezf_table`,
            `ezform`.`ezf_id`,
            `ezform_fields`.`ezf_field_name`,
            `ezform_fields`.`ezf_field_label`,
            `ezform_fields`.`ezf_field_ref`
            FROM
            `ezform`
            JOIN `ezform_fields`
            ON `ezform`.`ezf_id` = `ezform_fields`.`ezf_id`
            WHERE
            `ezform_fields`.`ezf_field_ref` = :ezf_field_ref
		";

        return Yii::$app->db->createCommand($sql, [':ezf_field_ref' => $ezf_field_ref])->queryAll();
    }

    public static function builderSql($select, $table, $where, $params = []) {
        $query = new \yii\db\Query();
        $query->select($select);
        $query->from($table);
        $query->where($where, $params);

        return $query;
    }

    public static function builderSqlGetScalar($select, $table, $where, $params = []) {
        $query = self::builderSql($select, $table, $where, $params);

        return $query->createCommand()->queryScalar();
    }

    public static function builderSqlGetOne($select, $table, $where, $params = []) {
        $query = self::builderSql($select, $table, $where, $params);

        return $query->createCommand()->queryOne();
    }

    public static function builderSqlGetAll($select, $table, $where, $params = []) {
        $query = self::builderSql($select, $table, $where, $params);

        return $query->createCommand()->queryAll();
    }
    
    public static function getOrderFav($userid) {
        $sql = "SELECT MAX(forder)+1 AS num
		FROM ezform_favorite
		WHERE userid=:userid
		";
        $order = Yii::$app->db->createCommand($sql, [':userid' => $userid])->queryScalar();
        return isset($order) ? (int) $order : 1;
    }
    
    public static function getEzformStapleList($staple_id) {
        $model = Ezform::find()
                ->innerJoin('ezform_fields', 'ezform_fields.ezf_id = ezform.ezf_id')
                ->where('status = 1 AND ezf_field_type = 92 AND (ezform_fields.ref_ezf_id=:staple_id)', [':staple_id' => $staple_id])
                ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id) OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->groupBy('ezform.ezf_id')
                ->all();
        return $model;
    }
    
    public static function getEzformList($ezf_id) {
        $model = Ezform::find()
                ->innerJoin('ezform_fields', 'ezform_fields.ezf_id = ezform.ezf_id')
                ->where('status = 1 AND ezf_field_type<>0 AND (ezform_fields.parent_ezf_id=:ezf_id OR ezform.ezf_id=:ezf_id)', [':ezf_id' => $ezf_id])
                ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id) OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->groupBy('ezform.ezf_id')
                ->all();
        return $model;
    }
    
    public static function getEzformListByGroup($category) {
        $model = Ezform::find()
                ->innerJoin('ezform_fields', 'ezform_fields.ezf_id = ezform.ezf_id')
                ->where('status = 1 AND ezf_field_type<>0 AND (ezform.category_id=:category_id)', [':category_id' => $category])
                ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id) OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                ->groupBy('ezform.ezf_id')
                ->all();
        return $model;
    }

    public static function genCidForeigner($table, $field, $prefix = 9999) {
        $sql = "SELECT MAX(`$field`) FROM $table WHERE `$field` like :cid";//not in(0,3)
        return Yii::$app->db->createCommand($sql, [':cid' => "$prefix%"])->queryScalar();
    }

    public static function getEzWorkBtn($unit) {
        $r = '';
        
        $sql = "SELECT
                `in_ezf_id`,
                `unit_target` AS unit,
                `tab_name`,
                u.unit_name
                FROM
                `zdata_working_unit_setting`
                inner join zdata_working_unit u on u.id = zdata_working_unit_setting.unit_target
                WHERE zdata_working_unit_setting.unit_code=:unit ORDER BY unit_target
            ";

        return Yii::$app->db->createCommand($sql, [':unit' => $unit])->queryAll();
    }
    
    public static function getFieldAllVersion($ezf_id) {
        $modelFields = EzformFields::find()
                ->where("ezf_id = :ezf_id", [':ezf_id' => $ezf_id])
                ->orderBy(['ezf_version' => SORT_ASC, 'ezf_field_order' => SORT_ASC])
                ->all();
        return $modelFields;
    }
    public static function getDropDownRefAllVersion($ezf_id) {
        $sql = "SELECT `ezf_field_name` AS id, ref_ezf_id AS name FROM `ezform_fields` WHERE table_field_type<>'none' AND table_field_type<>'field' AND `ezf_id` = :id AND (ezf_field_type=80 OR ezf_field_type=87)";
        $data = Yii::$app->db->createCommand($sql, [':id' => $ezf_id])->queryAll();
        return $data;
    }
    public static function getDropDownAllVersion($ezf_id) {
        $sql = "SELECT `ezf_field_name` AS id, concat(`ezf_field_label`, ' (', `ezf_field_name`, ')', ' [', `ezf_version`, ']') AS name, ezf_field_name FROM `ezform_fields` WHERE table_field_type<>'none' AND table_field_type<>'field' AND `ezf_id` = :id AND (ezf_field_type=80 OR ezf_field_type=87)";
        $data = Yii::$app->db->createCommand($sql, [':id' => $ezf_id])->queryAll();
        return $data;
    }

    public static function getFieldsListAllVersion($ezf_id) {
        $sql = "SELECT `ezf_field_id` AS id, concat(`ezf_field_label`, ' (', `ezf_field_name`, ')', ' [', `ezf_version`, ']') AS name, ezf_field_name, ezf_field_id, ezf_field_type, ref_ezf_id, ref_field_id FROM `ezform_fields` WHERE table_field_type<>'none' AND table_field_type<>'field' AND `ezf_id` = :id ";
        $data = Yii::$app->db->createCommand($sql, [':id' => $ezf_id])->queryAll();
        return $data;
    }
    
    public static function getFieldsListVersion($ezf_id, $version) {
        $sql = "SELECT `ezf_field_id` AS id, concat(`ezf_field_label`, ' (', `ezf_field_name`, ')') AS name, ezf_field_name FROM `ezform_fields` WHERE table_field_type<>'none' AND table_field_type<>'field' AND `ezf_id` = :id AND (ezf_version = :ezf_version OR ezf_version='all')";
        $data = Yii::$app->db->createCommand($sql, [':id' => $ezf_id, ':ezf_version' => $version])->queryAll();
        return $data;
    }

    public static function getFieldAll($ezf_id, $version) {
        $modelFields = EzformFields::find()
                ->where("ezf_id = :ezf_id AND (ezf_version = :ezf_version OR ezf_version='all')", [':ezf_id' => $ezf_id, ':ezf_version' => $version])
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();
        return $modelFields;
    }
    
    public static function getFieldStapleAll($ezf_id, $version) {
        $modelFields = EzformFields::find()
                ->where("ezf_id = :ezf_id AND ezf_field_type in(92, 93) AND (ezf_version = :ezf_version OR ezf_version='all')", [':ezf_id' => $ezf_id, ':ezf_version' => $version])
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();
        return $modelFields;
    }

    public static function getFieldAllNotLabel($ezf_id, $version) {
        $modelFields = EzformFields::find()
                ->where("table_field_type<>'none' AND table_field_type<>'field' AND ezf_id = :ezf_id AND (ezf_version = :ezf_version OR ezf_version='all')", [':ezf_id' => $ezf_id, ':ezf_version' => $version])
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();
        return $modelFields;
    }

    public static function getFieldAllByVersion($ezf_id, $version) {
        $modelFields = EzformFields::find()
                ->where("ezf_id = :ezf_id AND ezf_version = :ezf_version", [':ezf_id' => $ezf_id, ':ezf_version' => $version])
                ->all();
        return $modelFields;
    }

    public static function getEzformConfig($ezf_id, $version) {
        $modelFields = EzformVersion::find()
                ->where("ezf_id = :ezf_id AND ver_code = :ezf_version", [':ezf_id' => $ezf_id, ':ezf_version' => $version])
                ->one();
        return $modelFields;
    }

    public static function getEzformConfigApprov($ezf_id, $version) {
        $modelFields = EzformVersion::find()
                ->where("ezform_version.ezf_id = :ezf_id AND ver_code = :ezf_version AND ver_approved=2", [':ezf_id' => $ezf_id, ':ezf_version' => $version])
                ->one();
        return $modelFields;
    }

    public static function getEzformVersionApprovList($ezf_id) {
        $modelFields = EzformVersion::find()
                ->where("ezf_id = :ezf_id AND ver_approved=2", [':ezf_id' => $ezf_id])
                ->all();
        return $modelFields;
    }

    public static function getEzformVersionList($ezf_id) {
        $modelFields = EzformVersion::find()
                ->where("ezf_id = :ezf_id", [':ezf_id' => $ezf_id])
                ->all();
        return $modelFields;
    }

    public static function getChoiceAllByVersion($ezf_id, $version) {
        $model = \backend\modules\ezforms2\models\EzformChoice::find()
                ->where("ezf_id = :ezf_id AND ezf_version = :ezf_version", [':ezf_id' => $ezf_id, ':ezf_version' => $version])
                ->all();
        return $model;
    }

    public static function getConditionAllByVersion($ezf_id, $version) {
        $model = \backend\modules\ezforms2\models\EzformCondition::find()
                ->where("ezf_id = :ezf_id AND ezf_version = :ezf_version", [':ezf_id' => $ezf_id, ':ezf_version' => $version])
                ->all();
        return $model;
    }

    public static function getFieldAllVersionByName($ezf_id, $ezf_field_name, $v) {
        $sql = "SELECT
                `ezform_fields`.`ezf_id`,
                `ezform_fields`.`ezf_field_id`,
                `ezform_fields`.`ezf_version`,
                `ezform_fields`.`ezf_field_name`,
                `ezform_fields`.`ezf_field_type`,
                `ezform_version`.`ver_approved`,
                `ezform_version`.`ver_active`,
                `ezform_version`.`ver_code`
                FROM
                `ezform_fields`
                JOIN `ezform_version`
                ON `ezform_fields`.`ezf_id` = `ezform_version`.`ezf_id` AND `ezform_fields`.`ezf_version` = `ezform_version`.`ver_code` 
                WHERE
                `ezform_fields`.`ezf_id` = :ezf_id AND `ezform_fields`.`ezf_field_name` = :ezf_field_name  AND `ezform_version`.`ver_code`<> :v
            ";

        return Yii::$app->db->createCommand($sql, [':ezf_id' => $ezf_id, ':ezf_field_name' => $ezf_field_name, ':v' => $v])->queryAll();
    }

    public static function getFieldsList($ezf_id) {
        $sql = "SELECT `ezf_field_id` AS id, concat(`ezf_field_name`, ' (', `ezf_field_label`, ')', ' [', `ezf_version`, ']') AS name, ezf_field_name FROM `ezform_fields` WHERE table_field_type<>'none' AND table_field_type<>'field' AND `ezf_id` = :id";
        $data = Yii::$app->db->createCommand($sql, [':id' => $ezf_id])->queryAll();
        return $data;
    }

    public static function showTable($table) {
        $sql = "SHOW TABLES LIKE :table
		";
        return Yii::$app->db->createCommand($sql, [':table' => $table])->queryAll();
    }

    public static function getFieldRef($ezf_id, $ezf_field_name, $v) {
        $sql = "SELECT
                `ezform_fields`.`ezf_field_id`,
                `ezform_fields`.`ezf_id`,
                `ezform_fields`.`ezf_field_name`,
                `ezform_fields`.`ezf_field_label`,
                `ezform_fields`.`ezf_field_type`,
                `ezform_fields`.`ezf_field_ref`
                FROM
                `ezform_fields`
                INNER JOIN (
                SELECT
                `ezf_field_id`,
                `ezf_id`,
                `ezf_field_name`,
                `ezf_field_label`,
                `ezf_field_type`,
                `ezf_field_ref`
                FROM
                `ezform_fields`
                WHERE
                `ezf_id`=:ezf_id and `ezf_field_name`=:ezf_field_name and `ezf_version` = :v
                ) field_ref ON field_ref.ezf_field_id = ezform_fields.ezf_field_ref

            ";

        return Yii::$app->db->createCommand($sql, [':ezf_id' => $ezf_id, ':ezf_field_name' => $ezf_field_name, ':v' => $v])->queryAll();
    }

    public static function getFieldRefId($ezf_field_id) {
        $sql = "SELECT
                `ezf_field_id`,
                `ezf_id`,
                `ezf_field_name`,
                `ezf_field_label`,
                `ezf_field_name`,
                `ezf_field_type`,
                `ezf_field_ref`
                FROM
                `ezform_fields`
                WHERE
                `ezf_field_ref`=:ezf_field_id
            ";

        return Yii::$app->db->createCommand($sql, [':ezf_field_id' => $ezf_field_id])->queryAll();
    }
    
    public static function getUserProfile($user_id) {

        $sql = "SELECT *, CONCAT(`firstname`,' ',`lastname`) as fullname
		FROM profile
                WHERE user_id=:user_id
		";

        return Yii::$app->db->createCommand($sql, [':user_id' => $user_id])->queryOne();
    }

    public static function getUserProfileIn($user_id) {

        $sql = "SELECT user_id, CONCAT(`firstname`,' ',`lastname`) as fullname
		FROM profile
                WHERE user_id in($user_id)
		";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }
    
    public static function getEzSqlIn($id) {

        $sql = "SELECT id, sql_name
		FROM zdata_ezsql
                WHERE id in($id)
		";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getEventStop($start, $end) {
        $y = substr($start, 0, 4);
        $m = substr($start, 5, 2);

        $y2 = substr($end, 0, 4);

        if ($y < $y2 && $m == '01') {
            $y = $y2;
        }

        $sql = "SELECT id, CONCAT('$y', '-', lpad(hmonth,2,0), '-', lpad(hday,2,0)) AS ddate, user_create,
		hname
		FROM zdata_holiday WHERE rstat<>3 AND rstat<>0 AND hstatus=1
		";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getEventStopCustom($start, $end) {
        $sarry = date_parse($start);
        $earry = date_parse($end);

        $sdate = "{$sarry['year']}-{$sarry['month']}-{$sarry['day']}";
        $edate = "{$earry['year']}-{$earry['month']}-{$earry['day']}";

        $sql = "SELECT id, CONCAT(lpad(hyear,4,0), '-', lpad(hmonth,2,0), '-', lpad(hday,2,0)) AS ddate, user_create,
		hname
		FROM zdata_holiday WHERE rstat<>3 AND rstat<>0 AND hstatus=0 AND CONCAT(lpad(hyear,4,0), '-', lpad(hmonth,2,0), '-', lpad(hday,2,0)) BETWEEN :start AND :end
		";

        return Yii::$app->db->createCommand($sql, [':start' => $sdate, ':end' => $edate])->queryAll();
    }

    public static function getEventEzForm($start, $end, $ezform, $options, $search) {
        $model = new \backend\modules\ezforms2\models\TbdataAll();
        $model->setTableName($ezform->ezf_table);



        $query = $model->find()->where('rstat not in(0,3)');

        if ($search == '') {
            $query->andWhere("DATE(`{$options['start']}`) between '{$start}' AND '{$end}'");
        }

        $modelEvent = EzfQuery::getEventFields($ezform->ezf_id);
        $modelFields;
        if ($modelEvent) {
            foreach ($modelEvent as $key => $value) {
                if ($value['ezf_target'] == 1) {
                    $modelFields = EzfQuery::findSpecialOne($ezform->ezf_id);
                } elseif ($value['ezf_special'] == 1) {
                    $modelFields = true;
                }
            }
        }

        if (isset($modelFields) || $ezform['public_listview'] == 2) {
            $query->andWhere('xsourcex = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
        }

        if ($ezform['public_listview'] == 3) {
            $query->andWhere('xdepartmentx = :unit', [':unit' => Yii::$app->user->identity->profile->department]);
        }

        if ($ezform['public_listview'] == 0) {
            if ($ezform->ezf_table == 'zdata_ezcalendar') {
                $query->andWhere("(user_create=:created_by OR share LIKE :created_by_like)", [':created_by' => Yii::$app->user->id, ':created_by_like' => '%' . Yii::$app->user->id . '%']);
            } else {
                $query->andWhere("user_create=:created_by", [':created_by' => Yii::$app->user->id]);
            }
        }



        $repeat = isset($options['repeat']) ? $options['repeat'] : '';

        if ($repeat != '') {
            $query->andWhere("`{$repeat}`='' OR ISNULL(`{$repeat}`) ");
        }

        if ($search != '') {
            $query->andWhere("`{$options['subject']}` LIKE :search", [':search' => "%$search%"]);
        }

        return $query->all();
    }

    public static function getRepeatEventEzForm($start, $end, $ezform, $options, $search) {
        $model = new \backend\modules\ezforms2\models\TbdataAll();
        $model->setTableName($ezform->ezf_table);

        $query = $model->find()->where('rstat not in(0,3)');

        $modelEvent = EzfQuery::getEventFields($ezform->ezf_id);
        $modelFields;
        if ($modelEvent) {
            foreach ($modelEvent as $key => $value) {
                if ($value['ezf_target'] == 1) {
                    $modelFields = EzfQuery::findSpecialOne($ezform->ezf_id);
                } elseif ($value['ezf_special'] == 1) {
                    $modelFields = true;
                }
            }
        }

        if (isset($modelFields) || $ezform['public_listview'] == 2) {
            $query->andWhere('xsourcex = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
        }

        if ($ezform['public_listview'] == 3) {
            $query->andWhere('xdepartmentx = :unit', [':unit' => Yii::$app->user->identity->profile->department]);
        }

        if ($ezform['public_listview'] == 0) {
            $query->andWhere("user_create=:created_by", [':created_by' => Yii::$app->user->id]);
        }

        $repeat = isset($options['repeat']) ? $options['repeat'] : '';

        if ($repeat != '') {
            $query->andWhere("`{$repeat}`!='' OR (`{$repeat}`='year' AND MONTH(DATE(`{$options['start']}`)) between MONTH('{$start}') AND MONTH('{$end}')) ");
        }

        if ($search != '') {
            $query->andWhere("`{$options['subject']}` LIKE :search", [':search' => "%$search%"]);
        }

        return $query->all();
    }
    
    public static function getEzformCoDevTableAll() {
        $query = new \yii\db\Query();
        $query->select([
            'ezf_id',
            'ezf_name',
            'ezf_table',
        ])  ->from('ezform')
            ->where('status = 1')
            ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id) OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
            ->orderBy('created_at DESC');
        
        return $query->createCommand()->queryAll();
    }
    
    public static function getSqlById($id) {
        $sql = "SELECT id, sql_name AS name
		FROM zdata_ezsql
                WHERE id=:id
		";

        return Yii::$app->db->createCommand($sql, [':id' => $id])->queryOne();
    }
    
    public static function getUserProfileById($user_id, $mode = 'all') {
        $model = \common\modules\user\models\Profile::find()->where('user_id=:user_id', [':user_id'=>$user_id])->one();
        
        $str = '';
        if($mode=='name'){
            $str = $model['firstname'].' '.$model['lastname'];
            
            return $str;   
        }
        return $model;
    }
    
    public static function getFieldByTbNameVersion($ezf_table, $varname) {
        $sql = "SELECT
                `ezform`.`ezf_table`,
                `ezform_fields`.`ezf_field_name`,
                `ezform_fields`.`ezf_field_type`,
                `ezform`.`ezf_id`,
                `ezform`.`ezf_version`,
                `ezform_fields`.`ezf_field_id`
                FROM
                `ezform`
                JOIN `ezform_fields`
                ON `ezform`.`ezf_id` = `ezform_fields`.`ezf_id`
                AND `ezform`.`ezf_version` = `ezform_fields`.`ezf_version`
                WHERE
                `ezform`.`ezf_table` = :ezf_table AND `ezform_fields`.`ezf_field_name` = :varname AND `ezform`.`status` = 1
		";

        return Yii::$app->db->createCommand($sql, [':ezf_table' => $ezf_table, ':varname' => $varname])->queryOne();
    }
    
    public static function countIncomming($created_at) {
        $userProfile = Yii::$app->user->identity->profile;
        $str = '';
        if($created_at!=''){
            $daterang = explode(' to ', $created_at);
            if (isset($daterang[1])) {
                $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                $str = " AND date(queue_log.created_at) BETWEEN '$sdate' AND '$edate'";
            }
        }
        
        $sql = "SELECT count(*) as num FROM `queue_log` WHERE `enable`=1 AND status='in_comming' AND queue_log.unit=:unit $str";
        $data = Yii::$app->db->createCommand($sql, [':unit'=>$userProfile->department])->queryScalar();
        return $data;
    }
    
    public static function countInprocess($created_at) {
        $userProfile = Yii::$app->user->identity->profile;
        
        $str = '';
        if($created_at!=''){
            $daterang = explode(' to ', $created_at);
            if (isset($daterang[1])) {
                $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                $str = " AND date(queue_log.created_at) BETWEEN '$sdate' AND '$edate'";
            }
        }
        
        $sql = "SELECT count(*) as num FROM `queue_log` WHERE `enable`=1 AND status='process' AND queue_log.unit=:unit $str";
        $data = Yii::$app->db->createCommand($sql, [':unit'=>$userProfile->department])->queryScalar();
        return $data;
    }
    
    public static function countIncompleted($created_at) {
        $userProfile = Yii::$app->user->identity->profile;
        
        $str = '';
        if($created_at!=''){
            $daterang = explode(' to ', $created_at);
            if (isset($daterang[1])) {
                $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                $str = " AND date(queue_log.created_at) BETWEEN '$sdate' AND '$edate'";
            }
        }
        
        $sql = "SELECT count(*) as num FROM `queue_log` WHERE `enable`=1 AND status='completed' AND queue_log.unit=:unit $str";
        $data = Yii::$app->db->createCommand($sql, [':unit'=>$userProfile->department])->queryScalar();
        return $data;
    }
    
    public static function countOutgoing($created_at) {
        $userProfile = Yii::$app->user->identity->profile;
        
        $str = '';
        if($created_at!=''){
            $daterang = explode(' to ', $created_at);
            if (isset($daterang[1])) {
                $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                $str = " AND date(queue_log.created_at) BETWEEN '$sdate' AND '$edate'";
            }
        }
        
        $sql = "SELECT count(*) as num FROM `queue_log` WHERE `enable`=1 AND queue_log.current_unit=:unit $str";
        $data = Yii::$app->db->createCommand($sql, [':unit'=>$userProfile->department])->queryScalar();
        return $data;
    }
    
    public static function getRoleAll() {
        $sql = "SELECT
                    `zdata_role_permissions`.`id`,
                    `zdata_role_permissions`.`target`,
                    `zdata_role_permissions`.`xsourcex`,
                    `zdata_role_permissions`.`xdepartmentx`,
                    `zdata_role_permissions`.`rstat`,
                    `zdata_role_permissions`.`role_name`,
                    `zdata_role_permissions`.`role_desc`
                    FROM
                    `zdata_role_permissions`
                WHERE
                `zdata_role_permissions`.`rstat` not in(0 , 3)
		";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }
    
    public static function getRoleAllByEzform($ezf_id) {
        $sql = "SELECT
                    `zdata_role_permissions`.`id`,
                    `zdata_role_permissions`.`target`,
                    `zdata_role_permissions`.`xsourcex`,
                    `zdata_role_permissions`.`xdepartmentx`,
                    `zdata_role_permissions`.`rstat`,
                    `zdata_role_permissions`.`role_name`,
                    `zdata_role_permissions`.`role_desc`
                    FROM
                    `zdata_role_permissions`
                    inner join ezform_role on zdata_role_permissions.role_name = ezform_role.role AND `zdata_role_permissions`.`rstat` not in(0 , 3)
                WHERE
                 ezf_id =:ezf_id
		";

        return Yii::$app->db->createCommand($sql, [':ezf_id'=>$ezf_id])->queryAll();
    }
    
    public static function getRoleAllByEzmodule($ezm_id) {
        $sql = "SELECT
                    `zdata_role_permissions`.`id`,
                    `zdata_role_permissions`.`target`,
                    `zdata_role_permissions`.`xsourcex`,
                    `zdata_role_permissions`.`xdepartmentx`,
                    `zdata_role_permissions`.`rstat`,
                    `zdata_role_permissions`.`role_name`,
                    `zdata_role_permissions`.`role_desc`
                    FROM
                    `zdata_role_permissions`
                    inner join ezmodule_role on zdata_role_permissions.role_name = ezmodule_role.role AND `zdata_role_permissions`.`rstat` not in(0 , 3)
                WHERE
                 ezm_id =:ezm_id
		";

        return Yii::$app->db->createCommand($sql, [':ezm_id'=>$ezm_id])->queryAll();
    }
    
    public static function getUnitParent() {
        $sitecode = Yii::$app->user->identity->profile->sitecode;

        $sql = "SELECT u.id as id, 
		    CONCAT(u.unit_code, ' ', u.unit_name) AS text,
                    t.unit_type,
                    u.unit_parent
		FROM zdata_working_unit u
                left join zdata_unit_type t on t.id = u.unit_type
                WHERE u.xsourcex=:sitecode AND u.rstat not in(0,3) AND u.unit_show=1
		";

        return Yii::$app->db->createCommand($sql, [':sitecode' => $sitecode])->queryAll();
    }
    
    public static function getFieldStaple() {
        $sql = "SELECT
                `ezform_autonum`.`id`,
                `ezform_autonum`.`type`,
                `ezform_autonum`.`ezf_field_id`,
                `ezform_autonum`.`ezf_id`,
                `ezform_autonum`.`label`,
                `ezform_autonum`.`status`,
                `ezform_fields`.`ezf_field_name`
                FROM
                `ezform_autonum`
                JOIN `ezform_fields`
                ON `ezform_autonum`.`ezf_field_id` = `ezform_fields`.`ezf_field_id`
                WHERE
                `ezform_autonum`.`status` = 1 
            "; //AND `ezform_autonum`.`type` = 2

        return Yii::$app->db->createCommand($sql)->queryAll();
    }
    
}
