<?php

namespace backend\modules\tctr\controllers;

use yii\web\Controller;

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
        return $this->renderAjax('index',[
            ]);
    }
}
