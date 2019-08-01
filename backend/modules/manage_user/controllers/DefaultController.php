<?php

namespace backend\modules\manage_user\controllers;

use yii\web\Controller;

/**
 * Default controller for the `manage_user` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
