<?php

/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 9/5/2018
 * Time: 4:44 PM
 */

namespace backend\modules\api\v1\controllers;

use backend\modules\api\v1\classes\LogStash;
use backend\modules\patient\classes\PatientQuery;
use Yii;
use yii\db\Query;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;

class ThaiHisController extends BaseApiController {

    public $request = null;

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin,Cache-Control,Content-Type,Authorization,user_id,application,platform,device_id,version");
        $this->request = Yii::$app->request;
        return parent::beforeAction($action);
    }

    public function actionSaveProfile() {
        if (Yii::$app->request->post()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $ezf_id = '1503378440057007100';
            $dataProfile = Yii::$app->request->post('EZ1503378440057007100');
            $data['pt_cid'] = str_replace('-', '', $dataProfile['pt_cid']);
            $profileData = PatientQuery::getPatientByCid($data['pt_cid'], '12276');
            LogStash::Log('1', 'actionSaveProfile::profileData', var_export($profileData,true), '', 'thaihis');

            if($profileData){
                $filePath = \Yii::getAlias('@storage').'/web/ezform/fileinput/'; //'/var/www/thaihis/portal/storage/web/ezform/fileinput/';
                $md5Server = null;
                $md5Upload = null;
                $skipNoImage = false;
                try{
                    if (strpos($profileData[0]['pt_pic'], 'nouser') !== false) {
                        $skipNoImage = true;
                    }
                }catch (\Exception $e){
                    LogStash::Log('1', 'actionSaveProfile::strpos($profileData[0]','', '', 'thaihis');
                }

                if(isset($profileData[0]['pt_pic']) && $profileData[0]['pt_pic'] != null && !$skipNoImage)
                    $md5Server = md5_file($filePath.$profileData[0]['pt_pic']);
               
                if(isset($_FILES['EZ1503378440057007100']['tmp_name']['pt_pic']) && $_FILES['EZ1503378440057007100']['tmp_name']['pt_pic'] != '')
                    $md5Upload = md5_file($_FILES['EZ1503378440057007100']['tmp_name']['pt_pic']);
                
                if($md5Server == $md5Upload){
                    unset($_FILES['EZ1503378440057007100']);
//                    echo $md5Server.'|'.$md5Upload;
                }

                LogStash::Log('1', 'actionSaveProfile::md5', $filePath.$profileData[0]['pt_pic'], $md5Server.'|'.$md5Upload, 'thaihis');
            }

            //init data
            $data['pt_pic'] = '';
            $name = explode(" ", $dataProfile['fullname_th']);
            $data['pt_bdate'] = $dataProfile['bdate'];
            $dateArr = explode('-',$data['pt_bdate']);
            if($dateArr[1] == '00' || $dateArr[2] == '00'){
                if($dateArr[1] == '00'){
                    $dateArr[1] = '06';
                }
                if($dateArr[2] == '00'){
                    $dateArr[2] = '15';
                }
                $data['pt_bdate'] = implode('-',$dateArr);
            }

            $data['pt_bdate_th'] = '00/00/0000';
            $data['pt_type_bdate'] = '2';
            
            $dataSex = PatientQuery::getPrefixId($name[0]);
            $data['pt_sex'] = $dataSex['prefix_sex'];
            $data['pt_prefix_id'] = $dataSex['prefix_id'];
            $data['pt_firstname'] = $name[1];
            $data['pt_lastname'] = $name[2];
            $address = explode("#", trim($dataProfile['address']));
            try{
                LogStash::Log('1', 'actionSaveProfile::address', $dataProfile['address'],var_export( $address , true), 'thaihis');
            }catch (\Exception $e){

            }
            $arrLength = count($address) - 1;
            //\appxq\sdii\utils\VarDumper::dump($arrLength);
            $data['pt_address'] = $address[0];
            if($address[1] == 'หมู่ที่'){
                $data['pt_moi'] = str_replace("หมู่ที่", "", $address[2]);
            }else{
                $data['pt_moi'] = "";
            }
            $data['pt_addr_tumbon'] = str_replace("ตำบล", "", $address[$arrLength - 2]);
            $data['pt_addr_amphur'] = str_replace("อำเภอ", "", $address[$arrLength - 1]);
            $data['pt_addr_province'] = str_replace("จังหวัด", "", $address[$arrLength]);

            $dataTAC = PatientQuery::getProviceByName($data['pt_addr_tumbon'], $data['pt_addr_amphur'], $data['pt_addr_province']);
            $data['pt_addr_tumbon'] = $dataTAC['DISTRICT_CODE'];
            $data['pt_addr_amphur'] = $dataTAC['AMPHUR_CODE'];
            $data['pt_addr_province'] = $dataTAC['PROVINCE_CODE'];
            $data['pt_addr_zipcode'] = $dataTAC['zipcode'];

            if (empty($profileData)) {
                $dataSerene = \backend\modules\patient\classes\PatientFunc::checkPtProfileOld($data['pt_cid']);//isset($data['pt_cid']) ? \backend\modules\patient\classes\PatientFunc::checkPtProfileOld($data['pt_cid']) : '';
                if (isset($dataSerene) && $dataSerene['value']['status'] == 'OLD') {
                    $dataSerene = $dataSerene['value'];
                    $data['pt_hn'] = $dataSerene['pt_hn'];
                    $data['pt_national_id'] = $dataSerene['pt_national_id'];
                    $data['pt_origin_id'] = $dataSerene['pt_national_id'];
                    $data['pt_religion_id'] = $dataSerene['pt_religion_id'];
                    $data['pt_mstatus'] = $dataSerene['pt_mstatus'];
                    $data['pt_occ'] = $dataSerene['pt_occ'];
                    $data['pt_phone2'] = $dataSerene['pt_phone2'];
                    $data['pt_contact_name'] = $dataSerene['pt_contact_name'];
                    $data['pt_contact_status'] = $dataSerene['pt_contact_status'];
                    $data['pt_contact_phone'] = $dataSerene['pt_contact_phone'];
                }
                LogStash::Log('1', 'actionSaveProfile::dataSerene', var_export(  $dataSerene , true),var_export( $data , true), 'thaihis');
                $dataid = \backend\modules\patient\classes\PatientFunc::backgroundInsert($ezf_id, '', '', $data)['data']['id'];
            } else {
                $dataid = $profileData[0]['id'];//isset( $profileData[0]['id']) ?  $profileData[0]['id'] : ''; 
                $dataid = \backend\modules\patient\classes\PatientFunc::backgroundInsert($ezf_id, $dataid, '', $data)['data']['id'];
            }
            $res = ['success' => $dataid, 'ptid' => "$dataid"];
            return $res;
        } else {
            throw new MethodNotAllowedHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionCheckExistProfile() {
        if (Yii::$app->request->post()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $ezf_id = '1503378440057007100';
            $cid = Yii::$app->request->post('cid', null);
            if($cid == null){
                throw new HttpException(500, 'require CID');
            }
            $profileData = PatientQuery::getPatientSearch($cid, '12276',false);
            if($profileData && count($profileData) > 0){
                $id = $profileData[0]['id'];
                $res = ['success' => $id, 'ptid' => $id];
                return $res;
            }else{
                $res = ['success' => false, 'message' => 'not found'];
                return $res;
            }
        } else {
            throw new MethodNotAllowedHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }


    function actionGetLastPrintQueue() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $res = (new Query)->select('*')->from('user_print_queue')->where(['user_id' => $this->user_id,'rstat' => 1])->limit(1)->one();
        if ($res != false) {
            return ['success' => true, 'data' => $res];
        } else {
            return ['success' => false];
        }
    }

    function actionGetLastVisit() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $target = Yii::$app->request->get('target', null);
        if ($target != null) {
            $res = (new Query)->select('id')->from('zdata_visit')->where(['ptid' => $target])->orderBy(['update_date' => SORT_DESC])->limit(1)->scalar();
            if ($res != false) {
                return ['success' => true, 'data' => $res];
            } else {
                return ['success' => false];
            }
        }
    }



}
