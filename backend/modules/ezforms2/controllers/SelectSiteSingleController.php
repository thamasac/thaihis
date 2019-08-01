<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class SelectSiteSingleController extends Controller {

    public static function initSite($model, $modelFields) {
        $id = $model[$modelFields['ezf_field_name']];
        $str = '';
        if (isset($id) && !empty($id)) {
            $sitecode = \Yii::$app->user->identity->profile->sitecode;

            $data = (new \yii\db\Query())->select(["site_name as id", "CONCAT(site_detail,'(',site_name,')') as name"])
                    ->from('zdata_sitecode')
                    ->where(["site_name" => $id])
                    ->andWhere('rstat not in (0,3)')
                    ->one();

            $str = $data['name'] . " ({$data['id']}) ";
        }

        return $str;
    }

    public function actionGetSite($q = null, $id = null) {

        //$sitecode = \Yii::$app->user->identity->profile->sitecode;      
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
        $sql = "SELECT site_name as id, CONCAT(site_detail,' (',site_name,')') as text FROM zdata_sitecode WHERE CONCAT(site_detail,'(',site_name,')') LIKE :q AND rstat not in (0,3)";
        $data = Yii::$app->db->createCommand($sql, [":q" => "%$q%"])->queryAll();

//        $i = 0;
//        
//        foreach ($data as $value) {
//            $out["results"][$i] = ['id' => $value['id'], 'text' => $value["name"]];
//            $i++;
//        }
        if ($data) {
            $out['results'] = array_values($data);
        } else {
            $out['results'] = [['id'=>'-9999','text' => Yii::t('app', 'New')." '$q'"]];
        }

        return $out;
    }

}
