<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfUiFunc;
use appxq\sdii\utils\SDUtility;


/**
 * CustomConfigController implements the CRUD actions for EzformInput model.
 */
class BtnActionController extends Controller
{
    
    public function actionUpdateData()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $ezf_id = isset($_GET['ezf_id'])?$_GET['ezf_id']:0;
            $id = isset($_GET['id'])?$_GET['id']:0;
            
            $field = isset($_GET['field'])?$_GET['field']:'';
            $value = isset($_GET['value'])?$_GET['value']:NULL;
            
            Yii::$app->response->format = Response::FORMAT_JSON;
            $ezform = EzfQuery::getEzformById($ezf_id);
            
            if(!empty($field)){
                 $data = Yii::$app->db->createCommand()->update($ezform['ezf_table'], [
                    $field=>$value, 
                    'update_date' => date('Y-m-d H:i:s'),
                    'user_update' => Yii::$app->user->id,
                    ], 'id=:id', [':id'=>$id])->execute();  
                if($data){
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('ezform', 'Update completed.'),
                    ];
                    return $result;
                }
            } 
	    
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Update failed.'),
            ];
            return $result;
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    
}
