<?php

namespace backend\modules\gantt\controllers;

use yii\web\Controller;
use Yii;
use backend\modules\gantt\models\GanttForum;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use yii\web\NotFoundHttpException;
use backend\modules\subjects\classes\SubjectManagementQuery;
use appxq\sdii\utils\VarDumper;
use backend\modules\gantt\Module;
use appxq\sdii\utils\SDUtility;

/**
 * Default controller for the `webboard` module
 */
class GanttForumController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->renderAjax('index');
    }
    public function actionGetWebboard(){
        if(\Yii::$app->request->isAjax){
            $data = (new \yii\db\Query())
                    ->select(['w.id','w.title','w.create_date', "concat(p.`firstname`,' ', p.`lastname`) as name"])
                    ->from('zdata_webboard as w')
                    ->innerJoin('profile as p', 'w.user_create=p.user_id')
                    ->where('w.rstat <> 3 and w.rstat <> 0')
                    ->orderBy(['id'=>SORT_DESC])->all();
            $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels'=>$data,
                'sort' => [
                    'attributes' => ['id', 'title', 'name'],
                ],
                'pagination' => [
                    'pageSize' => 50,
                ],
            ]); 
            return $this->renderAjax('get-webboard',['dataProvider'=>$dataProvider]); 
        }
    }
    public function actionView()
    {        
        try{
            $id = isset($_GET['target']) ? $_GET['target'] : '';
            $parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : '';
            $view_post = 0;
            $view_post_str = (new \yii\db\Query())->select('view_post')->from('zdata_webboard')->where('id=:id',[':id'=>$id])->one();
            $view_post = isset($view_post_str['view_post']) ? $view_post_str['view_post'] : 0;
            $view_post += 1;
            \Yii::$app->db->createCommand()->update('zdata_webboard', ['view_post'=>$view_post],['id'=>$id])->execute();
            $data = (new \yii\db\Query())
                        ->select(['w.title','w.id','w.detail','w.create_date', 'p.firstname', 'p.lastname'])
                        ->from('zdata_webboard as w')
                        ->innerJoin('profile as p', 'w.user_create=p.user_id')
                        ->where('w.rstat <> 3 and w.rstat <> 0')
                        ->andWhere('id=:id',[':id'=>$id])
                        ->one();
        } catch (\yii\base\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex->getMessage());
        }
        return $this->renderAjax('view',['model'=>$data, 'id'=>$id,'parent_id'=>$parent_id]); 

    }
    public function actionDelete(){
       if(\Yii::$app->request->isAjax){
           Yii::$app->response->format = Response::FORMAT_JSON;
           $id = isset($_POST['id']) ? $_POST['id'] : '';
           
           if(\Yii::$app->db->createCommand()->update('gantt_forum', ['rstat'=>3],['id'=>$id])->execute()){
               $result = [
			'status' => 'success',
			'action' => 'delete',
			'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
		    ];
               return $result;
           }else{
               $result = [
			'status' => 'error',
			'action' => 'delete',
			'message' => SDHtml::getMsgError() . Yii::t('app', 'Delete fail!'),
		    ];
               return $result;
           }
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
            
            $parent_obj = GanttForum::find()
                    ->select(['gantt_forum.*', 
                        'concat(profile.firstname, " ", profile.lastname) AS user_name',
                        //"(select CONCAT(`firstname`,' ',`lastname`) from profile where user_id = gantt_forum.send_to) AS send_to_name",
                        'profile.avatar_path', 'profile.avatar_base_url'])
                    ->innerJoin('profile', 'profile.user_id=gantt_forum.created_by')
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

            $model = new GanttForum();
            $model->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $model->dataid = $dataid;
            $model->object_id = $object_id;
            $model->query_tool = $query_tool;
            $model->parent_id = $parent_id;
            $model->field = $field;
            $model->type = $type;
            $model->status = 0;
            $model->rstat = 1;
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;

                $send_to = null;
                if(!empty($model->send_to)){
                    $send_to = $model->send_to;
                    $model->send_to = implode(',', $model->send_to);
                }
                
                $conten = $model->content;

                if ($model->save()) {
                	$user_id = Yii::$app->user->id;
                	
                	$profile = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile ( $user_id );
                	$taskData = SubjectManagementQuery::GetTableData ( 'zdata_activity', [
                			'id' => $parent_id
                	], 'one' );
                	$mainData = SubjectManagementQuery::GetTableData ( 'zdata_project', [
                			'id' => $taskData ['target']
                	], 'one' );
                	$pmsData = SubjectManagementQuery::GetTableDataNotEzform ( 'pms_task_target', "rstat NOT IN(0,3) AND dataid='{$parent_id}'", 'one' );
                	$user_assign = SDUtility::string2Array($pmsData['assign_user_accept']);
                        $co_owner = SDUtility::string2Array($pmsData['co_owner']);
                        $reviewer = SDUtility::string2Array($pmsData['reviewer']);
                	$user_assign[] = $taskData ['user_create'];
                        if(count($co_owner)>0)
                            $user_assign = array_merge($user_assign,$co_owner);
                        if(count($reviewer)>0)
                            $user_assign = array_merge($user_assign,$reviewer);

                	$user_assign = array_diff($user_assign, [$user_id]);
                	
                	$detail_msg = "New comment by {$profile['firstname']} {$profile['lastname']} at Task Item namely {$taskData['task_name']}. you can see in Main Task namely {$mainData['project_name']}";
                	$detail_msg2 = $model['content'];
                	
                	$response_ezf_id = Module::$formsId['response_ezf_id'];
                	$url_link = "/gantt/pms-response?page_from=other&taskid={$parent_id}&ezf_id={$taskData['ezf_id']}&response_ezf_id={$response_ezf_id}";
                	
                        
                    if(!is_array($user_assign)){
                        $nonify_setting = GanttQuery::getNotifySetting($user_assign);
                        $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                        $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                        $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;
                        \dms\aomruk\classese\Notify::setNotify ()->assign ( $user_assign )->notify ( $detail_msg )->url ( $url_link )->detail ( $detail_msg2 )->type_link ( '1' )->send_email ( $noti_email )->send_line ( $noti_line )->sendStatic ();
                    }else{
                        foreach ($user_assign as $valuser){
                            $nonify_setting = GanttQuery::getNotifySetting($valuser);
                            $noti_sys = $nonify_setting['noti_sys'] == '1' ? true : false;
                            $noti_email = $nonify_setting['noti_email'] == '1' ? true : false;
                            $noti_line = $nonify_setting['noti_line'] == '1' ? true : false;
                          \dms\aomruk\classese\Notify::setNotify ()->assign ( $valuser )->notify ( $detail_msg )->url ( $url_link )->detail ( $detail_msg2 )->type_link ( '1' )->send_email ( $noti_email )->send_line ( $noti_line )->sendStatic (); 
                        }
                    }
                	
                    //$user = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($model->created_by);
                    $value = GanttForum::find()
                    ->select(['gantt_forum.*', 
                        'concat(profile.firstname, " ", profile.lastname) AS user_name',
                        //"(select CONCAT(`firstname`,' ',`lastname`) from profile where user_id = gantt_forum.send_to) AS send_to_name",
                        'profile.avatar_path', 'profile.avatar_base_url'])
                    ->innerJoin('profile', 'profile.user_id=gantt_forum.created_by')
                    ->where('id=:id', [':id'=>$model->id])
                    ->one();
                    
                    $html = $this->renderAjax('_comment_item', [
                        'value' => $value,
                        'modal' => $modal,
                        'dataid' => $dataid,
                    ]);

                    if(isset($send_to) && !empty($send_to)){
                        if(is_array($send_to)){
                           $notify = \dms\aomruk\classese\Notify::setNotify()
                                    ->notify('Communication Pad')
                                    ->detail($conten)->assign($send_to);
                           if($type=='ezform'){
                               $notify->type_link(2)->ezf_id($modal->object_id)->data_id($modal->dataid);
                           } else {
                               
                           }
                           
                           $notify->sendSatatic();
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
	    $model = GanttForum::find()
                    ->select(['gantt_forum.*', 
                        'concat(profile.firstname, " ", profile.lastname) AS user_name',
                        //"(select CONCAT(`firstname`,' ',`lastname`) from profile where user_id = gantt_forum.send_to) AS send_to_name",
                        'profile.avatar_path', 'profile.avatar_base_url'])
                    ->innerJoin('profile', 'profile.user_id=gantt_forum.created_by')
                    ->where(' parent_id=:parent_id', [':parent_id'=>$parent_id,])
                    ->andWhere("gantt_forum.rstat NOT IN(0,3)")
                    ->orderBy('created_at ASC')
                    ->offset($start)
                    ->limit($limit)
                    ->all();
            
            $count = GanttForum::find()
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
     
}
