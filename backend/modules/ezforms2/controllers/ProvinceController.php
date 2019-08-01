<?php
namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Json;

class ProvinceController extends Controller{
    
    public $enableCsrfValidation = false;
    
    public function beforeAction($action) {
	if (parent::beforeAction($action)) {
	    if (in_array($action->id, array('get-amphur', 'get-tumbon'))) {
                
	    }
	    return true;
	} else {
	    return false;
	}
    }
    
    public function actionGetProvince($q = null, $id = null) {
	Yii::$app->response->format = Response::FORMAT_JSON;
	$out = ['results' => ''];
	if (is_null($id)) {
	    $sql = "SELECT `PROVINCE_ID` AS id, `PROVINCE_CODE` AS code,`PROVINCE_NAME` AS text FROM `const_province` WHERE `PROVINCE_NAME` LIKE :q";
	    $data = Yii::$app->db->createCommand($sql, [':q'=>"%$q%"])->queryAll();
	    $out['results'] = array_values($data);
	} elseif ($id > 0) {
	    $model = \backend\modules\ezforms2\models\ConstProvince::find($id);
	    $out['results'] = ['id' => $id, 'code' => $model->PROVINCE_CODE, 'text' => $model->PROVINCE_NAME];
	}
	return $out;
    }

    public function actionGetAmphur(){
	$out = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
	if (isset($_POST['depdrop_parents'])) {
	    $parents = $_POST['depdrop_parents'];
	    if ($parents != null) {
		$id = empty($parents[0]) ? null : $parents[0];
		if ($id != null) {
		    $param1 = null;
		    if (!empty($_POST['depdrop_params'])) {
			$params = $_POST['depdrop_params'];
			$param1 = $params[0]; // get the value of input-type-1
		    }
		    $pid = Yii::$app->db->createCommand("SELECT PROVINCE_ID FROM const_province WHERE PROVINCE_CODE = :id", [':id'=>$id])->queryScalar();
		    
		    $sql = "SELECT `AMPHUR_CODE` AS id,`AMPHUR_NAME` AS name FROM `const_amphur` WHERE `PROVINCE_ID` = :id";
		    $data = Yii::$app->db->createCommand($sql, [':id'=>$pid])->queryAll();
		    
		    $out = array_values($data);
		    $out = ['output'=>empty($out)?'':$out, 'selected'=>$param1];
		   
		    return $out;
		}
	    }
	}
	return ['output'=>'', 'selected'=>''];
    }
    
    public function actionGetTumbon(){
	$out = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
	if (isset($_POST['depdrop_parents'])) {
	    $parents = $_POST['depdrop_parents'];
	    $ids = $_POST['depdrop_parents'];
	    //$pid = empty($ids[0]) ? null : $ids[0];
	    $aid = empty($ids[0]) ? null : $ids[0];
	    if ($aid != null) {
		$param1 = null;
		if (!empty($_POST['depdrop_params'])) {
		    $params = $_POST['depdrop_params'];
		    $param1 = $params[0]; // get the value of input-type-1
		}
		
		$id = Yii::$app->db->createCommand("SELECT AMPHUR_ID FROM const_amphur WHERE AMPHUR_CODE = :id", [':id'=>$aid])->queryScalar();
		
		$sql = "SELECT `DISTRICT_CODE` as id,`DISTRICT_NAME` as name FROM `const_district` WHERE `AMPHUR_ID` = :aid";
		$data = Yii::$app->db->createCommand($sql, [':aid'=>$id])->queryAll();
		
		$out = array_values($data);
		$out = ['output'=>empty($out)?'':$out, 'selected'=>$param1];
		   
		    return $out;
	    }
	}
	return ['output'=>'', 'selected'=>''];
    }
    
}
