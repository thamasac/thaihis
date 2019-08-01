<?php

namespace backend\modules\patient\controllers;

use Yii;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\patient\classes\Order2Func;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use appxq\sdii\utils\SDdate;

class Order2Controller extends \yii\web\Controller {

    public function actionIndex() {
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();

        return $this->render('index');
    }

    public function actionQueueView() {
        if (Yii::$app->getRequest()->isAjax) {
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
            $dept = Yii::$app->request->get('dept');
            if (empty($dept)) {
                $sect_code = (isset(Yii::$app->user->identity->profile->attributes['department']) ? Yii::$app->user->identity->profile->attributes['department'] : null);
                $dept = PatientQuery::getDepartmentOne($sect_code)['sect_his_type'];
            }
            $searchModel = \backend\modules\patient\classes\PatientFunc::getModel($ezf_id, '');
            $searchModel['order_tran_status'] = '1';
            $searchModel['create_date'] = date('d/m/Y');

            $dataProvider = Order2Func::getOrderCounter($searchModel, Yii::$app->request->get(), $dept);

            return $this->renderAjax('_que', [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                        'reloadDiv' => $reloadDiv,
                        'dept' => $dept,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderContent() {
        if (Yii::$app->getRequest()->isAjax) {
            $pt_id = Yii::$app->request->get('pt_id');
            $visit_id = Yii::$app->request->get('visit_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $dept = Yii::$app->request->get('dept');
            $sect_code = Yii::$app->request->get('sect_code');
            $order_status = Yii::$app->request->get('order_status');
            $visit_type = Yii::$app->request->get('visit_type');

            return $this->renderAjax('_order_content', [
                        'visit_id' => $visit_id,
                        'order_status' => $order_status,
                        'reloadDiv' => $reloadDiv,
                        'dept' => $dept,
                        'sect_code' => $sect_code,
                        'visit_type' => $visit_type,
                        'pt_id' => $pt_id,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderLists() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
            $ezfAppoint_id = \backend\modules\patient\Module::$formID['appoint'];
            $visit_id = Yii::$app->request->get('visit_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $dept = Yii::$app->request->get('dept');
            $sect_code = Yii::$app->request->get('sect_code');
            $order_status = Yii::$app->request->get('order_status');
            $dept_code = Yii::$app->request->get('dept_code'); //?

            $data = PatientQuery::getOrderCounterItem($dept, $order_status, $visit_id);

            return $this->renderAjax('_order_lists', [
                        'visit_id' => $visit_id,
                        'order_status' => $order_status,
                        'reloadDiv' => $reloadDiv,
                        'data' => $data,
                        'dept' => $dept,
                        'sect_code' => $sect_code,
                        'ezf_id' => $ezf_id, 'ezfAppoint_id' => $ezfAppoint_id, 'dept_code'=>$dept_code
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderHistory() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['visit'];
            $pt_id = Yii::$app->request->get('pt_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $dept = Yii::$app->request->get('dept');
            $ragedate = Yii::$app->request->get('ragedate');

            $searchModel = PatientFunc::getModel($ezf_id, '');
            $searchModel['target'] = $pt_id;
            $searchModel['visit_date'] = $ragedate;
            $dataProvider = Order2Func::getOrderHistoryVisit($searchModel, $dept);

            return $this->renderAjax('_visit_date_que', [
                        'dataProvider' => $dataProvider,
                        'pt_id' => $pt_id,
                        'reloadDiv' => $reloadDiv,
                        'searchModel' => $searchModel,
                        'reloadChildDiv' => 'view-detail', //fix id ไปก่อน
                        'dept' => $dept
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
