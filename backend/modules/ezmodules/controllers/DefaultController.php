<?php

namespace backend\modules\ezmodules\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\modules\ezmodules\classes\ModuleQuery;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;

/**
 * Default controller for the `ezmodules` module
 */
class DefaultController extends Controller
{
    public function beforeAction($action)
    {
        $layout = \Yii::$app->request->get('layout', '');
        if($layout=="nolayout"){
           $this->layout = "@backend/views/layouts/main2";
        }
        return parent::beforeAction($action); 
    }
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        \backend\modules\manageproject\classes\CNFunc::addLog("View module all");
        return $this->render('index');
    }
    
    public function actionInfoApp($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $userId = Yii::$app->user->id;
	    $model = ModuleQuery::getModuleOne($id, $userId);
            if(!$model){
                return $this->renderAjax('_error', [
                        'msg' => Yii::t('ezmodule', 'You do not have right to use this page.'),
                ]);
            }
            
            $ncount = ModuleQuery::countUseModule($id);
            $pcoc = NULL;
            if(isset($model['ezf_table']) && $model['ezf_table']!=''){
                $pcoc = ModuleQuery::countPcocModule($ezf_table);
            }
        
            $private = 1;
            if($model['public']==1 && $model['approved']==1){
                $private = 0;
            }

            $star = 0;
            $total = 0;
            $starAVG = \backend\modules\ezmodules\models\EzmoduleRating::find()->select('AVG(star) AS star, count(*) AS total')->where('ezm_id=:ezm_id', [':ezm_id'=>$id])->one();
            if($starAVG){
                $star = round($starAVG['star']);
                $total = number_format($starAVG['total']);
            }
            
	    return $this->renderAjax('_info-app', [
		'model' => $model,
                'ncount' => $ncount,
                'pcoc' => $pcoc,  
                'private'=>$private,
                'star' => $star,
                'total'=>$total,
	    ]);
	} else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }
    
    public function actionFavorite($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $userId = Yii::$app->user->id;
	    $model = \backend\modules\ezmodules\models\EzmoduleFavorite::find()->where('ezm_id=:ezm_id AND user_id=:user_id', [':user_id'=>$userId, ':ezm_id'=>$id])->one();
	    
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    
	    $active = 0;
	    if($model){
		$model->delete();
		$active = 0;
	    } else {
		$model = new \backend\modules\ezmodules\models\EzmoduleFavorite();
                $model->fav_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
		$model->ezm_id = $id;
		$model->user_id = $userId;
		$model->save();
		
		$active = 1;
	    }
	    
	    $result = [
		'status' => 'success',
		'action' => 'update',
		'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
		'active' => $active,
	    ];
	    return $result;
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionDeleteCommt()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $id = Yii::$app->request->post('id', 0);
            
	    $model = \backend\modules\ezmodules\models\EzmoduleComment::find()->where('commt_id=:id', [':id'=>$id])->one();
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    if($model){
		if($model->delete()){
                    $result = [
                        'status' => 'success',
                        'action' => 'update',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                        'data' => $model,
                    ];
                    return $result;
                } else{
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not update the data.'),
                        'data' => $model,
                    ];
                    return $result;
                }
	    } else {
		$result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'No results found.'),
                    ];
                    return $result;
	    }
	    
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionRating($ezm_id)
    {
	if (Yii::$app->getRequest()->isAjax) {
            $star = Yii::$app->request->post('star', 0);
            
	    $userId = Yii::$app->user->id;
	    $model = \backend\modules\ezmodules\models\EzmoduleRating::find()->where('ezm_id=:ezm_id AND created_by=:created_by', [':ezm_id'=>$ezm_id, ':created_by'=>$userId])->one();
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    
	    if($model){
		
	    } else {
		$model = new \backend\modules\ezmodules\models\EzmoduleRating();
                $model->rating_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
		$model->ezm_id = $ezm_id;
	    }
            
            $model->star = $star;
            
	    if($model->save()){
                $result = [
                    'status' => 'success',
                    'action' => 'update',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                    'data' => $model,
                ];
                return $result;
            } else{
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not update the data.'),
                    'data' => $model,
                ];
                return $result;
            }
	    
	    
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionComment()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $ezm_id = Yii::$app->request->get('ezm_id', 0);
            
	    $userId = Yii::$app->user->id;
            $model = new \backend\modules\ezmodules\models\EzmoduleComment();
            $model->commt_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $model->ezm_id = $ezm_id;
            
            $star = \backend\modules\ezmodules\models\EzmoduleRating::find()->select('star')->where('ezm_id=:ezm_id AND created_by=:created_by', [':ezm_id'=>$ezm_id, ':created_by'=>$userId])->scalar();
            if(!$star){
                $star = 0;
            }    
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                if($model->isNewRecord){
                    $model->commt_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
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
		return $this->renderAjax('_comment', [
		    'model' => $model,
                    'star' => $star,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionCommentList()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $ezm_id = Yii::$app->request->get('ezm_id', 0);
            $start = Yii::$app->request->get('start', 0);
            
            $limit = 10;
	    $userId = Yii::$app->user->id;
	    $model = \backend\modules\ezmodules\models\EzmoduleComment::find()
                    ->select(['ezmodule_comment.*', 
                        'concat(profile.firstname, " ", profile.lastname) AS user_name',
                        'profile.avatar_path', 'profile.avatar_base_url'])
                    ->innerJoin('profile', 'profile.user_id=ezmodule_comment.created_by')
                    ->where('ezm_id=:ezm_id', [':ezm_id'=>$ezm_id])
                    ->orderBy('created_at DESC')
                    ->offset($start)
                    ->limit($limit)
                    ->all();
            
            $count = \backend\modules\ezmodules\models\EzmoduleComment::find()
                    ->select(['ezmodule_comment.*', 
                        'concat(profile.firstname, " ", profile.lastname) AS user_name',
                        'profile.avatar_path', 'profile.avatar_base_url'])
                    ->innerJoin('profile', 'profile.user_id=ezmodule_comment.created_by')
                    ->where('ezm_id=:ezm_id', [':ezm_id'=>$ezm_id])->count();
            
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
            ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
}
