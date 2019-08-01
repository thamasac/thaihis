<?php

namespace frontend\modules\tctr\controllers;

use yii\web\Controller;
use backend\modules\tctr\classes\TctrFunction;

/**
 * Default controller for the `tctr` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->renderAjax('index');
    }
    public function actionShowMap() {
        $dropdown= TctrFunction::getAllChoice('1520776142078903600','recruitment_status');
        $dropdown[] = ['value' => '0','ezf_choicelabel' => 'All'];

        return $this->renderAjax("_map",['dropdown' => $dropdown]);
    }
}
