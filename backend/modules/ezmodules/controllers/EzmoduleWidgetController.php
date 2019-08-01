<?php

namespace backend\modules\ezmodules\controllers;

use Yii;
use backend\modules\ezmodules\models\EzmoduleWidget;
use backend\modules\ezmodules\models\EzmoduleWidgetSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;

/**
 * EzmoduleWidgetController implements the CRUD actions for EzmoduleWidget model.
 */
class EzmoduleWidgetController extends Controller
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
     * Lists all EzmoduleWidget models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EzmoduleWidgetSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionList()
    {
        $searchModel = new EzmoduleWidgetSearch();
        $dataProvider = $searchModel->searchList(Yii::$app->request->queryParams);

        return $this->renderAjax('list', [
            'ezm_id' => 0,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionListModule($ezm_id)
    {
        $searchModel = new EzmoduleWidgetSearch();
        //$searchModel->ezm_id = $ezm_id;
        $dataProvider = $searchModel->searchListModule(Yii::$app->request->queryParams, $ezm_id);

        return $this->renderAjax('list', [
            'ezm_id' => $ezm_id,
            
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EzmoduleWidget model.
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
     * Creates a new EzmoduleWidget model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $ezm_id = Yii::$app->request->get('ezm_id', 0);
            $ezf_id = Yii::$app->request->get('ezf_id', 0);
            $modal = isset($_GET['modal'])?$_GET['modal']:0;
            
	    $model = new EzmoduleWidget();
            $model->widget_id = SDUtility::getMillisecTime();
            $model->enable = 1;
            $model->ezm_id = $ezm_id;
            $model->ezf_id = $ezf_id;
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                $model->widget_id = SDUtility::getMillisecTime();
                
                if($model->widget_type=='core'){
                    $model->ezm_id = 0;
                    $model->ezf_id = 0;
                } else {
                    $model->widget_render = '_widget_dynamic';
                }
                
                if(isset($_POST['options'])){
                    $model->options = SDUtility::array2String($_POST['options']);
                }
                
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
                    'modal' => $modal,
                    'ezm_id' => $ezm_id,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Updates an existing EzmoduleWidget model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $modal = isset($_GET['modal'])?$_GET['modal']:0;
        
	if (Yii::$app->getRequest()->isAjax) {
	    $model = $this->findModel($id);
            $ezm_id = $model->ezm_id;
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;

                if($model->widget_type=='core'){
                    $model->ezm_id = 0;
                    $model->ezf_id = 0;
                } else {
                    $model->widget_render = '_widget_dynamic';
                }
                
                if(isset($_POST['options'])){
                    $model->options = SDUtility::array2String($_POST['options']);
                }
                
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
                    'modal' => $modal,
                    'ezm_id' => $ezm_id,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Deletes an existing EzmoduleWidget model.
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
    
    public function actionRenderWidget()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
	if (Yii::$app->getRequest()->isAjax) {
            $varname = isset($_POST['varname'])?$_POST['varname']:'';
            $ezm_id = isset($_GET['ezm_id'])?$_GET['ezm_id']:'';
            $op_params = isset($_POST['op'])?\backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($_POST['op']):[];
            
            $widget = \backend\modules\ezmodules\models\EzmoduleWidget::find()->where("ezm_id=:ezm_id AND widget_varname=:widget ", [':ezm_id'=>$ezm_id, ':widget'=>$varname])->one();
            if($widget){
                if($widget['widget_render']=='_widget_dynamic'){
                    $widget['widget_render'] = '/ezmodule/'.$widget['widget_render'];
                }
                $op_params['reloadDiv'] = $op_params['reloadDiv'].'-'.$widget['widget_varname'].'-'.SDUtility::getMillisecTime();
                $html = $this->renderAjax($widget['widget_render'], \yii\helpers\ArrayHelper::merge($op_params, ['widget_config'=>$widget]));

                return '<div class="col-md-12" data-dad-id="'.$widget['widget_varname'].'" data-dad-col="12" style="margin-bottom: 10px;">'.$html.'</div>';
            } else {
                return '';
            }
	} else {
            return '';
	}
    }
    
    public function actionGetForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
	if (Yii::$app->getRequest()->isAjax) {
            $widget = isset($_POST['widget'])?$_POST['widget']:'';
            $id = isset($_POST['id'])?$_POST['id']:'';
            $ezf_id = isset($_POST['ezf_id'])?$_POST['ezf_id']:'';
            $ezm_id = isset($_POST['ezm_id'])?$_POST['ezm_id']:'';
            
            $items_widget = array_keys(\backend\modules\ezmodules\classes\ModuleFunc::itemAlias('widget'));
            $items_widget_db = \backend\modules\core\classes\CoreFunc::itemAlias('widget');
            $items_widget = yii\helpers\ArrayHelper::merge($items_widget, array_keys($items_widget_db));
            
            $items_list = array_keys($items_widget_db);
            
            $html = '';
            if(in_array($widget, $items_widget)){
                $model = EzmoduleWidget::findOne($id);
                if(!isset($model)){
                   $model = new EzmoduleWidget();
                }
                 
                $html = $this->renderAjax('/ezmodule-widget/assets/'.$widget.'/config', [
                    'model' => $model,
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                ]);
            } 
            
            $result = [
		    'status' => 'success',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Gen completed.'),
		    'html' => $html,
		];
		return $result;
	} else {
	    $result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('app', 'Invalid request. Please do not repeat this request again.'),
		];
		return $result;
	}
    }
    
    public function actionGetDb2() {
        $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : 0;
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $value = isset($_POST['value']) ? $_POST['value'] : '';
        $id = isset($_POST['id']) ? $_POST['id'] : \appxq\sdii\utils\SDUtility::getMillisecTime();
        
        $modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
        
        if(isset($modelEzf->ezf_db2) && $modelEzf->ezf_db2==1){
            return \backend\modules\ezforms2\classes\EzformWidget::checkbox($name, $value, ['id'=>$id, 'label'=> Yii::t('ezmodule', 'Key Operator 2')]);
        } 
        
        return '';
    }
    
    public function actionTabsRender($path, $params) {
        $params = \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($params);
        
        return \yii\helpers\Json::encode($this->renderAjax($path, $params));
    }
    
    public function actionProcessControl()
    {
        $module = isset($_GET['module'])?$_GET['module']:0;
        
        return $this->renderAjax('/ezmodule-widget/_process_control', [
            'module'=>$module,
        ]);
    }
    
    public function actionProcessReceive()
    {
        $id = isset($_POST['id'])?$_POST['id']:0;
        
       Yii::$app->response->format = Response::FORMAT_JSON;
        
	if (Yii::$app->getRequest()->isAjax) {
            $modelQueue = \backend\modules\ezforms2\models\QueueLog::find()->where('id=:id', [':id'=>$id])->one();
            if($modelQueue){

                $modelQueue->user_receive = Yii::$app->user->id;
                $modelQueue->time_receive = new \yii\db\Expression('NOW()');
                $modelQueue->status = 'process';
                
                if($modelQueue->save()){
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                        'data' => $modelQueue->attributes,
                    ];
                    return $result;
                }
            } 
            
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not update the data.'),
                'data' => $id,
            ];
            return $result;
        } else {
	    $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Invalid request. Please do not repeat this request again.'),
            ];
            return $result;
	}
    }
    
    public function actionProcessComplete()
    {
        $id = isset($_POST['id'])?$_POST['id']:0;
        
       Yii::$app->response->format = Response::FORMAT_JSON;
        
	if (Yii::$app->getRequest()->isAjax) {
            $modelQueue = \backend\modules\ezforms2\models\QueueLog::find()->where('id=:id', [':id'=>$id])->one();
            if($modelQueue){
                
                $modelQueue->status = 'completed';
                
                if($modelQueue->save()){
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                        'data' => $modelQueue->attributes,
                    ];
                    return $result;
                }
            } 
            
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not update the data.'),
                'data' => $id,
            ];
            return $result;
        } else {
	    $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Invalid request. Please do not repeat this request again.'),
            ];
            return $result;
	}
    }
    
    public function actionGetMyWidget($q = null, $id = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        //$sitecode = \Yii::$app->user->identity->profile->sitecode;
        
        $out = ['results' => []];
        
        if (is_null($q)) {
            $q = '';
        }
        
        $data = \backend\modules\ezmodules\classes\ModuleQuery::getWidgetList($q);
        
        foreach ($data as $value) {
            $out["results"][] = ['id' => "{$value['widget_id']}", 'text' => $value["fname"]];
        }
        
        return $out;
    }
    
    public function actionCloneWidget()
    {
        $id = isset($_POST['id'])?$_POST['id']:0;
        $ezm_id = isset($_GET['ezm_id'])?$_GET['ezm_id']:0;
        
       Yii::$app->response->format = Response::FORMAT_JSON;
        
	if (Yii::$app->getRequest()->isAjax) {
            
            $modelWidget = EzmoduleWidget::find()->where('widget_id=:id', [':id'=>$id])->one();
            if($modelWidget){
                
                $model = new EzmoduleWidget();
                $model->attributes = $modelWidget->attributes;
                
                $model->widget_id = SDUtility::getMillisecTime();
                $model->ezm_id = $ezm_id;
                $model->widget_varname = '';
                $model->widget_name = $model->widget_name.'-clone';
                $model->created_by = Yii::$app->user->id;
                $model->created_at = new \yii\db\Expression('NOW()');
                $model->updated_by = Yii::$app->user->id;
                $model->updated_at = new \yii\db\Expression('NOW()');
                
                if($model->save()){
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                        'data' => $model->attributes,
                    ];
                    return $result;
                }
            } 
            
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not clone the widget.'),
                'data' => $id,
            ];
            return $result;
        } else {
	    $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Invalid request. Please do not repeat this request again.'),
            ];
            return $result;
	}
    }
    /**
     * Finds the EzmoduleWidget model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EzmoduleWidget the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EzmoduleWidget::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
