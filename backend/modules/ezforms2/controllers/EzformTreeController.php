<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * EzformInputController implements the CRUD actions for EzformInput model.
 */
class EzformTreeController extends Controller {

    public function behaviors() {
        return [
//	    'access' => [
//		'class' => AccessControl::className(),
//		'rules' => [
//		    [
//			'allow' => true,
//			'actions' => ['index', 'view'], 
//			'roles' => ['?', '@'],
//		    ],
//		    [
//			'allow' => true,
//			'actions' => ['view', 'create', 'update', 'delete', 'deletes'], 
//			'roles' => ['@'],
//		    ],
//		],
//	    ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action) {
        if (parent::beforeAction($action)) {
            if (in_array($action->id, array('create', 'update'))) {
                
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Lists all EzformInput models.
     * @return mixed
     */
    public function actionIndex() {

        return $this->render('index', [
        ]);
    }

}
