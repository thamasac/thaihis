<?php

namespace backend\modules\patient\controllers;

use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use appxq\sdii\utils\VarDumper;
use backend\modules\ezforms2\classes\EzfFunc;
use Yii;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;
use yii\db\Query;
use yii\web\Response;

class EmrController extends \yii\web\Controller {

    public function actionIndex() {
        return $this->renderAjax('index');
    }

    public function actionView() {
        if (Yii::$app->getRequest()->isAjax) {
            $dept = Yii::$app->user->identity->profile->attributes['department'];
            $dataid = Yii::$app->request->get('dataid');

            $dataVisit = PatientQuery::getVisitTran($dataid, $dept, date('Y-m-d'), '1');

            return $this->renderAjax('emr', [
                        'pt_id' => $dataid,
                        'dataVisit' => $dataVisit,
                        'dept' => $dept,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionVs() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['vs'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['vs'];
            $dataid = Yii::$app->request->get('dataid');
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $btnDisabled = Yii::$app->request->get('btnDisabled');

            if ($dataid) {
                $model = EzfUiFunc::loadTbData($ezf_tbname, $dataid);
            } else {
                $model = PatientFunc::loadTbDataByTarget($ezf_tbname, $target);
            }

            return $this->renderAjax('_vs', [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'dataid' => $dataid,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
                        'btnDisabled' => $btnDisabled,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionBmi() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['bmi'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['bmi'];
            $dataid = Yii::$app->request->get('dataid');
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $btnDisabled = Yii::$app->request->get('btnDisabled');

            if ($dataid) {
                $model = EzfUiFunc::loadTbData($ezf_tbname, $dataid);
            } else {
                $model = PatientFunc::loadTbDataByTarget($ezf_tbname, $target);
            }

            return $this->renderAjax('_bmi', [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'dataid' => $dataid,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
                        'btnDisabled' => $btnDisabled,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionTk($dataid) {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['tk'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['tk'];
            $dataid = Yii::$app->request->get('dataid');
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $btnDisabled = Yii::$app->request->get('btnDisabled');
            $view = Yii::$app->request->get('view');

            if ($dataid) {
                $model = EzfUiFunc::loadTbData($ezf_tbname, $dataid);
            } else {
                $model = PatientFunc::loadTbDataByTarget($ezf_tbname, $target);
            }

            return $this->renderAjax($view, [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'dataid' => $dataid,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
                        'btnDisabled' => $btnDisabled
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionTkLast($target) {
        if (Yii::$app->getRequest()->isAjax) {
            $data = PatientQuery::getTkLast($target);

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionTkLastNew($ptid, $ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $ezfData = \backend\modules\ezforms2\models\Ezform::findOne(['ezf_id' => $ezf_id]);
            if ($ezfData) {
                $data = PatientQuery::getTkLastNew($ptid, $ezfData['ezf_table']);
            } else {
                $data = [];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionPatientHistory($ptid) {
        if (Yii::$app->getRequest()->isAjax) {
            $data = PatientQuery::getPatientHistoryNew($ptid);

            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($data) {
                return [
                    'success' => TRUE,
                    'data' => $data
                ];
            } else {
                return [
                    'success' => false
                ];
            }
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionFmTk($ptid) {
        if (Yii::$app->getRequest()->isAjax) {
            $data = PatientQuery::getFmTk($ptid);

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionPe() {
        if (Yii::$app->getRequest()->isAjax) {
            $pt_id = Yii::$app->request->get('pt_id');
            $dataid = Yii::$app->request->get('dataid');
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $btnDisabled = Yii::$app->request->get('btnDisabled');
            $view = Yii::$app->request->get('view');

            $profile = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($pt_id);
            $ezf_id = \backend\modules\patient\Module::$formID['pe'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['pe'];
//            if ($profile['pt_sex'] == 2) {
//                $ezf_id = \backend\modules\patient\Module::$formID['pe_f'];
//                $ezf_tbname = \backend\modules\patient\Module::$formTableName['pe_f'];
//            } else {
//                $ezf_id = \backend\modules\patient\Module::$formID['pe_m'];
//                $ezf_tbname = \backend\modules\patient\Module::$formTableName['pe_m'];
//            }

            if ($dataid) {
                $model = EzfUiFunc::loadTbData($ezf_tbname, $dataid);
            } else {
                $model = PatientFunc::loadTbDataByTarget($ezf_tbname, $target);
            }

            return $this->renderAjax($view, [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'dataid' => $dataid,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
                        'btnDisabled' => $btnDisabled
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionDi() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['di'];
            $ezfStag_id = \backend\modules\patient\Module::$formID['staging'];
            $ezfComo_id = \backend\modules\patient\Module::$formID['diag_como'];
            $ezfComp_id = \backend\modules\patient\Module::$formID['diag_comp'];
            $ezfOperat_id = \backend\modules\patient\Module::$formID['operat'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['di'];
            $dataid = Yii::$app->request->get('dataid');
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $btnDisabled = Yii::$app->request->get('btnDisabled');
            $modelStag = [];

            $model = PatientFunc::loadTbDataByTarget($ezf_tbname, $target);
            $dataDiagComo = '';
            $dataDiagComp = '';
            $dataOperat = '';
            if ($model['id']) {
                $dataDiagComo = PatientQuery::getDiagComo($target);
                $dataDiagComp = PatientQuery::getDiagComp($target);
                $dataOperat = PatientQuery::getOperat($target);
            }

            if (isset($model['id'])) {
                $ezf_tbname = \backend\modules\patient\Module::$formTableName['staging'];
                $modelStag = ''; //PatientFunc::loadTbDataByTarget($ezf_tbname, $model['id']);
            }

            return $this->renderAjax('_di', [
                        'ezf_id' => $ezf_id,
                        'ezfStag_id' => $ezfStag_id,
                        'ezfComp_id' => $ezfComp_id,
                        'ezfComo_id' => $ezfComo_id,
                        'ezfOperat_id' => $ezfOperat_id,
                        'model' => $model,
                        'modelStag' => $modelStag,
                        'dataDiagComo' => $dataDiagComo,
                        'dataDiagComp' => $dataDiagComp,
                        'dataOperat' => $dataOperat,
                        'dataid' => $dataid,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
                        'btnDisabled' => $btnDisabled
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionStaging() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['staging'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['staging'];
            $dataid = Yii::$app->request->get('dataid');
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');

            if ($dataid) {
                $model = EzfUiFunc::loadTbData($ezf_tbname, $dataid);
            } else {
                $model = PatientFunc::loadTbDataByTarget($ezf_tbname, $target);
            }

            return $this->renderAjax('_staging', [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'dataid' => $dataid,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionDoctorTreat() {
        if (Yii::$app->getRequest()->isAjax) {
            $visit_id = Yii::$app->request->get('visit_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $view = Yii::$app->request->get('view');

            $data = PatientQuery::getDoctorTreat($visit_id);

            return $this->renderAjax('_doctor_treat_cpoe', [
                        'data' => $data,
                        'visit_id' => $visit_id,
                        'reloadDiv' => $reloadDiv,
                        'view' => $view
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionSoap() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['soap'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['soap'];
            $dataid = Yii::$app->request->get('dataid');
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $btnDisabled = Yii::$app->request->get('btnDisabled');

            if ($dataid) {
                $model = EzfUiFunc::loadTbData($ezf_tbname, $dataid);
            } else {
                $model = PatientFunc::loadTbDataByTarget($ezf_tbname, $target);
            }

            return $this->renderAjax('_soap', [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'dataid' => $dataid,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
                        'btnDisabled' => $btnDisabled
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionTreatment() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['treatment'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['treatment'];
            $treat_id = Yii::$app->request->get('treat_id');
            $visit_id = Yii::$app->request->get('visit_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $btnDisabled = Yii::$app->request->get('btnDisabled');
//            $view = Yii::$app->request->get('view');

            if ($treat_id) {
                $model = EzfUiFunc::loadTbData($ezf_tbname, $treat_id);
            } else {
                $model = PatientFunc::loadTbDataByTarget($ezf_tbname, $visit_id);
            }

            return $this->renderAjax('_treatment', [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'treat_id' => $treat_id,
                        'visit_id' => $visit_id,
                        'reloadDiv' => $reloadDiv,
                        'btnDisabled' => $btnDisabled
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public static function initSelect2Doctor($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        if (isset($code) && !empty($code)) {
            $data = PatientQuery::getUserByAssignment('doctor', $code);

            $str = isset($data[0]['fullname']) ? $data[0]['fullname'] : '';
        }

        return $str;
    }

    public function actionSearchUser($q = null, $item_name) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['results' => []];
        $data = PatientQuery::getUserByAssignment($item_name, $q);
        $i = 0;

        foreach ($data as $value) {
            $certi = isset($value['certificate']) ? $value['certificate'] : '';
            $result["results"][$i] = ['id' => $value['user_id'], 'text' => $certi . ' ' . $value["fullname"]];
            $i++;
        }

        return $result;
    }

    public function actionAdviceDetail($advice_id) {
        $data = PatientQuery::getAdviceDetail($advice_id);

        return $data['advice_detail'];
    }

    public function actionTemptextDetail($ezf_table, $temptext_id) {
//        $ezf_table = \backend\modules\patient\Module::$formTableName['temptext'];
        $data = EzfUiFunc::loadTbData($ezf_table, $temptext_id);

        return $data['tt_detail'];
    }

    public function actionReferReceive() { //form refer
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['refer_receive'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['refer_receive'];
            $dataid = Yii::$app->request->get('dataid');
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');

            if ($dataid) {
                $model = EzfUiFunc::loadTbData($ezf_tbname, $dataid);
            } else {
                $model = PatientFunc::loadTbDataByTarget($ezf_tbname, $target);
            }

            return $this->renderAjax('_refer_receive', [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'dataid' => $dataid,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionDiCpoe() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['di'];
            $ezfComo_id = \backend\modules\patient\Module::$formID['diag_como'];
            $ezfComp_id = \backend\modules\patient\Module::$formID['diag_comp'];
            $ezfOperat_id = \backend\modules\patient\Module::$formID['operat'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['di'];
            $dataid = Yii::$app->request->get('dataid');
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');

            $model = PatientFunc::loadTbDataByTarget($ezf_tbname, $target);
            $dataDiagComo = [];
            $dataDiagComp = [];
            $dataOperat = [];
            if ($model['id']) {
                $dataDiagComo = PatientQuery::getDiagComo($target);
                $dataDiagComp = PatientQuery::getDiagComp($target);
                $dataOperat = PatientQuery::getOperat($target);
            }

            return $this->renderAjax('_di_cpoe', [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'dataDiagComo' => $dataDiagComo,
                        'dataDiagComp' => $dataDiagComp,
                        'dataOperat' => $dataOperat,
                        'dataid' => $dataid,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionAppointBtn() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['appoint'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['appoint'];
            $dataid = Yii::$app->request->get('dataid');
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $dept = Yii::$app->request->get('dept');
            $userProfile = Yii::$app->user->identity->profile->attributes;

            $model = PatientFunc::loadDataByTarget($ezf_id, $ezf_table, $target);

            return $this->renderAjax('_appoint_btn', [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'dataid' => $dataid,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
                        'dept' => $dept,
                        'userProfile' => $userProfile,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionCertificateBtn() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['certificate'];
//            $ezf_id = \backend\modules\patient\Module::$formID['certificate'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['certificate'];
//            $ezf_table = \backend\modules\patient\Module::$formTableName['certificate'];
            $dataid = Yii::$app->request->get('dataid');
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $dept = Yii::$app->request->get('dept');
            $userProfile = Yii::$app->user->can('doctor');
            $model_bmi = PatientQuery::getMaxBmiByPtid($target);
            $model_vs = PatientQuery::getMaxVitalSignByPtid($target);
            $model_history = PatientQuery::gethistoryByPtid($target); //pt_disease_status
            $model = PatientFunc::loadDataByTarget($ezf_id, $ezf_table, $target);
            $options = EzfFunc::stringDecode2Array(Yii::$app->request->get('options', ''));

            return $this->renderAjax('_certificate_btn', [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'dataid' => $dataid,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
                        'dept' => $dept,
                        'userProfile' => $userProfile,
                        'model_bmi' => $model_bmi,
                        'model_vs' => $model_vs,
                        'model_history' => $model_history,
                        'reloadDiv' => $reloadDiv,
                        'options' => $options
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionCerSaveVisitDate() {
        if (Yii::$app->getRequest()->isAjax) {
            $options = \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array(Yii::$app->request->get('options', ''));
            $dataid = Yii::$app->request->get('dataid', '');
            $target = Yii::$app->request->get('target', '');
            return $this->renderAjax('_app_print_certificate', [
                        'dataid' => $dataid,
                        'options' => $options,
                        'target' => $target
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionPreviewPrintCe() {
        $options = Yii::$app->request->post('options', '');
//        \appxq\sdii\utils\VarDumper::dump($options);
//        $options = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($options);
        return $this->renderAjax('_app_print_certificate', ['options' => $options, 'dataid' => '', 'target' => '']);
//        \appxq\sdii\utils\VarDumper::dump(EzfFunc::stringDecode2Array($data));
    }

    public function actionCloseVisitBtn() {
        if (Yii::$app->getRequest()->isAjax) {
            $current_url = Yii::$app->request->get('current_url', '');
            $visit_tran_id = Yii::$app->request->get('visit_tran_id', '');
            $reloadDiv = Yii::$app->request->get('reloadDiv', '');
            $options = Yii::$app->request->get('options', '');

            if ($options != '') {
                $options = \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($options);
            }

            $ezf_visit_id = isset($options['ezf_id']) ? $options['ezf_id'] : '';
            $ezf_visittran_id = isset($options['refform']) ? $options['refform'] : '';
            $btn_icon = isset($options['btn_icon']) ? $options['btn_icon'] : '';
            $btn_text = isset($options['btn_text']) ? $options['btn_text'] : '';
            $btn_color = isset($options['btn_color']) ? $options['btn_color'] : '';
            $btn_style = isset($options['btn_style']) ? $options['btn_style'] : '';

            $ezfVisitTran = \backend\modules\ezforms2\classes\EzfQuery::getEzformById($ezf_visittran_id);
            if ($ezfVisitTran) {
                $ezf_id = $ezfVisitTran['ezf_id'];
                $ezf_tbname = $ezfVisitTran['ezf_table'];
            } else {
                $ezf_id = \backend\modules\patient\Module::$formID['visit_tran'];
                $ezf_tbname = \backend\modules\patient\Module::$formTableName['visit_tran'];
            }

            $data = EzfUiFunc::loadTbData($ezf_tbname, $visit_tran_id);
            if ($data['visit_tran_status'] == '2' && $data['visit_tran_close_type'] == '2') { //visit_tran_close_type 1=ตรวจเสร็จ,2=ส่งต่อไปแผนกอื่น,3=ส่งพบแพทย์
                $initdata = ['visit_tran_dept' => $data['visit_tran_to_dept']
                    , 'visit_tran_status' => '1'];
                \backend\modules\thaihis\controllers\PatientVisitController::saveVisitTran($data['ptid'], $data['visit_tran_visit_id'], $initdata
                        , ['ezf_id' => $ezf_id, 'ezf_table' => $ezf_tbname]);
//                PatientController::saveVisit($data['visit_tran_status'], $data['visit_tran_visit_id'], $visit_type, $dept, $ezf_visit_id, $ezf_visittran_id);
            } elseif ($data['visit_tran_status'] == '2' && $data['visit_tran_close_type'] == '3') {
                $initdata['visit_tran_dept'] = $data['visit_tran_dept'];
                $initdata['visit_tran_status'] = '1';
                $initdata['visit_tran_doctor'] = $data['visit_tran_doctor'];
//                $initdata['visit_tran_doc_status'] = '1'; //ยกเลิก Version ThaiHIS ByOak
                \backend\modules\thaihis\controllers\PatientVisitController::saveVisitTran($data['ptid'], $data['visit_tran_visit_id'], $initdata
                        , ['ezf_id' => $ezf_id, 'ezf_table' => $ezf_tbname]);
//                PatientController::saveVisitTran($data['ptid'], $data['visit_tran_visit_id'], $initdata, $ezf_visittran_id);
            }

            return $this->renderAjax('_close_visit', [
                        'ezf_id' => $ezf_visittran_id,
                        'data' => $data,
                        'visit_tran_id' => $visit_tran_id,
                        'reloadDiv' => $reloadDiv,
                        'current_url' => $current_url,
                        'btn_icon' => $btn_icon,
                        'btn_text' => $btn_text,
                        'btn_color' => $btn_color,
                        'btn_style' => $btn_style
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionAppoint() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['appoint'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['appoint'];
            $dataid = Yii::$app->request->get('dataid');
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');

            $model = EzfUiFunc::loadTbData($ezf_tbname, $dataid);

            return $this->renderAjax('_appoint', [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'dataid' => $dataid,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionCheckAppoint() {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $ezf_id = \backend\modules\patient\Module::$formID['appoint'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['appoint'];

            $ezfVisit_id = \backend\modules\patient\Module::$formID['visit'];
            $ezfVisit_tbname = \backend\modules\patient\Module::$formTableName['visit'];

            $dataid = Yii::$app->request->get('dataid');

            $modelAppoint = EzfUiFunc::loadTbData($ezf_tbname, $dataid);
            if ($modelAppoint) {
                if ($modelAppoint['app_status'] == '3') {
                    PatientFunc::saveDataNoSys($ezf_id, $ezf_tbname, $dataid, ['rstat' => '3']);
                } elseif ($modelAppoint['app_visit_id'] == '') {
                    //find visit
//                $modelVisit = EzfUiFunc::loadTbData($ezfVisit_tbname, $modelAppoint['app_visit_id']);
//                $modelVisit['visit_date'] = substr($modelVisit['visit_date'], 0, 10);
//                $modelAppoint['app_date'] = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($modelAppoint['app_date'], "-");
//                //check visit date = appoint date
//                if ($modelAppoint['app_date'] != $modelVisit['visit_date']) {
//                    $modelVisitDup = PatientFunc::loadTbDataByField($ezfVisit_tbname, [
//                                'ptid' => $modelVisit['ptid'],
//                                'visit_date' => $modelVisit['visit_date'],
//                    ]);
//                    if ($modelVisitDup) {
//                        //ถ้าซ้ำให้ยกเลิก visit ที่ใช้ร่วมกันกับ appoint นี้
//                        PatientFunc::saveDataNoSys($ezfVisit_id, $ezfVisit_tbname, $modelVisit['id'], ['rstat' => '3']);
//                    } else {
//                        //ไม่มี visit ซ้ำให้ เปลี่ยนวันที่ตาม appoint
//                        PatientFunc::saveDataNoSys($ezfVisit_id, $ezfVisit_tbname, $modelVisit['id'], ['visit_date' => $modelAppoint['app_date']]);
//                    }
//                }
                    $dataVisit = (new Query())->select('*')
                                    ->from($ezfVisit_tbname)
                                    ->where(['DATE(visit_date)' => $modelAppoint['app_date'], 'target' => $modelAppoint['target']])
                                    ->andWhere('rstat NOT IN (0,3)')->one();
                    if ($dataVisit) {
                        Yii::$app->db->createCommand()->update($ezf_tbname, ['app_date' => $modelAppoint['app_date'], 'app_visit_id' => $dataVisit['id']], ['id' => $modelAppoint['id']])->execute();
                    } else {
                        Yii::$app->db->createCommand()->update($ezf_tbname, ['app_date' => $modelAppoint['app_date']], ['id' => $modelAppoint['id']])->execute();
                    }
                } elseif ($modelAppoint['app_visit_id'] != '') {
                    $dataVisit = (new Query())->select('*')
                                    ->from($ezfVisit_tbname)
                                    ->where(['id' => $modelAppoint['app_visit_id'], 'target' => $modelAppoint['target']])
                                    ->andWhere('rstat NOT IN (0,3)')->one();
                    $dataVisitDate = (new Query())->select('*')
                                    ->from($ezfVisit_tbname)
                                    ->where(['DATE(visit_date)' => $modelAppoint['app_date'], 'target' => $modelAppoint['target']])
                                    ->andWhere('rstat NOT IN (0,3)')->one();
                    if ($dataVisit) {
                        if ($dataVisitDate && $dataVisitDate['id'] == $dataVisit['id']) {
                            Yii::$app->db->createCommand()->update($ezfVisit_tbname, ['visit_date' => $modelAppoint['app_date']], ['id' => $dataVisit['id']])->execute();
//                        Yii::$app->db->createCommand()->update($ezf_tbname, ['app_date' => $modelAppoint['app_date']], ['id' => $modelAppoint['id']])->execute();
                        } elseif ($dataVisitDate && $dataVisitDate['id'] != $dataVisit['id']) {
//                                        Yii::$app->db->createCommand()->update($visit_ezf_table, ['visit_date' => $startAllDay], ['id' => $dataVisit['id']])->execute();
//                            if($dataVisit['id'] != $modelAppoint['app_visit_id']){
                            Yii::$app->db->createCommand()->update($ezfVisit_tbname, ['rstat' => '3'], ['id' => $dataVisit['id'], 'target' => $dataVisit['target']])->execute();
//                            }else{
                            Yii::$app->db->createCommand()->update($ezfVisit_tbname, ['visit_date' => $modelAppoint['app_date']], ['id' => $dataVisit['id']])->execute();
//                            }
                            Yii::$app->db->createCommand()->update($ezf_tbname, ['app_date' => $modelAppoint['app_date'], 'app_visit_id' => $dataVisitDate['id']], ['id' => $modelAppoint['id']])->execute();
                        } else {
                            Yii::$app->db->createCommand()->update($ezfVisit_tbname, ['visit_date' => $modelAppoint['app_date']], ['id' => $dataVisit['id']])->execute();
//                        Yii::$app->db->createCommand()->update($ezf_tbname, ['app_date' => $modelAppoint['app_date']], ['id' => $modelAppoint['id']])->execute();
                        }
                    } else {
                        if ($dataVisitDate) {
                            Yii::$app->db->createCommand()->update($ezf_tbname, ['app_visit_id' => $dataVisitDate['id']], ['id' => $modelAppoint['id']])->execute();

//                    } else {
//                        Yii::$app->db->createCommand()->update($modelEzf['ezf_table'], [$field_dstart => $startAllDay], ['id' => $dataTbCalendar['id']])->execute();
                        }
                    }
                }
                $result = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save the data success'),
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                ];
            }


            return $result;


//            return $this->renderAjax('_appoint', [
//                        'ezf_id' => $ezf_id,
//                        'model' => $model,
//                        'dataid' => $dataid,
//                        'target' => $target,
//                        'reloadDiv' => $reloadDiv,
//            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
