<?php

namespace backend\modules\cpoe\controllers;

use Yii;
use backend\modules\patient\classes\PatientQuery;

class CpoeController extends \yii\web\Controller {

    public function actionIndex() {
        return $this->renderAjax('index');
    }

    public function actionPtSelect() {
        if (Yii::$app->getRequest()->isAjax) {
            $pt_id = Yii::$app->request->get('ptid');
            $action = Yii::$app->request->get('action');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $action_id = Yii::$app->request->get('actionid'); //visit_id or appoint_id
            $visit_tran_id = Yii::$app->request->get('visit_tran_id');
            $visit_type = Yii::$app->request->get('visit_type');
            $sitecode = Yii::$app->user->identity->profile->attributes['sitecode'];
            $userProfile = Yii::$app->user->identity->profile->attributes;
            $close_visit = FALSE;
            if ($action <> 'que' || empty($visit_tran_id)) {
                $ezf_table = \backend\modules\patient\Module::$formTableName['visit'];
//                $dataVisit = \backend\modules\patient\classes\PatientFunc::loadTbDataByTarget($ezf_table, $pt_id, date('Y-m-d'));
                $dataVisit = PatientQuery::getVisitByDate($pt_id, date('Y-m-d'));

                if ($dataVisit) {
                    if ($userProfile['position'] == '2') {
                        $data = PatientQuery::getVisitTranDoctor($pt_id, $userProfile['user_id'], date('Y-m-d'));
                    } else {
                        $data = PatientQuery::getVisitTran($pt_id, $userProfile['department'], date('Y-m-d'), '1');
                    }

                    if ($data) {
                        $visit_type = $data['visit_type'];
                        $action_id = $data['visit_id'];
                        $visit_tran_id = $data['visit_tran_id'];
                    } else {
                        $visit_type = '';
                        //$action_id = '';
                        $visit_tran_id = '';
                        $close_visit = true;
                    }
                } else {
                    $visit_type = '';
                    //$action_id = '';
                    $visit_tran_id = '';
                    $close_visit = FALSE;
                }
            }

            if ($pt_id) {
                $data = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($pt_id);
            }
            
            return $this->renderAjax('_cpoe', [
                        'pt_id' => $pt_id,
                        'action_id' => $action_id,
                        'visit_type' => $visit_type,
                        'sitecode' => $sitecode,
                        'userProfile' => $userProfile,
                        'data' => $data,
                        'reloadDiv' => $reloadDiv,
                        'action' => $action,
                        'visit_tran_id' => $visit_tran_id,
                        'close_visit' => $close_visit,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionRadt() {
        if (Yii::$app->getRequest()->isAjax) {
            $view_type = Yii::$app->request->get('view_type');
            $ezf_id = '';
            if ($view_type == 'R') {
                $ezf_id = \backend\modules\patient\Module::$formID['profile'];
            } elseif ($view_type == 'A') {
                $ezf_id = \backend\modules\patient\Module::$formID['admit'];
            }
            $pt_id = Yii::$app->request->get('pt_id');
            $visit_id = Yii::$app->request->get('visit_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $btnDisabled = Yii::$app->request->get('btnDisabled');

            return $this->renderAjax('_radt', [
                        'ezf_id' => $ezf_id,
                        'pt_id' => $pt_id,
                        'visit_id' => $visit_id,
                        'reloadDiv' => $reloadDiv,
                        'view_type' => $view_type,
                        'btnDisabled' => $btnDisabled
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionResultOrder() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_table = \backend\modules\patient\Module::$formTableName['visit'];
            $view_type = Yii::$app->request->get('view_type');
            $pt_id = Yii::$app->request->get('pt_id');
            $pt_hn = Yii::$app->request->get('pt_hn');
            $visit_id = Yii::$app->request->get('visit_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');

            $model = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezf_table, $visit_id);

            return $this->renderAjax('_result_order_cpoe', [
                        'pt_id' => $pt_id,
                        'pt_hn' => $pt_hn,
                        'visit_id' => $visit_id,
                        'reloadDiv' => $reloadDiv,
                        'view_type' => $view_type,
                        'date' => substr($model['visit_date'], 0, 10),
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionCpoeView() {
        $pt_id = Yii::$app->request->get('ptid');
        $visit_id = Yii::$app->request->get('visitid');
        $visit_type = Yii::$app->request->get('visit_type');
        $reloadDiv = '';

        $data = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($pt_id);

        return $this->renderAjax('_cpoe_view', [
                    'pt_id' => $pt_id,
                    'visit_id' => $visit_id,
                    'visit_type' => $visit_type,
                    'data' => $data,
                    'reloadDiv' => $reloadDiv,
        ]);
    }

}
