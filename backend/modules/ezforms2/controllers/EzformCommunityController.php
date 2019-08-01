<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use backend\modules\ezforms2\models\EzformCommunity;
use backend\modules\ezforms2\models\EzformCommunitySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * EzformCommunityController implements the CRUD actions for EzformCommunity model.
 */
class EzformCommunityController extends Controller
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
     * Lists all EzformCommunity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EzformCommunitySearch();
        $searchModel->type = 'query_tool';
        $searchModel->parent_id = 0;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EzformCommunity model.
     * @param integer $id
     * @return mixed
     */
    public function actionView()
    {
	$dataid = isset($_GET['dataid']) ? $_GET['dataid'] : 0;
            $object_id = isset($_GET['object_id']) ? $_GET['object_id'] : 0;
            $ezf_name = isset($_GET['ezf_name']) ? $_GET['ezf_name'] : '';
         
        if (Yii::$app->getRequest()->isAjax) {
	    return $this->renderAjax('view', [
		'dataid' => $dataid,
                    'object_id' => $object_id,
                'ezf_name' => $ezf_name,
	    ]);
	} else {
	    return $this->render('view', [
		'dataid' => $dataid,
                    'object_id' => $object_id,
                'ezf_name' => $ezf_name,
	    ]);
	}    
	
    }

    public function actionQueryTool()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : 0;
            $object_id = isset($_GET['object_id']) ? $_GET['object_id'] : 0;
            $query_tool = isset($_GET['query_tool']) ? $_GET['query_tool'] : 0;
            $parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : 0;
            $field = isset($_GET['field']) ? $_GET['field'] : '';
            $value_old = isset($_GET['value_old']) ? $_GET['value_old'] : '';
            $type = isset($_GET['type']) ? $_GET['type'] : 'none';
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
            
            $modelEzf = EzfQuery::getEzformOne($object_id);
            $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $dataid);
            $userZdata = EzfQuery::getUserProfile($modelZdata->user_update);
            
	    $model = new EzformCommunity();
            $model->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $model->dataid = $dataid;
            $model->object_id = $object_id;
            $model->query_tool = $query_tool;
            $model->parent_id = $parent_id;
            $model->field = $field;
            $model->value_old = $value_old;
            $model->type = $type;
            $model->status = 0;
            $model->approv_status = 0;
            $model->send_to = ["{$userZdata['user_id']}"];
            
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                //\appxq\sdii\utils\VarDumper::dump($model->send_to);
                if(!empty($model->send_to)){
                    $model->send_to = implode(',', $model->send_to);
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
		return $this->renderAjax('_form_querytool', [
		    'model' => $model,
                    'dataid' => $dataid,
                    'object_id' => $object_id,
                    'query_tool' => $query_tool,
                    'parent_id' => $parent_id,
                    'field' => $field,
                    'type' => $type,
                    'limit' => $limit,
                    'modal' => $modal,
                    'value_old' => $value_old,
                    'userZdata' => $userZdata,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    /**
     * Creates a new EzformCommunity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionComment()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : 0;
            $object_id = isset($_GET['object_id']) ? $_GET['object_id'] : 0;
            $query_tool = isset($_GET['query_tool']) ? $_GET['query_tool'] : 0;
            $parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : 0;
            $field = isset($_GET['field']) ? $_GET['field'] : '';
            $type = isset($_GET['type']) ? $_GET['type'] : 'none';
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;

            $model = new EzformCommunity();
            $model->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $model->dataid = $dataid;
            $model->object_id = $object_id;
            $model->query_tool = $query_tool;
            $model->parent_id = $parent_id;
            $model->field = $field;
            $model->type = $type;
            $model->status = 0;
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                
                $send_to = null;
                if(!empty($model->send_to)){
                    $send_to = $model->send_to;
                    $model->send_to = implode(',', $model->send_to);
                }
                
                $conten = $model->content;

                if ($model->save()) {
                    //$user = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($model->created_by);
                    $value = EzformCommunity::find()
                    ->select(['ezform_community.*', 
                        'concat(profile.firstname, " ", profile.lastname) AS user_name',
                        //"(select CONCAT(`firstname`,' ',`lastname`) from profile where user_id = ezform_community.send_to) AS send_to_name",
                        'profile.avatar_path', 'profile.avatar_base_url'])
                    ->innerJoin('profile', 'profile.user_id=ezform_community.created_by')
                    ->where('id=:id', [':id'=>$model->id])
                    ->one();
                    
                    $html = $this->renderAjax('_comment_item', [
                        'value' => $value,
                        'modal' => $modal,
                        'dataid' => $dataid,
                    ]);

                    if(isset($send_to) && !empty($send_to)){
                        if(is_array($send_to)){
                            
//                           $notify = \dms\aomruk\classese\Notify::setNotify()
//                                    ->notify('Communication Pad')
//                                    ->detail($conten)->assign($send_to);
//                           if($type=='ezform'){
//                               $notify->type_link(2)->ezf_id($modal->object_id)->data_id($modal->dataid);
//                           } else {
//                               
//                           }
//                           
//                           $notify->sendSatatic();
                           
                        }
                    }
                    
		    $result = [
			'status' => 'success',
			'action' => 'create',
			'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
			'data' => $model,
                        'html' =>$html,
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
		return $this->renderAjax('_form', [
		    'model' => $model,
                    'dataid' => $dataid,
                    'object_id' => $object_id,
                    'query_tool' => $query_tool,
                    'parent_id' => $parent_id,
                    'field' => $field,
                    'type' => $type,
                    'limit' => $limit,
                    'modal' => $modal,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionQcomment()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : 0;
            $object_id = isset($_GET['object_id']) ? $_GET['object_id'] : 0;
            $query_tool = isset($_GET['query_tool']) ? $_GET['query_tool'] : 0;
            $parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : 0;
            $field = isset($_GET['field']) ? $_GET['field'] : '';
            $type = isset($_GET['type']) ? $_GET['type'] : 'none';
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
            
            $parent_obj = EzformCommunity::find()
                    ->select(['ezform_community.*', 
                        'concat(profile.firstname, " ", profile.lastname) AS user_name',
                        //"(select CONCAT(`firstname`,' ',`lastname`) from profile where user_id = ezform_community.send_to) AS send_to_name",
                        'profile.avatar_path', 'profile.avatar_base_url'])
                    ->innerJoin('profile', 'profile.user_id=ezform_community.created_by')
                    ->where('id=:id', [':id'=>$parent_id])
                    ->one();
            
	    $model = new EzformCommunity();
            $model->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $model->dataid = $parent_obj->dataid;
            $model->object_id = $parent_obj->object_id;
            $model->query_tool = $parent_obj->query_tool;
            $model->parent_id = $parent_obj->id;
            $model->field = $parent_obj->field;
            
            $model->type = $parent_obj->type;
            $model->send_to = $parent_obj->send_to;
            $model->status = 0;
            $model->approv_status = 0;
            
            $modelEzf = EzfQuery::getEzformOne($model->object_id);
            $version = $modelEzf->ezf_version;
            $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $model->dataid);
            
            if ($modelZdata) {
                if($modelZdata->rstat!=0 && !empty($modelZdata->ezf_version)){
                    $version = (in_array($modelZdata->rstat, [0,1]))?$version:$modelZdata->ezf_version;
                }
                if(!empty($modelZdata->ezf_version)){
                    $modelEzf->ezf_version = (in_array($modelZdata->rstat, [0,1]))?$version:$modelZdata->ezf_version;
                }
            }
            
            $model->value_old = $modelZdata->{$parent_obj->field};
            
            $modelFields = EzfQuery::getFieldByNameVersion($modelEzf->ezf_id, $model->field, $version);
            
            Yii::$app->session['show_varname'] = 0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
                            

            if($model->send_to!=''){
                $model->send_to = explode(',', $model->send_to);
            }
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                
                if(!empty($model->send_to)){
                    $model->send_to = implode(',', $model->send_to);
                }
                $model->approv_by = \Yii::$app->user->id;
                $model->approv_date = date('Y-m-d H:i:s');
                
		if ($model->save()) {
                    $parent_obj->status = $model->approv_status;
                    $parent_obj->save();
                    
                    if($model->approv_status==2){
                        $modelFieldsAll = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);
                        $modelZ = EzfFunc::setDynamicModel($modelFieldsAll, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
                        $modelZ = EzfUiFunc::loadData($modelZ, $modelEzf->ezf_table, $model->dataid);
                        if($modelZ){
                            $modelZ->{$model->field} = $model->value_new;
                            $modelZ->user_update = \Yii::$app->user->id;
                            $modelZ->update_date = date('Y-m-d H:i:s');
                            
                            $result = EzfUiFunc::saveData($modelZ, $modelEzf->ezf_table, $modelEzf->ezf_id, $modelZ->id);
                        }
                    }
                    
                    //$user = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($model->created_by);
                    $value = EzformCommunity::find()
                    ->select(['ezform_community.*', 
                        'concat(profile.firstname, " ", profile.lastname) AS user_name',
                        //"(select CONCAT(`firstname`,' ',`lastname`) from profile where user_id = ezform_community.send_to) AS send_to_name",
                        'profile.avatar_path', 'profile.avatar_base_url'])
                    ->innerJoin('profile', 'profile.user_id=ezform_community.created_by')
                    ->where('id=:id', [':id'=>$model->id])
                    ->one();
                    
                    $html = $this->renderAjax('_qcomment_item', [
                        'value' => $value,
                        'modal' => $modal,
                        'dataid' => $dataid,
                    ]);
                    
		    $result = [
			'status' => 'success',
			'action' => 'create',
			'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
			'data' => $model,
                        'html' =>$html,
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
		return $this->renderAjax('_qform', [
		    'model' => $model,
                    'dataid' => $dataid,
                    'object_id' => $object_id,
                    'query_tool' => $query_tool,
                    'parent_id' => $parent_id,
                    'field' => $field,
                    'type' => $type,
                    'limit' => $limit,
                    'modal' => $modal,
                    'modelFields' => $modelFields,
                    'modelEzf' => $modelEzf,
                    'ezf_input' =>Yii::$app->session['ezf_input'],
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    public function actionCommentList()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $object_id = isset($_GET['object_id']) ? $_GET['object_id'] : '';
            $query_tool = isset($_GET['query_tool']) ? $_GET['query_tool'] : 0;
            $parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : '';
            $field = isset($_GET['field']) ? $_GET['field'] : '';
            $type = isset($_GET['type']) ? $_GET['type'] : 'none';
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
            $start = Yii::$app->request->get('start', 0);
            
	    $userId = Yii::$app->user->id;
	    $model = EzformCommunity::find()
                    ->select(['ezform_community.*', 
                        'concat(profile.firstname, " ", profile.lastname) AS user_name',
                        //"(select CONCAT(`firstname`,' ',`lastname`) from profile where user_id = ezform_community.send_to) AS send_to_name",
                        'profile.avatar_path', 'profile.avatar_base_url'])
                    ->innerJoin('profile', 'profile.user_id=ezform_community.created_by')
                    ->where('object_id=:object_id AND parent_id=:parent_id AND type=:type AND dataid=:dataid AND query_tool=:query_tool', [':object_id'=>$object_id, ':parent_id'=>$parent_id, ':type'=>$type, ':dataid'=>$dataid, ':query_tool'=>$query_tool])
                    ->orderBy('created_at DESC')
                    ->offset($start)
                    ->limit($limit)
                    ->all();
            
            $count = EzformCommunity::find()
                    ->where('object_id=:object_id AND parent_id=:parent_id AND type=:type AND dataid=:dataid AND query_tool=:query_tool', [':object_id'=>$object_id, ':parent_id'=>$parent_id, ':type'=>$type, ':dataid'=>$dataid, ':query_tool'=>$query_tool])
                    ->count();
            
            $start = $start + $limit;
            
            $moreitem = 0;
            if($start<$count){
                $moreitem = 1;
            }
            
            
	    return $this->renderAjax('_comment_list', [
                'model' => $model,
                'moreitem' => $moreitem,
                'start' => $start,
                'count' => $count,
                'dataid' => $dataid,
                'object_id' => $object_id,
                'query_tool' => $query_tool,
                'parent_id' => $parent_id,
                'field' => $field,
                'type' => $type,
                'limit' => $limit,
                'modal' => $modal,
            ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionQcommentList()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $object_id = isset($_GET['object_id']) ? $_GET['object_id'] : '';
            $query_tool = isset($_GET['query_tool']) ? $_GET['query_tool'] : 0;
            $parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : '';
            $field = isset($_GET['field']) ? $_GET['field'] : '';
            $type = isset($_GET['type']) ? $_GET['type'] : 'none';
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
            $start = Yii::$app->request->get('start', 0);
            
	    $userId = Yii::$app->user->id;
	    $model = EzformCommunity::find()
                    ->select(['ezform_community.*', 
                        'concat(profile.firstname, " ", profile.lastname) AS user_name',
                        //"(select CONCAT(`firstname`,' ',`lastname`) from profile where user_id = ezform_community.send_to) AS send_to_name",
                        'profile.avatar_path', 'profile.avatar_base_url'])
                    ->innerJoin('profile', 'profile.user_id=ezform_community.created_by')
                    ->where('object_id=:object_id AND parent_id=:parent_id AND type=:type AND dataid=:dataid AND query_tool=:query_tool', [':object_id'=>$object_id, ':parent_id'=>$parent_id, ':type'=>$type, ':dataid'=>$dataid, ':query_tool'=>$query_tool])
                    ->orderBy('created_at DESC')
                    ->offset($start)
                    ->limit($limit)
                    ->all();
            
            $count = EzformCommunity::find()
                    ->where('object_id=:object_id AND parent_id=:parent_id AND type=:type AND dataid=:dataid AND query_tool=:query_tool', [':object_id'=>$object_id, ':parent_id'=>$parent_id, ':type'=>$type, ':dataid'=>$dataid, ':query_tool'=>$query_tool])
                    ->count();
            
            $start = $start + $limit;
            
            $moreitem = 0;
            if($start<$count){
                $moreitem = 1;
            }
            
            Yii::$app->session['show_varname'] = 0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            
	    return $this->renderAjax('_qcomment_list', [
                'model' => $model,
                'moreitem' => $moreitem,
                'start' => $start,
                'count' => $count,
                'dataid' => $dataid,
                'object_id' => $object_id,
                'query_tool' => $query_tool,
                'parent_id' => $parent_id,
                'field' => $field,
                'type' => $type,
                'limit' => $limit,
                'modal' => $modal,
                
            ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionQueryList()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $object_id = isset($_GET['object_id']) ? $_GET['object_id'] : '';
            $query_tool = isset($_GET['query_tool']) ? $_GET['query_tool'] : 0;
            $parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : '';
            $field = isset($_GET['field']) ? $_GET['field'] : '';
            $type = isset($_GET['type']) ? $_GET['type'] : 'none';
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
            $start = Yii::$app->request->get('start', 0);
            
	    $userId = Yii::$app->user->id;
	    $model = EzformCommunity::find()
                    ->select(['ezform_community.*', 
                        'concat(profile.firstname, " ", profile.lastname) AS user_name',
                        //"(select CONCAT(`firstname`,' ',`lastname`) from profile where user_id = ezform_community.send_to) AS send_to_name",
                        'profile.avatar_path', 'profile.avatar_base_url'])
                    ->innerJoin('profile', 'profile.user_id=ezform_community.created_by')
                    ->where('object_id=:object_id AND parent_id=:parent_id AND type=:type AND dataid=:dataid AND query_tool=:query_tool', [':object_id'=>$object_id, ':parent_id'=>$parent_id, ':type'=>$type, ':dataid'=>$dataid, ':query_tool'=>$query_tool])
                    ->orderBy('created_at DESC')
                    ->offset($start)
                    ->limit($limit)
                    ->all();
            
            $count = EzformCommunity::find()
                    ->where('object_id=:object_id AND parent_id=:parent_id AND type=:type AND dataid=:dataid AND query_tool=:query_tool', [':object_id'=>$object_id, ':parent_id'=>$parent_id, ':type'=>$type, ':dataid'=>$dataid, ':query_tool'=>$query_tool])
                    ->count();
            
            $start = $start + $limit;
            
            $moreitem = 0;
            if($start<$count){
                $moreitem = 1;
            }
            
            
	    return $this->renderAjax('_query_list', [
                'model' => $model,
                'moreitem' => $moreitem,
                'start' => $start,
                'count' => $count,
                'dataid' => $dataid,
                'object_id' => $object_id,
                'query_tool' => $query_tool,
                'parent_id' => $parent_id,
                'field' => $field,
                'type' => $type,
                'limit' => $limit,
                'modal' => $modal,
            ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    /**
     * Updates an existing EzformCommunity model.
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
     * Deletes an existing EzformCommunity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $id = Yii::$app->request->post('id', 0);
            
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
    
    public function actionCommunityPad()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $object_id = isset($_GET['object_id']) ? $_GET['object_id'] : '';
            $query_tool = isset($_GET['query_tool']) ? $_GET['query_tool'] : '';
            $parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : '';
            $field = isset($_GET['field']) ? $_GET['field'] : '';
            $type = isset($_GET['type']) ? $_GET['type'] : 'none';
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
            
	    return $this->renderAjax('_comment', [
                'modal' => $modal,
                'reloadDiv' => $reloadDiv,
                'dataid' => $dataid,
                'object_id' => $object_id,
                'query_tool' => $query_tool,
                'parent_id' => $parent_id,
                'field' => $field,
                'type' => $type,
                'limit' => $limit,
            ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionQueryPad()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $object_id = isset($_GET['object_id']) ? $_GET['object_id'] : '';
            $query_tool = isset($_GET['query_tool']) ? $_GET['query_tool'] : '';
            $parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : '';
            $field = isset($_GET['field']) ? $_GET['field'] : '';
            $type = isset($_GET['type']) ? $_GET['type'] : 'none';
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
            
	    return $this->renderAjax('_querytool', [
                'modal' => $modal,
                'reloadDiv' => $reloadDiv,
                'dataid' => $dataid,
                'object_id' => $object_id,
                'query_tool' => $query_tool,
                'parent_id' => $parent_id,
                'field' => $field,
                'type' => $type,
                'limit' => $limit,
            ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionQueryComment()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $object_id = isset($_GET['object_id']) ? $_GET['object_id'] : '';
            $query_tool = isset($_GET['query_tool']) ? $_GET['query_tool'] : '';
            $parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : '';
            $field = isset($_GET['field']) ? $_GET['field'] : '';
            $type = isset($_GET['type']) ? $_GET['type'] : 'none';
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
            
            $parent_obj = EzformCommunity::find()
                    ->select(['ezform_community.*', 
                        'concat(profile.firstname, " ", profile.lastname) AS user_name',
                        //"(select CONCAT(`firstname`,' ',`lastname`) from profile where user_id = ezform_community.send_to) AS send_to_name",
                        'profile.avatar_path', 'profile.avatar_base_url'])
                    ->innerJoin('profile', 'profile.user_id=ezform_community.created_by')
                    ->where('id=:id', [':id'=>$parent_id])
                    ->one();
            
	    return $this->renderAjax('_query_comment', [
                'modal' => $modal,
                'reloadDiv' => $reloadDiv,
                'dataid' => $dataid,
                'object_id' => $object_id,
                'query_tool' => $query_tool,
                'parent_id' => $parent_id,
                'field' => $field,
                'type' => $type,
                'limit' => $limit,
                'parent_obj' => $parent_obj,
            ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    /**
     * Finds the EzformCommunity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EzformCommunity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EzformCommunity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
