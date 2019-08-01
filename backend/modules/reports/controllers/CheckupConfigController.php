<?php

namespace backend\modules\reports\controllers;

use Yii;
use backend\modules\cpoe\classes\CpoeFunc;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;
class CheckupConfigController extends \yii\web\Controller {
    public function actionIndex() {
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        return $this->renderAjax('index');
    }

}

?>