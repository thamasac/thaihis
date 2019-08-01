<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use backend\modules\ezforms2\models\EzformAutonum;
use backend\modules\ezforms2\models\EzformAutonumSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;

/**
 * EzformAutonumController implements the CRUD actions for EzformAutonum model.
 */
class EzformAutonumController extends Controller
{
    public function behaviors()
    {
        return [
/*	    'access' => [
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
     * Lists all EzformAutonum models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EzformAutonumSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EzformAutonum model.
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
     * Creates a new EzformAutonum model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $model = new EzformAutonum();
            $model->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $model->status = 1;
            $model->count = 1;
            $model->digit = 5;
            $model->per_time = 1;
            $model->type = 1;
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                
		if ($model->save()) {
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
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    public function actionSave($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
            $modal = Yii::$app->request->get('modal','modal-ezform-autonum');
            if($id>0){
                $model = $this->findModel($id);
                
                if(!$model){
                    $model = new EzformAutonum();
                    $model->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
                    $model->status = 1;
                    $model->count = 1;
                    $model->digit = 5;
                    $model->per_time = 1;
                    $model->type = 1;
                }
            } else {
                $model = new EzformAutonum();
                $model->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
                $model->status = 1;
                $model->count = 1;
                $model->digit = 5;
                $model->per_time = 1;
                $model->type = 1;
            }
            
            $ezf_id = isset($_GET['ezf_id'])?$_GET['ezf_id']:'';
            $ezf_field_id = isset($_GET['ezf_field_id'])?$_GET['ezf_field_id']:'';
            $reloadDiv = isset($_GET['reloadDiv'])?$_GET['reloadDiv']:'reload_id';
            $label = isset($_GET['label'])?$_GET['label']:'';
            $type = isset($_GET['type'])?$_GET['type']:1;
            
            $model->label = isset($model->label) && !empty($model->label)?$model->label:$label;
            $model->type = $type;
            $model->ezf_id = isset($model->ezf_id) && !empty($model->ezf_id)?$model->ezf_id:$ezf_id;
            $model->ezf_field_id = isset($model->ezf_field_id) && !empty($model->ezf_field_id)?$model->ezf_field_id:$ezf_field_id;

	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                
		if ($model->save()) {
                    $data = $model->attributes;
                    $data['id'] = "{$data['id']}";
		    $result = [
			'status' => 'success',
			'action' => 'update',
			'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
			'data' => $data,
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
		return $this->renderAjax('_form_widget', [
		    'model' => $model,
                    'modal'=>$modal,
                    'reloadDiv' => $reloadDiv,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionSelect($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $type = isset($_GET['type'])?$_GET['type']:1;
        
        $userProfile = Yii::$app->user->identity->profile;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
            $data = \backend\modules\ezforms2\models\EzformAutonum::find()
                    ->select('ezform_autonum.*')
                    ->innerJoin('ezform', 'ezform.ezf_id = ezform_autonum.ezf_id')
                    ->where('ezform_autonum.type = :type AND ezform_autonum.label LIKE :q', [':q'=>"%$q%", ':type'=>$type])
                    ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND xsourcex=:xsourcex) OR (shared =2 AND INSTR(assign, :user_id)) OR ezform.created_by=:user_id OR INSTR(co_dev, :user_id) OR ezform.ezf_id in (SELECT ezform_role.ezf_id FROM ezform_role WHERE ezform_role.role '.\backend\modules\ezforms2\classes\EzfForm::getRoleIn().' )', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                    ->all();
            foreach($data as $value){
                $out["results"][] = ['id'=>"{$value['id']}",'text'=>$value['label']];//.' : '.\backend\modules\ezforms2\classes\EzfFunc::getAutoNumber($value)
            }
            
//        if ($id > 0) {
//            $out['results'] = ['id' => $id, 'text' => City::find($id)->name];
//        }

        return $out;
    }
    
    /**
     * Updates an existing EzformAutonum model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $model = $this->findModel($id);
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
		if ($model->save()) {
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
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Deletes an existing EzformAutonum model.
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
     * Finds the EzformAutonum model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EzformAutonum the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EzformAutonum::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
