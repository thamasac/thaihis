<?php

namespace backend\modules\ezmodules\controllers;

use common\models\User;
use MongoDB\Driver\Exception\AuthenticationException;
use Yii;
use backend\modules\ezmodules\models\Ezmodule;
use backend\modules\ezmodules\models\EzmoduleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezmodules\classes\ModuleQuery;
use backend\modules\ezmodules\classes\ModuleFunc;
use backend\modules\ezmodules\models\EzmoduleTemplate;
use backend\modules\ezmodules\models\EzmoduleMenu;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\models\TbdataAll;
use backend\modules\ezmodules\models\EzmoduleFields;
use yii\web\UnauthorizedHttpException;

/**
 * EzmoduleController implements the CRUD actions for Ezmodule model.
 */
class EzmoduleController extends Controller
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
     * Lists all Ezmodule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EzmoduleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        \backend\modules\manageproject\classes\CNFunc::addLog("EzModule Managemen ". SDUtility::array2String($dataProvider->getModels()));
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Ezmodule model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
       
        
        $menu = Yii::$app->request->get('menu', 0);
        $module = Yii::$app->request->get('id', 0);
        $addon = Yii::$app->request->get('addon', 0);
        $tab = Yii::$app->request->get('tab', 0);
        $filter = Yii::$app->request->get('filter', '0');
        $target = Yii::$app->request->get('target', '');
        
        $userId = Yii::$app->user->id;
        $model = ModuleQuery::getModuleOne($id, $userId);
        if(!$model){
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgSuccess() . Yii::t('ezmodule', 'You do not have right to use this page.'),
                'options' => ['class' => 'alert-warning']
            ]);
            
            return $this->redirect(['/ezmodules/default/index']);
        }
        
        if($addon>0){
            $modelModule = ModuleQuery::getModuleOne($addon, $userId);
            $modelWidget = \backend\modules\ezmodules\models\EzmoduleWidget::find()->where("widget_type='core' OR ezm_id=:ezm_id", [':ezm_id'=>$addon])->all();
        } else {
            $modelModule = $model;
            $modelWidget = \backend\modules\ezmodules\models\EzmoduleWidget::find()->where("widget_type='core' OR ezm_id=:ezm_id", [':ezm_id'=>$module])->all();
        }
        
        if(!$modelModule && !isset($modelModule->ezf_id)){
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgSuccess() . Yii::t('ezmodule', 'You do not have right to use this page.'),
                'options' => ['class' => 'alert-warning']
            ]);
            
            return $this->redirect(['/ezmodules/default/index']);
        }
        
        $template = EzmoduleTemplate::findOne($modelModule->template_id);
        if(!isset($model->ezm_html) && $template){
            $modelModule->ezm_html = $template->template_html;
            $modelModule->ezm_css = $template->template_css;
            $modelModule->save();
        }
        
        $ezf_id = $modelModule->ezf_id;
            
        $dataFilter = ModuleQuery::getFilterList($modelModule->ezm_id, $userId);

        $modelFilter = NULL;
        if($filter==0 && $filter!=''){
           $modelFilter = \backend\modules\ezmodules\models\EzmoduleFilter::find()->where('ezm_id = :ezm_id AND `ezm_default`=1', [':ezm_id'=>$modelModule->ezm_id])->one();
           $filter = isset($modelFilter->filter_id)?$modelFilter->filter_id:0;
        } else {
            $modelFilter = \backend\modules\ezmodules\models\EzmoduleFilter::find()->where('filter_id = :filter_id', [':filter_id'=>$filter])->one();
        }
        
        Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
        
        $layout = \Yii::$app->request->get('layout', '');
        
         \backend\modules\manageproject\classes\CNFunc::addLog("View module {$model->ezm_name} ". SDUtility::array2String($model));
         
        if ($layout == "nolayout") {
            $this->layout = "@backend/views/layouts/main2";
            return $this->render('view', [
                'model' => $model,
                'template' => $template,
                'menu'=>$menu,
                'module'=>$module,
                'addon'=>$addon,
                'tab'=>$tab,
                'filter'=>$filter,
                'dataFilter'=>$dataFilter,
                'modelModule'=>$modelModule,
                'modelFilter'=>$modelFilter,
                'modelWidget'=>$modelWidget,
                'target'=>$target,
                'id'=>$id
            ]);
        }
        
        if(\Yii::$app->request->isAjax){
            return $this->renderAjax('view', [
                'model' => $model,
                'template' => $template,
                'menu'=>$menu,
                'module'=>$module,
                'addon'=>$addon,
                'tab'=>$tab,
                'filter'=>$filter,
                'dataFilter'=>$dataFilter,
                'modelModule'=>$modelModule,
                'modelFilter'=>$modelFilter,
                'modelWidget'=>$modelWidget,
                'target'=>$target,
                'id'=>$id
            ]);
        }        
	return $this->render('view', [
            'model' => $model,
            'template' => $template,
            'menu'=>$menu,
            'module'=>$module,
            'addon'=>$addon,
            'tab'=>$tab,
            'filter'=>$filter,
            'dataFilter'=>$dataFilter,
            'modelModule'=>$modelModule,
            'modelFilter'=>$modelFilter,
            'modelWidget'=>$modelWidget,
            'target'=>$target,
            'id'=>$id
        ]);
    }
    
    public function actionTemplate($id)
    {
        $module = Yii::$app->request->get('id', 0);
        $template = Yii::$app->request->get('template', 0);

        $userId = Yii::$app->user->id;
        $model = ModuleQuery::getModuleOne($id, $userId);
        if(!$model){
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgSuccess() . Yii::t('ezmodule', 'You do not have right to use this page.'),
                'options' => ['class' => 'alert-warning']
            ]);
            
            return $this->redirect(['/ezmodules/default/index']);
        }
        
        if($template==0){
            $template = $model->template_id;
        }
        
        $modelTemplate = EzmoduleTemplate::findOne($template);
        if(!$modelTemplate){
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgSuccess() . Yii::t('ezmodule', 'Template not found.'),
                'options' => ['class' => 'alert-warning']
            ]);
            
            return $this->redirect(['/ezmodules/ezmodule/template', 'id'=>$module]);
        }
        
        if ($modelTemplate->load(Yii::$app->request->post())) {
        
	    if ($modelTemplate->save()) {
		Yii::$app->session->setFlash('alert', [
		    'body' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
		    'options' => ['class' => 'alert-success']
		]);
                
                $model->template_id = $modelTemplate->template_id;
                $model->save();
                
		return $this->redirect(['/ezmodules/ezmodule/template', 'id'=>$module, 'template'=>$modelTemplate->template_id]);
	    } else {
		Yii::$app->session->setFlash('alert', [
		    'body' => SDHtml::getMsgError() . Yii::t('app', 'Can not create the data.'),
		    'options' => ['class' => 'alert-danger']
		]);
	    }
	}
        
        $ezf_id = $model->ezf_id;
        
        $modelWidget = \backend\modules\ezmodules\models\EzmoduleWidget::find()->where("widget_type='core' OR ezm_id=:ezm_id", [':ezm_id'=>$module])->all();
        $modelDataTmp = \backend\modules\ezmodules\models\EzmoduleTemplate::find()->where("template_system=1 OR created_by=:created_by", [':created_by'=>$userId])->all();
        
        $searchModel = new \backend\modules\ezmodules\models\EzmoduleWidgetSearch();
        $dataProvider = $searchModel->searchListModule(Yii::$app->request->queryParams, $module);
                
	return $this->render('template', [
            'model' => $model,
            'template' => $template,
            'modelTemplate' => $modelTemplate,
            'modelWidget' => $modelWidget,
            'modelDataTmp' => $modelDataTmp,
            'module' => $module,
            'ezf_id' => $ezf_id,
            'userId' => $userId,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionReport($id)
    {
        $menu = Yii::$app->request->get('menu', 0);
        $module = Yii::$app->request->get('id', 0);
        $addon = Yii::$app->request->get('addon', 0);
        $filter = Yii::$app->request->get('filter', '0');

        $userId = Yii::$app->user->id;
        $model = ModuleQuery::getModuleOne($id, $userId);
        if(!$model){
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgSuccess() . Yii::t('ezmodule', 'You do not have right to use this page.'),
                'options' => ['class' => 'alert-warning']
            ]);
            
            return $this->redirect(['/ezmodules/default/index']);
        }
        
        $template = EzmoduleTemplate::findOne($model->template_id);
        if(!$template){
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgSuccess() . Yii::t('ezmodule', 'Template not found.'),
                'options' => ['class' => 'alert-warning']
            ]);
            
            return $this->redirect(['/ezmodules/default/index']);
        }
        
        $modelModule = $model;
        if($addon>0){
            $modelModule = ModuleQuery::getModuleOne($addon, $userId);
        }
        if(!$modelModule && !isset($modelModule->ezf_id)){
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgSuccess() . Yii::t('ezmodule', 'You do not have right to use this page.'),
                'options' => ['class' => 'alert-warning']
            ]);
            
            return $this->redirect(['/ezmodules/default/index']);
        }
        
        $ezf_id = $modelModule->ezf_id;
                
	return $this->render('report', [
            'model' => $model,
            'template' => $template,
            'menu'=>$menu,
            'module'=>$module,
            'addon'=>$addon,
            'filter'=>$filter,
            'modelModule'=>$modelModule,
        ]);
    }
    
    public function actionGrid() {
        if (Yii::$app->getRequest()->isAjax) {
            $module = isset($_GET['module']) ? $_GET['module'] : 0;
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $addon = isset($_GET['addon']) ? $_GET['addon'] : 0;
            $filter = isset($_GET['filter']) ? $_GET['filter'] : 0;
            $modelFilter = isset($_GET['modelFilter']) ? \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($_GET['modelFilter']) : NULL;

            $userId = Yii::$app->user->id;
            $userProfile = Yii::$app->user->identity->profile;
            
            $moduleId = $module;
            if($addon>0){
                $moduleId = $addon;
            }
            
            $modelModule = ModuleQuery::getModuleID($moduleId);
            if(!isset($modelModule->ezf_id)){
                return $this->renderAjax('_error', [
                            'msg' => Yii::t('ezmodule', 'Please select a form. ').'<a class="btn btn-primary btn-sm btn-ezmodule" href="'.\yii\helpers\Url::to(['/ezmodules/ezmodule/update','id'=>$module, 'tab'=>3]).'" title="Module Settings" data-toggle="tooltip">Module Settings</a>',
                ]);
            }
            
            $ezf_id = $modelModule->ezf_id;
            $ezform = EzfQuery::getEzformOne($modelModule->ezf_id);
            
            $searchModel = new TbdataAll();
            $searchModel->setTableName($ezform->ezf_table);

            $ezformParent = Null;
            $targetField = EzfQuery::getTargetOne($ezform->ezf_id);
            if(isset($targetField)){
                $ezformParent = EzfQuery::getEzformById($targetField->ref_ezf_id);
                $specialField = EzfQuery::getSpecialOne($targetField->parent_ezf_id);
            } else {
                $specialField = EzfQuery::getSpecialOne($ezform->ezf_id);
            }
            
            $modelFields = ModuleQuery::getFieldsList($moduleId, $userId);
            
            $dataProvider = ModuleFunc::modelSearch($searchModel, $ezform, $targetField, $specialField, $ezformParent, $modelFields, $modelFilter, $filter, $moduleId, Yii::$app->request->queryParams);
            
            try {
                //total
                $query = new \yii\db\Query();
                $query->select('count(*) AS num')
                        ->from($ezform['ezf_table'])
                        ->where("{$ezform['ezf_table']}.rstat not in(0,3)");
                if (isset($targetField)) {// join 
                    $pk = $targetField->ezf_field_name;
                    $pkJoin = $targetField->ref_field_id;

                    $query->innerJoin($ezformParent['ezf_table'], "`{$ezformParent['ezf_table']}`.`$pkJoin` = `{$ezform['ezf_table']}`.`$pk`");
                    $query->andWhere("`{$ezformParent['ezf_table']}`.rstat NOT IN(0,3)");
                }

                if ($ezform['public_listview'] != 1) {
                    $showStatus = \backend\modules\ezforms2\classes\EzfUiFunc::showListDataEzf($ezform, $userId);
                    $query->andWhere("{$ezform['ezf_table']}.user_create=:created_by || $showStatus", [':created_by' => $userId]);
                }

                if (isset($specialField)) {
                    $query->andWhere("{$ezform['ezf_table']}.hsitecode = :site", [':site' => $userProfile->sitecode]);
                } else {
                    $query->andWhere("{$ezform['ezf_table']}.xsourcex = :site", [':site' => $userProfile->sitecode]);
                }

                $totalModule = $query->createCommand()->queryScalar();
            } catch (\yii\db\Exception $e) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                return $this->renderAjax('_error', [
                            'msg' => $e->getMessage(),
                ]);
            }
            
            return $this->renderAjax('_grid', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'ezform' => $ezform,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'module' => $module,
                        'moduleId' => $moduleId,
                        'addon'=>$addon,
                        'filter'=>$filter,
                        'modelModule'=>$modelModule,
                        'modelFields'=>$modelFields,
                        'targetField'=>$targetField,
                        'ezformParent'=>$ezformParent,
                        'specialField'=>$specialField,
                        'totalModule'=>$totalModule,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionMenu($id)
    {
        $menu = Yii::$app->request->get('id', 0);
        $module = Yii::$app->request->get('module', 0);
        
        $userId = Yii::$app->user->id;
        $model = ModuleQuery::getModuleOne($module, $userId);
        
        if(!$model){
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgSuccess() . Yii::t('ezmodule', 'You do not have right to use this page.'),
                'options' => ['class' => 'alert-warning']
            ]);
            
            return $this->redirect(['/ezmodules/default/index']);
        }
        
        $modelMenu = EzmoduleMenu::find()->where('menu_id=:id AND ezm_id=:ezm_id', [':id'=>$id, ':ezm_id'=>$module])->one();
        
        if(!$modelMenu){
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgSuccess() . Yii::t('ezmodule', 'Menu not found.'),
                'options' => ['class' => 'alert-warning']
            ]);
            
            return $this->redirect(['/ezmodules/ezmodule/view', 'id'=>$module]);
        }
        
	return $this->render('menu', [
            'model' => $model,
            'modelMenu' => $modelMenu,
            'menu'=>$menu,
            'module'=>$module,
        ]);
    }

    public function actionFormCreate() {
        if (Yii::$app->getRequest()->isAjax) {
            $searchModel = new EzmoduleSearch();
            $searchModel->ezm_system = 1;
            $searchModel->ezm_template = 1;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->pagination = [
                'pageSize' => 18,
            ];
            
            $sum = [];
            if (isset($_FILES['excel_file']['name']) && $_FILES['excel_file']['name'] != '') {
                Yii::$app->response->format = Response::FORMAT_JSON;
                
                ini_set('max_execution_time', 0);
                set_time_limit(0);
                ini_set('memory_limit', '256M');

                $excel_file = \yii\web\UploadedFile::getInstanceByName('excel_file');

                if ($excel_file) {

                    $sum = ModuleFunc::importModule($excel_file->tempName, 1);
                    if(isset($sum['Ezmodule']['ezm_id'])){
                        $modelNew = Ezmodule::findOne($sum['Ezmodule']['ezm_id']);

                        $modelFav = new \backend\modules\ezmodules\models\EzmoduleFavorite();
                        $modelFav->fav_id = SDUtility::getMillisecTime();
                        $modelFav->ezm_id = $modelNew->ezm_id;
                        $modelFav->user_id = Yii::$app->user->id;
                        $modelFav->save();
                    }
                    $html = '';
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('ezform', 'Import completed.'),
                        'html' => $html,
                        'id' => isset($sum['Ezmodule']['ezm_id'])?$sum['Ezmodule']['ezm_id']:0
                    ];
                    return $result;

                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('yii', 'File upload failed.'),
                    ];
                    return $result;
                }
            } else {
                \backend\modules\manageproject\classes\CNFunc::addLog("Create New Module ". SDUtility::array2String($dataProvider->getModels()));
                return $this->renderAjax('_form_create', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }
    /**
     * Creates a new Ezmodule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $model = new Ezmodule();
            $model->ezm_id = SDUtility::getMillisecTime();
            $model->ezm_system = 0;
            $model->approved = 0;
            $model->active = 1;
            $model->template_id =1;
            $model->ezm_template = 0;
            $template = EzmoduleTemplate::findOne($model->template_id);
            if($template){
                $model->ezm_js = $template->template_js;
                $model->ezm_html = $template->template_html;
                $model->ezm_css = $template->template_css;
            }
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                if(isset($model->ezm_id) && !empty($model->ezm_id)){
                    
                } else {
                    $model->ezm_id = SDUtility::getMillisecTime();
                }
                
                if(isset($model->ezm_builder) && !empty($model->ezm_builder)){
                    $model->ezm_builder = implode(',', $model->ezm_builder);
                }
                if(isset($model->share) && !empty($model->share)){
                    $model->share = implode(',', $model->share);
                }
                $model->ezm_role = SDUtility::array2String($model->ezm_role);
                if(isset($model->options) && !empty($model->options)){
                    $model->options = SDUtility::array2String($model->options);
                }
                
		if ($model->save()) {
                    if(isset($model->ezf_id) && !empty($model->ezf_id)){
                        $modelForms = new \backend\modules\ezmodules\models\EzmoduleForms();
                        $modelForms->form_id = SDUtility::getMillisecTime();
                        $modelForms->form_order = ModuleQuery::getFormsCountById($model->ezm_id);
                        $modelForms->form_name = \backend\modules\ezforms2\models\Ezform::findOne($model->ezf_id)->ezf_name;
                        $modelForms->ezm_id = $model->ezm_id;
                        $modelForms->ezf_id = $model->ezf_id;
                        $modelForms->form_default = 1;
                        $modelForms->save();
                    }
                    
                    ModuleFunc::saveEzmRole(SDUtility::string2Array($model->ezm_role), $model->ezm_id);
                    
		    $result = [
			'status' => 'success',
			'action' => 'create',
			'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
			'data' => $model,
		    ];
                    \backend\modules\manageproject\classes\CNFunc::addLog("Create module {$model->ezm_name} ". \appxq\sdii\utils\SDUtility::array2String($model));
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
		\backend\modules\manageproject\classes\CNFunc::addLog("View form create module {$model->ezm_name} ". \appxq\sdii\utils\SDUtility::array2String($model));
                return $this->renderAjax('create', [
		    'model' => $model,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    public function actionSave()
    {
        $id = isset($_GET['id'])?$_GET['id']:0;
        
	if (Yii::$app->getRequest()->isAjax) {
	    $model = Ezmodule::findOne($id);
            $isNewRecord = true;
            if($model){
                $model->ezm_builder = explode(',', $model->ezm_builder);
                $model->share = explode(',', $model->share);
                if(!isset($model->ezm_html)){
                    $template = EzmoduleTemplate::findOne($model->template_id);
                    if($template){
                        //$model->ezm_js = $template->template_js;
                        $model->ezm_html = $template->template_html;
                        $model->ezm_css = $template->template_css;
                    }
                }
                $isNewRecord = FALSE;
            } else {
                $model = new Ezmodule();
                $model->ezm_id = SDUtility::getMillisecTime();
                $model->ezm_system = 0;
                $model->approved = 0;
                $model->active = 1;
                $model->template_id =1;
                $model->ezm_type = 0;
                $model->ezm_template = 0;
                $template = EzmoduleTemplate::findOne($model->template_id);
                if($template){
                    $model->ezm_js = $template->template_js;
                    $model->ezm_html = $template->template_html;
                    $model->ezm_css = $template->template_css;
                }
            }
            \backend\modules\manageproject\classes\CNFunc::addLog("Create New Module ". SDUtility::array2String($model));
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                
                if(isset($model->ezm_builder) && !empty($model->ezm_builder)){
                    $model->ezm_builder = implode(',', $model->ezm_builder);
                }
                if(isset($model->share) && !empty($model->share)){
                    $model->share = implode(',', $model->share);
                }
                
                $model->ezm_role = SDUtility::array2String($model->ezm_role);
                
                if(isset($model->options) && !empty($model->options)){
                    $model->options = SDUtility::array2String($model->options);
                }
                
		if ($model->save()) {
                    if($isNewRecord){
                        $modelFav = new \backend\modules\ezmodules\models\EzmoduleFavorite();
                        $modelFav->fav_id = SDUtility::getMillisecTime();
                        $modelFav->ezm_id = $model->ezm_id;
                        $modelFav->user_id = Yii::$app->user->id;
                        $modelFav->save();
                        
                        if(isset($model->ezf_id) && !empty($model->ezf_id)){
                            $modelForms = new \backend\modules\ezmodules\models\EzmoduleForms();
                            $modelForms->form_id = SDUtility::getMillisecTime();
                            $modelForms->form_order = ModuleQuery::getFormsCountById($model->ezm_id);
                            $modelForms->form_name = \backend\modules\ezforms2\models\Ezform::findOne($model->ezf_id)->ezf_name;
                            $modelForms->ezm_id = $model->ezm_id;
                            $modelForms->ezf_id = $model->ezf_id;
                            $modelForms->form_default = 1;
                            $modelForms->save();
                        }
                    }
                    
                    ModuleFunc::saveEzmRole(SDUtility::string2Array($model->ezm_role), $model->ezm_id);
                    
		    $result = [
			'status' => 'success',
			'action' => 'update',
			'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
			'data' => $model,
		    ];
                    \backend\modules\manageproject\classes\CNFunc::addLog("Save Module {$model->ezm_name} ". SDUtility::array2String($model));
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
		return $this->renderAjax('_form_mini', [
		    'model' => $model,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    /**
     * Updates an existing Ezmodule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
            $tab = isset($_GET['tab'])?$_GET['tab']:1;
            
	    $model = $this->findModel($id);
            \backend\modules\manageproject\classes\CNFunc::addLog("View update module {$model->ezm_name} ". SDUtility::array2String($model));
            
            $model->ezm_builder = explode(',', $model->ezm_builder);
            $model->share = explode(',', $model->share);
            if(!isset($model->ezm_html)){
                $template = EzmoduleTemplate::findOne($model->template_id);
                if($template){
                    //$model->ezm_js = $template->template_js;
                    $model->ezm_html = $template->template_html;
                    $model->ezm_css = $template->template_css;
                }
            }
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                
                if(isset($model->ezm_builder) && !empty($model->ezm_builder)){
                    $model->ezm_builder = implode(',', $model->ezm_builder);
                }
                if(isset($model->share) && !empty($model->share)){
                    $model->share = implode(',', $model->share);
                }
                
                if(isset($model->options) && !empty($model->options)){
                    $model->options = SDUtility::array2String($model->options);
                }
                
                $model->ezm_role = SDUtility::array2String($model->ezm_role);
                
		if ($model->save()) {
                    
                    ModuleFunc::saveEzmRole(SDUtility::string2Array($model->ezm_role), $model->ezm_id);
                    
		    $result = [
			'status' => 'success',
			'action' => 'update',
			'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
			'data' => $model,
		    ];
                    \backend\modules\manageproject\classes\CNFunc::addLog("Update module {$model->ezm_name} ". SDUtility::array2String($model));
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
                    'tab' => $tab,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Deletes an existing Ezmodule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($id);
            $model->active = 0;
	    //if ($this->findModel($id)->delete()) {
            if ($model->save()) {    
                //\backend\modules\ezmodules\models\EzmoduleRole::deleteAll(['ezm_id' => $id]);
		$result = [
		    'status' => 'success',
		    'action' => 'update',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		    'data' => $id,
		];
                \backend\modules\manageproject\classes\CNFunc::addLog("Delete module {$model->ezm_name} ". SDUtility::array2String($model));
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
		    //$this->findModel($id)->delete();
                    $model = $this->findModel($id);
                    $model->active = 0;
                    $model->save();
                    \backend\modules\manageproject\classes\CNFunc::addLog("Delete module {$model->ezm_name} ". SDUtility::array2String($model));
                    //\backend\modules\ezmodules\models\EzmoduleRole::deleteAll(['ezm_id' => $id]);
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
    
//    public function actionApprove($id) {
//        $model = $this->findModel($id);
//        if (Yii::$app->getRequest()->isAjax) {
//            Yii::$app->response->format = Response::FORMAT_JSON;
//
//            try {
//                if($model->approved==1){
//                    $model->approved=0;
//                } else {
//                    $model->approved=1;
//                }
//                
//                if ($model->save()) {
//                    $result = [
//                        'status' => 'success',
//                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
//                        'data' => $model,
//                    ];
//                    return $result;
//                } else {
//                    $result = [
//                        'status' => 'error',
//                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
//                        'data' => $model,
//                    ];
//                    return $result;
//                }
//            } catch (\yii\db\Exception $e) {
//                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
//                $result = [
//                    'status' => 'error',
//                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
//                    'data' => $model,
//                ];
//                return $result;
//            }
//        } else {
//            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
//        }
//    }
    
    public function actionGetFields(){
	$out = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
	if (isset($_POST['depdrop_parents'])) {
	    $parents = $_POST['depdrop_parents'];
	    if ($parents != null) {
		$id = empty($parents[0]) ? null : $parents[0];
		if ($id != null) {
		    $param1 = null;
		    if (!empty($_POST['depdrop_params'])) {
			$params = $_POST['depdrop_params'];
			$param1 = $params[0]; // get the value of input-type-1
		    }
		    $sql = "SELECT `ezf_field_name` AS id, concat(`ezf_field_name`, ' (', `ezf_field_label`, ')', ' [', `ezf_version`, ']') AS name FROM `ezform_fields` WHERE `ezf_id` = :id";
		    $data = ModuleQuery::getFieldsOptionList($id);
		    
		    $out = array_values($data);
		    return ['output'=>empty($out)?'':$out, 'selected'=>$param1];
		   
		}
	    }
	}
	echo Json::encode(['output'=>'', 'selected'=>'']);
    }
    
    public function actionAddTmp()
    {
	if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
	    $model = new EzmoduleTemplate();
            $model->template_name = 'Unknown template';
            $model->template_id = SDUtility::getMillisecTime();
            $model->template_system = 0;
            $model->save();
            
            $result = [
                'status' => 'success',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                'template_id' => "{$model->template_id}",
            ];
            return $result;
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    public function actionGetTemplate()
    {
        $id = isset($_POST['id'])?$_POST['id']:0;
        
	if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
	    $template = EzmoduleTemplate::findOne($id);
            
            $result = [
                'status' => 'success',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                'html' => $template->template_html,
                'js' => $template->template_js,
                'css' => $template->template_css,
            ];
            return $result;
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionCloneAjax($ezm_id) {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            ini_set('max_execution_time', 0);
            set_time_limit(0);
            ini_set('memory_limit', '256M');

            $model = Ezmodule::findOne($ezm_id);
            
            \backend\modules\manageproject\classes\CNFunc::addLog("Clone module ajax {$model->ezm_name} ". SDUtility::array2String($model));
            
            $filename = ModuleFunc::exportModule($ezm_id, $model);
            if (isset($filename) && !empty($filename)) {
                $sum = ModuleFunc::importModule(Yii::getAlias('@backend/web/print/') . $filename, 1);
                if(isset($sum['Ezmodule']['ezm_id'])){
                    $modelNew = Ezmodule::findOne($sum['Ezmodule']['ezm_id']);
                    
                    $modelFav = new \backend\modules\ezmodules\models\EzmoduleFavorite();
                    $modelFav->fav_id = SDUtility::getMillisecTime();
                    $modelFav->ezm_id = $modelNew->ezm_id;
                    $modelFav->user_id = Yii::$app->user->id;
                    $modelFav->save();
                }
                
                $result = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('ezform', 'Import completed.'),
                    'id' => isset($sum['Ezmodule']['ezm_id'])?$sum['Ezmodule']['ezm_id']:0
                ];
                return $result;
            } 

            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('yii', 'File upload failed.'),
            ];
            return $result;
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }    
    }
    
    public function actionClone($ezm_id) {
 
            ini_set('max_execution_time', 0);
            set_time_limit(0);
            ini_set('memory_limit', '256M');

            $model = $this->findModel($ezm_id);
            
            \backend\modules\manageproject\classes\CNFunc::addLog("Clone module {$model->ezm_name} ". SDUtility::array2String($model));
            
            $filename = ModuleFunc::exportModule($ezm_id, $model);
            if (isset($filename) && !empty($filename)) {
                $sum = ModuleFunc::importModule(Yii::getAlias('@backend/web/print/') . $filename, 1);
                if(isset($sum['Ezmodule']['ezm_id'])){
                    $modelNew = Ezmodule::findOne($sum['Ezmodule']['ezm_id']);
                    
                    $modelFav = new \backend\modules\ezmodules\models\EzmoduleFavorite();
                    $modelFav->fav_id = SDUtility::getMillisecTime();
                    $modelFav->ezm_id = $modelNew->ezm_id;
                    $modelFav->user_id = Yii::$app->user->id;
                    $modelFav->save();
                }
                
            } 
            
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgSuccess() . Yii::t('ezform', 'Import completed.'),
                'options' => ['class' => 'alert-success']
            ]);

            return $this->redirect('/ezmodules/ezmodule/index');
        
    }
    
    public function actionExport($ezm_id)
    {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', '256M');

        $model = $this->findModel($ezm_id);
	
        \backend\modules\manageproject\classes\CNFunc::addLog("Backup module {$model->ezm_name} ". SDUtility::array2String($model));
        
        $filename = ModuleFunc::exportModule($ezm_id, $model);
        $this->redirect(Yii::getAlias('@web/print/').$filename);
        
        
    }
    /**
     * Finds the Ezmodule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ezmodule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ezmodule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionPermission(){
        $user_id = Yii::$app->user->id;
        $module_id = isset($_GET["id"]) ? $_GET["id"] : "";
        $model = Ezmodule::find()->where('ezm_id=:id',[':id'=>$module_id])->one();
        //\appxq\sdii\utils\VarDumper::dump($model);
        \backend\modules\manageproject\classes\CNFunc::addLog("View Permission module {$model->ezm_name} ". SDUtility::array2String($model));
        return $this->renderAjax("permission", ["module_id"=>$module_id, "user_id"=>$user_id]);
    }
}
