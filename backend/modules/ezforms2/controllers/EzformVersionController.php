<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use backend\modules\ezforms2\models\EzformVersion;
use backend\modules\ezforms2\models\EzformVersionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;

/**
 * EzformVersionController implements the CRUD actions for EzformVersion model.
 */
class EzformVersionController extends Controller
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
     * Lists all EzformVersion models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tab = (isset($_GET['tab']) && in_array($_GET['tab'], [1,2,3,4]))?$_GET['tab']:4;
        $searchModel = new EzformVersionSearch();
        $searchModel->ver_approved = $tab;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'tab' => $tab,
        ]);
    }

    /**
     * Displays a single EzformVersion model.
     * @param string $ver_code
     * @param integer $ezf_id
     * @return mixed
     */
    public function actionView($ver_code, $ezf_id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    return $this->renderAjax('view', [
		'model' => $this->findModel($ver_code, $ezf_id),
	    ]);
	} else {
	    return $this->render('view', [
		'model' => $this->findModel($ver_code, $ezf_id),
	    ]);
	}
    }

    /**
     * Creates a new EzformVersion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($ezf_id, $v)
    {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        
	if (Yii::$app->getRequest()->isAjax) {
            $modelVersion = \backend\modules\ezforms2\classes\EzfQuery::getEzformConfig($ezf_id, $v);
            if(!$modelVersion){
                
                
                return $this->renderAjax('/ezform-data/_error', [
		    'ezf_id' => $ezf_id,
                    'modelEzf' => $modelEzf,
                    'msg' => Yii::t('app', 'No version found.'),
		]);
            }
            $modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
            
	    $model = new EzformVersion();
            $model->ver_active = 0;
            $model->ver_approved = 0;
            $model->ver_for = $v;
            $model->ezf_id = $ezf_id;
            $model->ver_options = $modelVersion->ver_options;
            $model->field_detail = $modelVersion->field_detail;
            $model->ezf_sql = $modelVersion->ezf_sql;
            $model->ezf_js = $modelVersion->ezf_js;
            $model->ezf_error = $modelVersion->ezf_error;
            $model->ezf_options = $modelVersion->ezf_options;
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                
                if(!$model->validate()) {
                    $emsg = '';
                    if(isset($model->errors)){
                        foreach ($model->errors as $ekey => $evalue) {
                            $evalue = implode(', ', $evalue);
                            $emsg .= "<strong>[$ekey]</strong> : $evalue<br>";
                        }
                    }
                    
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not update the data.<br>'.$emsg),
                    ];
                    return $result;
                }
                $filename = \backend\modules\ezforms2\classes\EzfFunc::exportForm($ezf_id, $modelEzf, $modelVersion);
                if (isset($filename) && !empty($filename)) {
                    $sum = \backend\modules\ezforms2\classes\EzfFunc::importForm(Yii::getAlias('@backend/web/print/') . $filename, 0, $model->ver_code);
                    
                    $result = [
			'status' => 'success',
			'action' => 'create',
			'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
			'data' => $sum,
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
                
//		if ($model->save()) {
//                    //clone field
//                    $modelFields = \backend\modules\ezforms2\classes\EzfQuery::getFieldAllByVersion($ezf_id, $v);
//                    if(isset($modelFields) && !empty($modelFields)){
//                        foreach ($modelFields as $key => $value) {
//                            try {
//                                $modelCloneField = new \backend\modules\ezforms2\models\EzformFields();
//                                $modelCloneField->attributes = $value->attributes;
//                                $modelCloneField->ezf_field_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
//                                $modelCloneField->ezf_version = $model->ver_code;
//                                $modelCloneField->save();
//                            } catch (\yii\db\Exception $e) {
//                                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
//                            }
//                        }
//                    }
//                    
//		    
//		} 
	    } else {
		return $this->renderAjax('create', [
		    'model' => $model,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Updates an existing EzformVersion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $ver_code
     * @param integer $ezf_id
     * @return mixed
     */
    public function actionUpdate($ezf_id, $v)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $model = $this->findModel($v, $ezf_id);

	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                
                if(!$model->validate()) {
                    $emsg = '';
                    if(isset($model->errors)){
                        foreach ($model->errors as $ekey => $evalue) {
                            $evalue = implode(', ', $evalue);
                            $emsg .= "<strong>[$ekey]</strong> : $evalue<br>";
                        }
                    }
                    
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not update the data.<br>'.$emsg),
                    ];
                    return $result;
                }
                
		if ($model->save()) {
                    try {
                        $queury = Yii::$app->db->createCommand()->update('ezform_fields', ['ezf_version'=>$model->ver_code], 'ezf_id=:ezf_id AND ezf_version=:v', ['v' => $v, 'ezf_id' => $ezf_id])->execute();
                        
                        Yii::$app->db->createCommand()->update('ezform_choice', ['ezf_version'=>$model->ver_code], 'ezf_id=:ezf_id AND ezf_version=:v', ['v' => $v, 'ezf_id' => $ezf_id])->execute();
                        Yii::$app->db->createCommand()->update('ezform_condition', ['ezf_version'=>$model->ver_code], 'ezf_id=:ezf_id AND ezf_version=:v', ['v' => $v, 'ezf_id' => $ezf_id])->execute();
                        
                    } catch (\yii\db\Exception $e) {
                        \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                    }
                    
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
     * Deletes an existing EzformVersion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $ver_code
     * @param integer $ezf_id
     * @return mixed
     */
    public function actionDelete($ezf_id, $v)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    if ($this->findModel($v, $ezf_id)->delete()) {
                try {
                    $queury = Yii::$app->db->createCommand()->delete('ezform_fields', 'ezf_id=:ezf_id AND ezf_version=:v', ['v' => $v, 'ezf_id' => $ezf_id])->execute();
                    
                    Yii::$app->db->createCommand()->delete('ezform_choice', 'ezf_id=:ezf_id AND ezf_version=:v', ['v' => $v, 'ezf_id' => $ezf_id])->execute();
                    Yii::$app->db->createCommand()->delete('ezform_condition', 'ezf_id=:ezf_id AND ezf_version=:v', ['v' => $v, 'ezf_id' => $ezf_id])->execute();
                } catch (\yii\db\Exception $e) {
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                }
                
		$result = [
		    'status' => 'success',
		    'action' => 'update',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		];
		return $result;
	    } else {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    public function actionCancel($ezf_id, $v)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($v, $ezf_id);
            $model->ver_approved = 0;
	    if ($model->save()) {
               
		$result = [
		    'status' => 'success',
		    'action' => 'update',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		];
		return $result;
	    } else {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionActive($ezf_id, $v)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($v, $ezf_id);
            $model->ver_active = 1;
            
	    if ($model->save()) {
               Yii::$app->db->createCommand()->update('ezform_version', ['ver_active'=>0], 'ezf_id=:ezf_id AND ver_code<>:v', [':ezf_id'=>$ezf_id, ':v'=>$v])->execute();
                
               $modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
               if($modelEzf){
                    $modelEzf->ezf_version = $model->ver_code;
                    $modelEzf->field_detail = $model->field_detail;
                    $modelEzf->ezf_sql = $model->ezf_sql;
                    $modelEzf->ezf_js = $model->ezf_js;
                    $modelEzf->ezf_error = $model->ezf_error;
                    $modelEzf->ezf_options = $model->ezf_options;
                    $modelEzf->save();
                }
               
		$result = [
		    'status' => 'success',
		    'action' => 'update',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		];
		return $result;
	    } else {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionApproved($ezf_id, $v)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($v, $ezf_id);
            $model->ver_approved = 2;
            $model->ver_active = 1;
            $model->approved_by = Yii::$app->user->id;
            $model->approved_date = new \yii\db\Expression('NOW()');
            
	    if ($model->save()) {
               Yii::$app->db->createCommand()->update('ezform_version', ['ver_active'=>0], 'ezf_id=:ezf_id AND ver_code<>:v', [':ezf_id'=>$ezf_id, ':v'=>$v])->execute();
               
               $modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
               if($modelEzf){
                    $modelEzf->ezf_version = $model->ver_code;
                    $modelEzf->field_detail = $model->field_detail;
                    $modelEzf->ezf_sql = $model->ezf_sql;
                    $modelEzf->ezf_js = $model->ezf_js;
                    $modelEzf->ezf_error = $model->ezf_error;
                    $modelEzf->ezf_options = $model->ezf_options;
                    $modelEzf->save();
                }
                
		$result = [
		    'status' => 'success',
		    'action' => 'update',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		];
		return $result;
	    } else {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionViewer($ezf_id, $v)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($v, $ezf_id);
            $model->ver_approved = 4;
	    if ($model->save()) {
               
		$result = [
		    'status' => 'success',
		    'action' => 'update',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		];
		return $result;
	    } else {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionApprov($ezf_id, $v)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($v, $ezf_id);
            $model->ver_approved = 1;
	    if ($model->save()) {
               
		$result = [
		    'status' => 'success',
		    'action' => 'update',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		];
		return $result;
	    } else {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionApprovCancel($ezf_id, $v)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($v, $ezf_id);
            $model->ver_approved = 3;
	    if ($model->save()) {
               
		$result = [
		    'status' => 'success',
		    'action' => 'update',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		];
		return $result;
	    } else {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
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
     * Finds the EzformVersion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $ver_code
     * @param integer $ezf_id
     * @return EzformVersion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ver_code, $ezf_id)
    {
        if (($model = EzformVersion::findOne(['ver_code' => $ver_code, 'ezf_id' => $ezf_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
