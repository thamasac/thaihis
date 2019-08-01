<?php

namespace backend\modules\ezmodules\controllers;

use Yii;
use backend\modules\ezmodules\models\EzmoduleForms;
use backend\modules\ezmodules\models\EzmoduleFormsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezmodules\classes\ModuleQuery;
use backend\modules\ezforms2\classes\EzfQuery;

/**
 * EzmoduleFormsController implements the CRUD actions for EzmoduleForms model.
 */
class EzmoduleFormsController extends Controller
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
     * Creates a new EzmoduleForms model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $module = isset($_GET['module']) ? $_GET['module'] : 0;
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $addon = isset($_GET['addon']) ? $_GET['addon'] : 0;
            $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : 0;
            
            $moduleId = $module;
            if($addon>0){
                $moduleId = $addon;
            }
            
	    $model = new EzmoduleForms();
            $model->form_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $model->form_order = ModuleQuery::getFormsCountById($moduleId);
            $model->ezm_id = $moduleId;
            $model->form_default = 0;
            $modelModule = ModuleQuery::getModuleID($moduleId);
            if($modelModule){
                if($modelModule->created_by == Yii::$app->user->id){
                    $model->form_default = 1;
                }
            }
            
            $targetField = EzfQuery::getTargetOne($ezf_id);
            if(isset($targetField)){
                $ezf_id = $targetField->ref_ezf_id;
            }
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                $model->form_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
                
                if(isset($_POST['options'])){
                    $model->options = \appxq\sdii\utils\SDUtility::array2String($_POST['options']);
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
                    'ezf_id'=>$ezf_id,
                    'reloadDiv'=>$reloadDiv,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Updates an existing EzmoduleForms model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $model = $this->findModel($id);
            $module = isset($_GET['module']) ? $_GET['module'] : 0;
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $addon = isset($_GET['addon']) ? $_GET['addon'] : 0;
            $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : 0;
            
            $targetField = EzfQuery::getTargetOne($ezf_id);
            if(isset($targetField)){
                $ezf_id = $targetField->ref_ezf_id;
            }
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                
                if(isset($_POST['options'])){
                    $model->options = \appxq\sdii\utils\SDUtility::array2String($_POST['options']);
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
                    'ezf_id'=>$ezf_id,
                    'reloadDiv'=>$reloadDiv,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Deletes an existing EzmoduleForms model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
            $module = isset($_GET['module']) ? $_GET['module'] : 0;
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $addon = isset($_GET['addon']) ? $_GET['addon'] : 0;
            $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : 0;
            
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

    public function actionGetForms($q = null, $id = null) {
        $ezf_id = Yii::$app->request->get('ezf_id', 0);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
        
        $model = \backend\modules\ezmodules\classes\ModuleQuery::getEzformListFind($ezf_id, $q);
        // Previous array lacks keywords needed to use optgroups
        // in AJAX-based Select2: 'results', 'id', 'text', 'children'.
        // Let's insert them.
        $results = []; 
        foreach ($model as $key => $form) {
            $results[] = ['id' => $form['ezf_id'], 'text' => $form['ezf_name']];
        }
        $out['results'] = $results;
        
        return $out;
    }
    
    public function actionGetShow()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = Yii::$app->request->get('ezf_id', 0);
            $prefix = Yii::$app->request->get('prefix', '');
            
            $modelFields = ModuleQuery::getFieldsOptionList($ezf_id);
            $dataFields = [];
            if(isset($modelFields)){
                $dataFields = \yii\helpers\ArrayHelper::map($modelFields, 'id', 'name');
            }
            $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $value = [];
	    return $this->renderAjax('_show', [
                'id'=>$id,
                'ezf_id'=>$ezf_id,
                'dataFields'=>$dataFields,
                'value'=>$value,
                'prefix'=>$prefix,
                ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionGetField()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = Yii::$app->request->get('ezf_id', 0);
            $prefix = Yii::$app->request->get('prefix', '');
            
            $modelFields = ModuleQuery::getFieldsOptionList($ezf_id);
            $dataFields = [];
            if(isset($modelFields)){
                $dataFields = \yii\helpers\ArrayHelper::map($modelFields, 'id', 'name');
            }
            $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $value = [];
	    return $this->renderAjax('_field', [
                'id'=>$id,
                'ezf_id'=>$ezf_id,
                'dataFields'=>$dataFields,
                'value'=>$value,
                'prefix'=>$prefix,
                ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionGetCondition()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = Yii::$app->request->get('ezf_id', 0);
            $prefix = Yii::$app->request->get('prefix', '');
            
            $modelForms = \backend\modules\ezforms2\classes\EzfQuery::getEzformList($ezf_id);
            $dataForm = [];
            if(isset($modelForms)){
                $dataForm = \yii\helpers\ArrayHelper::map($modelForms, 'ezf_id', 'ezf_name');
            }

            $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $value = [];
	    return $this->renderAjax('_condition', [
                'id'=>$id,
                'ezf_id'=>$ezf_id,
                'dataForm'=>$dataForm,
                'value'=>$value,
                'prefix'=>$prefix,
                ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionGetSubform()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = Yii::$app->request->get('ezf_id', 0);
            $parent_ezf_id = Yii::$app->request->get('parent_ezf_id', 0);
            $lvl = Yii::$app->request->get('lvl', 1);
            $color = Yii::$app->request->get('color', 'default');
            $prefix = Yii::$app->request->get('prefix', '');
            $margin = Yii::$app->request->get('margin', 0);
            
            $modelForms = ModuleQuery::getEzformList($ezf_id);
            $dataForm = [];
            if(isset($modelForms)){
                $dataForm = \yii\helpers\ArrayHelper::map($modelForms, 'ezf_id', 'ezf_name');
            }
            $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $value = [];
            $dataFormCond = [];
	    return $this->renderAjax('_subform', [
                'id'=>$id,
                'ezf_id'=>$ezf_id,
                'parent_ezf_id'=>$parent_ezf_id,
                'lvl'=>$lvl,
                'color'=>$color,
                'prefix'=>$prefix,
                'margin'=>$margin,
                'dataForm'=>$dataForm,
                'value'=>$value,
                'dataFormCond'=>$dataFormCond,
                ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionFormsWidget()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = Yii::$app->request->get('ezf_id', 0);
            $parent_ezf_id = Yii::$app->request->get('parent_ezf_id', 0);
            $special = Yii::$app->request->get('special', 0);
            $reloadDiv = Yii::$app->request->get('reloadDiv', 'nodivname');
            $modal = Yii::$app->request->get('modal', 'modal-ezform-main');
            $options = Yii::$app->request->get('options', '');
            $data = Yii::$app->request->get('data', '');
            $data = \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($data);
            $options = \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($options);
            
	    return $this->renderAjax('_grid_item', [
                'ezf_id'=>$ezf_id,
                'parent_ezf_id' => $parent_ezf_id,
                'special'=>$special,
                'data'=>$data,
                'reloadDiv'=>$reloadDiv,
                'modal'=>$modal,
                'options'=>$options,
            ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    /**
     * Finds the EzmoduleForms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EzmoduleForms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EzmoduleForms::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
