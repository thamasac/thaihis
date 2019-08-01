<?php

namespace backend\modules\cpoe\controllers;

use Yii;
use backend\modules\cpoe\classes\CpoeFunc;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\patient\classes\PatientFunc;
use yii\web\Response;
use yii\web\NotFoundHttpException;

class ReportCheckupController extends \yii\web\Controller {

    public function actionIndex() {
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $target = Yii::$app->request->get('target');
        $pt_hn = Yii::$app->request->get('pt_hn');
        $visit_id = Yii::$app->request->get('visitid');
        $report_status = Yii::$app->request->get('report_status', '1');
        $action = Yii::$app->request->get('action');
        $ezf_id = \backend\modules\patient\Module::$formID['report_checkup'];
        $ezfProfile_id = \backend\modules\patient\Module::$formID['profile'];
        $userProfile = Yii::$app->user->identity->profile->attributes;
        $que_type = Yii::$app->request->get('que_type', '1');
        $page = Yii::$app->request->get('page');
        $data = null;
        if ($action == 'que') {
            $data = \backend\modules\cpoe\classes\CpoeQuery::getVisitCheckupReport($visit_id, 'visit', $report_status, $que_type);
        }

        return $this->render('index', [
                    'target' => $target,
                    'pt_hn' => $pt_hn,
                    'visit_id' => $visit_id,
                    'report_status' => $report_status,
                    'ezf_id' => $ezf_id,
                    'ezfProfile_id' => $ezfProfile_id,
                    'userProfile' => $userProfile,
                    'data' => $data,
                    'que_type' => $que_type,
                    'page' => $page
        ]);
    }

    public function actionQueueView() {
        if (Yii::$app->getRequest()->isAjax) {
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $target = Yii::$app->request->get('target');
            $userProfile = Yii::$app->user->identity->profile->attributes;
            $report_status = Yii::$app->request->get('report_status');
            $page = Yii::$app->request->get('page', 1);
            $que_type = Yii::$app->request->get('que_type', '1');
            if ($que_type == '1') {
                $dataProviderQue = CpoeFunc::getReportCheckupDoctor($userProfile['user_id'], $report_status, $page - 1);
            } else {
                $dataProviderQue = CpoeFunc::getReportPapDoctor($userProfile['user_id'], $report_status, $page - 1);
            }

            return $this->renderAjax('_que', [
                        'dataProviderQue' => $dataProviderQue,
                        'reloadDiv' => $reloadDiv,
                        'target' => $target,
                        'report_status' => $report_status,
                        'que_type' => $que_type,
                        'page' => $page
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionResultLab() {
        if (Yii::$app->getRequest()->isAjax) {
//            $ezf_table = \backend\modules\patient\Module::$formTableName['order_tran'];
            $visit_id = Yii::$app->request->get('visit_id');
            $secname = Yii::$app->request->get('secname', null);
            
            $data = PatientQuery::getOrderByVisit($visit_id);
            $dataProfile = [];
            if(isset($data[0]))
                $dataProfile = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($data[0]['ptid']);

            return $this->renderAjax('_btn_result_lab', [
                        'secname' => $secname,
                        'data' => $data,
                        'dataProfile' => $dataProfile,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionResultXray() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['report_xray'];
            $visit_id = Yii::$app->request->get('visit_id');
            $order_code = Yii::$app->request->get('order_code');

            $data = PatientQuery::getOrderCounterItemReport($visit_id, $order_code);
            return $this->renderAjax('_btn_result_xray', [
                        'data' => $data, 'ezf_id' => $ezf_id
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionResultCyto() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['cytoreport'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['cytoreport'];
            $order_id = Yii::$app->request->get('order_id');

            $data = \backend\modules\patient\classes\PatientFunc::loadTbDataByTarget($ezf_table, $order_id);

            return $this->renderAjax('_btn_result_cyto', [
                        'data' => $data, 'ezf_id' => $ezf_id
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionReportApprove() {
        if (Yii::$app->getRequest()->isAjax) {
            $report_id = Yii::$app->request->get('dataid');
            $data = \backend\modules\cpoe\classes\CpoeQuery::getVisitCheckupReport($report_id, 'report', '1');

            return $this->renderAjax('_report_approve', [
                        'data' => $data, 'report_id' => $report_id
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionReportSaveApprove($report_status) {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['report_checkup'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['report_checkup'];
            $report_id = Yii::$app->request->get('report_id');

            $data['ckr_status'] = $report_status;
            $result = PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $report_id, $data);

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionNextPt() {
        if (Yii::$app->getRequest()->isAjax) {
            $userProfile = Yii::$app->user->identity->profile->attributes;

            $data = \backend\modules\cpoe\classes\CpoeQuery::getVisitCheckupReport($userProfile['user_id'], 'doctor', '1');
            if ($data) {
                \Yii::$app->response->redirect(['/cpoe/report-checkup', 'ptid' => $data['ptid'], 'visitid' => $data['visit_tran_visit_id'],
                    'pt_hn' => $data['pt_hn'], 'action' => 'que', 'report_status' => '1']);
            } else {
                \Yii::$app->response->redirect(['/cpoe/report-checkup']);
            }
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

// report 2 doc
    public function actionQueViewR2d() {
        if (Yii::$app->getRequest()->isAjax) {
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $target = Yii::$app->request->get('target');
            $page = Yii::$app->request->get('page', 1);
            $report_status = Yii::$app->request->get('report_status', '1');
            $que_type = Yii::$app->request->get('que_type', '1');
            if ($que_type == '1') {
                $dataProviderQue = CpoeFunc::getReportCheckup2Doctor($report_status, $page - 1);
            } else {
                $dataProviderQue = CpoeFunc::getReportPap2Doctor($page - 1);
            }

            return $this->renderAjax('_que', [
                        'dataProviderQue' => $dataProviderQue,
                        'reloadDiv' => $reloadDiv,
                        'pt_id' => '',
                        'report_status' => $report_status,
                        'que_type' => $que_type, 'page' => $page
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionReportToDoc() {
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $pt_id = '';
        $report_status = '1';

        return $this->render('report2doc', [
                    'pt_id' => $pt_id,
                    'report_status' => $report_status,
        ]);
    }

    public function actionReportToDocView() {
        if (Yii::$app->getRequest()->isAjax) {
            $visit_id = Yii::$app->request->get('visit_id');
            $que_type = Yii::$app->request->get('que_type');
            $doc_id = Yii::$app->request->post('doc_lists');
            if ($doc_id) {
                $doc_old = Yii::$app->request->post('doc_old');
                $pt_id = Yii::$app->request->get('ptid');

                if ($doc_old <> $doc_id) {
                    $data['visit_tran_doctor'] = $doc_id;
                    $data['visit_tran_doc_status'] = '2';

                    $data['visit_tran_dept'] = 'S074';
                    $data['visit_tran_status'] = '2';

                    \backend\modules\patient\controllers\PatientController::saveVisitTran($pt_id, $visit_id, $data);
                }
                if ($que_type == '2') {
                    $ezf_id = \backend\modules\patient\Module::$formID['report_checkup'];
                    $ezf_table = \backend\modules\patient\Module::$formTableName['report_checkup'];
                    $data = PatientFunc::loadDataByTarget($ezf_id, $ezf_table, $visit_id);
                    if (empty($data)) {
                        \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($ezf_id, '', $visit_id, ['ckr_status' => '1']);
                    }
                }
            }

            $data = \backend\modules\cpoe\classes\CpoeQuery::getVisitTranDoctorByVisit($visit_id);
//            $doc_lists = PatientQuery::getUserByPosition('', '2');

            return $this->renderAjax('_report2doc_view', [
                        'visit_id' => $visit_id,
                        'data' => $data,
//                        'doc_lists' => $doc_lists
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
