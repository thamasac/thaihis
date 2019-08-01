<?php

namespace app\modules\purify\controllers;

class TableController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function actionViewForm(){
        return $this->render('view-form');
    }

}
