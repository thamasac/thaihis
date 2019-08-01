<?php

namespace backend\modules\restfull\controllers;

use yii\filters\AccessControl;
use common\models\AccessTokens;
use common\models\LoginForm;
use common\models\AuthorizationCodes;
use backend\behaviours\Verbcheck;
use backend\behaviours\Apiauth;
use backend\modules\restfull\controllers\RestController;
use backend\modules\restfull\classes\RestfullQuery;
use yii\web\Response;
use Yii;
use backend\modules\patient\classes\PatientFunc;
class Restfull2Controller extends RestController {

    public function behaviors() {

        $behaviors = parent::behaviors();

        return $behaviors + [
            'apiauth' => [
                'class' => Apiauth::className(),
                'exclude' => [ 'authorize', 'accesstoken'],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
//                    [
//                        'actions' => ['signup'],
//                        'allow' => true,
//                        'roles' => ['?'],
//                    ],
                    [
                        'actions' => ['logout', 'me'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
//                    [
//                        'actions' => [ 'authorize', 'register', 'accesstoken', 'checkmregis', 'getlistcheckup', 'checkdate', 'login', 'getprofile', 'visits', 'reportnavi', 'getdateappoint'],
//                        'allow' => true,
//                        'roles' => ['*'],
//                    ],
                ],
            ],
            'verbs' => [
                'class' => Verbcheck::className(),
                'actions' => [
                    'logout' => ['GET'],
                    'authorize' => ['POST'],
                    'accesstoken' => ['POST'],
                    'me' => ['GET'],
                ],
            ],
        ];
    }

    public function actionMe() {
        $data = Yii::$app->user->identity;
        $data = $data->attributes;
        unset($data['auth_key']);
        unset($data['password_hash']);
        unset($data['password_reset_token']);

        Yii::$app->api->sendSuccessResponse($data);
    }

    public function actionGetPatient() {
        $page = Yii::$app->request->get('page');
        $q = Yii::$app->request->get('q');
        $limit = Yii::$app->request->get('perpage');
        if (empty($page)) {
            $page = 1;
        }
        if (empty($limit)) {
            $limit = 10;
        }
        $model = RestfullQuery::getPatientData($page, $q, $limit);
        $modelcount = RestfullQuery::getCountPatientData($q);
        $data['result'] = $model;
        $data['count'] = $modelcount['COUNTS'];
        $data['page'] = $page;
        $data['perpage'] = $limit;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['status' => '1', 'data' => $data];
        //Yii::$app->api->sendSuccessResponse($data);
    }

    public function actionGetAppoint() {
        $cid = Yii::$app->request->get('cid');

        $model = RestfullQuery::getAppointHistory($cid);
        $data['result'] = $model;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['status' => '1', 'data' => $data];
        //  Yii::$app->api->sendSuccessResponse($data);
    }

    public function actionGetDept() {
        $model = RestfullQuery::getDept();
        $data['result'] = $model;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['status' => '1', 'data' => $data];
        //  Yii::$app->api->sendSuccessResponse($data);
    }

    public function actionGetDoctor() {
        $model = RestfullQuery::getDoctor(2);
        $data['result'] = $model;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['status' => '1', 'data' => $data];
        //  Yii::$app->api->sendSuccessResponse($data);
    }

    public function actionGetNurse() {
        $model = RestfullQuery::getDoctor(0);
        $data['result'] = $model;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['status' => '1', 'data' => $data];
        //  Yii::$app->api->sendSuccessResponse($data);
    }

    public function actionGetPatientDisease() {
        $cid = Yii::$app->request->get('cid');
        $model = RestfullQuery::getPatientDisease($cid);
        $data['result'] = $model;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['status' => '1', 'data' => $data];
        // Yii::$app->api->sendSuccessResponse($data);
    }

    public function actionGetPatientHistory() {
        $cid = Yii::$app->request->get('cid');
        $model = RestfullQuery::getHistoryPatient($cid);
        $data['result'] = $model;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['status' => '1', 'data' => $data];
        // Yii::$app->api->sendSuccessResponse($data);
    }

    public function actionGetNsho() {
        $cid = Yii::$app->request->get('cid');
        $model = PatientFunc::getRightOnlineByNhso($cid);
        unset($model['birthdate']);
        unset($model['count_select']);
        unset($model['fname']);
        unset($model['lname']);
        unset($model['title_name']);
       // unset($model['person_id']);
        unset($model['primary_amphur_name']);
        unset($model['primary_moo']);
        unset($model['primary_mooban_name']);
        unset($model['primary_province_name']);
        unset($model['primary_tumbon_name']);
        unset($model['sex']);
        unset($model['title']);
        $data['result'] = $model;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['status' => '1', 'data' => $data];
    }

    public function actionAccesstoken() {

        if (!isset($this->request["authorization_code"])) {
            Yii::$app->api->sendFailedResponse("Authorization code missing");
        }

        $authorization_code = $this->request["authorization_code"];
        $auth_code = AuthorizationCodes::isValid($authorization_code);
        if (!$auth_code) {
            Yii::$app->api->sendFailedResponse("Invalid Authorization Code");
        }

        $accesstoken = Yii::$app->api->createAccesstoken($authorization_code);

        $data = [];
        $data['access_token'] = $accesstoken->token;
        $data['expires_at'] = $accesstoken->expires_at;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $data;
        //   Yii::$app->api->sendSuccessResponse($data);
    }

    public function actionAuthorize() {
        $model = new LoginForm();

        $model->attributes = $this->request;
        if ($model->validate() && $model->login()) {
            $auth_code = Yii::$app->api->createAuthorizationCode(Yii::$app->user->identity['id']);

            $data = [];
            $data['authorization_code'] = $auth_code->code;
            $data['expires_at'] = $auth_code->expires_at;

            Yii::$app->api->sendSuccessResponse($data);
        } else {
            Yii::$app->api->sendFailedResponse($model->errors);
        }
    }

    public function actionLogout() {
        $headers = Yii::$app->getRequest()->getHeaders();
        $access_token = $headers->get('x-access-token');

        if (!$access_token) {
            $access_token = Yii::$app->getRequest()->getQueryParam('access-token');
        }

        $model = AccessTokens::findOne(['token' => $access_token]);

        if ($model->delete()) {

            Yii::$app->api->sendSuccessResponse(["Logged Out Successfully"]);
        } else {
            Yii::$app->api->sendFailedResponse("Invalid Request");
        }
    }

}
