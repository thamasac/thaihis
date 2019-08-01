<?php

namespace backend\modules\thaihis\controllers;

use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\VarDumper;
use backend\modules\api\v1\classes\LogStash;
use backend\modules\api\v1\classes\Nhso;
use backend\modules\api\v1\classes\PatientRight;
use Yii;
use backend\modules\thaihis\classes\ThaiHisQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\ezforms2\models\TbdataAll;
use backend\modules\thaihis\classes\ThaiHisFunc;
use yii\db\Expression;
use yii\db\Query;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use backend\modules\patient\classes\PatientQuery;

class PatientVisitController extends \yii\web\Controller {

    public function actionGetFormRef() {
        $ezf_id = Yii::$app->request->post('ezf_id', 0);
        $name = Yii::$app->request->post('name', 0);
        $value_ref = Yii::$app->request->post('value_ref', 0);
        $multiple = Yii::$app->request->post('multiple', 0);
        $id = Yii::$app->request->post('id');

        $dataForm = ThaiHisQuery::getEzformRefAll($ezf_id);

        return $this->renderAjax('_ref_form', [
                    'ezf_id' => $ezf_id,
                    'dataForm' => \yii\helpers\ArrayHelper::map($dataForm, 'id', 'name'),
                    'multiple' => $multiple,
                    'value' => $value_ref,
                    'name' => $name,
                    'id' => $id,
        ]);
    }

    public function actionGetFormRef2() {
        $ezf_id = Yii::$app->request->post('ezf_id', 0);
        $name = Yii::$app->request->post('name', 0);
        $value_ref = Yii::$app->request->post('value_ref', 0);
        $multiple = Yii::$app->request->post('multiple', 0);
        $id = Yii::$app->request->post('id');

        $dataForm = ThaiHisQuery::getEzformRefAll2($ezf_id, $value_ref);

        return $this->renderAjax('_ref_form', [
                    'ezf_id' => $ezf_id,
                    'dataForm' => \yii\helpers\ArrayHelper::map($dataForm, 'id', 'name'),
                    'multiple' => $multiple,
                    'value' => $value_ref,
                    'name' => $name,
                    'id' => $id,
        ]);
    }

    public function actionGetFieldsForms() {
        $ezf_id = Yii::$app->request->post('ezf_id', 0);
        $main_ezf_id = Yii::$app->request->post('main_ezf_id', 0);
        $name = Yii::$app->request->post('name', 0);
        $value = Yii::$app->request->post('value', 0);
        $multiple = Yii::$app->request->post('multiple', 0);
        $id = Yii::$app->request->post('id');
        $dataForm = [];
        if ($ezf_id) {
            foreach ($ezf_id as $value_ezf_id) {
                foreach (ThaiHisQuery::getFields($value_ezf_id) as $value_field) {
                    $dataForm[] = $value_field;
                }
            }
        }

        if ($main_ezf_id) {
            foreach (ThaiHisQuery::getFields($main_ezf_id) as $value_field) {
                $dataForm[] = $value_field;
            }
        }
        return $this->renderAjax('_ref_form', [
                    'ezf_id' => $ezf_id,
                    'dataForm' => \yii\helpers\ArrayHelper::map($dataForm, 'id', 'name'),
                    'multiple' => $multiple,
                    'value' => $value,
                    'name' => $name,
                    'id' => $id,
        ]);
    }

    public function actionGetFieldsForms2() {
        $ezf_id = Yii::$app->request->post('ezf_id', 0);
        $main_ezf_id = Yii::$app->request->post('main_ezf_id', 0);
        $name = Yii::$app->request->post('name', 0);
        $value = Yii::$app->request->post('value', 0);
        $multiple = Yii::$app->request->post('multiple', 0);
        $id = Yii::$app->request->post('id');
        $dataForm = [];
        if (is_array($ezf_id)) {
            foreach ($ezf_id as $value_ezf_id) {
                foreach (ThaiHisQuery::getFields2($value_ezf_id) as $value_field) {
                    $dataForm[] = $value_field;
                }
            }
        } else {
            foreach (ThaiHisQuery::getFields2($ezf_id) as $value_field) {
                $dataForm[] = $value_field;
            }
        }

        if ($main_ezf_id) {
            foreach (ThaiHisQuery::getFields2($main_ezf_id) as $value_field) {
                $dataForm[] = $value_field;
            }
        }
        return $this->renderAjax('_ref_form', [
                    'ezf_id' => $ezf_id,
                    'dataForm' => \yii\helpers\ArrayHelper::map($dataForm, 'id', 'name'),
                    'multiple' => $multiple,
                    'value' => $value,
                    'name' => $name,
                    'id' => $id,
        ]);
    }

    public function actionVisitContent() {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
            $initdata = isset($_GET['initdata']) ? $_GET['initdata'] : false;
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $widget_id = isset($_GET['widget_id']) ? $_GET['widget_id'] : '';
            $key_gen = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $options = isset($_GET['options']) ? $_GET['options'] : '';
            $visit_id = isset($_GET['visitid']) ? $_GET['visitid'] : '';
            $visit_type = isset($_GET['visit_type']) ? $_GET['visit_type'] : '';
            //$tabs = isset($_POST['tabs']) ? $_POST['tabs'] : '';
            $widgetData = \backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($widget_id);
            $widget_opt = \appxq\sdii\utils\SDUtility::string2Array($widgetData['options']);
            $options = EzfFunc::stringDecode2Array($options);
            $tabs = isset($widget_opt['tabs']) ? $widget_opt['tabs'] : null;

            if (!isset(Yii::$app->session['ezf_input'])) {
                Yii::$app->session['show_varname'] = 0;
                Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            }


            $modelFields_tabs = [];
            $model_tabs = [];
            $fields = '';
            $forms = '';
            $left_forms = '';
            $ezform = '';
            $main_ezform = '';
            $main_ezf_id = '';
            $visit_ezf_id = '';
            if (isset($tabs) && is_array($tabs)) {
                foreach ($tabs as $key => $val) {
                    $ezformTab = EzfQuery::getEzformOne($val['ezf_id']);
                    if ($val['tab_type'] == '1' && isset($val['field_display']) && is_array($val['field_display'])) {
                        $customSelect = null;
                        $main_ezf_id = isset($val['main_ezf_id']) ? $val['main_ezf_id'] : null;
                        $visit_ezf_id = isset($val['ezf_id']) ? $val['ezf_id'] : null;
                        $fields = isset($val['field_display']) ? $val['field_display'] : null;
                        $forms = isset($val['refform']) ? $val['refform'] : null;
                        $forms[] = $main_ezf_id;
                        $forms[] = $visit_ezf_id;
                        $left_forms = isset($val['left_refform']) ? $val['left_refform'] : null;
                        $ezform = isset($val['ezf_id']) ? EzfQuery::getEzformOne($val['ezf_id']) : null;
                        $main_ezform = isset($val['main_ezf_id']) ? EzfQuery::getEzformOne($val['main_ezf_id']) : null;

                        $subcontent = isset($val['subcontent']) ? $val['subcontent'] : null;
                        if ($subcontent != null) {

                            usort($subcontent, function($a, $b) {
                                if (isset($a['suborder']) && $a['suborder'] != '')
                                    return $a['suborder'] - $b['suborder'];
                            });

                            $tabs[$key]['subcontent'] = $subcontent;
                        }
                        $customSelect[] = $main_ezform['ezf_table'] . ".id as dataid";

                        $modelFilter = [];
                        if ($ezform) {
                            $modelFilter = [$ezform['ezf_table'] . '.target=' . $target];
                            if ($visit_id && $visit_id != '')
                                $modelFilter[] = $ezform['ezf_table'] . '.id=' . $visit_id;
                        }else {
                            $modelFilter = [$main_ezform['ezf_table'] . '.id=' . $target];
                        }


                        $subquery = new Query();
                        $subquery->select('*')
                                ->from('zdata_patientright')
                                ->where(['zdata_patientright.ptid' => $target])
                                ->andWhere(' zdata_patientright.rstat NOT IN(0,3) ')
                                ->orderBy('zdata_patientright.create_date DESC')
                                ->limit(1);
                        if ($main_ezform)
                            $reponseQuery = ThaiHisQuery::getDynamicQuery($fields, $forms, $main_ezform, null, null, null, $customSelect, $modelFilter, null, $left_forms, ['column' => 'create_date', 'order' => 'DESC'], null, null, $subquery);

                        //$data_tab = ThaiHisFunc::modelSearch($searchModelTab, $ezformTab, $targetField, $ezformParent, $fieldsTab, $modelFilterTab, 0, Yii::$app->request->queryParams);
                        if (isset($reponseQuery['modelDynamic']))
                            $model_tabs[$key] = $reponseQuery['modelDynamic'];

                        if (isset($reponseQuery['modelFields']))
                            $modelFields_tabs[$key] = $reponseQuery['modelFields'];
                    } else {
                        $modelFields_tabs[$key] = null;
                    }
                }
            }

            $view = '_content_data';
            if (isset($tabs) && is_array($tabs)) {
                $view = '../patient-visit/_content_tabs';
            }

            return $this->renderAjax($view, [
//                        'modelFields' => $modelFields,
//                        'model' => $model,
                        'ezf_id' => $main_ezf_id,
                        'visit_ezf_id' => $visit_ezf_id,
                        'modelEzf' => $ezform,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'fields' => $fields,
                        'target' => $target,
                        'targetField' => $targetField,
                        'initdata' => $initdata,
                        'dataid' => $dataid,
                        'visitid' => $visit_id,
                        'visit_type' => $visit_type,
                        'widget_id' => $widget_id,
                        'options' => $options,
                        'tabs' => $tabs,
                        'modelFields_tabs' => $modelFields_tabs,
                        'model_tabs' => $model_tabs,
                        'key_gen' => $key_gen,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    /**
     * main working table zdata_visit and patient_right
     * @param $pt_id
     * @param $ezf_id
     * @param $visit_type
     * @param null $dept
     * @param null $modelVisit
     * @return \appxq\sdii\models\SDDynamicModel|array|bool
     * @throws HttpException
     * @throws \yii\db\Exception
     */
    public static function actionSaveVisit($pt_id, $ezf_id, $visit_type, $dept = null, $modelVisit = null) {

        Yii::$app->response->format = Response::FORMAT_JSON;
        $modelVisit = \appxq\sdii\utils\SDUtility::string2Array($modelVisit);
        $modelEzf = EzfQuery::getEzformOne($ezf_id);
        $profileTable = \backend\modules\patient\Module::$formTableName['profile'];
        $cid = (new Query())->select('pt_cid')->from($profileTable)->where(['ptid' => $pt_id])->scalar();
        //init data visit
        $data['visit_date'] = date('Y-m-d H:i:s');
        $data['visit_status'] = '1';
        $data['visit_type'] = $visit_type;
        $visit_id = isset($modelVisit['id']) ? $modelVisit['id'] : null;
        $result = [];

        // No Visit model check and get from zdata_visit table
        if ($modelVisit == null) {
            $modelVisit = Yii::$app->db->createCommand('SELECT * FROM zdata_visit WHERE ptid = :ptid and rstat <> 3 and DATE(visit_date) = CURDATE()', [':ptid' => $pt_id])->queryOne();
            LogStash::Log('1', 'actionSaveVisit::modelVisit', '', var_export($modelVisit, true), 'thaihis');
            if ($modelVisit) {
                //update visit
                $result = PatientFunc::saveDataNoSys($ezf_id, $modelEzf->ezf_table, $modelVisit['id'], $data);
                $result['action'] = 'update';
            } else {
                //insert visit
                $result = PatientFunc::backgroundInsert($ezf_id, '', $pt_id, $data);
                $result['action'] = 'insert';
            }
            LogStash::Log('1', 'actionSaveVisit::modelVisit::result', '', var_export($result, true), 'thaihis');
            $visit_id = (string) $result['data']['id'];
        } else {
            $data['visit_date'] = $modelVisit['visit_date'];
            $result = PatientFunc::saveDataNoSys($ezf_id, $modelEzf->ezf_table, $visit_id, $data);
        }
        $data = null;

        if ($visit_type != '1') {
            $visit_dept = self::checkRightByPass($visit_id, $pt_id, $cid, $visit_type, $dept);
        } else {
            $visit_dept = $dept;
        }

        LogStash::Log(Yii::$app->user->id, 'actionSaveVisit', $visit_dept, '', 'thaihis');
        if ($visit_dept != null && $visit_dept != '') {
            if (Yii::$app->user->can('doctor')) {
                $data['visit_tran_doctor'] = Yii::$app->user->identity->profile->user_id;
            }
            $data['visit_tran_dept'] = $visit_dept;
            $data['visit_tran_status'] = '1';
            $dataVT = self::saveVisitTran($pt_id, $visit_id, $data);
        }

        $result['data']['id'] = (string) $visit_id;
        $result['data']['visit_tran_id'] = isset($dataVT['data']['id']) ? (string) $dataVT['data']['id'] : '';
//        $result['data']['app_id'] = isset($dataApp['id']) ? $dataApp['id'] : '';
        // print queue for kiosk
        if (!($visit_type == '2' && $result['data']['visit_tran_id'] == "")) {
            Yii::$app->db->createCommand('UPDATE user_print_queue SET rstat = 1,visit_id = :visit_id WHERE user_id = :user_id AND rstat = 0', [':visit_id' => $visit_id, ':user_id' => Yii::$app->user->id])->execute();
        }
        // End print queue for kiosk
        return $result;
    }

    public static function saveVisitTran($pt_id, $visit_id, $data, $ezf = null) {
        if (empty($ezf)) {
            $ezfVisitTran_id = \backend\modules\patient\Module::$formID['visit_tran'];
            $ezfVisitTran_tbname = \backend\modules\patient\Module::$formTableName['visit_tran'];
        } else {
            $ezfVisitTran_id = $ezf['ezf_id'];
            $ezfVisitTran_tbname = $ezf['ezf_table'];
        }

        if ($data['visit_tran_dept']) {
            //chk visit_tran insert,update
            $dataVisitTran = PatientQuery::getVisitTran($pt_id, $data['visit_tran_dept'], date('Y-m-d'));
            if ($dataVisitTran) {
                $result = PatientFunc::saveDataNoSys($ezfVisitTran_id, $ezfVisitTran_tbname, $dataVisitTran['visit_tran_id'], $data);
            } else {
                $result = PatientFunc::backgroundInsert($ezfVisitTran_id, '', $visit_id, $data);
            }
        } else {
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Find not found.'),
            ];
        }
        return $result;
    }

    public function actionAddNewtab() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $tabs = Yii::$app->request->get('tabs');
        $key_index = Yii::$app->request->get('key_index');
        $act = Yii::$app->request->get('act');
        $firstIndex = Yii::$app->request->get('firstIndex');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/his_patient_visit/_tab', [
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                    'tabs' => $tabs,
                    'key_index' => $key_index,
                    'firstIndex' => $firstIndex,
                    'act' => $act,
        ]);
    }

    public function actionAddNewBoxtab() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $tabs = Yii::$app->request->get('tabs');
        $key_index = Yii::$app->request->get('key_index');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/his_box_content/_tab', [
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                    'tabs' => $tabs,
                    'key_index_tab' => $key_index,
        ]);
    }

    public function actionAddNewcondition() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $main_ezf_id = Yii::$app->request->get('main_ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $act = Yii::$app->request->get('act');
        $conditions = Yii::$app->request->get('conditions');
        $key_index = Yii::$app->request->get('key_index');
        $sub_index = Yii::$app->request->get('sub_index');
        $sub_index = empty($sub_index) ? $key_index : $sub_index;

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/his_cashier/_form', [
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                    'act' => $act,
                    'main_ezf_id' => $main_ezf_id,
                    'conditions' => $conditions,
                    'key_index' => $key_index, 'sub_index' => $sub_index
        ]);
    }

    public function actionAddNewsummary() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $main_ezf_id = Yii::$app->request->get('main_ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $act = Yii::$app->request->get('act');
        $summarys = Yii::$app->request->get('summarys');
        $key_index = Yii::$app->request->get('key_index');
        $sub_index = Yii::$app->request->get('sub_index');
        $nameType = Yii::$app->request->get('nameType');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/his_cashier/_form_1', [
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                    'act' => $act,
                    'main_ezf_id' => $main_ezf_id,
                    'summarys' => $summarys,
                    'key_index' => $key_index,
                    'sub_index' => $sub_index, 'nameType' => $nameType
        ]);
    }

    public function actionContent($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
            $fields = isset($_GET['fields']) ? $_GET['fields'] : '';
            $initdata = isset($_GET['initdata']) ? $_GET['initdata'] : false;
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $options = isset($_GET['options']) ? $_GET['options'] : '';

            $fields = EzfFunc::stringDecode2Array($fields);
            $options = EzfFunc::stringDecode2Array($options);

            $modelEzf = EzfQuery::getEzformOne($ezf_id);
            $version = (isset($_GET['v']) && $_GET['v'] != '') ? $_GET['v'] : $modelEzf->ezf_version;

            //fix version by dataid
            if ($dataid != '') {
                $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $dataid);
                if ($modelZdata) {
                    if ($modelZdata->rstat != 0 && !empty($modelZdata->ezf_version)) {
                        $version = $modelZdata->ezf_version;
                    }
                    if (!empty($modelZdata->ezf_version)) {
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

            if ($modelEzf->enable_version) {
                $modelVersion = EzfQuery::getEzformConfigApprov($modelEzf->ezf_id, $version);
            } else {
                $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $version);
            }
            if ($modelVersion) {
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

            if ($dataid != '') {
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
                if ($initdata) {
                    $modelLastRecord = EzfUiFunc::loadLastRecord($model, $modelEzf->ezf_table, $target);

                    if ($modelLastRecord) {
                        $model = $modelLastRecord;
                    }
                }
            }

            return $this->renderAjax('_content_data', [
                        'modelFields' => $modelFields,
                        'model' => $model,
                        'ezf_id' => $ezf_id,
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

    public static function initSelect2Dept($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';

        if (isset($code) && !empty($code)) {
            $data = EzfQuery::getDepartmentByID($code);

            $str = $data['name'];
        }

        return $str;
    }

    public function actionSearchDept($q = null, $sht) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['results' => []];
        $data = ThaiHisQuery::getDepartmentByName($q, $sht);
        $i = 0;

        foreach ($data as $value) {
            $old_code = isset($value['unit_code_old']) ? $value['unit_code_old'] : '';
            $result["results"][$i] = ['id' => $value['code'], 'text' => $old_code . ' ' . $value["name"]];
            $i++;
        }

        return $result;
    }

    public function actionGetEzformData() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = new Query;
        $res = $query->select(['visit_type_code', 'visit_type_name', 'xdepartmentx'])->from('zdata_visit_type')->all();
        return $res;
    }

    public function actionCreateVisitChoice() {
        $visit_code = Yii::$app->request->post("visit_code", null);
        $query = new Query;
        $visitTypeList = $query->select(['visit_type_code', 'visit_type_name', 'xdepartmentx'])
                ->from('zdata_visit_type')
                ->all();

        return $this->renderAjax("_form_visit_choice", [
                    "visit_code" => $visit_code,
                    "visitTypeList" => $visitTypeList
        ]);
    }

    public function actionSubmitVisitCheckup() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $checkUpItemJson = Yii::$app->request->post('checkup_item');
        $checkUpItem = json_decode($checkUpItemJson, true);
        $visit_id = Yii::$app->request->post('visit_id');
        $pt_id = Yii::$app->request->post('pt_id');
        $patientAge = Yii::$app->request->post('patient_age');
        $insureType = Yii::$app->request->post('insure_type');
        $current_price = Yii::$app->request->post('current_price', '0');
        $orderArray = [];
        foreach ($checkUpItem as $value) {
            if ($value['enable']) {
                array_push($orderArray, $value['order_id']);
            }
        }
        $orderArray = (new Query)->select(['zdata_order_lists.id as id', 'order_code', 'zdata_order_type.order_type_code as type_code'])
                ->from('zdata_order_lists')
                ->innerJoin('zdata_order_type', 'zdata_order_lists.group_type = zdata_order_type.id')
                ->where(['zdata_order_lists.id' => $orderArray])
                ->all();

        if (Yii::$app->getRequest()->isAjax) {
            //checkup

            $visit_type = '1';
            $right_code = $this->checkRightCheckUp($pt_id, $visit_id, $insureType);

            if ($visit_id) {
                $ezf_table = \backend\modules\patient\Module::$formTableName['report_checkup'];
                $dataReportCheckup = PatientFunc::loadTbDataByTarget($ezf_table, $visit_id);
                if (empty($dataReportCheckup)) {
                    $ezf_id = \backend\modules\patient\Module::$formID['report_checkup'];
                    PatientFunc::backgroundInsert($ezf_id, '', $visit_id, ['ckr_status' => '1']);
                }
                $order_status = $visit_type . '^' . $right_code['right_code'] . '^' . $patientAge;
                //check & inser order header 
                $ezf_order_id = \backend\modules\patient\Module::$formID['order_header'];
                $ezf_order_table = \backend\modules\patient\Module::$formTableName['order_header'];
                $userProfile = Yii::$app->user->identity->profile->attributes;
                $order_header = PatientFunc::loadTbDataByField($ezf_order_table, ['order_visit_id' => $visit_id, 'order_dept' => $userProfile['department']]);
                if (empty($order_header)) {
                    $order_header_insert = PatientFunc::backgroundInsert($ezf_order_id, '', $visit_id, ['order_dept' => $userProfile['department']]);
                    if ($order_header_insert['status'] == 'error') {
                        throw new HttpException('backgroundInsert Error' . $order_header['message']);
                    } else {
                        $order_header = $order_header_insert['data'];
                    }
                }

                $checkOrder = OrderController::checkOrderCancelStatus($visit_id);
                foreach ($checkUpItem as $value) {
                    // Query Item
                    $tempIndex = array_search($value['order_id'], array_column($orderArray, 'id'));
                    $itemType = $orderArray[$tempIndex]['type_code'];
                    $itemCode = $orderArray[$tempIndex]['order_code'];

                    // LAB CODE                                       
                    if ($visit_type == '1') {
                        OrderController::saveOrderItem($order_header['id'], $itemCode, $itemType, $order_status);
                    }
                }

                // print queue for kiosk
                Yii::$app->db->createCommand('UPDATE user_print_queue SET data= :price ,rstat = 1,visit_id = :visit_id WHERE user_id = :user_id AND rstat = 1', [':price' => $current_price, ':visit_id' => $visit_id, ':user_id' => Yii::$app->user->id])->execute();
                // End print queue for kiosk
                $result = ['success' => true, 'visit_id' => $visit_id];
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . 'ไม่สามารถส่งตรวจได้ กรุณาลองใหม่',
                ];
            }
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    private function checkRightCheckUp($pt_id, $visit_id, $insureType) {
        $ezfproject_patient_name_id = \backend\modules\patient\Module::$formID['project_patient_name'];
        $ezfproject_patient_name_tbname = \backend\modules\patient\Module::$formTableName['project_patient_name'];
        $data['right_code'] = 'CASH';


        // Check is If Checkup is organize mode
        $projectRecord = PatientQuery::getprojectidByptid($pt_id);
        if (isset($projectRecord) && $projectRecord != false) {
            PatientFunc::saveDataNoSys($ezfproject_patient_name_id, $ezfproject_patient_name_tbname, $projectRecord['id'], ['status_project' => 1]);
            $insureType = '1';
        }
        if ($insureType == '1') {
            $newData = 'NEW';
            $data['right_code'] = 'PRO';
            $data['right_status'] = '2';
            $data['right_project_id'] = $projectRecord['ms_project_id'];
        } elseif ($insureType == '3') {
            $dataHos = PatientQuery::getRightLast($pt_id);
            if (empty($dataHos['right_prove_no'])) {
                $dataProfile = ThaiHisQuery::getPtProfile($pt_id);

                $nhsoJsonData = Nhso::getNhso($dataProfile['pt_cid']);
                $dataOnline = json_decode($nhsoJsonData, true);
                if (empty($dataOnline['status-system'])) {
                    if (isset($dataOnline['maininscl'])) {
                        if ($dataOnline['maininscl'] == 'WEL') {
                            $dataOnline['maininscl'] = 'UCS';
                        }
                    } else {
                        $dataOnline['maininscl'] = '';
                        $dataOnline['hmain_name'] = '';
                        $dataOnline['maininscl_name'] = '';
                        $dataOnline['subinscl'] = '';
                        $dataOnline['hmain_name'] = '';
                    }
                } else {
                    $dataOnline['maininscl'] = '';
                    $dataOnline['hmain_name'] = '';
                }

                if (isset($dataOnline['subinscl']) && $dataOnline['subinscl'] == 'L1') {
                    $newData = 'NEW';
                    $data['right_code'] = 'LGO';
                    $data['right_sub_code'] = 'L1';
                    $data['right_prove_no'] = ''; //$dataNhso['authno']; //นำเลขที่ค้าหาเจอมาลง approve มาลง
                    $data['right_status'] = '2';
                } else {
                    $newData = 'NEW';
                    $data['right_status'] = '2';
                    $data['right_code'] = 'CASH';
                }
            } else {
                $newData = '';
                $data['right_code'] = 'LGO';
            }
        } elseif ($insureType == '4') {
            $newData = 'NEW';
            $data['right_code'] = 'ORI-G';
            $data['right_status'] = '2';
        } elseif ($insureType == '5') {
            $newData = 'NEW';
            $data['right_code'] = 'ORI';
            $data['right_status'] = '2';
        } else {
            $newData = 'NEW';
            $data['right_status'] = '2';
            $data['right_code'] = 'CASH';
        }
        $this->saveRight($visit_id, $data);

        return $data;
    }

    /**
     * @param $visit_id
     * @param $pt_id
     * @param $cid
     * @param $visit_type
     * @param null $dept
     * @return null|string
     * @throws \yii\db\Exception
     * @throws HttpException
     */
    public static function checkRightByPass($visit_id, $pt_id, $cid, $visit_type, $dept = null) {
        //ตรวจสอบ web service สปสช
        $rightData = PatientQuery::getRightByVisitId($visit_id);
        if (!$rightData || $dept == null || $dept == "") {
            $rightData = PatientFunc::getRightOnlineByNhso($cid);
            list($visit_dept, $data) = PatientRight::validateRight($pt_id, $visit_type, $dept, $rightData);
            self::saveRight($visit_id, $data);
        } else {
            $visit_dept = $dept;
        }

        return $visit_dept;
    }

    public static function testSaveRight($visit_id, $data) {
        return self::saveRight($visit_id, $data);
    }

    /**
     * @param $visit_id
     * @param $data
     * @return \appxq\sdii\models\SDDynamicModel|array|bool
     * @throws HttpException
     */
    private static function saveRight($visit_id, $data) {
        //zdata_patientright save or update
        $ezfPtRight_id = \backend\modules\patient\Module::$formID['patientright'];
        $ezfPtRight_tbname = \backend\modules\patient\Module::$formTableName['patientright'];

        $dataHos = PatientFunc::loadDataByTarget($ezfPtRight_id, $ezfPtRight_tbname, $visit_id);
        if ($dataHos) {
            $rightID = PatientFunc::saveDataNoSys($ezfPtRight_id, $ezfPtRight_tbname, $dataHos['id'], $data);
        } else {
            $rightID = PatientFunc::backgroundInsert($ezfPtRight_id, '', $visit_id, $data);
        }
        return $rightID;
    }

    public function actionGetsexByvisit($visit_id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = ThaiHisQuery::getSex($visit_id);

        return $data;
    }

    public function actionEzformContent() {
        $keyTab = Yii::$app->request->get('keyTab');
        $options = Yii::$app->request->get('options');
        $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
        $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
        $target = isset($_GET['target']) ? $_GET['target'] : '';
        $widget_id = isset($_GET['widget_id']) ? $_GET['widget_id'] : '';
        $key_gen = \appxq\sdii\utils\SDUtility::getMillisecTime();
        $visit_id = isset($_GET['visitid']) ? $_GET['visitid'] : '';
        $visit_type = isset($_GET['visit_type']) ? $_GET['visit_type'] : '';

        $widgetData = \backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($widget_id);
        $widget_opt = \appxq\sdii\utils\SDUtility::string2Array($widgetData['options']);
        $options = EzfFunc::stringDecode2Array($options);
        $tabs = isset($widget_opt['tabs']) ? $widget_opt['tabs'] : null;
        $valTab = $tabs[$keyTab];

        $modelFields_tabs = [];
        $model_tabs = [];
        $fields = '';
        $forms = '';
        $left_forms = '';
        $ezform = '';
        $main_ezform = '';
        $main_ezf_id = '';
        $visit_ezf_id = '';

        $val = $valTab;
        $ezformTab = EzfQuery::getEzformOne($val['ezf_id']);
        if ($val['tab_type'] == '1' && isset($val['field_display']) && is_array($val['field_display'])) {
            $main_ezf_id = isset($val['main_ezf_id']) ? $val['main_ezf_id'] : null;
            $visit_ezf_id = isset($val['ezf_id']) ? $val['ezf_id'] : null;
            $fields = isset($val['field_display']) ? $val['field_display'] : null;
            $forms = isset($val['refform']) ? $val['refform'] : null;
            $forms[] = $main_ezf_id;
            $forms[] = $visit_ezf_id;
            $left_forms = isset($val['left_refform']) ? $val['left_refform'] : null;
            $ezform = isset($val['ezf_id']) ? EzfQuery::getEzformOne($val['ezf_id']) : null;
            $main_ezform = isset($val['main_ezf_id']) ? EzfQuery::getEzformOne($val['main_ezf_id']) : null;

            $subcontent = isset($val['subcontent']) ? $val['subcontent'] : null;
            if ($subcontent != null) {
                usort($subcontent, function($a, $b) {
                    if (isset($a['suborder']) && $a['suborder'] != '')
                        return $a['suborder'] - $b['suborder'];
                });

                $val['subcontent'] = $subcontent;
            }

            $customSelect[] = $main_ezform['ezf_table'] . ".id as dataid";

            $modelFilter = [];
            if ($ezform) {
                $modelFilter = [$ezform['ezf_table'] . '.target=' . $target];
                if ($visit_id && $visit_id != '')
                    $modelFilter[] = $ezform['ezf_table'] . '.id=' . $visit_id;
            }else {
                $modelFilter = [$main_ezform['ezf_table'] . '.id=' . $target];
            }


            $subquery = new Query();
            $subquery->select('*')
                    ->from('zdata_patientright')
                    ->where(['zdata_patientright.ptid' => $target])
                    ->andWhere(' zdata_patientright.rstat NOT IN(0,3) ')
                    ->orderBy('zdata_patientright.create_date DESC')
                    ->limit(1);
            if ($main_ezform)
                $reponseQuery = ThaiHisQuery::getDynamicQuery($fields, $forms, $main_ezform, null, null, null, $customSelect, $modelFilter, null, $left_forms, ['column' => 'create_date', 'order' => 'DESC'], null, null, $subquery);


            //$data_tab = ThaiHisFunc::modelSearch($searchModelTab, $ezformTab, $targetField, $ezformParent, $fieldsTab, $modelFilterTab, 0, Yii::$app->request->queryParams);
            if (isset($reponseQuery['modelDynamic']))
                $model_tabs = $reponseQuery['modelDynamic'];

            if (isset($reponseQuery['modelFields']))
                $modelFields_tabs = $reponseQuery['modelFields'];
        } else {
            $modelFields_tabs = null;
        }


        return $this->renderAjax('_form_content', [
                    'valTab' => $val,
                    'options' => $options,
                    'model_tabs' => $model_tabs,
                    'modelFields_tabs' => $modelFields_tabs,
                    'visitid' => $visit_id,
                    'visit_type' => $visit_type,
                    'target' => $target,
                    'reloadDiv' => $reloadDiv,
                    'modal' => $modal,
                    'key_gen' => $key_gen,
                    'widget_id' => $widget_id,
        ]);
    }

    public function actionWidgetContent() {
        $keyTab = Yii::$app->request->get('keyTab');
        $options = Yii::$app->request->get('options');
        $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
        $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
        $target = isset($_GET['target']) ? $_GET['target'] : '';
        $widget_id = isset($_GET['widget_id']) ? $_GET['widget_id'] : '';
        $key_gen = \appxq\sdii\utils\SDUtility::getMillisecTime();
        $visit_id = isset($_GET['visitid']) ? $_GET['visitid'] : '';
        $visit_type = isset($_GET['visit_type']) ? $_GET['visit_type'] : '';

        $widgetData = \backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($widget_id);
        $widget_opt = \appxq\sdii\utils\SDUtility::string2Array($widgetData['options']);
        $options = EzfFunc::stringDecode2Array($options);
        $tabs = isset($widget_opt['tabs']) ? $widget_opt['tabs'] : null;
        $valTab = $tabs[$keyTab];

        return $this->renderAjax('_widget_content', [
                    'valTab' => $valTab,
                    'options' => $options,
                    'visitid' => $visit_id,
                    'visit_type' => $visit_type,
                    'target' => $target,
                    'reloadDiv' => $reloadDiv,
                    'modal' => $modal,
                    'key_gen' => $key_gen,
                    'widget_id' => $widget_id,
        ]);
    }

}
