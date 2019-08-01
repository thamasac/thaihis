<?php

namespace backend\modules\ezmodules\controllers;

use Yii;
use backend\modules\ezmodules\models\EzmoduleFilter;
use backend\modules\ezmodules\models\EzmoduleFilterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezmodules\classes\ModuleQuery;
use backend\modules\ezforms2\classes\EzfQuery;

/**
 * EzmoduleFilterController implements the CRUD actions for EzmoduleFilter model.
 */
class EzmoduleFilterController extends Controller
{
    

    /**
     * Creates a new EzmoduleFilter model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($module)
    {
	if (Yii::$app->getRequest()->isAjax) {
            $userProfile = Yii::$app->user->identity->profile;
            
	    $model = new EzmoduleFilter();
            $model->filter_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $model->ezm_id = $module;
            $model->sitecode = $userProfile->sitecode;
            $model->filter_order = 1000;
            $model->ezm_default = 0;
            $model->filter_type = 0;
            
            $userId = Yii::$app->user->id;
            $modelModule = ModuleQuery::getModuleID($module);
            if(!isset($modelModule->ezf_id)){
                return $this->renderAjax('_error', [
                            'msg' => Yii::t('ezmodule', 'Please select a form.'),
                ]);
            }
            $targetField = EzfQuery::getTargetOne($modelModule->ezf_id);
            $ezf_id;
            if(isset($targetField)){
                $ezf_id = $targetField->parent_ezf_id;
            } else {
                $ezf_id = $modelModule->ezf_id;
            }
            
            $user_module = isset($modelModule['created_by'])?$modelModule['created_by']:0;
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                $model->filter_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
                
                if(isset($model->share) && !empty($model->share)){
                    $model->share = implode(',', $model->share);
                }
                
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
                    'module'=>$module,
                    'user_module'=>$user_module,
                    'userId'=>$userId,
                    'ezf_id'=>$ezf_id,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Updates an existing EzmoduleFilter model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $model = $this->findModel($id);
            $model->share = explode(',', $model->share);
            
            $userId = Yii::$app->user->id;
            $modelModule = ModuleQuery::getModuleOne($model->ezm_id, $userId);
            if(!isset($modelModule->ezf_id)){
                return $this->renderAjax('_error', [
                            'msg' => Yii::t('ezmodule', 'Please select a form.'),
                ]);
            }
            $targetField = EzfQuery::getTargetOne($modelModule->ezf_id);
            $ezf_id;
            if(isset($targetField)){
                $ezf_id = $targetField->parent_ezf_id;
            } else {
                $ezf_id = $modelModule->ezf_id;
            }
            
            $user_module = isset($modelModule['created_by'])?$modelModule['created_by']:0;
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                
                if($model->ezm_default==1){
                    Yii::$app->db->createCommand()->update('ezmodule_filter', ['ezm_default'=>0], 'ezm_id=:ezm_id', [':ezm_id'=>$model->ezm_id])->execute();
                }
                
                if(isset($model->share) && !empty($model->share)){
                    $model->share = implode(',', $model->share);
                }
                
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
                    'module'=>$model->ezm_id,
                    'user_module'=>$user_module,
                    'userId'=>$userId,
                    'ezf_id'=>$ezf_id,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Deletes an existing EzmoduleFilter model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $addon = Yii::$app->request->get('addon', 0);
        $module = Yii::$app->request->get('module', 0);
        $model = $this->findModel($id);

        if ($model->delete()) {    
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
                'options' => ['class' => 'alert-success']
            ]);
        } else {
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgSuccess() . Yii::t('app', 'Can not delete the data.'),
                'options' => ['class' => 'alert-warning']
            ]);
        }

        return $this->redirect(['/ezmodules/ezmodule/view', 'id'=>$module, 'addon'=>$addon]);

    }
    
    public function actionGetFilter()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $addon = Yii::$app->request->get('addon', 0);
            $menu = Yii::$app->request->get('menu', 0);
            $module = Yii::$app->request->get('module', 0);
            $moduleID = Yii::$app->request->get('moduleID', 0);
            $controllerID = Yii::$app->request->get('controllerID', 0);
            $actionID = Yii::$app->request->get('actionID', 0);
            $filter = Yii::$app->request->get('filter', 0);
            
            $userId = Yii::$app->user->id;
            $moduleId = $module;
            
            if($addon>0){
                $moduleId = $addon;
            } 
            $model = ModuleQuery::getModuleOne($moduleId, $userId);
            
            $dataFilter = ModuleQuery::getFilterList($moduleId, $userId);
            
	    return $this->renderAjax('/ezmodule/_widget_filter_items', [
                'moduleID'=>$moduleID,
                'controllerID'=>$controllerID,
                'actionID'=>$actionID,
                'model' => $model,
                'menu'=>$menu,
                'addon'=>$addon,
                'module'=>$module,
                'filter'=>$filter,
                'dataFilter'=>$dataFilter,
            ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionListFilter()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $module = Yii::$app->request->get('module', 0);
            $filter = Yii::$app->request->get('filter', 0);
            
            $userId = Yii::$app->user->id;
            
            $dataFilter = ModuleQuery::getFilterListCutom($module, $filter, $userId);
            
	    return $this->renderAjax('_list', [
                'module'=>$module,
                'filter'=>$filter,
                'dataFilter'=>$dataFilter,
            ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionAddFilter() {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
            $module = Yii::$app->request->get('module', 0);
            $filter = Yii::$app->request->get('filter', 0);
            
	    if (isset($_POST['selection'])) {
		foreach ($_POST['selection'] as $id) {
		    //$this->findModel($id)->delete();
                    try {
                        $model = new \backend\modules\ezmodules\models\EzmoduleFilterList();
                        $model->list_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
                        $model->ezm_id = $module;
                        $model->filter_id = $filter;
                        $model->dataid = $id;
                        $model->save();
                    } catch (\yii\db\Exception $e) {
                        \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                    }
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
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionDelFilter() {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
            $module = Yii::$app->request->get('module', 0);
            $filter = Yii::$app->request->get('filter', 0);
            
	    if (isset($_POST['selection'])) {
		foreach ($_POST['selection'] as $id) {
                    try {
                        $model = \backend\modules\ezmodules\models\EzmoduleFilterList::find()
                                ->where('ezm_id=:ezm_id AND filter_id=:filter_id AND dataid=:dataid', [':ezm_id'=>$module, ':filter_id'=>$filter, ':dataid'=>$id])->one();
                        if($model){
                            $model->delete();
                        }
                    } catch (\yii\db\Exception $e) {
                        \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                    }
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
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionGetCondition()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = Yii::$app->request->get('ezf_id', 0);
            $modelForms = \backend\modules\ezforms2\classes\EzfQuery::getEzformList($ezf_id);
            $dataForm= [];
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
                ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    /**
     * Finds the EzmoduleFilter model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EzmoduleFilter the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EzmoduleFilter::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
