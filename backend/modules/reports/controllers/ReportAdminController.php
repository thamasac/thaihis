<?php

namespace backend\modules\reports\controllers;

use Yii;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\reports\classes\CusFunc;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use appxq\sdii\utils\SDdate;

class ReportAdminController extends \yii\web\Controller {

    public function actionReportAppointCheckup() {
        $ezf_id = Yii::$app->controller->module->FormID['order_tran'];
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $searchModel = PatientFunc::getModel($ezf_id, '');
        $searchModel['create_date'] = date('d-m-Y');
        $dataProvider = CusFunc::getReportadmin($searchModel, Yii::$app->request->get());
        return $this->renderAjax('index', [
                    'ezf_id' => $ezf_id,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGrid() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = Yii::$app->controller->module->FormID['order_tran'];
            $searchModel = PatientFunc::getModel($ezf_id, '');
            $dataProvider = CusFunc::getReportadmin($searchModel, Yii::$app->request->post());
            return $this->renderAjax('_grid', [
                        'ezf_id' => $ezf_id,
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionReportOpdIndex() {
        $ezf_id = Yii::$app->controller->module->FormID['visit'];
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $searchModel = PatientFunc::getModel($ezf_id, '');
        $searchModel['create_date'] = date('d-m-Y');

        return $this->renderAjax('index_opdreport', [
                    'ezf_id' => $ezf_id,
                    'searchModel' => $searchModel,
        ]);
    }

    public function actionReportOpdGrid() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = Yii::$app->controller->module->FormID['visit'];

            $searchModel = PatientFunc::getModel($ezf_id, '');
            $filter = Yii::$app->request->post();
            switch ($filter['EZ1503589101005614900']['visit_type']) {
                case 'VISIT_IC':
                    $dataProvider = \backend\modules\reports\classes\CusFunc::getReportIC($searchModel, $filter);
                    break;
                case 'VISIT_EMER':
                    $dataProvider = \backend\modules\reports\classes\CusFunc::getReportEmer($searchModel, $filter);
                    break;
                case 'VISIT_TREA':
                    $dataProvider = \backend\modules\reports\classes\CusFunc::getReportTrea($searchModel, $filter);
                    break;
                case 'VISIT_TYPE':
                    $dataProvider = \backend\modules\reports\classes\CusFunc::getReportType($searchModel, $filter);
                    break;
                case 'VISIT_TYPE_COUNT':
                    $dataProvider = \backend\modules\reports\classes\CusFunc::getReportTypeCount($searchModel, $filter);
                    break;
                case 'APPOINT_DATE':
                    $dataProvider = \backend\modules\reports\classes\CusFunc::getReportAppDate($searchModel, $filter);
                    break;
                case 'VISIT_PROJECT':
                     $dataProvider = \backend\modules\reports\classes\CusFunc::getReportProject($searchModel, $filter);
                    break;
                case 'VISIT_APPOINT_DOCTOR':
                     $dataProvider = \backend\modules\reports\classes\CusFunc::getReportTypeDoctor($searchModel, $filter);
                    break; 
                default:
                    return FALSE;
            }
           
            return $this->renderAjax('_grid_opdreport', [
                        'ezf_id' => $ezf_id,
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionReportXrayDoctor() {
        $ezf_id = Yii::$app->controller->module->FormID['report_xray'];
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $searchModel = PatientFunc::getModel($ezf_id, '');
        $searchModel['create_date'] = date('d-m-Y');

        return $this->render('index_xray_doctor', [
                    'ezf_id' => $ezf_id,
                    'searchModel' => $searchModel,
        ]);
    }

    public function actionReportXrayDoctorGrid() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = Yii::$app->controller->module->FormID['report_xray'];

            $searchModel = PatientFunc::getModel($ezf_id, '');
            $filter = Yii::$app->request->post();
            switch ($filter['EZ' . $ezf_id]['report_status']) {
                case 'COUNT_REPORT':
                    $dataProvider = \backend\modules\reports\classes\CusFunc::getXrayReportCountDoc($searchModel, $filter);
                    break;
               
                default:
                    return FALSE;
            }

            return $this->renderAjax('_grid_opdreport', [
                        'ezf_id' => $ezf_id,
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
