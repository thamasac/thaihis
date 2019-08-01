<?php
 
namespace common\modules\user\classes;
 

use backend\modules\ezforms2\models\EzformDynamic;
use Yii;
use backend\modules\ezforms2\models\EzformFields;
//use backend\modules\component\models\EzformComponent;
use backend\modules\ezforms2\models\Ezform;
use yii\base\DynamicModel;
use yii\db\Exception;
use yii\helpers\VarDumper;

class EzformQuery {

    public static function getFields($id) {
        $model = EzformFields::find()->where(['ezf_field_id' => $id])->andWhere('ezf_field_head_label = 0')->andWhere('ezf_field_type IS NOT NULL')->one();

        return $model;
    }

    public static function getInputAll() {
        $sql = "SELECT *
		FROM ezform_input
		WHERE input_version='v1'
		ORDER BY input_order";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getInputAllEdit() {
        $sql = "SELECT *
		FROM ezform_input WHERE input_id NOT IN (19, 18, 31, 10) AND input_version='v1'
		ORDER BY input_order ";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getInputId($id) {
        $sql = "SELECT *
		FROM ezform_input
		WHERE input_id = :id
		ORDER BY input_order";

        return Yii::$app->db->createCommand($sql, [':id' => $id])->queryOne();
    }

    public static function getChoiceId($id) {
        $sql = "SELECT *
		FROM ezform_choice
		WHERE ezf_field_id = :id AND `ezf_choiceetc` is NULL
		ORDER BY ezf_choice_id";

        return Yii::$app->db->createCommand($sql, [':id' => $id])->queryAll();
    }

    public static function getFieldsByEzf_id($ezf_id) {
        $model = EzformFields::find()
                ->where('ezf_id = :ezf_id', [':ezf_id' => $ezf_id])
                ->andWhere('ezf_field_head_label is NULL ')
                ->andWhere('ezf_field_type IS NOT NULL')
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();

        return $model;
    }

    public static function getChoiceOtherId($id) {
        $sql = "SELECT *
		FROM ezform_choice
		WHERE ezf_field_id = :id AND `ezf_choiceetc`='1'
		ORDER BY ezf_choice_id";

        return Yii::$app->db->createCommand($sql, [':id' => $id])->queryOne();
    }

    public static function getFieldComponent($ezf_id) {
        $ezformComponents = EzformComponent::find()
                ->where(['comp_id' => $ezf_id])
                ->one();
        return $ezformComponents;
    }

    public static function getForm($ezf_id) {
        $ezform = Ezform::find()
                ->where(['ezf_id' => $ezf_id])
                ->one();
        return $ezform;
    }

    public static function getFormTableName($ezf_id) {
        $ezform = Ezform::find()
                ->select('ezf_table, ezf_name, unique_record')
                ->where(['ezf_id' => $ezf_id])
                ->one();
        return $ezform;
    }

    public static function getProvince() {
        $sql = "SELECT `PROVINCE_ID`, `PROVINCE_CODE`,`PROVINCE_NAME` FROM `const_province`";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getCondition($ezf_id, $ezf_field_name) {
        $sql = "SELECT *
		FROM ezform_condition
		WHERE ezform_condition.ezf_id = :ezf_id AND ezform_condition.ezf_field_name = :ezf_field_name
		ORDER BY ezform_condition.cond_id;";

        return Yii::$app->db->createCommand($sql, [':ezf_id' => $ezf_id, ':ezf_field_name' => $ezf_field_name])->queryAll();
    }

    public static function getConditionFields($cond) {
        $sql = "SELECT *
		FROM ezform_fields
		WHERE ezform_fields.ezf_field_id in($cond) ";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getConditionFieldsName($field, $cond) {
        $sql = "SELECT $field
		FROM ezform_fields
		WHERE ezform_fields.ezf_field_id in($cond) ";

        if ($cond != '') {
            return Yii::$app->db->createCommand($sql)->queryAll();
        }
        return '';
    }

    public static function getDynamicFormById($table, $id) {
        $sql = "SELECT *
		FROM $table
		WHERE id = :id ";

        return Yii::$app->db->createCommand($sql, [':id' => $id])->queryOne();
    }

    public static function AlterAddField($table, $field_name, $field_type, $field_option = '') {

        $sql = "ALTER TABLE `$table` ADD COLUMN `$field_name` $field_type $field_option";

        return Yii::$app->db->createCommand($sql)->execute();
    }

    public static function AlterChangeField($table, $field_name, $change_field_name, $field_type, $field_option = '') {

        $sql = "ALTER TABLE `$table` CHANGE COLUMN `$field_name` `$change_field_name` $field_type $field_option";

        return Yii::$app->db->createCommand($sql)->execute();
    }

    public static function AlterDropField($table, $field_name) {
        return;
        //ไม่ drop ข้อมูล ***** ห้ามเลยนะ ****
        $sql = "ALTER TABLE `$table` DROP `$field_name`";

        return Yii::$app->db->createCommand($sql)->execute();
    }

    public static function getColumnName($table, $field_name) {
        $sql = "SELECT COLUMN_NAME
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE TABLE_NAME = :table AND COLUMN_NAME = :field_name AND table_schema = :database";
        return Yii::$app->db->createCommand($sql, [':table' => $table, ':field_name' => $field_name, ':database' => explode('=', getenv('DB_DSN'))['3']])->queryScalar();
    }

    public static function getFieldName($field_id, $value) {
        $model = EzFormFields::find()
                ->where(['ezf_field_id' => $field_id])
                ->andWhere(['ezf_field_name' => $value])
                ->exists();
        return $model;
    }

    public static function getFieldNameByID($ezf_field_id) {
        $model = EzformFields::find()
                ->select('ezf_id, ezf_field_name')
                ->where(['ezf_field_id' => $ezf_field_id])
                ->one();
        return $model;
    }

    public static function getFieldNameInTable($ezf_id, $value) {
        $model = EzformFields::find()
                ->where(['ezf_id' => $ezf_id])
                ->andWhere(['ezf_field_name' => $value])
                ->one();
        return $model;
    }

    public static function getIDkeyFromtable($table, $id) {
        try {
            $ezform = Yii::$app->db->createCommand("SELECT sitecode, ptcode, hsitecode, hptcode, ptid, target, xsourcex FROM `" . $table . "` WHERE id = '$id';")->queryOne();
        } catch (\yii\base\Exception $ex) {
            $ezform = Yii::$app->db->createCommand("SELECT target, xsourcex FROM `" . $table . "` WHERE id = '$id';")->queryOne();
        }
        return $ezform;
    }

    public static function getTargetFromtable($table, $id, $xsourcex) {
        try {
            $ezform = Yii::$app->db->createCommand("SELECT id, sitecode, ptcode, ptid, target, xsourcex FROM `" . $table . "` WHERE ptid = '$id' AND xsourcex = '$xsourcex';")->queryOne();
        } catch (\yii\base\Exception $ex) {
            $ezform = Yii::$app->db->createCommand("SELECT id, target, xsourcex FROM `" . $table . "` WHERE ptid = '$id' AND xsourcex = '$xsourcex';")->queryOne();
        }
        return $ezform;
    }

    public static function getPIDFromtable($table, $id) {
        $ezform = Yii::$app->db->createCommand("SELECT id, ptid FROM $table WHERE id = '$id';")->queryOne();
        return $ezform;
    }

    public static function getTablenameFromComponent($comp_id) {
        $ezform = EzformComponent::find()
                ->select('ezf_id')
                ->where(['comp_id' => $comp_id])
                ->one();
        $ezform = Ezform::find()
                ->select('ezf_table')
                ->where(['ezf_id' => $ezform->ezf_id])
                ->one();
        return $ezform;
    }

    public static function checkIsTableComponent($ezf_id) {
        $ezform = Yii::$app->db->createCommand("SELECT comp_id_target FROM ezform WHERE ezf_id = :ezf_id;", [':ezf_id' => $ezf_id])->queryOne();
        $ezform = Yii::$app->db->createCommand("SELECT * FROM ezform_component WHERE comp_id = :comp_id;", [':comp_id' => $ezform['comp_id_target']])->queryOne();
        return $ezform;
    }

    public static function getCheckData($table, $col) {
        $sql = "SELECT count(*) AS row FROM $table WHERE $col <> '' OR $col <> NULL";

        return Yii::$app->db->createCommand($sql)->queryScalar();
    }

    public static function getRstatFromEzfTarget($ezf_id, $dataid) {
        $ezf = self::getFormTableName($ezf_id);
        $sql = "SELECT rstat, xsourcex FROM $ezf->ezf_table WHERE id ='" . $dataid . "';";
        return Yii::$app->db->createCommand($sql)->queryOne();
    }

    public static function saveReferenceFields($ezf_field_ref_field, $ezf_field_ref_table, $target, $ezf_id, $ezf_field_id, $dataid) {
        $ezfField = \backend\modules\ezforms\models\EzformFields::find()->select('ezf_field_name')->where('ezf_field_id = :ezf_field_id', [':ezf_field_id' => $ezf_field_ref_field])->one();
        $field_name_ref = $ezfField->ezf_field_name;
        $ezf_ref_table = self::getFormTableName($ezf_field_ref_table);
        try {
            $modelDynamic = Yii::$app->db->createCommand("SELECT id FROM `" . ($ezf_ref_table->ezf_table) . "` WHERE " . 'target = :target AND xsourcex = :xsourcex AND `' . $field_name_ref . '` <> "" ORDER BY create_date DESC', [':target' => $target, ':xsourcex' => Yii::$app->user->identity->userProfile->sitecode]);
            $modelTable = $modelDynamic->queryOne();
            if (!$modelTable['id']) {
                $modelDynamic = Yii::$app->db->createCommand("SELECT id FROM `" . ($ezf_ref_table->ezf_table) . "` WHERE " . 'ptid = :target AND xsourcex = :xsourcex AND `' . $field_name_ref . '` <> "" ORDER BY create_date DESC', [':target' => $target, ':xsourcex' => Yii::$app->user->identity->userProfile->sitecode]);
                $modelTable = $modelDynamic->queryOne();
            }
            if ($modelTable['id']) {
                $source_dataid = $modelTable['id'];
                $sql = "INSERT INTO `ezform_data_relation` (`source_ezf_id`, `source_ezf_field_id`, `source_ezf_data_id`, `target_ezf_id`, `target_ezf_field_id`, `target_ezf_data_id`) ";
                $sql .= "VALUES ('" . $ezf_field_ref_table . "', '" . $ezf_field_ref_field . "', '" . $source_dataid . "', '" . $ezf_id . "', '" . $ezf_field_id . "', '" . $dataid . "');";
                Yii::$app->db->createCommand($sql)->execute();
            }
        } catch (Exception $e) {
            $ezfField = \backend\modules\ezforms\models\EzformFields::find()->select('ezf_field_name, ezf_field_label')->where('ezf_field_id = :ezf_field_id', [':ezf_field_id' => $ezf_field_id])->one();
            echo '<h1>Error</h1><hr>';
            echo '<h3>เนื่องการเชื่อมโยงของประเภทคำถาม Reference field ผิดพลาด การแก้ไขคือ ลบคำถามออกแล้วสร้างใหม่</h3>';
            echo '<h4>คำถามที่ผิดพลาดคือ : </h4>' . $ezfField->ezf_field_name . ' (' . $ezfField->ezf_field_label . ')';
            Yii::$app->end();
        }
    }

    public static function saveReference43Fields($ezf_field_ref_field, $ezf_field_ref_table, $target, $ezf_id, $ezf_field_id, $dataid) {
        $ezfField = \backend\modules\ezforms\models\EzformFields::find()->select('ezf_field_name')->where('ezf_field_id = :ezf_field_id', [':ezf_field_id' => $ezf_field_ref_field])->one();
        $field_name_ref = $ezfField->ezf_field_name;
        $ezf_ref_table = self::getFormTableName($ezf_field_ref_table);
        try {
            $modelDynamic = Yii::$app->db->createCommand("SELECT id FROM `" . ($ezf_ref_table->ezf_table) . "` WHERE " . 'target = :target AND xsourcex = :xsourcex AND `' . $field_name_ref . '` <> "" ORDER BY create_date DESC', [':target' => $target, ':xsourcex' => Yii::$app->user->identity->userProfile->sitecode]);
            $modelTable = $modelDynamic->queryOne();
            if (!$modelTable['id']) {
                $modelDynamic = Yii::$app->db->createCommand("SELECT id FROM `" . ($ezf_ref_table->ezf_table) . "` WHERE " . 'ptid = :target AND xsourcex = :xsourcex AND `' . $field_name_ref . '` <> "" ORDER BY create_date DESC', [':target' => $target, ':xsourcex' => Yii::$app->user->identity->userProfile->sitecode]);
                $modelTable = $modelDynamic->queryOne();
            }
            if ($modelTable['id']) {
                $source_dataid = $modelTable['id'];
                $sql = "INSERT INTO `ezform_data_relation` (`source_ezf_id`, `source_ezf_field_id`, `source_ezf_data_id`, `target_ezf_id`, `target_ezf_field_id`, `target_ezf_data_id`) ";
                $sql .= "VALUES ('" . $ezf_field_ref_table . "', '" . $ezf_field_ref_field . "', '" . $source_dataid . "', '" . $ezf_id . "', '" . $ezf_field_id . "', '" . $dataid . "');";
                Yii::$app->db->createCommand($sql)->execute();
            }
        } catch (Exception $e) {
            $ezfField = \backend\modules\ezforms\models\EzformFields::find()->select('ezf_field_name, ezf_field_label')->where('ezf_field_id = :ezf_field_id', [':ezf_field_id' => $ezf_field_id])->one();
            echo '<h1>Error</h1><hr>';
            echo '<h3>เนื่องการเชื่อมโยงของประเภทคำถาม Reference field ผิดพลาด การแก้ไขคือ ลบคำถามออกแล้วสร้างใหม่</h3>';
            echo '<h4>คำถามที่ผิดพลาดคือ : </h4>' . $ezfField->ezf_field_name . ' (' . $ezfField->ezf_field_label . ')';
            Yii::$app->end();
        }
    }

    public static function getReferenceData($target_ezf_id, $target_ezf_field_id, $target_ezf_data_id, $fieldName) {
        $res = Yii::$app->db->createCommand("SELECT `source_ezf_id`, `source_ezf_field_id`, `source_ezf_data_id` FROM `ezform_data_relation` WHERE target_ezf_id = :target_ezf_id AND target_ezf_field_id = :target_ezf_field_id AND target_ezf_data_id = :target_ezf_data_id", [':target_ezf_id' => $target_ezf_id, ':target_ezf_field_id' => $target_ezf_field_id, ':target_ezf_data_id' => $target_ezf_data_id]);
        if ($res->query()->count()) {
            $modelTable = $res->queryOne();
            $modelDynamic = self::getFormTableName($modelTable['source_ezf_id']);
            $res = Yii::$app->db->createCommand("SELECT " . $fieldName . " FROM " . $modelDynamic->ezf_table . " WHERE id = :id", [':id' => $modelTable['source_ezf_data_id']]);
            return $res->queryOne();
        }
    }

    public static function getUserProfile($id) {
        return \common\models\UserProfile::find()->where('user_id = :id', [':id' => $id])->one();
    }

    public static function getEmailAdmin() {
        $sql = "SELECT
				GROUP_CONCAT(`user`.email) as name
			FROM `user` INNER JOIN rbac_auth_assignment ON rbac_auth_assignment.user_id = `user`.id
			WHERE rbac_auth_assignment.item_name = 'administrator' limit 1";

        return Yii::$app->db->createCommand($sql)->queryScalar();
    }

    public static function getTargetFormEzf($ezf_id, $dataid) {
        //$ezfField = \backend\modules\ezforms\models\EzformFields::find()->select('ezf_field_name')->where('ezf_field_id = :ezf_field_id', [':ezf_field_id' => $ezf_field_ref_field])->one();
        //$field_name_ref =$ezfField->ezf_field_name;
        $comp = self::checkIsTableComponent($ezf_id);
        if ($comp['special'] == 1) {
            $field = 'ptid';
        } else {
            $field = 'target';
        }
        $modelDynamic = self::getFormTableName($ezf_id);
        $sql = "SELECT " . $field . " FROM " . ($modelDynamic->ezf_table) . " WHERE " . " id = '" . $dataid . "' ORDER BY create_date DESC";
        $modelTable = Yii::$app->db->createCommand($sql)->queryOne();

        return $modelTable;
    }

    public static function getHospital($sitecode) {
        $sql = "SELECT all_hospital_thai.hcode,
		all_hospital_thai.`name`,
		all_hospital_thai.tambon,
		all_hospital_thai.amphur,
		all_hospital_thai.province
	FROM all_hospital_thai
	WHERE all_hospital_thai.hcode = :hcode ";

        return Yii::$app->db->createCommand($sql, [':hcode' => $sitecode])->queryOne();
    }

    public static function getIcd9($code) {
        $sql = "SELECT `code`,`name` WHERE icd9 = :code ";

        return Yii::$app->db->createCommand($sql, [':code' => $code])->queryOne();
    }

    public static function getIcd10($code) {
        $sql = "SELECT `code`,`name` WHERE icd10 = :code ";

        return Yii::$app->db->createCommand($sql, [':code' => $code])->queryOne();
    }

    public static function getIntUser($uid) {
        $sql = "SELECT user_id,
			CONCAT(firstname, ' ', lastname) AS fullname
		FROM user_profile
		WHERE user_id IN ($uid)";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getIntUserAll() {
        $sql = "SELECT user_id as id,
			CONCAT(firstname, ' ', lastname) AS text
		FROM user_profile
		";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getSubmitState($ezf_id, $id) {
        $state = true;
        $sqlTable = "SELECT ezf_table,comp_id_target ,unique_record FROM ezform WHERE ezf_id=:ezf_id";
        $resTable = \Yii::$app->db->createCommand($sqlTable, [':ezf_id' => $ezf_id])->queryOne();
        $xsourcex = Yii::$app->user->identity->userProfile->sitecode;

        if ($resTable['unique_record'] == 3) {
            // get ezform target
            $sqlComp = "SELECT comp_id, ezf_id FROM ezform_component WHERE comp_id=:comp_id ";
            $resComp = \Yii::$app->db->createCommand($sqlComp, [':comp_id' => $resTable['comp_id_target']])->queryOne();

            // get table target
            $sqlTarget = " SELECT  ezf_table FROM ezform WHERE ezf_id=:ezf_id ";
            $resTarget = \Yii::$app->db->createCommand($sqlTarget, [':ezf_id' => $resComp['ezf_id']])->queryOne();

            // get ptid new submit request
            $sql = " SELECT ptid, ptcodefull,f1vdcomp FROM " . $resTable['ezf_table'] . " WHERE id=:id";
            $result = \Yii::$app->db->createCommand($sql, [':id' => $id])->queryOne();

            // check data submit
            $sqlChk = " SELECT id, ptid, ptcodefull,hsitecode, f1vdcomp FROM " . $resTable['ezf_table'] . " WHERE rstat=2 AND ptid=:ptid";
            $resChk = \Yii::$app->db->createCommand($sqlChk, [':ptid' => $result['ptid']])->queryOne();

            // get sys_dateoficf old data submit
            $sqlChkicf = " SELECT id,sys_dateoficf FROM " . $resTarget['ezf_table'] . " WHERE  ptid=:ptid AND hsitecode=:hsitecode";
            $resChkicf = \Yii::$app->db->createCommand($sqlChkicf, [':ptid' => $resChk['ptid'], ':hsitecode' => $resChk['hsitecode']])->queryOne();

            // get sys_dateoficf new data submit
            $sqlChkicfNew = " SELECT id,sys_dateoficf FROM " . $resTarget['ezf_table'] . " WHERE  ptcodefull=:ptcodefull AND xsourcex=:xsourcex ";
            $resChkicfNew = \Yii::$app->db->createCommand($sqlChkicfNew, [':ptcodefull' => $result['ptcodefull'], ':xsourcex' => $xsourcex])->queryOne();

            $date = strtotime($resChk['f1vdcomp']);
            $y = date('Y') - date('Y', $date);
            $m = date('m') - date('m', $date);

            if ($resChk) {
                $state = false;
                if ((($m >= 6 || $m <= -6) || $y > 0) && !($y < 0)) { // เชคว่าถ้ามีการ submit ค้างในระบบนานเกิน 6 เดือน
                    $icfDateNew = \backend\classes\DateForQuery::FormatDateThaiToDateMysql($resChkicfNew['sys_dateoficf']);
                    //$icfDateNew = $resChkicfNew['sys_dateoficf'];

                    if (( $result['f1vdcomp'] >= $icfDateNew) && ($resChkicfNew['sys_dateoficf'] != '')) { // data submit request new ต้องวันที่ icf <= วันที่ลงข้อมูล cca
                        $sqlUpdate = " UPDATE " . $resTable['ezf_table'] . " SET rstat=1 WHERE id=:id";
                        $resUpdate = \Yii::$app->db->createCommand($sqlUpdate, [':id' => $resChk['id']])->execute();
                        //\appxq\sdii\utils\VarDumper::dump($icfDateNew);
                        $state = true;
                    }
                } else if (( $resChk['f1vdcomp'] < $resChkicf['sys_dateoficf']) && ($resChkicf['sys_dateoficf'] == '')) { // data submit old วันที่ icf มากกว่า วันที่ ลงข้อมูล cca
                    if (( $result['f1vdcomp'] >= $resChkicfNew['sys_dateoficf']) && ($resChkicfNew['sys_dateoficf'] != '')) { // data submit request new ต้องวันที่ icf <= วันที่ลงข้อมูล cca
                        $sqlUpdate = " UPDATE " . $resTable['ezf_table'] . " SET rstat=1 WHERE id=:id";
                        $resUpdate = \Yii::$app->db->createCommand($sqlUpdate, [':id' => $resChk['id']])->execute();

                        $state = true;
                    }
                }
            }
        }
        return $state;
    }

}
