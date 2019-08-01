<?php

namespace backend\modules\ezmodules\controllers;

use Yii;
use backend\modules\ezmodules\models\EzmoduleFields;
use backend\modules\ezmodules\models\EzmoduleFieldsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezmodules\classes\ModuleQuery;

/**
 * EzmoduleFieldsController implements the CRUD actions for EzmoduleFields model.
 */
class EzmoduleFieldsController extends Controller
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
     * Creates a new EzmoduleFields model.
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
            
	    $model = new EzmoduleFields();
            $model->field_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $model->field_order = ModuleQuery::getFieldsCountById($moduleId);
            $model->ezm_id = $moduleId;
            $model->field_default = 0;
            $modelModule = ModuleQuery::getModuleID($moduleId);
            if($modelModule){
                if($modelModule->created_by == Yii::$app->user->id){
                    $model->field_default = 1;
                }
            }
            
            $inform = NULL;
            if($ezf_id>0){
                $inform = $ezf_id;
                $targetField = EzfQuery::getTargetOne($ezf_id);
                if(isset($targetField)){
                    $inform .= ','.$targetField->ref_ezf_id;
                }
            }
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                $model->field_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
                
                if(isset($model->options) && !empty($model->options)){
                    $model->options = \appxq\sdii\utils\SDUtility::array2String($model->options);
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
                    'inform'=>$inform,
                    'reloadDiv'=>$reloadDiv,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Updates an existing EzmoduleFields model.
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
            
            if(isset($model->options) && !empty($model->options)){
                $model->options = \appxq\sdii\utils\SDUtility::string2Array($model->options);
            }
            
            $inform = NULL;
            if($ezf_id>0){
                $inform = $ezf_id;
                $targetField = EzfQuery::getTargetOne($ezf_id);
                if(isset($targetField)){
                    $inform .= ','.$targetField->ref_ezf_id;
                }
            }

            if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                
                if(isset($model->options) && !empty($model->options)){
                    $model->options = \appxq\sdii\utils\SDUtility::array2String($model->options);
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
                    'inform'=>$inform,
                    'reloadDiv'=>$reloadDiv,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Deletes an existing EzmoduleFields model.
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

    public function actionGetFields($q = null, $id = null) {
        $inform = Yii::$app->request->get('inform', '');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }

        $data = \backend\modules\ezmodules\classes\ModuleQuery::getFieldsFind($inform, $q);
        $gArray = \yii\helpers\ArrayHelper::map($data, 'id', 'name', 'ezf_name');
        $ezfArray = \yii\helpers\ArrayHelper::map($data, 'id', 'ezf_id');
        // Previous array lacks keywords needed to use optgroups
        // in AJAX-based Select2: 'results', 'id', 'text', 'children'.
        // Let's insert them.
        $results = []; 
        foreach ($gArray as $form => $fArray) {
            $fields  = [];
            foreach ($fArray as $id => $name) {
                $fields[] = ['id' => "{$id}", 'text' => $name, 'ezf_id'=>"{$ezfArray[$id]}"];
            }
            $results[] = ['text' => $form, 'children' => $fields];
        }
        $out['results'] = $results;
//        foreach ($data as $value) {
//            $out["results"][] = ['id' => "{$value['id']}", 'text' => $value['name'], 'ezf_id' => $value['ezf_id']];
//        }
        
        return $out;
    }
    /**
     * Finds the EzmoduleFields model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EzmoduleFields the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EzmoduleFields::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
