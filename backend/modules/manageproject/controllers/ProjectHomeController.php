<?php

namespace backend\modules\manageproject\controllers;

use Yii;
use appxq\sdii\utils\VarDumper;
use yii\helpers\Json;
use appxq\sdii\helpers\SDHtml;
use yii\web\Controller;

/**
 * Default controller for the `manageproject` module
 */
class ProjectHomeController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionUpdateContent()
    {
        if (\Yii::$app->getRequest()->isAjax) {
            $ezf_id = Yii::$app->request->post('ezf_id');
            $data_id = Yii::$app->request->post('data_id');
            $content = Yii::$app->request->post('web_content');

            $update = Yii::$app->db->createCommand()
                ->update('zdata_site_frontend', ['menu_content' => $content], 'id=:id', [':id' => $data_id])
                ->execute();
            //VarDumper::dump($update,0);
            if (isset($update)) {
                $result = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error..'),
                ];
            }
            //VarDumper::dump(Json::encode($result),0);
            return Json::encode($result);
            $this->redirect($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']);
        }
    }
}
