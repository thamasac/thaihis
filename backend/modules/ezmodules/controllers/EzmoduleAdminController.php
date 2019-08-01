<?php

namespace backend\modules\ezmodules\controllers;

use Yii;
use backend\modules\ezmodules\models\Ezmodule;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;

/**
 * EzmoduleController implements the CRUD actions for Ezmodule model.
 */
class EzmoduleAdminController extends Controller {

    public function behaviors() {
        return [
            /* 	    'access' => [
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
              ], */
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

    public function actionApprove($id) {
        $model = $this->findModel($id);
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {
                if ($model->approved == 1) {
                    $model->approved = 0;
                } else {
                    $model->approved = 1;
                }

                if ($model->save()) {
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                        'data' => $model,
                    ];
                    \backend\modules\manageproject\classes\CNFunc::addLog("Approve module {$model->ezm_name} ". \appxq\sdii\utils\SDUtility::array2String($model));
                    return $result;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                        'data' => $model,
                    ];
                    return $result;
                }
            } catch (\yii\db\Exception $e) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                    'data' => $model,
                ];
                return $result;
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    /**
     * Finds the Ezmodule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ezmodule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Ezmodule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
