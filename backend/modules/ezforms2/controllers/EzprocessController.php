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
 * EzprocessController implements the CRUD actions for EzformInput model.
 */
class EzprocessController extends Controller
{
    
    public function actionCreateRole()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $dataid = isset($_GET['dataid'])?$_GET['dataid']:0;
            //zdata_role_permissions
            
	    $role_name = isset($_GET['role_name'])?$_GET['role_name']:'';
            $role_desc = isset($_GET['role_desc'])?$_GET['role_desc']:'';
            
            Yii::$app->response->format = Response::FORMAT_JSON;
           
            try {
                $system_role = \backend\modules\core\classes\CoreFunc::itemAlias('system_role');
                
                if(Yii::$app->user->can('administrator')){
                    if($role_name!='') {
                        if(!in_array($role_name, $system_role)){
                            $authManager = Yii::$app->authManager;
                            if($authManager->getRole($role_name)){
                                $role_obj = $authManager->createRole($role_name);
                                $role_obj->description = $role_desc;
                                $authManager->update($role_name, $role_obj);
                            } else {
                                $role_obj = $authManager->createRole($role_name);
                                $role_obj->description = $role_desc;
                                $authManager->add($role_obj);
                            }

                            $result = [
                                'status' => 'success',
                                'message' => SDHtml::getMsgSuccess() . Yii::t('ezform', 'Create role completed.'),
                            ];
                            return $result;
                        } else {
                            $this->delectRoleItemEzform($dataid);
                            
                            $result = [
                                'status' => 'error',
                                'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Duplicate with the system role.'),
                            ];
                            return $result;
                        }
                    } else {
                        $this->delectRoleItemEzform($dataid);
                        
                        $result = [
                            'status' => 'error',
                            'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Role Name cannot be blank.'),
                        ];
                        return $result;
                    }
                } else {//You do not have permission
                    $this->delectRoleItemEzform($dataid);
                    
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('ezform', 'You do not have permission'),
                    ];
                    return $result;
                }
            } catch (\Exception $e) {
                $this->delectRoleItemEzform($dataid);
                
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . $e->getMessage(),
                ];
                return $result;
            }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    protected function delectRoleItemEzform($dataid){
        return Yii::$app->db->createCommand()->delete('zdata_role_permissions', 'id=:id', [':id'=>$dataid])->execute();
    }

    protected function restoreRoleItemEzform($dataid){
        return Yii::$app->db->createCommand()->update('zdata_role_permissions', ['rstat'=>1], 'id=:id', [':id'=>$dataid])->execute();
    }
    
    public function actionDeleteRole()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $dataid = isset($_GET['dataid'])?$_GET['dataid']:0;
            //zdata_role_permissions
            
	    $role_name = isset($_GET['role_name'])?$_GET['role_name']:'';
            $role_desc = isset($_GET['role_desc'])?$_GET['role_desc']:'';
            
            Yii::$app->response->format = Response::FORMAT_JSON;
           
            try {
                $system_role = \backend\modules\core\classes\CoreFunc::itemAlias('system_role');
                
               if(Yii::$app->user->can('administrator')){
                   if(!in_array($role_name, $system_role)){
                        $authManager = Yii::$app->authManager;
                        $role_obj = $authManager->getRole($role_name);
                        if($role_obj){
                            $authManager->remove($role_obj);
                        }
                        
                        $result = [
                            'status' => 'success',
                            'message' => SDHtml::getMsgSuccess() . Yii::t('ezform', 'Delete role completed.'),
                        ];
                        return $result;
                    } else {
                        $this->restoreRoleItemEzform($dataid);

                        $result = [
                            'status' => 'error',
                            'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Unable to delete the system role.'),
                        ];
                        return $result;
                    }
               } else {
                   $this->restoreRoleItemEzform($dataid);
                   
                   $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('ezform', 'You do not have permission'),
                    ];
                    return $result;
               }
            } catch (\Exception $e) {
                $this->restoreRoleItemEzform($dataid);
                
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . $e->getMessage(),
                ];
                return $result;
            }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
}
