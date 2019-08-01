<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\api\v1\controllers;

use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\ezforms2\models\Ezform;
use DateTime;
use DateTimeZone;
use Yii;
use backend\modules\api\v1\classes\MainQuery;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;

/**
 * THE CONTROLLER ACTION
 */
class EzformController extends BaseApiController
{

    // public $enableCsrfValidation = false;

    public $request = null;

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin,Content-Type,Authorization,user_id,application,platform,uuid,version");
        $this->request = Yii::$app->request;
        return parent::beforeAction($action);
    }


    public function actionGetEzform()
    {
        $id = Yii::$app->request->get('ezf_id', null);
        $data = MainQuery::GetEZFormById($id);
        return $this->createResponseByVerifyData($data);
    }


    public function actionGetForm()
    {
        $id = Yii::$app->request->get('ezf_id', null);
        $data = MainQuery::GetEZFormOnlyById($id);

        return $this->createResponseByVerifyData($data);
    }

    public function actionGetFormAll()
    {
        $data = \backend\modules\ezforms2\models\Ezform::find()->limit(3)->all();
        $lineBot = [];
        if (isset($data)) {
            foreach ($data as $key => $value) {
                $lineBot[] = [
                    'type' => 'message',
                    'label' => $value['ezf_name'],
                    'text' => "ezform#{$value['ezf_id']}",
                ];
            }
        }

        return $this->createResponseByVerifyData(['items' => $lineBot]);
    }

    public function actionGetFix()
    {
        $data = \backend\modules\ezforms2\models\Ezform::find()->limit(4)->all();
        $lineBot = [];
        if (isset($data)) {
            foreach ($data as $key => $value) {
                $lineBot[] = [
                    'type' => 'message',
                    'label' => $value['ezf_name'],
                    'text' => "ezform#{$value['ezf_id']}",
                ];
            }
        }

        $dataTmp = [
            'type' => 'template',
            'altText' => 'this is a buttons template',
            'template' => [
                'type' => 'buttons',
                'actions' => $lineBot,
                'title' => 'EzForm',
                'text' => 'EzForm List'
            ]
        ];
        return $this->createResponseByVerifyData(['items' => $dataTmp]);
    }

    public function actionSyncEzformRecords()
    {
        $lastSync = Yii::$app->request->post('syncfrom', null);
        $ezfId = Yii::$app->request->post('ezf_id', null);
        $sitecode = Yii::$app->user->identity->profile['sitecode'];
        $syncTo = MainQuery::GetNowSql();
        $records = MainQuery::SyncFormRecords($ezfId, $sitecode, $lastSync, $syncTo);
        $data = [
            'records' => $records,
            'lastsync' => $syncTo
        ];
        return $this->createResponseByVerifyData($data);
    }

    public function actionSyncAllByRefSite()
    {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $EzfID_CCA01 = '1542690878086314900';
        $EzfID_CCA02 = '1542547119068468900';
        $EzfID_REG = '1542595061073426700';

        try {
            $sitecode = Yii::$app->request->post('sitecode', null);
            $lastSync = Yii::$app->request->post('last_sync', null);

            if ($sitecode == null) {
                $res["success"] = false;
                $res["message"] = "please insert SiteCode";
                return $res;
            }

            $syncTo = MainQuery::GetNowSql();
            if ($lastSync != null && $lastSync != "null" && $lastSync != "") {
                $lastSync = new DateTime($lastSync, new DateTimeZone("UTC"));
                $lastSync->setTimeZone(new DateTimeZone(date_default_timezone_get()));
                $lastSync = $lastSync->format('Y-m-d H:i:s');
                $reg = MainQuery::SyncFormRecords($EzfID_REG, $lastSync, $syncTo);

                $cca01_temp = MainQuery::SyncFormCreatedRecordsByReference($EzfID_CCA01, $EzfID_REG, $lastSync, $syncTo);
                $cca01 = MainQuery::SyncFormRecords($EzfID_CCA01, $lastSync, $syncTo);
                $cca01 = array_merge($cca01, $cca01_temp);

                $cca02_temp = MainQuery::SyncFormCreatedRecordsByReference($EzfID_CCA02, $EzfID_REG, $lastSync, $syncTo);
                $cca02 = MainQuery::SyncFormRecords($EzfID_CCA02, $lastSync, $syncTo);
                $cca02 = array_merge($cca02, $cca02_temp);

                $data = ["reg" => $reg,
                    "cca01" => $cca01,
                    "cca02" => $cca02,
                    "lastsync" => $syncTo
                ];
                return $this->createResponseByVerifyData($data);
            } else {
                $reg = MainQuery::SyncFormRecords($EzfID_REG, null);
                $cca01 = MainQuery::SyncFormRecords($EzfID_CCA01, null);
                $cca02 = MainQuery::SyncFormRecords($EzfID_CCA02, null);

                $data = [
                    "reg" => $reg,
                    "cca01" => $cca01,
                    "cca02" => $cca02,
                    "lastsync" => $syncTo
                ];
                return $this->createResponseByVerifyData($data);
            }
        } catch (\Exception $ex) {
            return $this->createResponseByVerifyData($ex);
        }
    }

    public function actionSaveEzform()
    {
        if (Yii::$app->request->post()) {
            $ezf_id = Yii::$app->request->post('ezf_id', null);
            $dataid = Yii::$app->request->post('data_id', null);
            $data = Yii::$app->request->post('data', null);
            if ($data != null) {
                $data = json_decode($data, true);
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (empty($dataid)) {
                $res = EzfUiFunc::backgroundInsert($ezf_id, '', '', $data);
                if ($res['status'] == 'success') {
                    $table_name = Ezform::findOne($ezf_id)->ezf_table;
                    $model = EzfUiFunc::loadTbData($table_name, $res['data']['id']);
                    $model['id'] = "".$model['id']."";
                    $model['ptid'] = "".$model['ptid']."";
                    $res['data'] = $model;
                }
            } else {
                $res = EzfUiFunc::backgroundInsert($ezf_id, $dataid, '', $data);
                if ($res['status'] == 'success') {
                    $table_name = Ezform::findOne($ezf_id)->ezf_table;
                    $model = EzfUiFunc::loadTbData($table_name, $res['data']['id']);
                    $model['id'] = "".$model['id']."";
                    $model['ptid'] = "".$model['ptid']."";
                    $res['data'] = $model;
                }
            }
            return $res;
        } else {
            throw new MethodNotAllowedHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
