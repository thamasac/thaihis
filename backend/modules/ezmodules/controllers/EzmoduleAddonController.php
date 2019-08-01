<?php

namespace backend\modules\ezmodules\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezmodules\classes\ModuleQuery;
use backend\modules\ezmodules\models\EzmoduleAddon;

/**
 * EzmoduleController implements the CRUD actions for Ezmodule model.
 */
class EzmoduleAddonController extends Controller
{

    public function beforeAction($action) {
	if (parent::beforeAction($action)) {
	    if (in_array($action->id, array('create', 'update'))) {
		
	    }
	    return true;
	} else {
	    return false;
	}
    }
    
    public function actionDelete()
    {
        $id = Yii::$app->request->get('id', 0);
        $module = Yii::$app->request->get('module', 0);

        $model = EzmoduleAddon::find()->where('module_id=:id AND ezm_id=:module', [':id'=>$id, ':module'=>$module])->one();

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
        
        return $this->redirect(['/ezmodules/ezmodule/view', 'id'=>$module]);
    }
    
    public function actionSave($module)
    {
        if (Yii::$app->getRequest()->isAjax) {
            $id = Yii::$app->request->get('id', 0);
            $user_module = Yii::$app->request->get('user_module', 0);
            
            $userId = Yii::$app->user->id;

            
            
            $model = new EzmoduleAddon();
            $model->addon_id = SDUtility::getMillisecTime();
            $model->addon_default = ($user_module == $userId)?1:0;
            $model->ezm_id = $module;
            $model->user_id = $userId;

            $modelModule = ModuleQuery::getModuleMyAllAddon($userId);

            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                
                $checkModule = EzmoduleAddon::find()->where('ezm_id=:ezm_id AND module_id=:module_id AND user_id=:userId', [':ezm_id'=>$module, ':module_id'=>$model->module_id, ':userId'=>$userId])->one();
                if($checkModule){
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'This module is already selected.'),
                        'data' => $model,
                    ];
                    return $result;
                }
                
                if ($model->save()) {
                    
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                        'data' => $model,
                    ];
                    return $result;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                        'data' => $model,
                    ];
                    return $result;
                }
            } else {
                return $this->renderAjax('/ezmodule/_saveaddon', [
                    'model' => $model,
                    'module'=>$module,
                    'modelModule' => $modelModule,
                    'id'=>$id,
                ]);
            } 
        } else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionGetModule()
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
            $model = ModuleQuery::getModuleOne($module, $userId);
            
	    return $this->renderAjax('/ezmodule/_widget_module_items', [
                'moduleID'=>$moduleID,
                'controllerID'=>$controllerID,
                'actionID'=>$actionID,
                'model' => $model,
                'menu'=>$menu,
                'addon'=>$addon,
                'module'=>$module,
                'filter'=>$filter,
            ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
}
