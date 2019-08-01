<?php

namespace backend\modules\patient\controllers;

use Yii;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\patient\classes\PatientFunc;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use appxq\sdii\utils\SDdate;

class DrgController extends \yii\web\Controller {

    public function actionIndex() {
        return $this->renderAjax('index');
    }

    public function actionDrgCounter() {
        $ezf_id = \backend\modules\patient\Module::$formID['di'];

        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();

        $searchModel = $searchModel = PatientFunc::getModel($ezf_id, '');
        $searchModel['create_date'] = date('d-m-Y');
        $dataProvider = PatientFunc::getDrgCounter($searchModel, Yii::$app->request->get());

        return $this->render('drgcounter', [
                    'ezf_id' => $ezf_id,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

    public function actionDrgPopup() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezfVisit_id = \backend\modules\patient\Module::$formID['visit'];
            $ezfVisitTran_id = \backend\modules\patient\Module::$formID['visit_tran'];
            $ezfVisitTran_tbname = \backend\modules\patient\Module::$formTableName['visit_tran'];

            $pt_id = Yii::$app->request->get('pt_id');
            $visit_id = Yii::$app->request->get('visit_id');

            return $this->renderAjax('_rightshow', [
                        'ezfVisit_id' => $ezfVisit_id,
                        'pt_id' => $pt_id,
                        'visit_id' => $visit_id,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionDrgDetail() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['patientright'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['patientright'];
            $dataid = Yii::$app->request->get('dataid');

            $model = EzfUiFunc::loadTbData($ezf_tbname, $dataid);

            return $this->renderAjax('_right_detail', [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'dataid' => $dataid,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
