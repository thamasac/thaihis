<?php

namespace backend\modules\ezmodules\controllers;

use Yii;
use backend\modules\ezmodules\models\EzmoduleTab;
use backend\modules\ezmodules\models\EzmoduleTabSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezmodules\classes\ModuleQuery;

/**
 * EzmoduleTabController implements the CRUD actions for EzmoduleTab model.
 */
class EzmoduleTabController extends Controller
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

    public function actionSave($module)
    {
        if (Yii::$app->getRequest()->isAjax) {
            $id = Yii::$app->request->get('id', 0);
            $user_module = Yii::$app->request->get('user_module', 0);
            
            $userId = Yii::$app->user->id;

            $model = ModuleQuery::getTab($id);
            if(!$model){
                $model = new EzmoduleTab();
                $model->tab_id = SDUtility::getMillisecTime();
                $model->tab_default = ($user_module == $userId)?1:0;
                $model->ezm_id = $module;
                $model->user_id = $userId;
                $model->order = ModuleQuery::getTabOrder($module);
                $model->template = '{tab-widget}';
                $model->widget = 'form';
            }

            $modelModule = ModuleQuery::getModuleMyAllAddon($userId);

            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                
                if(isset($_POST['options'])){
                    $model->options = SDUtility::array2String($_POST['options']);
                }
                $model->order = isset($model->order) && !empty($model->order)?$model->order:0;
                $model->parent = isset($model->parent) && !empty($model->parent)?$model->parent:0;
                
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
                return $this->renderAjax('/ezmodule/_savetab', [
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
    
    /**
     * Deletes an existing EzmoduleTab model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $id = Yii::$app->request->get('id', 0);
        $module = Yii::$app->request->get('module', 0);
        $ftab = Yii::$app->request->get('ftab', 0);

        $model = EzmoduleTab::find()->where('tab_id=:id', [':id'=>$id])->one();

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
        $params = ['/ezmodules/ezmodule/view', 'id'=>$module];
        if($ftab!=$id){
            $params['tab'] = $ftab;
        }
        
        return $this->redirect($params);
    }
    
    public function actionGetTab()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $addon = Yii::$app->request->get('addon', 0);
            $tab = Yii::$app->request->get('tab', 0);
            $menu = Yii::$app->request->get('menu', 0);
            $module = Yii::$app->request->get('module', 0);
            $moduleID = Yii::$app->request->get('moduleID', 0);
            $controllerID = Yii::$app->request->get('controllerID', 0);
            $actionID = Yii::$app->request->get('actionID', 0);
            $filter = Yii::$app->request->get('filter', 0);
            $target = Yii::$app->request->get('target', 0);
            
            $userId = Yii::$app->user->id;
            $model = ModuleQuery::getModuleOne($module, $userId);
            
	    return $this->renderAjax('/ezmodule/_widget_tab_items', [
                'moduleID'=>$moduleID,
                'controllerID'=>$controllerID,
                'actionID'=>$actionID,
                'model' => $model,
                'menu'=>$menu,
                'addon'=>$addon,
                'tab'=>$tab,
                'module'=>$module,
                'filter'=>$filter,
                'target'=>$target,
            ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionGetForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
	if (Yii::$app->getRequest()->isAjax) {
            $widget = isset($_POST['widget'])?$_POST['widget']:'';
            $id = isset($_POST['id'])?$_POST['id']:'';
            
            $items_widget = array_keys(\backend\modules\ezmodules\classes\ModuleFunc::itemAlias('tab'));
            $items_widget_db = \backend\modules\core\classes\CoreFunc::itemAlias('tab');
            $items_widget = yii\helpers\ArrayHelper::merge($items_widget, array_keys($items_widget_db));
            
            $items_list = array_keys($items_widget_db);
            
            $html = '';
            if(in_array($widget, $items_widget)){
                $model = EzmoduleTab::findOne($id);
                if(!isset($model)){
                   $model = new EzmoduleTab();
                }
                 
                $html = $this->renderAjax('/ezmodule-tab/'.$widget.'/config', [
                    'model' => $model,
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
    
    public function actionGetWidget($view) {
	if (Yii::$app->getRequest()->isAjax) {
            $options = [];
            if(isset($_POST) && !empty($_POST)){
                $options = $_POST;
            }
	    $html = $this->renderAjax($view, $options);
	    
	    return $html;
	}
    }
    /**
     * Finds the EzmoduleTab model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EzmoduleTab the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EzmoduleTab::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
