<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfForm;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\models\TbdataAll;
use backend\modules\ezforms2\models\EzformTarget;
use kartik\mpdf\Pdf;
use yii\helpers\Url;

/**
 * Description of EzformDataController
 *
 * @author appxq
 */
class EzformDataController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex($ezf_id) {
        $token = isset($_GET['token']) ? $_GET['token'] : '';
        $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
        $target = isset($_GET['target']) ? $_GET['target'] : '';
        $initdata = isset($_GET['initdata']) ? EzfFunc::stringDecode2Array($_GET['initdata']) : [];
        $disable = [];

        \backend\modules\core\classes\CoreFunc::setTokenOption();
        
        $modelEzf = EzfQuery::getEzformOne($ezf_id);
        if(!$modelEzf){
                $modelEzf = new \backend\modules\ezforms2\models\Ezform();
                $modelEzf->ezf_name = 'Unnamed Form';
                return $this->renderAjax('error', [
                            'ezf_id' => $ezf_id,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                    ]);
            }
        $version = (isset($_GET['v']) && $_GET['v']!='')?$_GET['v']:$modelEzf->ezf_version;
        
        //fix version by dataid
        if ($dataid != '') {
            $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $dataid);
            if ($modelZdata) {
                if($modelZdata->rstat!=0 && !empty($modelZdata->ezf_version)){
                        $version = $modelZdata->ezf_version;
                    }
                    if(!empty($modelZdata->ezf_version)){
                        $modelEzf->ezf_version = $modelZdata->ezf_version;
                    }
            } else {
                return $this->renderAjax('error', [
                        'ezf_id' => $ezf_id,
                        'dataid' => $dataid,
                        'modelEzf' => $modelEzf,
                        'msg' => Yii::t('app', 'No results found.'),
                ]);
            }
        }
        if($modelEzf->enable_version){
            $modelVersion = EzfQuery::getEzformConfigApprov($modelEzf->ezf_id, $version);
        } else {
            $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $version);
        }
        if($modelVersion){
            $modelEzf->field_detail = $modelVersion->field_detail;
            $modelEzf->ezf_sql = $modelVersion->ezf_sql;
            $modelEzf->ezf_js = $modelVersion->ezf_js;
            $modelEzf->ezf_error = $modelVersion->ezf_error;
            $modelEzf->ezf_options = $modelVersion->ezf_options;
        } else {
            return $this->renderAjax('error', [
                        'ezf_id' => $ezf_id,
                        'dataid' => $dataid,
                        'modelEzf' => $modelEzf,
                        'msg' => Yii::t('app', 'No version found.'),
            ]);
        }

        $modelFields = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);

        $options = SDUtility::string2Array($modelEzf->ezf_options);
        
        if(isset($options['token']) && $options['token']==$token && $token!=''){
            
        } else {
            return $this->renderAjax('token_expired', [
                        'ezf_id' => $ezf_id,
                        'dataid' => $dataid,
                        'modelEzf' => $modelEzf,
                        'token' => $token,
                        //'msg' => Yii::t('ezform', 'The token is invalid or expired.'),
            ]);
        }
        
        Yii::$app->session['show_varname'] = 0;
        Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
        //Yii::$app->session['ezform'] = $modelEzf->attributes;

        $userProfile = (!Yii::$app->user->isGuest) ? Yii::$app->user->identity->profile : \common\modules\user\models\Profile::findOne($modelEzf->created_by);
        
        $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
        $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);

        if (!$model) {// dataid ส่งมาผิดหาไม่เจอ / ไม่คิดรวมถ้าส่ง '' มา
            return $this->renderAjax('error', [
                        'ezf_id' => $ezf_id,
                        'dataid' => $dataid,
                        'modelEzf' => $modelEzf,
                        'msg' => Yii::t('app', 'No results found.'),
            ]);
        } else {
            $model->afterFind();
        }
        
        $options = SDUtility::string2Array($modelEzf->ezf_options);
        $unique = 0;
        if (!isset($model->id) && isset($options['token_unique_record']) && $options['token_unique_record']==1) {// ถ้ามี new record ที่คนนั้นสร้างไว้ ให้ใช้อันนั้น
                $ip = Yii::$app->getRequest()->getUserIP();
                //$modelNewRecord = EzfUiFunc::loadNewRecordByIp($model, $modelEzf->ezf_table, $ip, $token);
                $modelNewRecord = \backend\modules\ezforms2\models\EzformTokenLog::find()
                        ->where('ezf_id=:ezf_id AND token=:token AND ip=:ip', ['ezf_id'=>$ezf_id, 'token'=>$token, 'ip'=>$ip])
                        ->one();
                $unique = 1;
                if ($modelNewRecord) {
                    return $this->renderAjax('error', [
                        'ezf_id' => $ezf_id,
                        'dataid' => $dataid,
                        'modelEzf' => $modelEzf,
                        'msg' => Yii::t('ezform', 'This IP has saved the data.'),
                    ]);
                }
            }

        if (!empty($initdata)) {//กำหนดค่าเริ่มต้น
            $model->attributes = $initdata;
            $initdata = NULL;
        }

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if(!isset($model->rstat)){
                $model->rstat = 1;
            }
            
            $rstat = Yii::$app->request->post('savedata') ? Yii::$app->request->post('savedata') : $model->rstat;

            if (!isset($model->id) || empty($model->id)) {
                $hsitecode = $userProfile->sitecode;
                $hptcode = EzfQuery::getMaxCodeBySitecode($modelEzf->ezf_table, $hsitecode);
                
                $model->id = SDUtility::getMillisecTime();
                $model->target = $model->id;
                $model->ptid = $model->id;
                
                $model->sitecode = $hsitecode;
                $model->ptcode = $hptcode;
                $model->ptcodefull = $hptcode.$hsitecode;
                $model->hptcode = $hptcode;
                $model->hsitecode = $hsitecode;
                
                $model->user_create = $userProfile->user_id;
                $model->create_date = new \yii\db\Expression('NOW()');
                $model->xsourcex = $userProfile->sitecode;
                $model->xdepartmentx = $userProfile->department;
                
                try {
                    $token_log = new \backend\modules\ezforms2\models\EzformTokenLog();
                    $token_log->id = SDUtility::getMillisecTime();
                    $token_log->ezf_id = $ezf_id;
                    $token_log->token = $token;
                    $token_log->dataid = $model->id;
                    $token_log->ip = Yii::$app->getRequest()->getUserIP();
                    $token_log->save();
                } catch (\yii\db\Exception $e) {
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                }
            }
            $model->ezf_version = $version;
            $model->rstat = $rstat;
            $model->user_update = $userProfile->user_id;
            $model->update_date = new \yii\db\Expression('NOW()');

            $result = EzfUiFunc::saveData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $dataid);

            Yii::$app->session->setFlash('alert', [
                'body' => $result['message'],
                'options' => ['class' => 'alert-' . $result['status']]
            ]);
            
            if ($result['status'] == 'success') {
                $initdata = '';
            } else {
                $initdata = EzfFunc::arrayEncode2String($model->attributes);
            }

            return $this->redirect(['commit',
                        'ezf_id' => $ezf_id,
                        'dataid' => $model->id,
                        'initdata' => $initdata,
                        'token' => $token,
                        'unique'=>$unique,
                        //'modelEzf' => EzfFunc::arrayEncode2String($modelEzf),
            ]);
        }

        return $this->renderAjax('index', [
                    'ezf_id' => $ezf_id,
                    'dataid' => $model->id,
                    'modelEzf' => $modelEzf,
                    'modelFields' => $modelFields,
                    'model' => $model,
                    'initdata' => $initdata,
                    'disable' => $disable,
                    'token' => $token,
                    'modelVersion' => $modelVersion,
        ]);
    }
    
    public function actionCommit($ezf_id) {
        $token = isset($_GET['token']) ? $_GET['token'] : '';
        $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
        $target = isset($_GET['target']) ? $_GET['target'] : '';
        $initdata = isset($_GET['initdata']) ? EzfFunc::stringDecode2Array($_GET['initdata']) : [];
        $modelEzf = isset($_GET['modelEzf']) ? EzfFunc::stringDecode2Array($_GET['modelEzf']) : '';
        $unique = isset($_GET['unique']) ? $_GET['unique'] : 0;
        
        return $this->renderAjax('commit', [
                    'ezf_id' => $ezf_id,
                    'dataid' => $dataid,
                    'initdata' => $initdata,
                    'token' => $token,
                    'unique'=>$unique,
                    'modelEzf' => $modelEzf,
        ]);
    }

    public function actionDelete($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';

            $modelEzf = EzfQuery::getEzformOne($ezf_id);
            $version = (isset($_GET['v']) && $_GET['v']!='')?$_GET['v']:$modelEzf->ezf_version;

            //fix version by dataid
            if ($dataid != '') {
                $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $dataid);
                if ($modelZdata) {
                    if($modelZdata->rstat!=0 && !empty($modelZdata->ezf_version)){
                        $version = $modelZdata->ezf_version;
                    }
                    if(!empty($modelZdata->ezf_version)){
                        $modelEzf->ezf_version = $modelZdata->ezf_version;
                    }
                } else {
                    return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                    ]);
                }
            }
            
            if($modelEzf->enable_version){
                $modelVersion = EzfQuery::getEzformConfigApprov($modelEzf->ezf_id, $version);
            } else {
                $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $version);
            }
            if($modelVersion){
                $modelEzf->field_detail = $modelVersion->field_detail;
                $modelEzf->ezf_sql = $modelVersion->ezf_sql;
                $modelEzf->ezf_js = $modelVersion->ezf_js;
                $modelEzf->ezf_error = $modelVersion->ezf_error;
                $modelEzf->ezf_options = $modelVersion->ezf_options;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'No version found.'),
                    'data' => $dataid,
                ];
                return $result;
            }
        
            $modelFields = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);

            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();

            $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], 0);

            $result = EzfUiFunc::deleteDataRstat($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $dataid, $reloadDiv);
            
            $variable_core = [
                'ezf_id' => "{$ezf_id}",
                'dataid' => "{$model->id}",
                'target' => "{$model->target}",
                'modal' => '',
                'reloadDiv' => $reloadDiv,
            ];

            return ArrayHelper::merge($variable_core, $result);
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionDeleteData($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';

            $modelEzf = EzfQuery::getEzformOne($ezf_id);
            $version = (isset($_GET['v']) && $_GET['v']!='')?$_GET['v']:$modelEzf->ezf_version;

            //fix version by dataid
            if ($dataid != '') {
                $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $dataid);
                if ($modelZdata) {
                    if($modelZdata->rstat!=0 && !empty($modelZdata->ezf_version)){
                        $version = $modelZdata->ezf_version;
                    }
                    if(!empty($modelZdata->ezf_version)){
                        $modelEzf->ezf_version = $modelZdata->ezf_version;
                    }
                } else {
                    return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                    ]);
                }
            }
            
            if($modelEzf->enable_version){
                $modelVersion = EzfQuery::getEzformConfigApprov($modelEzf->ezf_id, $version);
            } else {
                $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $version);
            }
            if($modelVersion){
                $modelEzf->field_detail = $modelVersion->field_detail;
                $modelEzf->ezf_sql = $modelVersion->ezf_sql;
                $modelEzf->ezf_js = $modelVersion->ezf_js;
                $modelEzf->ezf_error = $modelVersion->ezf_error;
                $modelEzf->ezf_options = $modelVersion->ezf_options;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'No version found.'),
                    'data' => $dataid,
                ];
                return $result;
            }
        
            $modelFields = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);

            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();

            $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], 0);

            $result = EzfUiFunc::deleteData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $dataid, $reloadDiv);
            $variable_core = [
                'ezf_id' => "{$ezf_id}",
                'dataid' => "{$model->id}",
                'target' => "{$model->target}",
                'modal' => '',
                'reloadDiv' => $reloadDiv,
            ];

            return ArrayHelper::merge($variable_core, $result);
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionEzform($ezf_id) {
        
        if (Yii::$app->getRequest()->isAjax) {
            
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $reloadPage = isset($_GET['reloadPage']) ? base64_decode($_GET['reloadPage']) : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
            $initdata = isset($_GET['initdata']) ? EzfFunc::stringDecode2Array($_GET['initdata']) : [];
            $db2 = isset($_GET['db2']) ? $_GET['db2'] : 0;
            $ezf_box = isset($_GET['ezf_box']) ? $_GET['ezf_box'] : 0;
            
            $disable = [];
            
            $modelEzf = EzfQuery::getEzformOne($ezf_id);
            if(!$modelEzf){
                $modelEzf = new \backend\modules\ezforms2\models\Ezform();
                $modelEzf->ezf_name = 'Unnamed Form';
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                    ]);
            }
            \backend\modules\manageproject\classes\CNFunc::addLog("New Form ezf_id={$ezf_id}".SDUtility::array2String($modelEzf));
            $ezf_options = SDUtility::string2Array($modelEzf->ezf_options);
            if(isset($ezf_options['lock_data']) && $ezf_options['lock_data']==1){
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'This form and its database hase been locked. Please contact the Project Owner as needed.'),
                    ]);
            }
            
            
            
            if($db2==1){
                $checkDb2 = EzfFunc::updateDoubleData($modelEzf, $dataid);
                if($checkDb2){
                    $modelEzf->ezf_table = $checkDb2;
                } else {
                    return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'Can not update `double data`.'),
                    ]);
                }
            }

            $version = (isset($_GET['v']) && $_GET['v']!='')?$_GET['v']:$modelEzf->ezf_version;
            //fix version by dataid
            if ($dataid != '') {
                $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $dataid);
                if ($modelZdata) {
                    if($modelZdata->rstat!=0 && !empty($modelZdata->ezf_version)){
                        $version = (isset($_GET['v']) && $_GET['v']!='' && in_array($modelZdata->rstat, [0,1]))?$_GET['v']:$modelZdata->ezf_version;
                    }
                    if($db2==1 && !empty($modelZdata->ezf_version)){
                        $version = (isset($_GET['v']) && $_GET['v']!='' && in_array($modelZdata->rstat, [0,1]))?$_GET['v']:$modelZdata->ezf_version;
                    }
                    if(!empty($modelZdata->ezf_version)){
                        $modelEzf->ezf_version = (isset($_GET['v']) && $_GET['v']!='' && in_array($modelZdata->rstat, [0,1]))?$_GET['v']:$modelZdata->ezf_version;
                    }
                } else {
                    return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                    ]);
                }
            }
            
            if($modelEzf->enable_version){
                $modelVersion = EzfQuery::getEzformConfigApprov($modelEzf->ezf_id, $version);
            } else {
                $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $version);
                
            }
            
            if($modelVersion){
                $modelEzf->field_detail = $modelVersion->field_detail;
                $modelEzf->ezf_sql = $modelVersion->ezf_sql;
                $modelEzf->ezf_js = $modelVersion->ezf_js;
                $modelEzf->ezf_error = $modelVersion->ezf_error;
                $modelEzf->ezf_options = $modelVersion->ezf_options;
            } else {
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No version found.'),
                ]);
            }
            
            $modelFields = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);
            
            Yii::$app->session['show_varname'] = 0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            //Yii::$app->session['ezform'] = $modelEzf->attributes;

            $userProfile = Yii::$app->user->identity->profile;
            
            $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
            
            //Unique Record
            $modelLastRecord;
            if($target!='' && $dataid=='' && isset($modelEzf->unique_record) && $modelEzf->unique_record==2){
                $modelLastRecord = EzfUiFunc::loadLastRecord($model, $modelEzf->ezf_table, $target);
                if($modelLastRecord){
                    return $this->renderAjax('_error', [
                                'ezf_id' => $ezf_id,
                                'dataid' => $dataid,
                                'modelEzf' => $modelEzf,
                                'msg' => Yii::t('ezform', 'Add only 1 Record'),
                    ]);
                }
            } elseif ($target!='' && $dataid=='' && isset ($modelEzf->unique_record) && $modelEzf->unique_record==4) {
                $options = SDUtility::string2Array($modelEzf->ezf_options);
                $create_date_field = isset($options['create_date_field']) && !empty($options['create_date_field'])?$options['create_date_field']:'create_date';
                $unit_field = isset($options['unit_field']) && !empty($options['unit_field'])?$options['unit_field']:'';
                $enable_field = isset($options['enable_field']) && !empty($options['enable_field'])?$options['enable_field']:'';
                $modelLastRecord = EzfUiFunc::loadLastDateRecord($model, $modelEzf->ezf_table, $target, $create_date_field, $unit_field, $enable_field);
                if($modelLastRecord){
                    return $this->renderAjax('_error', [
                                'ezf_id' => $ezf_id,
                                'dataid' => $dataid,
                                'modelEzf' => $modelEzf,
                                'msg' => Yii::t('ezform', 'Add only 1 Record/Day'),
                    ]);
                }
            } elseif ($target!='' && $dataid=='' && isset ($modelEzf->unique_record) && $modelEzf->unique_record==3) {
                $options = SDUtility::string2Array($modelEzf->ezf_options);
                $modelLastRecord = EzfUiFunc::loadLastRecord($model, $modelEzf->ezf_table, $target, 2);
                if($modelLastRecord){
                    return $this->renderAjax('_error', [
                                'ezf_id' => $ezf_id,
                                'dataid' => $dataid,
                                'modelEzf' => $modelEzf,
                                'msg' => Yii::t('ezform', 'Cannot submit more than one record'),
                    ]);
                }
            }
            
            $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);
            
            if (!$model) {// dataid ส่งมาผิดหาไม่เจอ / ไมคิดรวมถ้าส่ง '' มา
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                ]);
            }
            $targetReset = false;
            if (!isset($model->id)) {// ถ้ามี new record ที่คนนั้นสร้างไว้ ให้ใช้อันนั้น
                $modelNewRecord = EzfUiFunc::loadNewRecordBySite($model, $modelEzf->ezf_table, $userProfile->user_id, $userProfile->sitecode);

                if ($modelNewRecord) {
                    $targetReset = true;
                    $model->ptid = $modelNewRecord->ptid;
                    $model->xsourcex = $modelNewRecord->xsourcex;
                    $model->xdepartmentx = $userProfile->department;
                    $model->rstat = $modelNewRecord->rstat;
                    $model->sitecode = $modelNewRecord->sitecode;
                    $model->ptcode = $modelNewRecord->ptcode;
                    $model->ptcodefull = $modelNewRecord->ptcodefull;
                    $model->hptcode = $modelNewRecord->hptcode;
                    $model->hsitecode = $modelNewRecord->hsitecode;
                    $model->user_create = $modelNewRecord->user_create;
                    $model->create_date = $modelNewRecord->create_date;
                    $model->user_update = $modelNewRecord->user_update;
                    $model->update_date = $modelNewRecord->update_date;
                    $model->target = $target;
                    $model->sys_lat = $modelNewRecord->sys_lat;
                    $model->sys_lng = $modelNewRecord->sys_lng;
                    $model->id = $modelNewRecord->id;
                }
                
                $model->ezf_version = $version;
            }

            if (!empty($initdata)) {//กำหนดค่าเริ่มต้น
                $model->attributes = $initdata;
                $initdata = NULL;
            }

            //ขั้นตอนกรอกข้อมูลสำคัญ
            $evenFields = EzfFunc::getEvenField($modelFields);
            $special = isset($evenFields['special']) && !empty($evenFields['special']);
           
            if (isset($evenFields['target']) && !empty($evenFields['target'])) { //มีเป้าหมาย
                if ($targetReset) {
                    $model[$evenFields['target']['ezf_field_name']] = '';
                }
                
                $modelEzfTarget = EzfQuery::getEzformOne($evenFields['target']['ref_ezf_id']);
                $target = ($target == '') ? $model[$evenFields['target']['ezf_field_name']] : $target;
                $dataTarget = EzfQuery::getTargetNotRstat($modelEzfTarget->ezf_table, $target);

                $disable[$evenFields['target']['ezf_field_name']] = 1;
                
                if ($dataTarget) {//เลือกเป้าหมายแล้ว
                    if (isset($modelEzf['unique_record']) && $modelEzf['unique_record'] == 2) {
                        $unique = EzfUiFunc::loadUniqueRecord($model, $modelEzf->ezf_table, $target);
                        if ($unique) {
                            return $this->renderAjax('_error', [
                                        'ezf_id' => $ezf_id,
                                        'dataid' => $model->id,
                                        'modelEzf' => $modelEzf,
                                        'msg' => Yii::t('ezform', 'This form only records 1 record.'),
                            ]);
                        }
                    }
                    
                    
                    //เพิ่มและแก้ไขข้อมูล system
                    $model->attributes = EzfUiFunc::setSystemProperty($model, $target, $dataTarget, $modelEzf->ezf_table, $evenFields['target']['ezf_field_name'], '', $special, $userProfile, $evenFields['target'], 0);
                    EzfFunc::inProcess($model, $modelEzfTarget->ezf_id, $modelEzf->ezf_table);
                    $model->afterFind();
                    
                } else { //ฟอร์มค้นหาเป้าหมาย
                    $modelTargetFields = [$evenFields['target']];
                    return $this->renderAjax('_ezform_target', [//ขั้นตอนการเลือกเป้าหมาย
                                'ezf_id' => $ezf_id,
                                'dataid' => $model->id,
                                'modelEzf' => $modelEzf,
                                'targetField' => $targetField,
                                'target' => $target,
                                'modelFields' => $modelTargetFields,
                                'model' => $model,
                                'modal' => $modal,
                                'reloadDiv' => $reloadDiv,
                                'reloadPage' => $reloadPage,
                                'initdata' => $initdata,
                                'type' => 1,
                                'modelVersion' => $modelVersion,
                    ]);
                }
            } else {// ไม่มีเป้าหมาย
                $fieldSpecial = EzfFunc::checkSpecial($model, $evenFields, $targetReset);

                if (!isset($fieldSpecial)) {
                    $specialFields = [$evenFields['special']];

                    return $this->renderAjax('_ezform_target', [//ตรวจสอบ คำถามพิเศษ
                                'ezf_id' => $ezf_id,
                                'dataid' => $model->id,
                                'modelEzf' => $modelEzf,
                                'targetField' => $targetField,
                                'target' => $target,
                                'modelFields' => $specialFields,
                                'model' => $model,
                                'modal' => $modal,
                                'reloadDiv' => $reloadDiv,
                                'reloadPage' => $reloadPage,
                                'initdata' => $initdata,
                                'type' => 2,
                                'modelVersion' => $modelVersion,
                    ]);
                }

                if ($model->id) {
                    $dataTarget = EzfQuery::getTargetNotRstat($modelEzf->ezf_table, $model->id);
                } else {
                    $dataTarget = [];
                }
                
                if (isset($evenFields['special']['ezf_field_name'])) {
                    $disable[$evenFields['special']['ezf_field_name']] = 1;
                }

                //เพิ่มและแก้ไขข้อมูล system
                $model->attributes = EzfUiFunc::setSystemProperty($model, $target, $dataTarget, $modelEzf->ezf_table, '', $fieldSpecial, $special, $userProfile, NULL, 0);
                $model->afterFind();
            }
            
            $rstat_old = isset($model['rstat'])?$model['rstat']:0;
            
            if ($model->load(Yii::$app->request->post())) {
                \backend\modules\manageproject\classes\CNFunc::addLog("Submit form ".SDUtility::array2String($model));
                Yii::$app->response->format = Response::FORMAT_JSON;

                $rstat = Yii::$app->request->post('submit') ? Yii::$app->request->post('submit') : $model->rstat;
                
                if($rstat_old==0){
                    $model->user_create = $userProfile->user_id;
                    $model->create_date = new \yii\db\Expression('NOW()');
                }
                
                $model->rstat = $rstat;
                $model->ezf_version = $version;
                $model->user_update = $userProfile->user_id;
                $model->update_date = new \yii\db\Expression('NOW()');

                $validate = [];
                foreach ($modelFields as $keyf => $valuef) {
                    $pos = strpos($valuef['ezf_field_validate'], 'UniqueValidator');
                    if ($pos === false) {
                    } else {
                        $validate[] = $valuef['ezf_field_name'];
                    }
                }
                
                if(!$model->validate($validate) && $rstat!=3) {
                    $emsg = '';
                    if(isset($model->errors)){
                        foreach ($model->errors as $ekey => $evalue) {
                            $evalue = implode(', ', $evalue);
                            $emsg .= "<strong>[$ekey]</strong> : $evalue<br>";
                        }
                    }
                    
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not update the data.<br>'.$emsg),
                        'data' => $dataid,
                    ];
                    return $result;
                }
                
                //save data
                $result = EzfUiFunc::saveData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $model->id);
                
                //Sql validate
                $sql_validate = SDUtility::string2Array($modelEzf->ezf_sql);
                $error_validate = [];
                if(isset($sql_validate) && !empty($sql_validate)){
                    $update_error = false;
                    foreach ($sql_validate as $key_sql => $value_sql) {
                        $data_validate;
                        try {
                            $model_sql = EzfUiFunc::modelSqlBuilder($value_sql);
                            $params_v = [':id'=>$model->id, ':target'=>$target];
                             if($model_sql){
                                 $sql_builder = SDUtility::string2Array($model_sql->sql_builder);
                                 $query_ex = EzfUiFunc::queryBuilder($sql_builder, $params_v);
                                 if($query_ex){
                                     if($model_sql->sql_load == 2){
                                         $data_validate = $query_ex->createCommand()->queryOne();
                                     } else {
                                         $data_validate = $query_ex->createCommand()->queryAll();
                                     }
                                 }
                             }
                         } catch (\yii\base\Exception $e) {
                             $error_validate[$value_sql] = $e->getMessage();
                         }
                         
                        if($data_validate){
                            $error_validate[$value_sql] = $model_sql->sql_name;
                            $update_error = true;
                        }
                    }
                    
                    $model->error = SDUtility::array2String($error_validate);
                    //end sql validate
                    $model->rstat = 1;
                    $result = EzfUiFunc::saveData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $model->id);
                    
                }
                
                if(isset($result['status']) && $result['status']=='success'){
                    try {
//                        $options = SDUtility::string2Array($modelEzf->ezf_options);
//                        $enable_after_save = isset($options['after_save']['enable'])?$options['after_save']['enable']:0;
//                        if($enable_after_save){
//                            //$modelFields, $model, $modelEzf
//                            if(isset($options['after_save']['php']) && $options['after_save']['php']!=''){
//                                eval("{$options['after_save']['php']};");
//                            }
//                        }
                    } catch (\yii\base\Exception $e) {
                        \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                    }
                }
                
                $variable_core = [
                    'ezf_id' => "{$ezf_id}",
                    'dataid' => "{$model->id}",
                    'targetField' => $targetField,
                    'target' => "{$target}",
                    'modal' => $modal,
                    'reloadDiv' => $reloadDiv,
                    'reloadPage' => $reloadPage,
                ];
                
                return ArrayHelper::merge($variable_core, $result);
            }
            
            $ezf_view = '_ezform';
            if($ezf_box>0){
                $ezf_view = '_ezform_box';
            }
            
            return $this->renderAjax($ezf_view, [
                        'ezf_id' => $ezf_id,
                        'dataid' => $model->id,
                        'modelEzf' => $modelEzf,
                        'modelFields' => $modelFields,
                        'model' => $model,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'reloadPage' => $reloadPage,
                        'initdata' => $initdata,
                        'disable' => $disable,
                        'targetField' => $targetField,
                        'target' => $target,
                        'modelVersion' => $modelVersion,
                        'db2' => $db2,
                        'ezf_box'=>$ezf_box,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionEzformRedirect($ezf_id) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : 'div-'.$ezf_id;
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
            $initdata = isset($_GET['initdata']) ? EzfFunc::stringDecode2Array($_GET['initdata']) : [];
            $db2 = isset($_GET['db2']) ? $_GET['db2'] : 0;
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';
            $disable = [];
            
            $modelEzf = EzfQuery::getEzformOne($ezf_id);
            if(!$modelEzf){
                $modelEzf = new \backend\modules\ezforms2\models\Ezform();
                $modelEzf->ezf_name = 'Unnamed Form';
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                    ]);
            }
            
            $ezf_options = SDUtility::string2Array($modelEzf->ezf_options);
            if(isset($ezf_options['lock_data']) && $ezf_options['lock_data']==1){
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'This form and its database hase been locked. Please contact the Project Owner as needed.'),
                    ]);
            }
            
            
            
            if($db2==1){
                $checkDb2 = EzfFunc::updateDoubleData($modelEzf, $dataid);
                if($checkDb2){
                    $modelEzf->ezf_table = $checkDb2;
                } else {
                    return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'Can not update `double data`.'),
                    ]);
                }
            }

            $version = (isset($_GET['v']) && $_GET['v']!='')?$_GET['v']:$modelEzf->ezf_version;
            //fix version by dataid
            if ($dataid != '') {
                $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $dataid);
                if ($modelZdata) {
                    if($modelZdata->rstat!=0 && !empty($modelZdata->ezf_version)){
                        $version = (isset($_GET['v']) && $_GET['v']!='' && in_array($modelZdata->rstat, [0,1]))?$_GET['v']:$modelZdata->ezf_version;
                    }
                    if($db2==1 && !empty($modelZdata->ezf_version)){
                        $version = (isset($_GET['v']) && $_GET['v']!='' && in_array($modelZdata->rstat, [0,1]))?$_GET['v']:$modelZdata->ezf_version;
                    }
                    if(!empty($modelZdata->ezf_version)){
                        $modelEzf->ezf_version = (isset($_GET['v']) && $_GET['v']!='' && in_array($modelZdata->rstat, [0,1]))?$_GET['v']:$modelZdata->ezf_version;
                    }
                } else {
                    return $this->render('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                    ]);
                }
            }
            
            if($modelEzf->enable_version){
                $modelVersion = EzfQuery::getEzformConfigApprov($modelEzf->ezf_id, $version);
            } else {
                $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $version);
                
            }
            
            if($modelVersion){
                $modelEzf->field_detail = $modelVersion->field_detail;
                $modelEzf->ezf_sql = $modelVersion->ezf_sql;
                $modelEzf->ezf_js = $modelVersion->ezf_js;
                $modelEzf->ezf_error = $modelVersion->ezf_error;
                $modelEzf->ezf_options = $modelVersion->ezf_options;
            } else {
                return $this->render('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No version found.'),
                ]);
            }
            
            $modelFields = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);
            
            Yii::$app->session['show_varname'] = 0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            //Yii::$app->session['ezform'] = $modelEzf->attributes;

            $userProfile = Yii::$app->user->identity->profile;
            
            $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
            //Unique Record
            $modelLastRecord;
            if($target!='' && $dataid=='' && isset($modelEzf->unique_record) && $modelEzf->unique_record==2){
                $modelLastRecord = EzfUiFunc::loadLastRecord($model, $modelEzf->ezf_table, $target);
                if($modelLastRecord){
                    return $this->renderAjax('_error', [
                                'ezf_id' => $ezf_id,
                                'dataid' => $dataid,
                                'modelEzf' => $modelEzf,
                                'msg' => Yii::t('ezform', 'Add only 1 Record'),
                    ]);
                }
            } elseif ($target!='' && $dataid=='' && isset ($modelEzf->unique_record) && $modelEzf->unique_record==4) {
                $options = SDUtility::string2Array($modelEzf->ezf_options);
                $create_date_field = isset($options['create_date_field']) && !empty($options['create_date_field'])?$options['create_date_field']:'create_date';
                $modelLastRecord = EzfUiFunc::loadLastDateRecord($model, $modelEzf->ezf_table, $target, $create_date_field);
                if($modelLastRecord){
                    return $this->renderAjax('_error', [
                                'ezf_id' => $ezf_id,
                                'dataid' => $dataid,
                                'modelEzf' => $modelEzf,
                                'msg' => Yii::t('ezform', 'Add only 1 Record/Day'),
                    ]);
                }
            } elseif ($target!='' && $dataid=='' && isset ($modelEzf->unique_record) && $modelEzf->unique_record==3) {
                $options = SDUtility::string2Array($modelEzf->ezf_options);
                $modelLastRecord = EzfUiFunc::loadLastRecord($model, $modelEzf->ezf_table, $target, 2);
                if($modelLastRecord){
                    return $this->renderAjax('_error', [
                                'ezf_id' => $ezf_id,
                                'dataid' => $dataid,
                                'modelEzf' => $modelEzf,
                                'msg' => Yii::t('ezform', 'Cannot submit more than one record'),
                    ]);
                }
            }
            
            $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);
            
            if (!$model) {// dataid ส่งมาผิดหาไม่เจอ / ไมคิดรวมถ้าส่ง '' มา
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                ]);
            }
            $targetReset = false;
            if (!isset($model->id)) {// ถ้ามี new record ที่คนนั้นสร้างไว้ ให้ใช้อันนั้น
                $modelNewRecord = EzfUiFunc::loadNewRecordBySite($model, $modelEzf->ezf_table, $userProfile->user_id, $userProfile->sitecode);

                if ($modelNewRecord) {
                    $targetReset = true;
                    $model->ptid = $modelNewRecord->ptid;
                    $model->xsourcex = $modelNewRecord->xsourcex;
                    $model->xdepartmentx = $userProfile->department;
                    $model->rstat = $modelNewRecord->rstat;
                    $model->sitecode = $modelNewRecord->sitecode;
                    $model->ptcode = $modelNewRecord->ptcode;
                    $model->ptcodefull = $modelNewRecord->ptcodefull;
                    $model->hptcode = $modelNewRecord->hptcode;
                    $model->hsitecode = $modelNewRecord->hsitecode;
                    $model->user_create = $modelNewRecord->user_create;
                    $model->create_date = $modelNewRecord->create_date;
                    $model->user_update = $modelNewRecord->user_update;
                    $model->update_date = $modelNewRecord->update_date;
                    $model->target = $target;
                    $model->sys_lat = $modelNewRecord->sys_lat;
                    $model->sys_lng = $modelNewRecord->sys_lng;
                    $model->ezf_version = $version;
                    $model->id = $modelNewRecord->id;
                }
            }

            if (!empty($initdata)) {//กำหนดค่าเริ่มต้น
                $model->attributes = $initdata;
                $initdata = NULL;
            }

            //ขั้นตอนกรอกข้อมูลสำคัญ
            $evenFields = EzfFunc::getEvenField($modelFields);
            $special = isset($evenFields['special']) && !empty($evenFields['special']);
            
            if (isset($evenFields['target']) && !empty($evenFields['target'])) { //มีเป้าหมาย
                if ($targetReset) {
                    $model[$evenFields['target']['ezf_field_name']] = '';
                }

                $modelEzfTarget = EzfQuery::getEzformOne($evenFields['target']['ref_ezf_id']);
                $target = ($target == '') ? $model[$evenFields['target']['ezf_field_name']] : $target;
                $dataTarget = EzfQuery::getTargetNotRstat($modelEzfTarget->ezf_table, $target);

                $disable[$evenFields['target']['ezf_field_name']] = 1;
                
                if ($dataTarget) {//เลือกเป้าหมายแล้ว
                    if (isset($modelEzf['unique_record']) && $modelEzf['unique_record'] == 2) {
                        $unique = EzfUiFunc::loadUniqueRecord($model, $modelEzf->ezf_table, $target);
                        if ($unique) {
                            return $this->render('_error', [
                                        'ezf_id' => $ezf_id,
                                        'dataid' => $model->id,
                                        'modelEzf' => $modelEzf,
                                        'msg' => Yii::t('ezform', 'This form only records 1 record.'),
                            ]);
                        }
                    }
                    
                    
                    //เพิ่มและแก้ไขข้อมูล system
                    $model->attributes = EzfUiFunc::setSystemProperty($model, $target, $dataTarget, $modelEzf->ezf_table, $evenFields['target']['ezf_field_name'], '', $special, $userProfile, $evenFields['target'], 0);
                    EzfFunc::inProcess($model, $modelEzfTarget->ezf_id, $modelEzf->ezf_table);
                    $model->afterFind();
                    
                } else { //ฟอร์มค้นหาเป้าหมาย
                    $modelTargetFields = [$evenFields['target']];
                    return $this->render('_ezform_target_redirect', [//ขั้นตอนการเลือกเป้าหมาย
                                'ezf_id' => $ezf_id,
                                'dataid' => $model->id,
                                'modelEzf' => $modelEzf,
                                'targetField' => $targetField,
                                'target' => $target,
                                'modelFields' => $modelTargetFields,
                                'model' => $model,
                                'modal' => $modal,
                                'reloadDiv' => $reloadDiv,
                                'initdata' => $initdata,
                                'type' => 1,
                                'modelVersion' => $modelVersion,
                                'db2' => $db2,
                        'redirect' => $redirect,
                    ]);
                }
            } else {// ไม่มีเป้าหมาย
                $fieldSpecial = EzfFunc::checkSpecial($model, $evenFields, $targetReset);

                if (!isset($fieldSpecial)) {
                    $specialFields = [$evenFields['special']];

                    return $this->render('_ezform_target_redirect', [//ตรวจสอบ คำถามพิเศษ
                                'ezf_id' => $ezf_id,
                                'dataid' => $model->id,
                                'modelEzf' => $modelEzf,
                                'targetField' => $targetField,
                                'target' => $target,
                                'modelFields' => $specialFields,
                                'model' => $model,
                                'modal' => $modal,
                                'reloadDiv' => $reloadDiv,
                                'initdata' => $initdata,
                                'type' => 2,
                                'modelVersion' => $modelVersion,
                                'db2' => $db2,
                        'redirect' => $redirect,
                    ]);
                }

                if ($model->id) {
                    $dataTarget = EzfQuery::getTarget($modelEzf->ezf_table, $model->id);
                } else {
                    $dataTarget = [];
                }

                if (isset($evenFields['special']['ezf_field_name'])) {
                    $disable[$evenFields['special']['ezf_field_name']] = 1;
                }

                //เพิ่มและแก้ไขข้อมูล system
                $model->attributes = EzfUiFunc::setSystemProperty($model, $target, $dataTarget, $modelEzf->ezf_table, '', $fieldSpecial, $special, $userProfile, NULL, 0);
                $model->afterFind();
            }
            
            $rstat_old = isset($model['rstat'])?$model['rstat']:0;
            if ($model->load(Yii::$app->request->post())) {
               
                Yii::$app->response->format = Response::FORMAT_JSON;

                $rstat = Yii::$app->request->post('submit') ? Yii::$app->request->post('submit') : $model->rstat;
                
                
                if($rstat_old==0){
                    $model->user_create = $userProfile->user_id;
                    $model->create_date = new \yii\db\Expression('NOW()');
                }
                
                $model->rstat = $rstat;
                $model->ezf_version = $version;
                $model->user_update = $userProfile->user_id;
                $model->update_date = new \yii\db\Expression('NOW()');
                
                if(!$model->validate() && $rstat!=3) {
                    $emsg = '';
                    if(isset($model->errors)){
                        foreach ($model->errors as $ekey => $evalue) {
                            $evalue = implode(', ', $evalue);
                            $emsg .= "<strong>[$ekey]</strong> : $evalue<br>";
                        }
                    }
                    
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not update the data.<br>'.$emsg),
                        'data' => $dataid,
                    ];
                    
                    Yii::$app->session->setFlash('alert', [
                            'body' => $result['message'],
                            'options' => ['class' => 'alert-danger']
                    ]);
                    
                    if($redirect!=''){
                        return $this->goBack($redirect);
                    } else {
                        return $this->redirect(Url::to(['/ezforms2/ezform-data/ezform-redirect', 'ezf_id'=>$ezf_id]));
                    }
                }

                $result = EzfUiFunc::saveData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $model->id);
                
                //Sql validate
                $sql_validate = SDUtility::string2Array($modelEzf->ezf_sql);
                $error_validate = [];
                if(isset($sql_validate) && !empty($sql_validate)){
                    $update_error = false;
                    foreach ($sql_validate as $key_sql => $value_sql) {
                        $data_validate;
                        try {
                            $model_sql = EzfUiFunc::modelSqlBuilder($value_sql);
                            $params_v = [':id'=>$model->id, ':target'=>$target];
                             if($model_sql){
                                 $sql_builder = SDUtility::string2Array($model_sql->sql_builder);
                                 $query_ex = EzfUiFunc::queryBuilder($sql_builder, $params_v);
                                 if($query_ex){
                                     if($model_sql->sql_load == 2){
                                         $data_validate = $query_ex->createCommand()->queryOne();
                                     } else {
                                         $data_validate = $query_ex->createCommand()->queryAll();
                                     }
                                 }
                             }
                         } catch (\yii\base\Exception $e) {
                             $error_validate[$value_sql] = $e->getMessage();
                         }
                         
                        if($data_validate){
                            $error_validate[$value_sql] = $model_sql->sql_name;
                            $update_error = true;
                        }
                    }
                    
                    $model->error = SDUtility::array2String($error_validate);
                    //end sql validate
                    $model->rstat = 1;
                    $result = EzfUiFunc::saveData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $model->id);
                }
                
                Yii::$app->session->setFlash('alert', [
                        'body' => $result['message'],
                        'options' => ['class' => 'alert-success']
                ]);
                
                if($redirect!=''){
                    return $this->goBack($redirect);
                } else {
                    return $this->redirect(Url::to(['/ezforms2/ezform-data/ezform-redirect', 'ezf_id'=>$ezf_id, 'dataid'=>$model->id]));
                }
                
            }
            
            return $this->render('_ezform_redirect', [
                        'ezf_id' => $ezf_id,
                        'dataid' => $model->id,
                        'modelEzf' => $modelEzf,
                        'modelFields' => $modelFields,
                        'model' => $model,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'initdata' => $initdata,
                        'disable' => $disable,
                        'targetField' => $targetField,
                        'target' => $target,
                        'modelVersion' => $modelVersion,
                        'db2' => $db2,
                        'redirect' => $redirect,
            ]);
    }

    public function actionEzformDictionary($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            
            $modelEzf = EzfQuery::getEzformOne($ezf_id);
            $version = (isset($_GET['v']) && $_GET['v']!='')?$_GET['v']:$modelEzf->ezf_version;
            
            if($modelEzf->enable_version){
                $modelVersion = EzfQuery::getEzformConfigApprov($modelEzf->ezf_id, $version);
            } else {
                $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $version);
            }
            
            if($modelVersion){
                $modelEzf->field_detail = $modelVersion->field_detail;
                $modelEzf->ezf_sql = $modelVersion->ezf_sql;
                $modelEzf->ezf_js = $modelVersion->ezf_js;
                $modelEzf->ezf_error = $modelVersion->ezf_error;
                $modelEzf->ezf_options = $modelVersion->ezf_options;
            } else {
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No version found.'),
                ]);
            }
            
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            $modelFields = EzfQuery::getFieldAllNotLabel($modelEzf->ezf_id, $version);
            
            $dataProvider = new \yii\data\ArrayDataProvider([
               'allModels' => $modelFields,
               'sort' => [
                   'attributes' => ['ezf_version', 'ezf_field_name'],
               ],
               'pagination' => FALSE,
           ]);
            \backend\modules\manageproject\classes\CNFunc::addLog("View form {$modelEzf->ezf_id} Dictionary ". SDUtility::array2String($modelFields));
            return $this->renderAjax('_dictionary', [
                        'modelFields' => $modelFields,
                        'modelEzf' => $modelEzf,
                        'dataProvider' => $dataProvider,
                        'dataid' => $dataid,
                        'ezf_id' => $ezf_id,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'modelVersion' => $modelVersion,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionEzformAnnotated($ezf_id) {
        $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
        $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
        $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
        
        $modelEzf = EzfQuery::getEzformOne($ezf_id);
        $version = (isset($_GET['v']) && $_GET['v']!='')?$_GET['v']:$modelEzf->ezf_version;

        if($modelEzf->enable_version){
                $modelVersion = EzfQuery::getEzformConfigApprov($modelEzf->ezf_id, $version);
            } else {
                $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $version);
            }
        if($modelVersion){
            $modelEzf->field_detail = $modelVersion->field_detail;
            $modelEzf->ezf_sql = $modelVersion->ezf_sql;
            $modelEzf->ezf_js = $modelVersion->ezf_js;
            $modelEzf->ezf_error = $modelVersion->ezf_error;
            $modelEzf->ezf_options = $modelVersion->ezf_options;
        } else {
            return $this->renderAjax('_error', [
                        'ezf_id' => $ezf_id,
                        'dataid' => $dataid,
                        'modelEzf' => $modelEzf,
                        'msg' => Yii::t('app', 'No version found.'),
            ]);
        }

        $modelFields = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);

        Yii::$app->session['show_varname'] = 1;
        Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
        //Yii::$app->session['ezform'] = $modelEzf->attributes;

        $userProfile = Yii::$app->user->identity->profile;

        $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
        \backend\modules\manageproject\classes\CNFunc::addLog("View form Annotated ". SDUtility::array2String($model));
        return $this->renderAjax('_ezform-annotated', [
                    'ezf_id' => $ezf_id,
                    'modelEzf' => $modelEzf,
                    'modelFields' => $modelFields,
                    'model' => $model,
                    'modal' => $modal,
                    'reloadDiv' => $reloadDiv,
                    'modelVersion' => $modelVersion,
        ]);
        
    }
    
    public function actionEzformView($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $initdata = isset($_GET['initdata']) ? EzfFunc::stringDecode2Array($_GET['initdata']) : [];
            $db2 = isset($_GET['db2']) ? $_GET['db2'] : 0;
            
            $modelEzf = EzfQuery::getEzformOne($ezf_id);
            if($db2==1){
                $checkDb2 = EzfFunc::updateDoubleData($modelEzf, $dataid);
                if($checkDb2){
                    $modelEzf->ezf_table = $checkDb2;
                } else {
                    return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'Can not update `double data`.'),
                    ]);
                }
            }
            $version = (isset($_GET['v']) && $_GET['v']!='')?$_GET['v']:$modelEzf->ezf_version;

            //fix version by dataid
            if ($dataid != '') {
                $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $dataid);
                if ($modelZdata) {
                    if($modelZdata->rstat!=0 && !empty($modelZdata->ezf_version)){
                        $version = $modelZdata->ezf_version;
                    }
                    if($db2==1 && !empty($modelZdata->ezf_version)){
                        $version = $modelZdata->ezf_version;
                    }
                    if(!empty($modelZdata->ezf_version)){
                        $modelEzf->ezf_version = $modelZdata->ezf_version;
                    }
                } else {
                    return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                    ]);
                }
            }
            
            if($modelEzf->enable_version){
                $modelVersion = EzfQuery::getEzformConfigApprov($modelEzf->ezf_id, $version);
            } else {
                $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $version);
            }
            if($modelVersion){
                $modelEzf->field_detail = $modelVersion->field_detail;
                $modelEzf->ezf_sql = $modelVersion->ezf_sql;
                $modelEzf->ezf_js = $modelVersion->ezf_js;
                $modelEzf->ezf_error = $modelVersion->ezf_error;
                $modelEzf->ezf_options = $modelVersion->ezf_options;
            } else {
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No version found.'),
                ]);
            }
        
            $modelFields = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);

            Yii::$app->session['show_varname'] = 0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            //Yii::$app->session['ezform'] = $modelEzf->attributes;

            $userProfile = Yii::$app->user->identity->profile;

            $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
            $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);

            if (!$model || $dataid == '') {// dataid ส่งมาผิดหาไม่เจอ / ไม่คิดรวมถ้าส่ง '' มา
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $model->id,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                ]);
            } else {
                $model->afterFind();
            }
            EzfFunc::inProcess($model, $ezf_id, $modelEzf->ezf_table);
            
            return $this->renderAjax('_ezform-view', [
                        'ezf_id' => $ezf_id,
                        'dataid' => $model->id,
                        'modelEzf' => $modelEzf,
                        'modelFields' => $modelFields,
                        'model' => $model,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'initdata' => $initdata,
                        'modelVersion' => $modelVersion,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionEzformPrint($ezf_id) {
        $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
        $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
        $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
        $target = isset($_GET['target']) ? $_GET['target'] : '';
        $initdata = isset($_GET['initdata']) ? EzfFunc::stringDecode2Array($_GET['initdata']) : [];
        $db2 = isset($_GET['db2']) ? $_GET['db2'] : 0;
        
        $modelEzf = EzfQuery::getEzformOne($ezf_id);
        if($db2==1){
                $checkDb2 = EzfFunc::updateDoubleData($modelEzf, $dataid);
                if($checkDb2){
                    $modelEzf->ezf_table = $checkDb2;
                } else {
                    return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'Can not update `double data`.'),
                    ]);
                }
            }
        $version = (isset($_GET['v']) && $_GET['v']!='')?$_GET['v']:$modelEzf->ezf_version;

        //fix version by dataid
        if ($dataid != '') {
            $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $dataid);
            if ($modelZdata) {
                if($modelZdata->rstat!=0 && !empty($modelZdata->ezf_version)){
                        $version = $modelZdata->ezf_version;
                    }
                    if($db2==1 && !empty($modelZdata->ezf_version)){
                        $version = $modelZdata->ezf_version;
                    }
                    if(!empty($modelZdata->ezf_version)){
                        $modelEzf->ezf_version = $modelZdata->ezf_version;
                    }
            } else {
                return $this->renderAjax('_error', [
                        'ezf_id' => $ezf_id,
                        'dataid' => $dataid,
                        'modelEzf' => $modelEzf,
                        'msg' => Yii::t('app', 'No results found.'),
                ]);
            }
        }

        if($modelEzf->enable_version){
                $modelVersion = EzfQuery::getEzformConfigApprov($modelEzf->ezf_id, $version);
            } else {
                $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $version);
            }
        if($modelVersion){
            $modelEzf->field_detail = $modelVersion->field_detail;
            $modelEzf->ezf_sql = $modelVersion->ezf_sql;
            $modelEzf->ezf_js = $modelVersion->ezf_js;
            $modelEzf->ezf_error = $modelVersion->ezf_error;
            $modelEzf->ezf_options = $modelVersion->ezf_options;
        } else {
            return $this->renderAjax('_error', [
                        'ezf_id' => $ezf_id,
                        'dataid' => $dataid,
                        'modelEzf' => $modelEzf,
                        'msg' => Yii::t('app', 'No version found.'),
            ]);
        }

        $modelFields = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);

        Yii::$app->session['show_varname'] = 0;
        Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
        //Yii::$app->session['ezform'] = $modelEzf->attributes;

        $userProfile = Yii::$app->user->identity->profile;

        $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
        $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);

        if (!$model || $dataid == '') {// dataid ส่งมาผิดหาไม่เจอ / ไม่คิดรวมถ้าส่ง '' มา
            return $this->renderAjax('_error', [
                        'ezf_id' => $ezf_id,
                        'dataid' => $model->id,
                        'modelEzf' => $modelEzf,
                        'msg' => Yii::t('app', 'No results found.'),
            ]);
        } else {
            $model->afterFind();
        }

        $content = $this->renderPartial('_ezform-view', [
            'ezf_id' => $ezf_id,
            'dataid' => $model->id,
            'modelEzf' => $modelEzf,
            'modelFields' => $modelFields,
            'model' => $model,
            'modal' => $modal,
            'reloadDiv' => $reloadDiv,
            'initdata' => $initdata,
            'modelVersion' => $modelVersion,
        ]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            'cssFile' => '@backend/web/css/pdf.css', //'@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => $modelEzf->ezf_name],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader' => [$modelEzf->ezf_name],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    public function actionView($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $popup = isset($_GET['popup']) ? $_GET['popup'] : 0;
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
            $data_column = isset($_GET['data_column']) ? $_GET['data_column'] : '';
            $disabled = isset($_GET['disabled']) ? $_GET['disabled'] : 0;
            $default_column = isset($_GET['default_column']) ? $_GET['default_column'] : 1;
            $pageSize = isset($_GET['pageSize']) ? $_GET['pageSize'] : 50;
            $order_column = isset($_GET['order_column']) ? $_GET['order_column'] : '';
            $orderby = isset($_GET['orderby']) ? (int)$_GET['orderby'] : SORT_DESC;
            $search_column = isset($_GET['search_column']) ? $_GET['search_column'] : '';
            $varname = isset($_GET['varname']) ? $_GET['varname'] : 0;
            $rawdata = isset($_GET['rawdata']) ? $_GET['rawdata'] : 0;
            $header = isset($_GET['header']) ? $_GET['header'] : '';
            $title = isset($_GET['title']) ? $_GET['title'] : '';
            $db2 = isset($_GET['db2']) ? $_GET['db2'] : 0;
            $addbtn = isset($_GET['addbtn']) ? $_GET['addbtn'] : 1;
            $actions = isset($_GET['actions']) ? $_GET['actions'] : '';
            $theme = isset($_GET['theme']) ? $_GET['theme'] : 'default';
            $filter = isset($_GET['filter'])?$_GET['filter']:1;
            
            $addbtn = $disabled==1?0:$addbtn;
            
            $data_column = EzfFunc::stringDecode2Array($data_column);
            $order_column = EzfFunc::stringDecode2Array($order_column);
            $search_column = EzfFunc::stringDecode2Array($search_column);
            $header = EzfFunc::stringDecode2Array($header);
            $actions = EzfFunc::stringDecode2Array($actions);
            
            $ezform = EzfQuery::getEzformOne($ezf_id);
            if(!$ezform){
                $ezform = new \backend\modules\ezforms2\models\Ezform();
                $ezform->ezf_name = 'Unnamed Form';
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'modelEzf' => $ezform,
                            'msg' => Yii::t('app', 'No results found.'),
                    ]);
            }
            
            if(empty($data_column)){
                $data_column = SDUtility::string2Array($ezform->field_detail);
                if(empty($data_column)){
                    $field_list = EzfQuery::getFieldsListVersion($ezform->ezf_id, $ezform->ezf_version);
                    $data_column = \yii\helpers\ArrayHelper::getColumn($field_list, 'ezf_field_name');
                    foreach ($data_column as $k => $v) {
                        if(in_array($v, ['id', 'sys_lat', 'sys_lng'])){
                            unset($data_column[$k]);
                        }
                    }
                }
            }

            $searchModel = NULL;
            $dataProvider = NULL;
            
            if($popup==0){
                
                $searchModel = new TbdataAll();
                $searchModel->setTableName($ezform->ezf_table);

                if($targetField==''){
                    $modelTarget = EzfQuery::getTargetOne($ezform->ezf_id);
                    if ($modelTarget) {
                        $targetField = $modelTarget['ezf_field_name'];
                    }
                }

                if($target != '' ){//&& $targetField!=''
                    $searchModel[$targetField] = $target;
                }
                
                if(isset($search_column) && !empty($search_column)){
                    foreach ($search_column as $key => $value) {
                        $searchModel->$key = $value;
                    }
                    //$key_search = array_keys($search_column);
                    //$data_column = ArrayHelper::merge($data_column, $key_search);
                }
                
                if($db2==1){
                    EzfFunc::updateDoubleData($ezform, '');
                    $dataProvider = EzfUiFunc::modelSearchDb2($searchModel, $ezform, $targetField, $data_column, Yii::$app->request->queryParams, $pageSize, $order_column, $orderby);
                } else {
                    $dataProvider = EzfUiFunc::modelSearch($searchModel, $ezform, $targetField, $data_column, Yii::$app->request->queryParams, $pageSize, $order_column, $orderby);
                }
                
            }
            
            $view = $popup ? '_view-popup' : '_view';
            \backend\modules\manageproject\classes\CNFunc::addLog("View form {$model->ezf_name} ". SDUtility::array2String($ezform));
            return $this->renderAjax($view, [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'ezform' => $ezform,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'data_column' => $data_column,
                        'target' => $target,
                        'targetField' => $targetField,
                        'disabled' => $disabled,
                        'theme' => $theme,
                        'default_column' => $default_column,
                        'pageSize' => $pageSize,
                        'order_column' => $order_column,
                        'orderby' => $orderby,
                        'actions' => $actions,
                        'search_column' => $search_column,
                        'varname' => $varname,
                        'header' => $header,
                        'rawdata' => $rawdata,
                        'title' => $title,
                        'db2' => $db2,
                        'addbtn' => $addbtn,
                        'filter' => $filter,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionViewSql($sql_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $data_column = isset($_GET['data_column']) ? $_GET['data_column'] : '';
            $header = isset($_GET['header']) ? $_GET['header'] : '';
            $title = isset($_GET['title']) ? $_GET['title'] : '';
            $actions = isset($_GET['actions']) ? $_GET['actions'] : '';
            $params = isset($_GET['params']) ? $_GET['params'] : '';
            $pageSize = isset($_GET['pageSize']) ? $_GET['pageSize'] : 50;
            $key_id = isset($_GET['key_id']) ? $_GET['key_id'] : '';
            $theme = isset($_GET['theme']) ? $_GET['theme'] : 'default';
            
            $data_column = EzfFunc::stringDecode2Array($data_column);
            $header = EzfFunc::stringDecode2Array($header);
            $actions = EzfFunc::stringDecode2Array($actions);
            $params = EzfFunc::stringDecode2Array($params);
            
            
            $sql_params = [];
            $sql_variable = [];
            $get_params = [];
            
            unset($_GET['target']);
            $get_params = ArrayHelper::merge($params, $_GET);

            foreach ($get_params as $key_get => $value_get) {
                $sql_variable["{{$key_get}}"] = $value_get;
                $sql_params[":{$key_get}"] = $value_get;
            }

            $query;
            try {
                $model_sql = EzfUiFunc::modelSqlBuilder($sql_id);
                 if($model_sql){
                     $sql_builder = SDUtility::string2Array($model_sql->sql_builder);
                     $query = EzfUiFunc::queryBuilder($sql_builder, $sql_params);
                 }
                 
                 if(empty($data_column)){
                    $data_column = isset($model_sql['variable'])?$model_sql['variable']:[];
                }
                
                $find_params = isset($sql_builder['params'])?$sql_builder['params']:[];

             } catch (\yii\base\Exception $e) {
                 \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                 return $e->getMessage();
             }
             
             if(!isset($query)){
                 return Yii::t('app', 'No results found.');
             }
             
            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => $query,
//                            'key' => $key_id,
                'sort' => ['attributes' => $data_column],
                'pagination' => [
                    'pageSize' => $pageSize,
                ],
            ]);
            
            if(!empty($key_id)){
                $dataProvider->key = $key_id;
            }
            
            $searchModel = new \yii\base\DynamicModel($data_column);

            return $this->renderAjax('_view_sql', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'data_column' => $data_column,
                        'params' => $params,
                        'pageSize' => $pageSize,
                        'actions' => $actions,
                        'header' => $header,
                        'title' => $title,
                        'theme' => $theme,
                        'get_params' => $get_params,
                        'find_params' => $find_params,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionCompareFields($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            
            $ezform = EzfQuery::getEzformOne($ezf_id);
            $data1 = EzfUiFunc::loadTbData($ezform->ezf_table, $dataid);
            $data2 = EzfUiFunc::loadTbData($ezform->ezf_table.'_db2', $dataid);
            
            if(!$data1 || !$data2){
                return $this->renderAjax('_error', [
                        'ezf_id' => $ezf_id,
                        'dataid' => $dataid,
                        'modelEzf' => $ezform,
                        'msg' => Yii::t('app', 'No results found.'),
                ]);
            }
            $fieldList = [];
            $modelFields = EzfQuery::getFieldAllNotLabel($ezf_id, $data2['ezf_version']);
            if(isset($modelFields) && !empty($modelFields)){
                foreach ($modelFields as $key => $value) {
                    if($data1[$value['ezf_field_name']] != $data2[$value['ezf_field_name']]){
                        $fieldList[] = $value->attributes;
                    }
                }
            }
            
            $dataProvider = new \yii\data\ArrayDataProvider([
               'allModels' => $fieldList,
               'sort' => [
                   'attributes' => ['ezf_field_name'],
               ],
               'pagination' => FALSE,
           ]);

            return $this->renderAjax('_compare_field', [
                        'modelFields' => $modelFields,
                        'ezform' => $ezform,
                        'data1' => $data1,
                        'data2' => $data2,
                        'fieldList' => $fieldList,
                        'dataProvider' => $dataProvider,
                        'dataid' => $dataid,
                        'ezf_id' => $ezf_id,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionCompare($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $popup = isset($_GET['popup']) ? $_GET['popup'] : 0;
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
            $data_column = isset($_GET['data_column']) ? $_GET['data_column'] : '';
            $disabled = isset($_GET['disabled']) ? $_GET['disabled'] : 0;
            $addbtn = isset($_GET['addbtn']) ? $_GET['addbtn'] : 1;
            $default_column = isset($_GET['default_column']) ? $_GET['default_column'] : 1;
            $pageSize = isset($_GET['pageSize']) ? $_GET['pageSize'] : 50;
            $order_column = isset($_GET['order_column']) ? $_GET['order_column'] : '';
            $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 4;
            $db2 = isset($_GET['db2']) ? $_GET['db2'] : 0;
            $search_column = isset($_GET['search_column']) ? $_GET['search_column'] : '';
            $title = isset($_GET['title']) ? $_GET['title'] : '';
            
            $data_column = EzfFunc::stringDecode2Array($data_column);
            $order_column = EzfFunc::stringDecode2Array($order_column);
            $search_column = EzfFunc::stringDecode2Array($search_column);

            $ezform = EzfQuery::getEzformOne($ezf_id);
            
            if(empty($data_column)){
                $data_column = SDUtility::string2Array($ezform->field_detail);
            }

            $searchModel = NULL;
            $dataProvider = NULL;
            
            if($popup==0){
                $searchModel = new TbdataAll();
                $searchModel->setTableName($ezform->ezf_table);

                if($targetField==''){
                    $modelTarget = EzfQuery::getTargetOne($ezform->ezf_id);
                    if ($modelTarget) {
                        $targetField = $modelTarget['ezf_field_name'];
                    }
                }
                
                if($target != ''){
                    $searchModel[$targetField] = $target;
                }
                
                if(isset($search_column) && !empty($search_column)){
                    foreach ($search_column as $key => $value) {
                        $searchModel->$key = $value;
                    }
                    $key_search = array_keys($search_column);
                    $data_column = ArrayHelper::merge($data_column, $key_search);
                }
                
                EzfFunc::updateDoubleData($ezform, '');
                $dataProvider = EzfUiFunc::modelSearchCompare($searchModel, $ezform, $targetField, $data_column, Yii::$app->request->queryParams, $pageSize, $order_column, $orderby);
                
            }
            
            return $this->renderAjax('_compare', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'ezform' => $ezform,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'data_column' => $data_column,
                        'target' => $target,
                        'targetField' => $targetField,
                        'disabled' => $disabled,
                        'addbtn' => $addbtn,
                        'default_column' => $default_column,
                        'pageSize' => $pageSize,
                        'order_column' => $order_column,
                        'orderby' => $orderby,
                        'db2' => $db2,
                        'search_column' => $search_column,
                        'title' => $title,
                        'addbtn' => $addbtn,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionHistory($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $popup = isset($_GET['popup']) ? $_GET['popup'] : 0;
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
            $data_column = isset($_GET['data_column']) ? $_GET['data_column'] : '';
            $disabled = isset($_GET['disabled']) ? $_GET['disabled'] : 0;
            $addbtn = isset($_GET['addbtn']) ? $_GET['addbtn'] : 1;
            $default_column = isset($_GET['default_column']) ? $_GET['default_column'] : 1;
            $pageSize = isset($_GET['pageSize']) ? $_GET['pageSize'] : 50;
            $order_column = isset($_GET['order_column']) ? $_GET['order_column'] : '';
            $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 4;
            $initdata = isset($_GET['initdata']) ? EzfFunc::stringDecode2Array($_GET['initdata']) : [];
            $varname = isset($_GET['varname']) ? $_GET['varname'] : 0;
            
            $data_column = EzfFunc::stringDecode2Array($data_column);
            $order_column = EzfFunc::stringDecode2Array($order_column);

            $ezform = EzfQuery::getEzformOne($ezf_id);
            if(!$ezform){
                $ezform = new \backend\modules\ezforms2\models\Ezform();
                $ezform->ezf_name = 'Unnamed Form';
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'modelEzf' => $ezform,
                            'msg' => Yii::t('app', 'No results found.'),
                    ]);
            }
            
            if(empty($data_column)){
                $data_column = SDUtility::string2Array($ezform->field_detail);
                if(empty($data_column)){
                    $field_list = EzfQuery::getFieldsListVersion($ezform->ezf_id, $ezform->ezf_version);
                    $data_column = \yii\helpers\ArrayHelper::getColumn($field_list, 'ezf_field_name');
                    foreach ($data_column as $k => $v) {
                        if(in_array($v, ['id', 'sys_lat', 'sys_lng'])){
                            unset($data_column[$k]);
                        }
                    }
                }
            }

            $searchModel = NULL;
            $dataProvider = NULL;
            
            if($popup==0){
                $searchModel = new TbdataAll();
                $searchModel->setTableName($ezform->ezf_table);

                if($targetField==''){
                    $modelTarget = EzfQuery::getTargetOne($ezform->ezf_id);
                    if ($modelTarget) {
                        $targetField = $modelTarget['ezf_field_name'];
                    }
                }
                
                if($target != ''){
                    $searchModel[$targetField] = $target;
                }
                
                $dataProvider = EzfUiFunc::modelSearch($searchModel, $ezform, $targetField, $data_column, Yii::$app->request->queryParams, $pageSize, $order_column, $orderby);
            }
            
            return $this->renderAjax('_history', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'ezform' => $ezform,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'data_column' => $data_column,
                        'target' => $target,
                        'targetField' => $targetField,
                        'disabled' => $disabled,
                        'addbtn' => $addbtn,
                        'default_column' => $default_column,
                        'pageSize' => $pageSize,
                        'order_column' => $order_column,
                        'orderby' => $orderby,
                        'initdata'=>$initdata,
                        'varname' => $varname,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionEvalutionForm() {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $disabled = isset($_GET['disabled']) ? $_GET['disabled'] : 0;
            $category = isset($_GET['category']) ? $_GET['category'] : '';
            $orderby = isset($_GET['orderby']) ? (int)$_GET['orderby'] : SORT_DESC;
            
            $searchModel = new EzformTarget();
            if (isset($target) && $target != '') {
                $searchModel->target_id = $target;
            }
            
            $dataProvider = EzfUiFunc::modelEvalutionSearch($searchModel, $target, $category, $orderby, Yii::$app->request->queryParams);

            Yii::$app->session['show_varname'] = 0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            
            if (isset($searchModel->target_id) && !empty($searchModel->target_id)) {
                $target = $searchModel->target_id;
            }

            return $this->renderAjax('_evalution', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'target' => $target,
                        'disabled' => $disabled,
                        'category' => $category,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionEmr($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $popup = isset($_GET['popup']) ? $_GET['popup'] : 0;
            $showall = isset($_GET['showall']) ? $_GET['showall'] : 0;
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $disabled = isset($_GET['disabled']) ? $_GET['disabled'] : 0;
            
            $searchModel = new EzformTarget();
            if (isset($target) && $target != '') {
                $searchModel->target_id = $target;
            }
            $dataProvider = EzfUiFunc::modelEmrSearch($searchModel, $target, $ezf_id, Yii::$app->request->queryParams, $showall);
            $modelEzf = EzfQuery::getEzformOne($ezf_id);

            Yii::$app->session['show_varname'] = 0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            
            if (isset($searchModel->target_id) && !empty($searchModel->target_id)) {
                $target = $searchModel->target_id;
            }

            
            if($popup==1){
                $view = '_emr_list';
            } else if($popup==2){
                $view = '_emr_table';
            } else if($popup==3){
                $view = '_emr_table';
            } else {
                $view = '_emr';
            }

            return $this->renderAjax($view, [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'ezf_id' => $ezf_id,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'target' => $target,
                        'showall' => $showall,
                        'disabled' => $disabled,
                        'modelEzf' => $modelEzf,
                        'popup' => $popup,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionEmrPopup($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $showall = isset($_GET['showall']) ? $_GET['showall'] : 0;
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $disabled = isset($_GET['disabled']) ? $_GET['disabled'] : 0;
            $popup = isset($_GET['popup']) ? $_GET['popup'] : 0;
            $addbtn = isset($_GET['addbtn']) ? $_GET['addbtn'] : 1;
            $title = isset($_GET['title']) ? $_GET['title'] : '';
            $db2 = isset($_GET['db2']) ? $_GET['db2'] : 0;
            
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
                        'showall' => $showall,
                        'disabled' => $disabled,
                        'modelEzf' => $modelEzf,
                        'addbtn' => $addbtn,
                        'title' => $title,
                        'db2' => $db2,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionContent($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
            $fields = isset($_GET['fields']) ? $_GET['fields'] : '';
            $initdata = isset($_GET['initdata']) ? $_GET['initdata'] : false;
            $initdate = isset($_GET['initdate']) ? $_GET['initdate'] : false;
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $options = isset($_GET['options']) ? $_GET['options'] : '';
            
            $fields = EzfFunc::stringDecode2Array($fields);
            $options = EzfFunc::stringDecode2Array($options);

            $modelEzf = EzfQuery::getEzformOne($ezf_id);
            $version = (isset($_GET['v']) && $_GET['v']!='')?$_GET['v']:$modelEzf->ezf_version;

            //fix version by dataid
            if ($dataid != '') {
                $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $dataid);
                if ($modelZdata) {
                    if($modelZdata->rstat!=0 && !empty($modelZdata->ezf_version)){
                        $version = $modelZdata->ezf_version;
                    }
                    if(!empty($modelZdata->ezf_version)){
                        $modelEzf->ezf_version = $modelZdata->ezf_version;
                    }
                } else {
                    return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                    ]);
                }
            }
            
            if($modelEzf->enable_version){
                $modelVersion = EzfQuery::getEzformConfigApprov($modelEzf->ezf_id, $version);
            } else {
                $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $version);
            }
            if($modelVersion){
                $modelEzf->field_detail = $modelVersion->field_detail;
                $modelEzf->ezf_sql = $modelVersion->ezf_sql;
                $modelEzf->ezf_js = $modelVersion->ezf_js;
                $modelEzf->ezf_error = $modelVersion->ezf_error;
                $modelEzf->ezf_options = $modelVersion->ezf_options;
            } else {
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No version found.'),
                ]);
            }
        
            $modelFields = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);

            Yii::$app->session['show_varname'] = 0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            //Yii::$app->session['ezform'] = $modelEzf->attributes;

            $userProfile = Yii::$app->user->identity->profile;

            $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
            
            if($dataid != ''){
                $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);
                
                if (!$model) {// dataid ส่งมาผิดหาไม่เจอ / ไม่คิดรวมถ้าส่ง '' มา
                    return $this->renderAjax('_error', [
                                'ezf_id' => $ezf_id,
                                'dataid' => $dataid,
                                'modelEzf' => $modelEzf,
                                'msg' => Yii::t('app', 'No results found.'),
                    ]);
                }
            } else {
                if($initdata){
                    if(!empty($initdate)){
                        $modelLastRecord = EzfUiFunc::loadLastDateRecord($model, $modelEzf->ezf_table, $target, $initdate);
                    } else {
                        $modelLastRecord = EzfUiFunc::loadLastRecord($model, $modelEzf->ezf_table, $target);
                    }

                    if ($modelLastRecord) {
                        $model = $modelLastRecord;
                    }
                }
            }
            
            return $this->renderAjax('_content_data', [
                        'modelFields' => $modelFields,
                        'model' => $model,
                        'ezf_id'=>$ezf_id,
                        'modelEzf' => $modelEzf,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'fields' => $fields,
                        'target' => $target,
                        'targetField' => $targetField,
                        'initdata' => $initdata,
                        'dataid' => $dataid,
                        'options' => $options,
                        'version' => $version,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionSidemenu($sql_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $options = isset($_GET['options']) ? $_GET['options'] : '';
            
            $options = EzfFunc::stringDecode2Array($options);
            
            $params = [];
            $sql_params = [];
            $sql_variable = [];
            $get_params = [];
            
            if(isset($options['params_all']) && !empty($options['params_all'])){
                unset($_GET['target']);
                $get_params = ArrayHelper::merge($options['params_all'], $_GET);
                
                foreach ($get_params as $key_get => $value_get) {
                    $sql_variable["{{$key_get}}"] = $value_get;
                    $sql_params[":{$key_get}"] = $value_get;
                }
            }
            
            $sql_builder = [];
            $query = EzfUiFunc::queryBuilderById($sql_id, $sql_params);
            
            $pageSize = 50;
            if(isset($options['page_size']) && !empty($options['page_size'])){
                $pageSize = $options['page_size'];
            }
            
            $dataProvider = new \yii\data\ActiveDataProvider([
                            'query' => $query,
//                            'totalCount' => $count,
//                            'sql' => $query->createCommand()->sql,
//                            'params' => $query->params,
                            'pagination' => [
                                'pageSize' => $pageSize,
                            ],
                        ]);
            
           
            
            return $this->renderAjax('_sidemenu', [
                        'sql_id'=>$sql_id,
                        'reloadDiv' => $reloadDiv,
                        'target' => $target,
                        'options' => $options,
                        'dataProvider' => $dataProvider,
                        'sql_builder' => $sql_builder,
                        'get_params' => $get_params,
                        'sql_variable' => $sql_variable,
                        'sql_params' => $sql_params,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionListview($sql_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $options = isset($_GET['options']) ? $_GET['options'] : '';
            
            $options = EzfFunc::stringDecode2Array($options);
            
            $params = [];
            $sql_params = [];
            $sql_variable = [];
            $get_params = [];
            
            if(isset($options['params_all']) && !empty($options['params_all'])){
                unset($_GET['target']);
                $get_params = ArrayHelper::merge($options['params_all'], $_GET);
                
                foreach ($get_params as $key_get => $value_get) {
                    $sql_variable["{{$key_get}}"] = $value_get;
                    $sql_params[":{$key_get}"] = $value_get;
                }
            }
            
            $sql_builder = [];
            $query = EzfUiFunc::queryBuilderById($sql_id, $sql_params);
            
            $pageSize = 50;
            if(isset($options['page_size']) && !empty($options['page_size'])){
                $pageSize = $options['page_size'];
            }
            
            $dataProvider = new \yii\data\ActiveDataProvider([
                            'query' => $query,
//                            'totalCount' => $count,
//                            'sql' => $query->createCommand()->sql,
//                            'params' => $query->params,
                            'pagination' => [
                                'pageSize' => $pageSize,
                            ],
                        ]);
            
            return $this->renderAjax('_listview', [
                        'sql_id'=>$sql_id,
                        'reloadDiv' => $reloadDiv,
                        'target' => $target,
                        'options' => $options,
                        'dataProvider' => $dataProvider,
                        'sql_builder' => $sql_builder,
                        'get_params' => $get_params,
                        'sql_variable' => $sql_variable,
                        'sql_params' => $sql_params,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionTargetSql($sql_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $options = isset($_GET['options']) ? $_GET['options'] : '';
            $placeholder = isset($_GET['placeholder']) ? $_GET['placeholder'] : 'Search ...';
            
            $options = EzfFunc::stringDecode2Array($options);
            $dataid = '';
            $params = [];
            $sql_params = [];
            $sql_variable = [];
            $get_params = [];
            
            if(isset($options['params_all']) && !empty($options['params_all'])){
                unset($_GET['target']);
                $get_params = ArrayHelper::merge($options['params_all'], $_GET);
                foreach ($get_params as $key_get => $value_get) {
                    $sql_variable["{{$key_get}}"] = $value_get;
                    $sql_params[":{$key_get}"] = $value_get;
                }
            }
            $sql_builder = [];
            $data = [];
            try {
                $model = EzfUiFunc::modelSqlBuilder($sql_id);
                 if($model){
                     $sql_builder = SDUtility::string2Array($model->sql_builder);
                     $query = EzfUiFunc::queryBuilder($sql_builder, $sql_params);
                     if($query){
                         $key_id = substr($options['key_id'], 1, -1);
                         
                         $dataid = isset($options['params_all'][$options['key_name']])?$options['params_all'][$options['key_name']]:'';
                         if(isset($sql_builder['mapping'][$key_id])){
                             $field = $sql_builder['mapping'][$key_id];
                             $query->andWhere("$field = $dataid");
                         }
                         
                         $data = $query->createCommand()->queryOne();
                         
                     }
                 }
                 
             } catch (\yii\base\Exception $e) {
             }
             
            
            
            return $this->renderAjax('_target_sql', [
                        'sql_id'=>$sql_id,
                        'reloadDiv' => $reloadDiv,
                        'target' => $target,
                        'options' => $options,
                        'data' => $data,
                        'dataid' => $dataid,
                        'sql_builder' => $sql_builder,
                        'get_params' => $get_params,
                        'sql_variable' => $sql_variable,
                        'sql_params' => $sql_params,
                        'placeholder' => $placeholder,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionSelectSql($sql_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $options = isset($_GET['options']) ? $_GET['options'] : '';
            $placeholder = isset($_GET['placeholder']) ? $_GET['placeholder'] : 'Search ...';
            
            $options = EzfFunc::stringDecode2Array($options);
            $dataid = '';
            $params = [];
            $sql_params = [];
            $sql_variable = [];
            $get_params = [];
            
            if(isset($options['params_all']) && !empty($options['params_all'])){
                unset($_GET['target']);
                $get_params = ArrayHelper::merge($options['params_all'], $_GET);
                
                foreach ($get_params as $key_get => $value_get) {
                    $sql_variable["{{$key_get}}"] = $value_get;
                    $sql_params[":{$key_get}"] = $value_get;
                }
            }
            $sql_builder = [];
            $data = [];
            try {
                $model = EzfUiFunc::modelSqlBuilder($sql_id);
                 if($model){
                     $sql_builder = SDUtility::string2Array($model->sql_builder);
                     $query = EzfUiFunc::queryBuilder($sql_builder, $sql_params);
                     if($query){
                         $key_id = substr($options['key_id'], 1, -1);
                         
                         $dataid = isset($options['params_all'][$options['key_name']])?$options['params_all'][$options['key_name']]:'';
                         if(isset($sql_builder['mapping'][$key_id])){
                             $field = $sql_builder['mapping'][$key_id];
                             $query->andWhere("$field = $dataid");
                         }
                         
                         $data = $query->createCommand()->queryOne();
                     }
                 }
                 
             } catch (\yii\base\Exception $e) {
             }
             
            return $this->renderAjax('_select_sql', [
                        'sql_id'=>$sql_id,
                        'reloadDiv' => $reloadDiv,
                        'target' => $target,
                        'options' => $options,
                        'data' => $data,
                        'dataid' => $dataid,
                        'sql_builder' => $sql_builder,
                        'get_params' => $get_params,
                        'sql_variable' => $sql_variable,
                        'sql_params' => $sql_params,
                        'placeholder' => $placeholder,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionDropdownMenu($sql_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $options = isset($_GET['options']) ? $_GET['options'] : '';
            
            $options = EzfFunc::stringDecode2Array($options);
            
            $params = [];
            $sql_params = [];
            $sql_variable = [];
            $get_params = [];
            
            if(isset($options['params_all']) && !empty($options['params_all'])){
                unset($_GET['target']);
                $get_params = ArrayHelper::merge($options['params_all'], $_GET);
                
                foreach ($get_params as $key_get => $value_get) {
                    $sql_variable["{{$key_get}}"] = $value_get;
                    $sql_params[":{$key_get}"] = $value_get;
                }
            }
            
            $sql_builder = [];
            $query = EzfUiFunc::queryBuilderById($sql_id, $sql_params);
            
            $count = $query->count();
            
            return $this->renderAjax('_dropdown_menu', [
                        'sql_id'=>$sql_id,
                        'reloadDiv' => $reloadDiv,
                        'target' => $target,
                        'options' => $options,
                        'sql_builder' => $sql_builder,
                        'get_params' => $get_params,
                        'sql_variable' => $sql_variable,
                        'sql_params' => $sql_params,
                        'count' => $count,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionDropdownItems($sql_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $options = isset($_GET['options']) ? $_GET['options'] : '';
            
            $options = EzfFunc::stringDecode2Array($options);
            
            $params = [];
            $sql_params = [];
            $sql_variable = [];
            $get_params = [];
            
            if(isset($options['params_all']) && !empty($options['params_all'])){
                unset($_GET['target']);
                $get_params = ArrayHelper::merge($options['params_all'], $_GET);
                
                foreach ($get_params as $key_get => $value_get) {
                    $sql_variable["{{$key_get}}"] = $value_get;
                    $sql_params[":{$key_get}"] = $value_get;
                }
            }
            
            $sql_builder = [];
            $query = EzfUiFunc::queryBuilderById($sql_id, $sql_params);
            
            $pageSize = 50;
            if(isset($options['page_size']) && !empty($options['page_size'])){
                $pageSize = $options['page_size'];
            }
            
            $offset = 0;
            if(isset($_GET['offset']) && !empty($_GET['offset'])){
                $offset = $_GET['offset'];
            }
            
            $count = $query->count();
            
            $query->offset($offset);
            $query->limit($pageSize);
            $data = $query->all();
            
            $offset = $offset + $pageSize;
            
            $html = '';
            if(isset($data) && !empty($data)){
                foreach ($data as $key => $model) {
                    $path = [];
                    foreach ($model as $key_var => $valu_var) {
                        $path["{{$key_var}}"] = $valu_var;
                    }

                    if(isset($options['image']) && !empty($options['image'])){
                        $width = isset($options['image_wigth'])?$options['image_wigth']:64;
                        $url = Yii::getAlias('@storageUrl/ezform/fileinput/');
                        $src = Yii::getAlias('@storageUrl/images/nouser.png');
                        if(isset($path[$options['image']]) && !empty($path[$options['image']])){
                            $src = $url . $path[$options['image']];
                        } 
                        $path["{image}"] = \yii\helpers\Html::img($src, ['class'=>'media-object img-rounded', 'width'=>$width]);

                    }

                    $key_id = '';
                    if(isset($options['key_id']) && !empty($options['key_id']) && isset($path[$options['key_id']])){
                        //$path['{key_id}'] = $path[$options['key_id']];
                        $key_id = $path[$options['key_id']];
                    }


                    $fix_path = $get_params;

                    $path['{title}'] = $options['title'];
                    $path['{width}'] = $options['width'];
                    $path['{image_wigth}'] = $options['image_wigth'];
                    $path['{module}'] = $fix_path['id'];
                    unset($fix_path['id']);
                    unset($fix_path['options']);

                    foreach ($fix_path as $key_get => $value_get) {
                        $path["{{$key_get}}"] = $value_get;
                    }

                    unset($get_params['sql_id']);
                    unset($get_params['reloadDiv']);
                    unset($get_params['options']);
                    $url = ['/ezmodules/ezmodule/view'];
                    
                   $item_params = $get_params;
                    if(isset($options['query_params']) && !empty($options['query_params']) ){
                        $query_params = strtr($options['query_params'], $path);
                        $arryq = explode('&', $query_params);
                         
                        if(isset($arryq) && !empty($arryq)){
                            foreach ($arryq as $keyq => $valueq) {
                                $arryq_var = explode('=', $valueq);
                                $var_name = isset($arryq_var[0])?$arryq_var[0]:\appxq\sdii\utils\SDUtility::getMillisecTime();
                                $var_value = isset($arryq_var[1])?$arryq_var[1]:'';
                                if($var_value=='{key_id}'){
                                    $var_value = $key_id;
                                    
                                    $path['{active}'] = isset($get_params[$var_name]) && $key_id == $get_params[$var_name]?'active':'';
                                    
                                }

                                $item_params[$var_name] = $var_value;
                            }
                        }
                    }

                    $path['{url}'] = \yii\helpers\Url::to(\yii\helpers\ArrayHelper::merge($url, $item_params));

                    $template = isset($options['template_content'])?$options['template_content']:'';
                    $template = str_replace('<ul>', '', $template);
                    $template = str_replace('</ul>', '', $template);
                    
                    $html .= strtr($template, $path);
                }
                
                if($offset <= $count) {
                    $url = \yii\helpers\Url::to(['/ezforms2/ezform-data/dropdown-items',
                    'sql_id' => $sql_id,
                    'reloadDiv' => $reloadDiv,
                    'target' => $target,
                    'offset' => $offset,
                    'options' => EzfFunc::arrayEncode2String($options),
                    ]);
                    
                    $html .= '<li><a class="more-items text-center" href="'.$url.'"><strong>More Items</strong></a></li>';
                }
            }
            
            return $html;
            
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionTarget($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
            $fields = isset($_GET['fields']) ? $_GET['fields'] : '';
            $fields_search = isset($_GET['fields_search']) ? $_GET['fields_search'] : '';
            $options = isset($_GET['options']) ? $_GET['options'] : '';
            $current_url = isset($_GET['current_url']) ? $_GET['current_url'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $placeholder = isset($_GET['placeholder']) ? $_GET['placeholder'] : 'Search ...';
            
            $fields = EzfFunc::stringDecode2Array($fields);
            $fields_search = EzfFunc::stringDecode2Array($fields_search);
            $options = EzfFunc::stringDecode2Array($options);
            $current_url = base64_decode($current_url);
            
            $modelEzf = EzfQuery::getEzformOne($ezf_id);
            if(!$modelEzf){
                $modelEzf = new \backend\modules\ezforms2\models\Ezform();
                $modelEzf->ezf_name = 'Unnamed Form';
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                    ]);
            }
            
            $version = (isset($_GET['v']) && $_GET['v']!='')?$_GET['v']:$modelEzf->ezf_version;

            //fix version by dataid
            if ($dataid != '') {
                $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $dataid);
                if ($modelZdata) {
                    if($modelZdata->rstat!=0 && !empty($modelZdata->ezf_version)){
                        $version = $modelZdata->ezf_version;
                    }
                    if(!empty($modelZdata->ezf_version)){
                        $modelEzf->ezf_version = $modelZdata->ezf_version;
                    }
                } else {
                    return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                    ]);
                }
            }
            
            if($modelEzf->enable_version){
                $modelVersion = EzfQuery::getEzformConfigApprov($modelEzf->ezf_id, $version);
            } else {
                $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $version);
            }
            if($modelVersion){
                $modelEzf->field_detail = $modelVersion->field_detail;
                $modelEzf->ezf_sql = $modelVersion->ezf_sql;
                $modelEzf->ezf_js = $modelVersion->ezf_js;
                $modelEzf->ezf_error = $modelVersion->ezf_error;
                $modelEzf->ezf_options = $modelVersion->ezf_options;
            } else {
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No version found.'),
                ]);
            }
        
            $modelFields = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);

            Yii::$app->session['show_varname'] = 0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            //Yii::$app->session['ezform'] = $modelEzf->attributes;

            $userProfile = Yii::$app->user->identity->profile;

            $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
            
            if($dataid != ''){
                $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);
                
                if (!$model) {// dataid ส่งมาผิดหาไม่เจอ / ไม่คิดรวมถ้าส่ง '' มา
                    return '<div class="alert alert-danger" role="alert"> '.SDHtml::getMsgWarning().' '.Yii::t('app', 'No results found.').'</div>';
                }
            }
            
            return $this->renderAjax('_target_widget', [
                        'modelFields' => $modelFields,
                        'model' => $model,
                        'ezf_id'=>$ezf_id,
                        'modelEzf' => $modelEzf,
                        'modal' => $modal,
                        'dataid' => $dataid,
                        'reloadDiv' => $reloadDiv,
                        'fields' => $fields,
                        'fields_search' => $fields_search,
                        'target' => $target,
                        'targetField' => $targetField,
                        'options' => $options,
                        'current_url' => $current_url,
                        'placeholder' => $placeholder,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionSearch($q = null) {
        if (Yii::$app->getRequest()->isAjax) {
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : 0;
            $fields = isset($_GET['fields']) ? $_GET['fields'] : '';
            $fields_search = isset($_GET['fields_search']) ? $_GET['fields_search'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $ageField = isset($_GET['ageField']) ? $_GET['ageField'] : '';
            
            $fields = EzfFunc::stringDecode2Array($fields);
            $fields_search = EzfFunc::stringDecode2Array($fields_search);
            if(empty($fields_search)){
                $fields_search = $fields;
            }
            
            $modelEzf = EzfQuery::getEzformOne($ezf_id);
            $version = (isset($_GET['v']) && $_GET['v']!='')?$_GET['v']:$modelEzf->ezf_version;

            //fix version by dataid
            if ($dataid != '') {
                $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $dataid);
                if ($modelZdata) {
                    if($modelZdata->rstat!=0 && !empty($modelZdata->ezf_version)){
                        $version = $modelZdata->ezf_version;
                    }
                    if(!empty($modelZdata->ezf_version)){
                        $modelEzf->ezf_version = $modelZdata->ezf_version;
                    }
                } else {
                    return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                    ]);
                }
            }
            
            if($modelEzf->enable_version){
                $modelVersion = EzfQuery::getEzformConfigApprov($modelEzf->ezf_id, $version);
            } else {
                $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $version);
            }
            if($modelVersion){
                $modelEzf->field_detail = $modelVersion->field_detail;
                $modelEzf->ezf_sql = $modelVersion->ezf_sql;
                $modelEzf->ezf_js = $modelVersion->ezf_js;
                $modelEzf->ezf_error = $modelVersion->ezf_error;
                $modelEzf->ezf_options = $modelVersion->ezf_options;
            } else {
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No version found.'),
                ]);
            }
        
            $modelFields = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);

            Yii::$app->session['show_varname'] = 0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            //Yii::$app->session['ezform'] = $modelEzf->attributes;

            $userProfile = Yii::$app->user->identity->profile;
            
            $sitecode = Yii::$app->user->identity->profile['sitecode'];
            
            $searchModel = new TbdataAll();
            $searchModel->setTableName($modelEzf->ezf_table);

            if($targetField==''){
                $modelTarget = EzfQuery::getTargetOne($modelEzf->ezf_id);
                if ($modelTarget) {
                    $targetField = $modelTarget['ezf_field_name'];
                }
            }

            if($target != ''){
                $searchModel[$targetField] = $target;
            }
           
            $dataProvider = EzfUiFunc::modelSearchSelect2($searchModel, $modelEzf, $targetField, $fields_search, $q);
            
            $dataProvider->pagination->page = $page-1;
            
            $data_items = [];
            $models = $dataProvider->getModels();

            foreach ($models as $i => $model){//แปลงข้อมูล
                $data_attr = $model->attributes;
                foreach ($modelFields as $key => $value) {
                    $var = $value['ezf_field_name'];
                    $label = $value['ezf_field_label'];
                    if(in_array($var, $fields)){
                        $dataInput;
                        if (isset(Yii::$app->session['ezf_input'])) {
                            $dataInput = EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
                        }
                        $data_attr[$var] = EzfUiFunc::getValueEzform($dataInput, $value, $model);
                        
                    }
                    if($ageField==$var){
                        $data_attr['fix_age'] = isset($data_attr[$var]) && $data_attr[$var]!=''?\appxq\sdii\utils\SDdate::getAgeMysqlDate($data_attr[$var]):'';
                    }
                }
                $data_attr['id'] = "{$data_attr['id']}";
                $data_items[] = $data_attr;
            }
            
            $result['items'] = $data_items;
            $result['total_count'] = $dataProvider->getTotalCount();
            return \yii\helpers\Json::encode($result);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionSearchEzsql($sql_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $options = isset($_GET['options']) ? $_GET['options'] : '';
            
            $options = EzfFunc::stringDecode2Array($options);
            
            $params = [];
            $sql_params = [];
            $sql_variable = [];
            $get_params = [];
            
            if(isset($options['params_all']) && !empty($options['params_all'])){
                unset($_GET['target']);
                $get_params = ArrayHelper::merge($options['params_all'], $_GET);
                
                foreach ($get_params as $key_get => $value_get) {
                    $sql_variable["{{$key_get}}"] = $value_get;
                    $sql_params[":{$key_get}"] = $value_get;
                }
            }

            $sql_builder = [];
            $query = EzfUiFunc::queryBuilderById($sql_id, $sql_params);
            $query->limit(isset($options['page_size'])?$options['page_size']:10);
            //\appxq\sdii\utils\VarDumper::dump($query->createCommand()->rawSql);
            $data_items = $query->createCommand()->queryAll();
            
            $result['items'] = $data_items;
            $result['total_count'] = $query->count();
            return \yii\helpers\Json::encode($result);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionEzmap() {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $addmap = isset($_GET['addmap'])?$_GET['addmap']:0;
            $sdate = isset($_GET['sdate'])?$_GET['sdate']:'2017-01-01';
            $edate = isset($_GET['edate'])?$_GET['edate']:date('Y-m-d');
            $lat_init = isset($_GET['lat_init'])?$_GET['lat_init']:'16.0148725';
            $lng_init = isset($_GET['lng_init'])?$_GET['lng_init']:'101.8819517';
            $zoom_init = isset($_GET['zoom_init'])?$_GET['zoom_init']:9;
            $forms = isset($_GET['forms'])?$_GET['forms']:'';
            
            $forms = EzfFunc::stringDecode2Array($forms);
            
            return $this->renderAjax('_ezmap', [
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'target' => $target,
                        'addmap' => $addmap,
                        'sdate' => $sdate,
                        'edate' => $edate,
                        'lat_init' => $lat_init,
                        'lng_init' => $lng_init,
                        'zoom_init' => $zoom_init,
                        'forms' => $forms,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionEzmapUrl() {
        if (Yii::$app->getRequest()->isAjax) {
            $lat = isset($_GET['lat']) ? $_GET['lat'] : '';
            $lng = isset($_GET['lng']) ? $_GET['lng'] : '';
            $lat_field = isset($_GET['lat_field']) ? $_GET['lat_field'] : '';
            $lng_field = isset($_GET['lng_field'])?$_GET['lng_field']:'';
            $url = isset($_GET['url'])?$_GET['url']:'';
            
            $initdata = [
                $lat_field => $lat,
                $lng_field => $lng
            ];
            
            $initdata = EzfFunc::arrayEncode2String($initdata);
            $new_url = \yii\helpers\Url::current(['initdata'=>$initdata]);
            
            return $new_url;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionEzcalendar() {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $defaultView = isset($_GET['defaultView']) ? $_GET['defaultView'] : 'month';
            $forms = isset($_GET['forms'])?$_GET['forms']:'';
            $search_cal = isset($_POST['search_cal'])?$_POST['search_cal']:'';
            
            $forms = EzfFunc::stringDecode2Array($forms);
            $view_menu = isset($_GET['view_menu'])?$_GET['view_menu']:'';
            $view_menu = EzfFunc::stringDecode2Array($view_menu);
            
            if(empty($view_menu)){
                $view_menu = ['month', 'agendaWeek', 'agendaDay'];
            }
            $view_menu = implode(',', $view_menu);
            
            $now_date = isset($_GET['now_date']) ? $_GET['now_date'] : date('Y-m-d');
            $eventSources = [];
            
            return $this->renderAjax('_ezcalendar', [
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'target' => $target,
                        'now_date' => $now_date,
                        'forms' => $forms,
                        'eventSources' => $eventSources,
                        'defaultView' => $defaultView,
                        'view_menu' => $view_menu,
                        'search_cal' => $search_cal,
                       
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    //_grid_column
    public function actionGridColumn($sql_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $options = isset($_GET['options']) ? $_GET['options'] : '';
            $params = isset($_GET['params']) ? $_GET['params'] : '';
            $pageSize = isset($_GET['pageSize']) ? $_GET['pageSize'] : 10;
            $key_id = isset($_GET['key_id']) ? $_GET['key_id'] : '';
            
            $data_column = isset($_GET['data_column']) ? $_GET['data_column'] : '';
            $header = isset($_GET['header']) ? $_GET['header'] : '';
            $actions = isset($_GET['actions']) ? $_GET['actions'] : '';
            
            $data_column = EzfFunc::stringDecode2Array($data_column);
            $header = EzfFunc::stringDecode2Array($header);
            $actions = EzfFunc::stringDecode2Array($actions);
            $options = EzfFunc::stringDecode2Array($options);
            $params = EzfFunc::stringDecode2Array($params);
            
            
            $sql_params = [];
            $sql_variable = [];
            $get_params = [];
            
            unset($_GET['target']);
            $get_params = ArrayHelper::merge($params, $_GET);

            foreach ($get_params as $key_get => $value_get) {
                $sql_variable["{{$key_get}}"] = $value_get;
                $sql_params[":{$key_get}"] = $value_get;
            }

            $query;
            try {
                $model_sql = EzfUiFunc::modelSqlBuilder($sql_id);
                 if($model_sql){
                     $sql_builder = SDUtility::string2Array($model_sql->sql_builder);
                     $query = EzfUiFunc::queryBuilder($sql_builder, $sql_params);
                 }
                 
                 if(empty($data_column)){
                    $data_column = isset($model_sql['variable'])?$model_sql['variable']:[];
                }
                
                $find_params = isset($sql_builder['params'])?$sql_builder['params']:[];

             } catch (\yii\base\Exception $e) {
                 \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                 return $e->getMessage();
             }
             
             if(!isset($query)){
                 return Yii::t('app', 'No results found.');
             }
             
            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => $query,
//                            'key' => $key_id,
                'pagination' => [
                    'pageSize' => $pageSize,
                ],
            ]);
            
            if(!empty($key_id)){
                $dataProvider->key = $key_id;
            }
            
            return $this->renderAjax('_grid_column', [
                'dataProvider' => $dataProvider,
                'modal' => $modal,
                'reloadDiv' => $reloadDiv,
                'model_sql' => $model_sql,
                'params' => $params,
                'pageSize' => $pageSize,
                'get_params' => $get_params,
                'find_params' => $find_params,
                'sql_id' => $sql_id,
                'data_column' => $data_column,
                'actions' => $actions,
                'header' => $header,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
}
