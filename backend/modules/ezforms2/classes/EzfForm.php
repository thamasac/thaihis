<?php

namespace backend\modules\ezforms2\classes;

use Yii;
use yii\helpers\ArrayHelper;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\models\EzformCoDev;
use backend\modules\ezforms2\models\EzformAssign;
use backend\modules\ezforms2\models\Ezform;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\models\EzformChoice;
use backend\modules\ezforms2\models\EzformCondition;
use backend\modules\ezforms2\models\EzformTarget;
use backend\modules\ezforms2\models\EzformVersion;
use backend\modules\ezforms2\models\EzformRole;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class EzfForm {

    public static function saveEzfForm($model) {
        $isNewRecord = $model->isNewRecord;

        if ($isNewRecord) {
            $action = 'create';
            $model->ezf_table = 'zdata_' . $model->ezf_id; //gen Zdata table name
            if (EzfQuery::findTable($model->ezf_table)) {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Table `{table}` has already been taken.', ['table'=>$model->ezf_table]),
                    'data' => $model,
                ];
                return $result;
            }
        } else {
            $action = 'update';
        }

        
        //array2String to field database
        $model->co_dev = SDUtility::array2String($model->co_dev);
        $model->consult_users = SDUtility::array2String($model->consult_users);
        $model->assign = SDUtility::array2String($model->assign);
        $model->ezf_role = SDUtility::array2String($model->ezf_role);
        $model->field_detail = SDUtility::array2String($model->field_detail);
        $model->ezf_options = SDUtility::array2String($model->ezf_options);
        $model->ezf_sql = SDUtility::array2String($model->ezf_sql);
        
        if ($model->save()) {
            EzfForm::saveEzfCoDev(SDUtility::string2Array($model->co_dev), $model->ezf_id);
            EzfForm::saveEzfAssign(SDUtility::string2Array($model->assign), $model->ezf_id);
            EzfForm::saveEzfRole(SDUtility::string2Array($model->ezf_role), $model->ezf_id);
            if ($isNewRecord) {
                EzfForm::initEzfField($model->ezf_id, ['id']);
                EzfForm::initEzfVersion($model->ezf_id, $model);
                
                $modelFav = new \backend\modules\ezforms2\models\EzformFavorite();
                $modelFav->ezf_id = $model->ezf_id;
                $modelFav->userid = Yii::$app->user->id;
                $modelFav->forder = EzfQuery::getOrderFav(Yii::$app->user->id);
                $modelFav->save();
                
                if (!EzfForm::createZdata($model->ezf_table)) {
                    self::deleteRecord($model->ezf_id);
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not create table.'),
                        'data' => $model,
                    ];
                    return $result;
                }
            } else {
                
                $modelVersion = EzfQuery::getEzformConfig($model->ezf_id, $model->ezf_version);
                
                $modelVersion->field_detail = $model->field_detail;
                $modelVersion->ezf_sql = $model->ezf_sql;
                $modelVersion->ezf_js = $model->ezf_js;
                $modelVersion->ezf_error = $model->ezf_error;
                $modelVersion->ezf_options = $model->ezf_options;
                $modelVersion->save();
            }

            $result = [
                'status' => 'success',
                'action' => $action,
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                'data' => $model,
            ];
            return $result;
        } else {
            if (!$isNewRecord) {
                self::rollbackRecord($model);
            }

            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                'data' => $model,
            ];
            return $result;
        }
    }

    public static function deleteEzfForm($model) {
        if (empty($model->ezf_id)) {
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('ezform', 'The requested field was not found. {value}', ['value'=>'<code>[ezf_id]</code>']),
            ];
            return $result;
        }

        try {
            self::deleteRecord($model->ezf_id);
            if (!EzfForm::dropZdata($model->ezf_table)) {
                self::rollbackRecord($model, 'delete');

                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Delete the data Error.'),
                    'data' => $model,
                ];
                return $result;
            }

            $result = [
                'status' => 'success',
                'action' => 'delete',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Delete completed.'),
                'data' => $model,
            ];
            return $result;
        } catch (\yii\db\Exception $e) {
            self::rollbackRecord($model, 'delete');
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
                'data' => $model,
            ];
            return $result;
        }
    }

    public static function trashEzfForm($model) {
        if ($model->status == '1') {
            $model->status = '0';
        } elseif ($model->status == '0') {
            $model->status = '1';
        }
        if ($model->save()) {
            $result = [
                'status' => 'success',
                'action' => 'create',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Delete completed.'),
                'data' => $model,
            ];
        } else {
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
                'data' => $model,
            ];
        }
        return $result;
    }

    public static function saveEzfCoDev($arrCoDev, $ezf_id) {
        try {
            $coIn = "user_co NOT IN('" . implode("','", $arrCoDev) . "')";
            EzformCoDev::deleteAll("ezf_id = '$ezf_id' AND $coIn");
            foreach ($arrCoDev as $user_id) {
                EzfQuery::insertEzformCoDev($ezf_id, $user_id);
            }
        } catch (\yii\db\Exception $e) {
            EzformCoDev::deleteAll(['ezf_id' => $ezf_id]);
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return FALSE;
        }
    }

    public static function saveEzfAssign($arrAssign, $ezf_id) {
        try {
            $assIn = "user_id NOT IN('" . implode("','", $arrAssign) . "')";
            EzformAssign::deleteAll("ezf_id = '$ezf_id' AND $assIn");
            foreach ($arrAssign as $user_id) {
                EzfQuery::insertEzformAssign($ezf_id, $user_id);
            }
        } catch (\yii\db\Exception $e) {
            EzformAssign::deleteAll(['ezf_id' => $ezf_id]);
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return FALSE;
        }
    }
    
    public static function saveEzfRole($arrRole, $ezf_id) {
        try {
            $roleIn = "role NOT IN('" . implode("','", $arrRole) . "')";
            EzformRole::deleteAll("ezf_id = '$ezf_id' AND $roleIn");
            foreach ($arrRole as $role_name) {
                EzfQuery::insertEzformRole($ezf_id, $role_name);
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            EzformRole::deleteAll(['ezf_id' => $ezf_id]);
            return FALSE;
        }
    }

    /**
     * Function initEzfField.
     * @param string $id ezf_id เช่น '1502169443023044700'
     * @param array $arrField ส่งค่า ezf_id และชื่อที่ต้องการบันทึกเช่น ['id','item_id','var_comment']
     */
    public static function initEzfField($id, $arrField) {
        if (isset($id) && isset($arrField) && is_array($arrField)) {
            foreach ($arrField as $value) {
                $model = new EzformFields();
                $model->ezf_field_id = SDUtility::getMillisecTime();
                $model->ezf_id = $id;
                $model->ezf_field_name = $value;
                $model->ezf_field_label = 'Data'.$model->getAttributeLabel($value);
                $model->ezf_field_type = '0';
                $model->table_field_type = 'BIGINT';
                $model->table_field_length = '20';
                $model->table_index = 1;
                $model->ezf_version = 'all';
                $model->save();
            }
        } else {
            return FALSE;
        }
    }
    
    public static function initEzfVersion($ezf_id, $modelEzf) {
        $model = new EzformVersion();
        $model->ver_code = $modelEzf->ezf_version;
        $model->ver_active = 1;
        $model->ver_approved = 0;
        $model->ezf_id = $modelEzf->ezf_id;
        $model->ezf_sql = $modelEzf->ezf_sql;
        $model->ezf_js = $modelEzf->ezf_js;
        $model->ezf_error = $modelEzf->ezf_error;
        $model->ezf_options = $modelEzf->ezf_options;
        $model->field_detail = $modelEzf->field_detail;

        if($model->save()){
            return true;
        }
        return false;
            
    }

    public static function createZdata($id) {
        try {
            EzfQuery::copyTable($id, 'zdata_init');
            return TRUE;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return FALSE;
        }
    }

    public static function dropZdata($tableName) {
        try {
            EzfQuery::dropTable($tableName);
            return TRUE;
        } catch (\yii\db\Exception $e) {
            return FALSE;
        }
    }

    private static function deleteRecord($id) {
        Ezform::deleteAll(['ezf_id' => $id]);
        EzformCoDev::deleteAll(['ezf_id' => $id]);
        EzformAssign::deleteAll(['ezf_id' => $id]);
        EzformRole::deleteAll(['ezf_id' => $id]);
        EzformFields::deleteAll(['ezf_id' => $id]);
        EzformChoice::deleteAll(['ezf_id' => $id]);
        EzformCondition::deleteAll(['ezf_id' => $id]);
        EzformTarget::deleteAll(['ezf_id' => $id]);
        \backend\modules\ezforms2\models\EzformAutonum::deleteAll(['ezf_id' => $id]);
    }

    private static function rollbackRecord($model, $action = null) {
        $model->attributes = $model->oldAttributes;
        EzfForm::saveEzfCoDev(SDUtility::string2Array($model->co_dev), $model->ezf_id);
        EzfForm::saveEzfAssign(SDUtility::string2Array($model->assign), $model->ezf_id);
        EzfForm::saveEzfRole(SDUtility::string2Array($model->ezf_role), $model->ezf_id);
        if ($action == 'delete') {
            $modelEzfom = new Ezform;
            $modelEzfom->attributes = $model->attributes;
            $modelEzfom->save();

            EzfForm::createZdata($model->ezf_id);
            $modelFields = EzformFields::findOne(['ezf_id' => $model->ezf_id, 'ezf_field_name' => 'id']);
            if (!$modelFields) {
                EzfForm::initEzfField($model->ezf_id, ['id']);
            }
        } else {
            $model->save();
        }
    }

    /**
     * Function checkEzfFormRight.
     * @param string $id ezf_id เช่น '1502169443023044700'
     * @param string $user_id 
     * @param string $action 'view','update','delete' ส่งมาเพียง action เดียว
     */
    public static function checkEzfFormRight($id, $user_id, $action) {
        $result = FALSE;
        if (empty($id) || empty($user_id) || empty($action)) {
            return $result;
        }
        $right = EzfQuery::getRightEzform($id, $user_id);

        if ($action == 'view') {
            if ($right['shared'] == '1') {
                $result = TRUE;
            } elseif ($right['shared'] == '0' && !empty($right['ezform'])) {
                $result = TRUE;
            } elseif (!empty($right['codev']) || !empty($right['assign'])) {
                $result = TRUE;
            } elseif ($right['shared'] == '3') {
                //check in site
            }
        } elseif ($action == 'update') {
            if (!empty($right['ezform']) || !empty($right['codev'])) {
                $result = TRUE;
            }
        } elseif ($action == 'delete') {
            if (!empty($right['ezform'])) {
                $result = TRUE;
            }
        }
        if ($result === FALSE) {
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgError() . Yii::t('ezform', 'You do not have the right to {action} form.', ['action'=> $action]),
                'options' => ['class' => 'alert-danger']
            ]);
            Yii::$app->controller->redirect(\yii\helpers\Url::to('/ezforms2/ezform'));
        }
    }
    
    public static function getDepartment($q = null, $sht = null) {
        return ArrayHelper::map(EzfQuery::getDepartmentByName($q, $sht), 'sect_code', 'sect_name');
    }

    public static function getRoleIn() {
        $roleIn = "in('')";
        $user_role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        if(isset($user_role) && !empty($user_role)){
            $user_role = array_values(\yii\helpers\ArrayHelper::getColumn($user_role, 'name'));
            $role_str = \backend\modules\ezforms2\classes\EzfFunc::array2valueStr($user_role, "'");
            $roleIn = 'in('.implode(',', $role_str).')';
            
        }
        
        return $roleIn;
    }
}

?>