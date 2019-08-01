<?php

namespace backend\modules\tctr\controllers;

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
use backend\modules\tctr\classes\TctrFunction;
/**
 * Description of EzformDataController
 *
 * @author appxq
 */
class TctrDataController extends Controller {

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
    public function actionEzform($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
            $initdata = isset($_GET['initdata']) ? EzfFunc::stringDecode2Array($_GET['initdata']) : [];
            $db2 = isset($_GET['db2']) ? $_GET['db2'] : 0;
            $disable = [];
            
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
                $modelNewRecord = EzfUiFunc::loadNewRecord($model, $modelEzf->ezf_table, $userProfile->user_id);

                if ($modelNewRecord) {
                    $targetReset = true;
                    $model = $modelNewRecord;
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
                                'initdata' => $initdata,
                                'type' => 2,
                                'modelVersion' => $modelVersion,
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
            
            if ($model->load(Yii::$app->request->post())) {
               
                Yii::$app->response->format = Response::FORMAT_JSON;

                $rstat = Yii::$app->request->post('submit') ? Yii::$app->request->post('submit') : $model->rstat;

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
                    return $result;
                }

                $result = EzfUiFunc::saveData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $model->id);
                
                return $result;
            }
            
            return $this->renderAjax('_ezform', [
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
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    
    public function  actionEzformView($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $initdata = isset($_GET['initdata']) ? EzfFunc::stringDecode2Array($_GET['initdata']) : [];
            $db2 = isset($_GET['db2']) ? $_GET['db2'] : 0;
            $type = isset($_GET['type']) ? $_GET['type'] : 'view';
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

            $userid = Yii::$app->user->id;
            $table = $modelEzf->ezf_table ;
            $sql = "select * FROM $table WHERE id = :id ";
            $data = Yii ::$app->db->createCommand($sql, [':id'=>$dataid])->queryOne();
            $primary = TctrFunction::QueryData($dataid,'zdata_tctr_part_primary','primary_target');
            $secondary = TctrFunction::QueryData($dataid,'zdata_tctr_part_secondary','sec_target');
            $intervention = TctrFunction::QueryData($dataid,'zdata_tctr_part_intervention','target_inven');
            $Arraysectionb = TctrFunction::QuerysectionB($dataid,'zdata_tctr_part_sectionb','facility_target');
            $sectionC = TctrFunction::QueryData($dataid,'zdata_tctr_part_sectionc','contact_target');
            $sectiondata='';
            $sectionB='';
            if(!empty($Arraysectionb)){
                foreach ($Arraysectionb as $key => $value) {
                    $sectiondata[] =  $value['site_country'];
                }
                $sectionB = join(',',$sectiondata);
            }
            if ($dataid == '') {
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No results found.'),
                ]);
            }
            return $this->renderAjax('_ezform-view', [
                        'ezf_id' => $ezf_id,
                        'modelEzf' => $modelEzf,
                        'modelFields' => $modelFields,
                        'modal' => $modal,
                        'primary' => $primary,
                        'secondary' =>$secondary,
                        'intervention' => $intervention,
                        'sectionB' => $sectionB,
                        'sectionC' => $sectionC,
                        'data' => $data,
                        'reloadDiv' => $reloadDiv,
                        'initdata' => $initdata,
                        'modelVersion' => $modelVersion,
                        'typeview' =>$type,
                        'userid' => $userid,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
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
            $addbtn = isset($_GET['addbtn']) ? $_GET['addbtn'] : 1;
            $default_column = isset($_GET['default_column']) ? $_GET['default_column'] : 1;
            $pageSize = isset($_GET['pageSize']) ? $_GET['pageSize'] : 50;
            $order_column = isset($_GET['order_column']) ? $_GET['order_column'] : '';
            $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 4;
            $db2 = isset($_GET['db2']) ? $_GET['db2'] : 0;
            $search_column = isset($_GET['search_column']) ? $_GET['search_column'] : '';
            $dataid= isset($_GET['dataid']) ? $_GET['dataid'] : '';
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
                if($db2==1){
                    EzfFunc::updateDoubleData($ezform, '');
                    $dataProvider = EzfUiFunc::modelSearchDb2($searchModel, $ezform, $targetField, $data_column, Yii::$app->request->queryParams, $pageSize, $order_column, $orderby);
                } else {
                        $dataProvider = EzfUiFunc::modelSearch($searchModel, $ezform, $targetField, $data_column, Yii::$app->request->queryParams, $pageSize, $order_column, $orderby);
                }
            }
            $view = $popup ? '_view-popup' : '_view';
            
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
                        'addbtn' => $addbtn,
                        'default_column' => $default_column,
                        'pageSize' => $pageSize,
                        'order_column' => $order_column,
                        'orderby' => $orderby,
                        'db2' => $db2,
                        'search_column' => $search_column,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    public static function modelSearch($model, $ezform, $targetField, $colSearch, $params, $pageSize=50, $order_column=[], $orderby=SORT_DESC,$dataid) {
        //$model = new TbdataAll();
        $query = $model->find()->where('rstat not in(0,3)'); //->where('rstat not in(0, 3)');
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
        $model->setColFieldsAddon(['userby', 'sitename']);
        
        //$query->innerJoin('profile', "profile.user_id = {$ezform['ezf_table']}.user_update");
        $query->select([
            "{$ezform['ezf_table']}.*",
            "(SELECT const_hospital.`name` FROM const_hospital WHERE const_hospital.code = {$ezform['ezf_table']}.xsourcex ) AS sitename",
            "(SELECT concat(firstname, ' ', lastname) AS `name` FROM profile WHERE profile.user_id = {$ezform['ezf_table']}.user_update ) AS userby"        
        ]);
        
        if (isset($modelFields) || $ezform['public_listview'] == 2) {
            $query->andWhere('xsourcex = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
        }
        
        if ($ezform['public_listview'] == 3) {
            $query->andWhere('xdepartmentx = :unit', [':unit' => Yii::$app->user->identity->profile->department]);
        }

        if ($ezform['public_listview'] == 0) {
            $query->andWhere("user_create=:created_by", [':created_by' => Yii::$app->user->id]);
        }
        $query->andWhere("id=:dataid", [':dataid' =>$dataid]);
        
        $defaultOrder = [];
        if(empty($order_column)){
            $defaultOrder = [
                'create_date' => $orderby
            ];
        } else {
            foreach ($order_column as $rkey => $rvalue) {
                $defaultOrder[$rvalue] = (int)$orderby;
            }
        }
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
            'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
                'defaultOrder' => $defaultOrder
            ]
        ]);

        $model->load($params);

        if (isset($model['create_date']) && !empty($model['create_date'])) {
            $daterang = explode(' to ', $model['create_date']);
            if (isset($daterang[1])) {
                $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                $query->andFilterWhere(['between', "date({$ezform['ezf_table']}.create_date)", $sdate, $edate]);
            }
        }
        
        if ($targetField!='') {
            $query->andFilterWhere(['like', $targetField, $model[$targetField]]);
        }

        $colSearch = \yii\helpers\ArrayHelper::merge($colSearch, ['id', 'sitecode', 'ptid', 'target', 'xsourcex', 'ptcode', 'hptcode', 'hsitecode', 'rstat']);
//        $query->andFilterWhere([
//            'id' => $model->id,
//        ]);

        foreach ($colSearch as $field) {
            if (is_array($field)) {
                if (isset($field['attribute'])) {
                    $query->andFilterWhere(['like', $field['attribute'], $model[$field['attribute']]]);
                }
            } else {
                $query->andFilterWhere(['like', $field, $model[$field]]);
            }
        }


        return $dataProvider;
    }
    public function actionDataTable() {
        $options = isset($_POST) ? $_POST :null;
        return $this->renderAjax("_datatable", [
                'options' =>$options,
            ]);
    }
    public function actionBackgroundInsert() {
        $MainForm = TctrFunction::backgroundInsert('1520776142078903600', '', '', '', '');
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $MainForm ;
    }
}
