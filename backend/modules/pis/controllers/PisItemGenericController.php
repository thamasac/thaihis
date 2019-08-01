<?php

namespace backend\modules\pis\controllers;

use yii\web\Controller;
use Yii;
use yii\web\Response;

/**
 * Default controller for the `modules` module
 */
class PisItemGenericController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $ezf_id = \backend\modules\patient\Module::$formID['pism_generic'];

        $searchModel = \backend\modules\patient\classes\PatientFunc::getModel($ezf_id, '');
        $dataProvider = \backend\modules\pis\classes\PisFunc::getItemGeneric($searchModel, Yii::$app->request->get());

        return $this->render('index', [
                    'ezf_id' => $ezf_id,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
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

}
