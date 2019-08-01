<?php

namespace backend\modules\pis\controllers;

use yii\web\Controller;
use Yii;
use yii\web\Response;
use backend\modules\pis\classes\PisQuery;
use backend\modules\patient\classes\PatientFunc;

/**
 * Default controller for the `modules` module
 */
class PisItemController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndexTrade() {
        $ezf_id = \backend\modules\patient\Module::$formID['pism_item'];
        $ezfGeneric_id = \backend\modules\patient\Module::$formID['pism_generic'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['pism_generic'];
        $target = Yii::$app->request->get('target');

        if (empty($target)) {
            Yii::$app->session->setFlash('alert', [
                'body' => \appxq\sdii\helpers\SDHtml::getMsgError() . Yii::t('patient', 'Please select generic'),
                'options' => ['class' => 'alert-warning']
            ]);

            Yii::$app->controller->redirect(\yii\helpers\Url::to('/pis/pis-item-generic'));
            return;
        }

        $dataGeneric = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezf_table, $target);
        $searchModel = \backend\modules\patient\classes\PatientFunc::getModel($ezf_id, '');
        $dataProvider = \backend\modules\pis\classes\PisFunc::getItemTrad($searchModel, Yii::$app->request->get(), $target);

        return $this->render('index-trade', [
                    'ezf_id' => $ezf_id,
                    'ezfGeneric_id' => $ezfGeneric_id,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                    'dataGeneric' => $dataGeneric,
        ]);
    }

    public function actionShowUseSet() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['pism_use_set'];
            $generic_id = Yii::$app->request->get('generic_id');
            $item_id = Yii::$app->request->get('item_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');

            $model = PisQuery::getUseSet($item_id, $generic_id);

            return $this->renderAjax('_use_set', [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'item_id' => $item_id,
                        'generic_id' => $generic_id,
                        'reloadDiv' => $reloadDiv,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionUseItemActive($generic_id, $item_id, $dataid) {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['pism_use_set'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['pism_use_set'];
            //update active = 0
            PisQuery::updateItemActive($generic_id, $item_id);
            //update active = 1 by dataid
            $result = PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $dataid, ['use_active' => 1]);

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionInitTimeFrame() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_table = \backend\modules\patient\Module::$formTableName['pism_timeframe'];
            $dataid = Yii::$app->request->get('dataid');

            $data = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezf_table, $dataid);
            if (!$data) {
                $data['attributes'] = false;
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data['attributes'];
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionIndexGeneric() {
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $ezf_id = \backend\modules\patient\Module::$formID['pism_generic'];

        $searchModel = \backend\modules\patient\classes\PatientFunc::getModel($ezf_id, '');
        $dataProvider = \backend\modules\pis\classes\PisFunc::getItemGeneric($searchModel, Yii::$app->request->get());

        return $this->render('index-generic', [
                    'ezf_id' => $ezf_id,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

    public function actionDrugAllergy($params) {
        if (Yii::$app->getRequest()->isAjax) {
            $data = PisQuery::getDrugAllergy($params);

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
