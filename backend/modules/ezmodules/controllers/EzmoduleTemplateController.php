<?php

namespace backend\modules\ezmodules\controllers;

use Yii;
use backend\modules\ezmodules\models\EzmoduleTemplate;
use backend\modules\ezmodules\models\EzmoduleTemplateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;

/**
 * EzmoduleTemplateController implements the CRUD actions for EzmoduleTemplate model.
 */
class EzmoduleTemplateController extends Controller
{
    public function behaviors()
    {
        return [
	    /*'access' => [
		'class' => AccessControl::className(),
		'rules' => [
		    [
			'allow' => true,
			'actions' => ['index', 'view'], 
			'roles' => ['?', '@'],
		    ],
		    [
			'allow' => true,
			'actions' => ['view', 'create', 'update', 'delete', 'deletes'], 
			'roles' => ['@'],
		    ],
		],
	    ],*/
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
     * Lists all EzmoduleTemplate models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EzmoduleTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EzmoduleTemplate model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    return $this->renderAjax('view', [
		'model' => $this->findModel($id),
	    ]);
	} else {
	    return $this->render('view', [
		'model' => $this->findModel($id),
	    ]);
	}
    }

    /**
     * Creates a new EzmoduleTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
	$model = new EzmoduleTemplate();
        $model->template_id = SDUtility::getMillisecTime();
        $model->template_system = 0;
        
        $userProfile = Yii::$app->user->identity->profile;
        $model->sitecode = $userProfile->sitecode;
        
	if ($model->load(Yii::$app->request->post())) {
            $model->template_id = SDUtility::getMillisecTime();
            
    	    if ($model->save()) {
            
		Yii::$app->session->setFlash('alert', [
		    'body' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
		    'options' => ['class' => 'alert-success']
		]);
		return $this->redirect(['index']);
	    } else {
		Yii::$app->session->setFlash('alert', [
		    'body' => SDHtml::getMsgError() . Yii::t('app', 'Can not create the data.'),
		    'options' => ['class' => 'alert-danger']
		]);
	    }
	} else {
	    return $this->render('create', [
		'model' => $model,
	    ]);
	}
    }

    /**
     * Updates an existing EzmoduleTemplate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
	$model = $this->findModel($id);
        $user_id = Yii::$app->user->id;
        
        if(!Yii::$app->user->can('administrator') && $model->created_by != $user_id){
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgError() . Yii::t('ezmodule', 'You do not have right to use this page.'),
                'options' => ['class' => 'alert-danger']
            ]);
            
            return $this->redirect(['index']);
        }
        
	if ($model->load(Yii::$app->request->post())) {
        
	    if ($model->save()) {
		Yii::$app->session->setFlash('alert', [
		    'body' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
		    'options' => ['class' => 'alert-success']
		]);
                
		return $this->redirect(['index', 'id' => $id]);
	    } else {
		Yii::$app->session->setFlash('alert', [
		    'body' => SDHtml::getMsgError() . Yii::t('app', 'Can not create the data.'),
		    'options' => ['class' => 'alert-danger']
		]);
	    }
	} else {
	    return $this->render('update', [
		'model' => $model,
	    ]);
	}
    }

    /**
     * Deletes an existing EzmoduleTemplate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    if ($this->findModel($id)->delete()) {
		$result = [
		    'status' => 'success',
		    'action' => 'update',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		    'data' => $id,
		];
		return $result;
	    } else {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
		    'data' => $id,
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    public function actionDeletes() {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    if (isset($_POST['selection'])) {
		foreach ($_POST['selection'] as $id) {
		    $this->findModel($id)->delete();
		}
		$result = [
		    'status' => 'success',
		    'action' => 'deletes',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		    'data' => $_POST['selection'],
		];
		return $result;
	    } else {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
		    'data' => $id,
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    /**
     * Finds the EzmoduleTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EzmoduleTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EzmoduleTemplate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
