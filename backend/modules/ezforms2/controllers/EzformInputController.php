<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use backend\modules\ezforms2\models\EzformInput;
use backend\modules\ezforms2\models\EzformInputSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;

/**
 * EzformInputController implements the CRUD actions for EzformInput model.
 */
class EzformInputController extends Controller
{
    public function behaviors()
    {
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
    public function actionIndex()
    {
        $searchModel = new EzformInputSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EzformInput model.
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
     * Creates a new EzformInput model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $model = new EzformInput();
	    $model->input_version = 'v2';
	    $model->input_order = EzformInput::find()->where('input_version="v2"')->count()+1;
	    $model->input_size = 3;
            $content = '';
            $lang = isset(Yii::$app->language)?substr(Yii::$app->language, 0, 2):'en';
            
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
		
		$model->input_validate = SDUtility::strArray2String($model->input_validate);
		$model->input_option = SDUtility::strArray2String($model->input_option);
		$model->input_specific = SDUtility::strArray2String($model->input_specific);
		
		if ($model->save()) {
                    $modelLang = \backend\modules\core\models\ContentLang::find()->where('language=:lang AND obj_id=:id', [':lang'=>$lang, ':id'=>$model->input_id])->one();
                    if(!$modelLang){
                        $modelLang = new \backend\modules\core\models\ContentLang();
                        $modelLang->id = SDUtility::getMillisecTime();
                        $modelLang->obj_id = $model->input_id;
                        $modelLang->language = $lang;
                        $modelLang->title = 'ezinput';
                        
                    }
                    $modelLang->content = isset($_POST['content'])?$_POST['content']:'';
                    $modelLang->save();
                    
		    $result = [
			'status' => 'success',
			'action' => 'create',
			'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
			'data' => $model,
		    ];
		    return $result;
		} else {
		    $result = [
			'status' => 'error',
			'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not create the data.'),
			'data' => $model,
		    ];
		    return $result;
		}
	    } else {
		return $this->renderAjax('create', [
		    'model' => $model,
                    'content' => $content,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Updates an existing EzformInput model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $model = $this->findModel($id);
	    $model->input_validate = SDUtility::string2strArray($model->input_validate);
	    $model->input_option = SDUtility::string2strArray($model->input_option);
	    $model->input_specific = SDUtility::string2strArray($model->input_specific);
	    $content = '';
            $lang = isset(Yii::$app->language)?substr(Yii::$app->language, 0, 2):'en';
            $modelLang = \backend\modules\core\models\ContentLang::find()->where('language=:lang AND obj_id=:id', [':lang'=>$lang, ':id'=>$model->input_id])->one();
                if($modelLang){
                    $content = $modelLang->content;
                }    
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
		$model->input_validate = SDUtility::strArray2String($model->input_validate);
		$model->input_specific = SDUtility::strArray2String($model->input_specific);
		$model->input_option = SDUtility::strArray2String($model->input_option);
		
		if ($model->save()) {
                    
                    if(!$modelLang){
                        $modelLang = new \backend\modules\core\models\ContentLang();
                        $modelLang->id = SDUtility::getMillisecTime();
                        $modelLang->obj_id = $model->input_id;
                        $modelLang->language = $lang;
                        $modelLang->title = 'ezinput';
                        
                    }
                    $modelLang->content = isset($_POST['content'])?$_POST['content']:'';
                    $modelLang->save();
                    
		    $result = [
			'status' => 'success',
			'action' => 'update',
			'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
			'data' => $model,
		    ];
		    return $result;
		} else {
		    $result = [
			'status' => 'error',
			'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not update the data.'),
			'data' => $model,
		    ];
		    return $result;
		}
	    } else {
		return $this->renderAjax('update', [
		    'model' => $model,
                    'content' => $content,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Deletes an existing EzformInput model.
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
     * Finds the EzformInput model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EzformInput the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EzformInput::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
