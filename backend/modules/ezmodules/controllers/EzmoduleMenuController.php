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
use backend\modules\ezmodules\models\EzmoduleMenu;

/**
 * EzmoduleController implements the CRUD actions for Ezmodule model.
 */
class EzmoduleMenuController extends Controller
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

        $model = EzmoduleMenu::find()->where('menu_id=:id', [':id'=>$id])->one();
        $model->menu_active = 0;
        //if ($this->findModel($id)->delete()) {
        if ($model->save()) {    
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
            $userId = Yii::$app->user->id;

            if(Yii::$app->user->can('administrator')){
                $model = EzmoduleMenu::find()->where('menu_id=:id AND ezm_id=:ezm_id AND menu_active=1', [':id'=>$id, ':ezm_id'=>$module])->one();
            } else {
                $model = EzmoduleMenu::find()->where('menu_id=:id AND ezm_id=:ezm_id AND created_by=:created_by AND menu_active=1', [':id'=>$id, ':ezm_id'=>$module, ':created_by'=>$userId])->one();
            }

            if(!$model){
                $model = new EzmoduleMenu();
                $model->menu_id = SDUtility::getMillisecTime();
                $model->ezm_id = $module;
                $model->menu_active = 1;
            }

            $modelMenu = EzmoduleMenu::find()->where(['ezm_id' => $module,'menu_parent'=>0, 'menu_active'=>1])->addOrderBy('menu_order')->all();
            $modelOrderMenu = EzmoduleMenu::find()->where(['ezm_id' => $module, 'menu_active'=>1])->addOrderBy('menu_order')->all();

            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                
                $model->menu_order -= 5; 
                
                if ($model->save()) {
                    $this->reorder($module);
                    
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
                return $this->renderAjax('/ezmodule/_savemenu', [
                    'model' => $model,
                    'module'=>$module,
                    'modelMenu' => $modelMenu,
                    'modelOrderMenu' => $modelOrderMenu,    
                    'id'=>$id,
                ]);
            } 
        } else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function reorder($module) {
        Yii::$app->db->createCommand("set @i:=0;UPDATE ezmodule_menu set `menu_order`=(@i:=@i+1)*10 WHERE ezm_id=:ezm_id order by `menu_order`", [':ezm_id'=>$module])->query();
    }
    
    public function actionGetMenu()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $menu = Yii::$app->request->get('menu', 0);
            $module = Yii::$app->request->get('module', 0);
            $moduleID = Yii::$app->request->get('moduleID', 0);
            $controllerID = Yii::$app->request->get('controllerID', 0);
            $actionID = Yii::$app->request->get('actionID', 0);
            
            $userId = Yii::$app->user->id;
            $model = ModuleQuery::getModuleOne($module, $userId);
            
	    return $this->renderAjax('/ezmodule/_widget_menu_items', [
                'moduleID'=>$moduleID,
                'controllerID'=>$controllerID,
                'actionID'=>$actionID,
                'model' => $model,
                'menu'=>$menu,
                'module'=>$module,
            ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
}
