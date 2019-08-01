<?php
namespace backend\controllers;
use Yii;
use yii\web\Response;
class ManageSitecodeController extends \yii\web\Controller {
 
    public function actionQuerys($q){
	$sql = "SELECT hcode AS `code`, CONCAT(IFNULL(`hcode`,''), ' : ', IFNULL(`name`,''), ' ต.', IFNULL(`tambon`,''), ' อ.', IFNULL(`amphur`,''), ' จ.', IFNULL(`province`,'')) AS `name` FROM `all_hospital_thai` WHERE `name` LIKE '%".$q."%' OR `hcode` LIKE '%".$q."%' LIMIT 0,10";
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        $json = array();
        foreach($data as $value){
            $json[] = ['id'=>$value['code'],'label'=>$value["name"]];
        }
        return $json;
    }
    public function actionQuerys2($q){
        if(Yii::$app->keyStorage->get('frontend.domain')=='thaicarecloud.org'){
	    $sql = "SELECT medshop AS `code`, CONCAT(IFNULL(`medshop`,''), ' : ', IFNULL(`nameshop`,'')) AS `name` FROM `tbdata_1483603520065899000` WHERE `nameshop` LIKE '%".$q."%' OR `medshop` LIKE '%".$q."%' LIMIT 0,10";
	} else {
	    $sql = "SELECT hcode AS `code`, CONCAT(IFNULL(`hcode`,''), ' : ', IFNULL(`name`,''), ' ต.', IFNULL(`tambon`,''), ' อ.', IFNULL(`amphur`,''), ' จ.', IFNULL(`province`,'')) AS `name` FROM `all_hospital_thai` WHERE `name` LIKE '%".$q."%' OR `hcode` LIKE '%".$q."%' LIMIT 0,10";
	}
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        $json = array();
        foreach($data as $value){
            $json[] = ['id'=>$value['code'],'label'=>$value["name"]];
        }
        return $json;

    }
    public function actionQuerys3($q){
        if(Yii::$app->keyStorage->get('frontend.domain')=='thaicarecloud.org' || Yii::$app->keyStorage->get('frontend.domain')=='yii2-starter-kit.dev'){
	    $sql = "SELECT orgcode AS `code`, CONCAT(IFNULL(`orgcode`,''), ' : ', IFNULL(`name`,''), ' จ.', IFNULL(`province`,'')) AS `name` FROM `all_hos_org` WHERE `name` LIKE '%".$q."%' OR `orgcode` LIKE '%".$q."%' LIMIT 0,10";
	} else {
	    $sql = "SELECT hcode AS `code`, CONCAT(IFNULL(`hcode`,''), ' : ', IFNULL(`name`,''), ' ต.', IFNULL(`tambon`,''), ' อ.', IFNULL(`amphur`,''), ' จ.', IFNULL(`province`,'')) AS `name` FROM `all_hospital_thai` WHERE `name` LIKE '%".$q."%' OR `hcode` LIKE '%".$q."%' LIMIT 0,10";
	}
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        $json = array();
        foreach($data as $value){
            $json[] = ['id'=>$value['code'],'label'=>$value["name"]];
        }
        return $json;

    }
}
