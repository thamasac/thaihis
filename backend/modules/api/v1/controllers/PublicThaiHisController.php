<?php

namespace backend\modules\api\v1\controllers;

use backend\modules\api\v1\classes\Nhso;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;

class PublicThaiHisController extends Controller
{

    public function beforeAction($action)
    {
        header('Access-Control-Allow-Origin: *');
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


    /**
     * @return array|mixed
     * @throws HttpException
     */
    public function actionGetInsure()
    {
        $appResponse = Yii::$app->response;
        $appResponse->format = \yii\web\Response::FORMAT_JSON;
        $cid = Yii::$app->request->get("cid", '');
        if (isset($cid) && $cid == '') {
            throw new HttpException(Yii::t('ezform', 'CID Require.'));
        }
        $nhsoJsonData = Nhso::getNhso($cid);
        $data = json_decode($nhsoJsonData,true);

        if(isset($data) && isset($data['status-system']) && $data['status-system'] == "error"){
            if($data['message'] =='NOT FOUND IN NHSO'){
                $data['message'] = 'ไม่พบสิทธิในระบบ NHSO';
            }
            return ['success'=>false,'message'=> $data['message']];
        }else{
            return $data;
        }
    }
}
