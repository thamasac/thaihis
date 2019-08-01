<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 3/29/2018
 * Time: 9:33 AM
 */

namespace backend\modules\api\v1\controllers;


use backend\modules\api\v1\classes\MainQuery;
use backend\modules\api\v1\models\DownloadLinkModel;
use Yii;

class PurifyController  extends BaseApiController
{
    public $request = null;


    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin,Content-Type,x-token,Authorization,user_id,application,platform,device_id,version");
        $this->request = Yii::$app->request;
        return parent::beforeAction($action);
    }

    public function actionGetEzformStruct()
    {
        $id = Yii::$app->request->post('ezf_id');
        $data = MainQuery::GetEZFormStructById($id);
        return $this->createResponseByVerifyData($data);
    }

    public function actionGetEzformStructs()
    {
        $ezforms = MainQuery::GetFavoriteEzform($this->user_id);
        $ezformsSturct = [];
        foreach ($ezforms as $key => $value){
           $data = MainQuery::GetEZFormStructById($value);
           if($data != null)
            array_push($ezformsSturct,$data);
        }
        return $this->createResponseByVerifyData($ezformsSturct);
    }

    public function actionRequestDownload()
    {
        $ezforms = MainQuery::GetFavoriteEzform($this->user_id);
        $links = [ "ref_id" => []];
        $sitecode = Yii::$app->user->identity->profile->sitecode;
        $secret = MainQuery::generateRandomString();
        foreach ($ezforms as $key => $value){
            $ezf = MainQuery::GetEZFormById($value["ezf_id"]);
            $data = null;
            if($ezf != null){
                $data = MainQuery::CreateDownloadLink($value["ezf_id"],$ezf["ezf_table"],$secret,$sitecode);
            }
            if($data != null){
                foreach ($data as $k => $v) {
                    array_push($links["ref_id"],$v);
                }

            }
        }
        $links["secret"] = $secret;
        $links["path"] = "/api/purify/get-data";
        return $this->createResponseByVerifyData($links);
    }

    public function actionGetData()
    {
        $id = Yii::$app->request->post('ref_id');
        $secret = Yii::$app->request->post('secret');
        $data = DownloadLinkModel::find()->where(['id'=>$id , 'secret'=>$secret])->one();
        $data = json_decode($data->getAttribute("data_string"));
        return $this->createResponseByVerifyData($data);
    }


    public function actionSyncData()
    {
        $ezf_id = Yii::$app->request->post('ezf_id');
        $syncfrom = Yii::$app->request->post('last_sync');
        $sitecode = $this->user->getAttribute('sitecode');
        $data = MainQuery::SyncFormRecords($ezf_id, $sitecode, $syncfrom);
        return $this->createResponseByVerifyData($data);
    }
}