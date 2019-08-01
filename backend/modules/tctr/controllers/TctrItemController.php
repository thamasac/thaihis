<?php

namespace backend\modules\tctr\controllers;

use appxq\sdii\utils\VarDumper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use backend\modules\tctr\classes\TctrFunction;
use backend\modules\tctr\classes\TctrValue;
use appxq\sdii\helpers\SDHtml;
use Yii;
use yii\helpers\Url;

class TctrItemController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionReadXmlfile() {
        header('Access-Control-Allow-Origin: *');
        $tctrid = isset($_GET['tctrdata']) ? $_GET['tctrdata'] : '';
        $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
        $load = TctrFunction::Downloadxml($tctrid);
        if ($load) {
            $filexml = 'xmlfile/' . $tctrid . '.xml';
            $rawdata = simplexml_load_string(file_get_contents($filexml));
            $jsondata = json_encode($rawdata->trial);
            $ArrayData = json_decode($jsondata, true);
            $data = TctrFunction::InsertdataXML($dataid, $ArrayData, $tctrid);
            if ($data) {
                $result[] = $data;
                $result[] = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Success.'),
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Insert not complete'),
                ];
            }
        } else {
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Not found.'),
            ];
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $result;
    }

    public function actionGetLatLng() {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', '1012M');

        $sql = "select id,city,map_lat,map_lng 
                from zdata_tctr_part_sectionc where rstat =1 and city <>'' and (map_lat is null or map_lng is null ) LIMIT 7000";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        if ($data) {
            foreach ($data as $key => $value) {
                $city = preg_replace('/[[:space:]]+/', '', trim(TctrValue::getValue($value['city'])));
                $id = $value['id'];
                $datamap = TctrFunction::GetLocation($city);
                if($datamap=='over_query'){
                    echo $datamap;
                    exit();
                }
                $map_lat = $datamap['results'][0]['geometry']['location']['lat'];
                $map_lng = $datamap['results'][0]['geometry']['location']['lng'];
                $sqlupdate = "UPDATE `zdata_tctr_part_sectionc` set map_lat=:lat,map_lng=:lng where id=:id";
                $params = [
                    ':lat' => $map_lat,
                    ':lng' => $map_lng,
                    ':id' => $id,
                ];
                \Yii::$app->db->createCommand($sqlupdate,$params)->execute();
            }
        }
        echo "end";
    }
    public function actionReadAllXmlfile() {
        $loop = isset($_GET['loop']) ? $_GET['loop'] * 1 : 0;
        $tctr = isset($tctr) ? $tctr : 'TCTR201804305u7';
        $filexml = 'xmlfile/TCTR201806112uk.xml';
        $rawdata = simplexml_load_string(file_get_contents($filexml));
        $n = 0;
        foreach ($rawdata->trial as $value) {
            if ($n == $loop) {
                $jsondata = json_encode($value);
                $ArrayData = json_decode($jsondata, true);
                $array = TctrFunction::InsertdataXML('', $ArrayData, '');
                $data['tctr'] = isset($array['data']['trial_id']) ? $array['data']['trial_id'] : '';
            }
            $n++;
        }
        $data['loop'] = $loop;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $data;
    }

    public function actionReadXmlNct() {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $loop = isset($_GET['loop']) ? $_GET['loop'] * 1 : 2;
        $dir = isset($_GET['dir']) ? $_GET['dir'] : '';
        $file = scandir("csv/ClinicalTrials/" . $dir); // Open a directory, and read its contents
        unset($file[0]);
        unset($file[1]);
        $count = count($file) + 1;
        $n = 0;
        foreach ($file as $key => $value) {
            if ($n == $loop) {
                $filexml = "csv/ClinicalTrials/" . $dir . "/" . $value;
                $data = @file_get_contents($filexml);
                if ($data === FALSE) {
                    echo "error";
                } else {
                    $rawdata = simplexml_load_string($data);
                    $jsondata = json_encode($rawdata);
                    $ArrayData = json_decode($jsondata, true);
                    $load = TctrFunction::InsertdataNCT($ArrayData);
                }
            }
            $n++;
        }

        $data = [];
        $data['loop'] = $loop;
        $data['end'] = $count;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $data;
    }

    public function actionReadAllXmlNct() {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', '2048M');
        $dir = scandir("csv/ClinicalTrials/");
        if (isset($dir[2])) {
            try {
                $file = scandir("csv/ClinicalTrials/" . $dir[2]);
                unset($file[0]);
                unset($file[1]);
                foreach ($file as $key => $value) {
                    $data = [];
                    $filexml = "csv/ClinicalTrials/" . $dir[2] . "/" . $value;
                    $data = @file_get_contents($filexml);
                    if ($data === FALSE) {
                        echo "error";
                    } else {
                        $rawdata = simplexml_load_string($data);
                        $jsondata = json_encode($rawdata);
                        $ArrayData = json_decode($jsondata, true);
                        $load = TctrFunction::InsertdataNCT($ArrayData);
                    }
                }
                rename("csv/ClinicalTrials/" . $dir[2], "csv/ClinicalTrials_finish/" . $dir[2]);
                return $this->redirect('read-all-xml-nct');
            } catch (\Exception $e) {
                echo $e;
            }
        } else {
            echo "end";
        }
    }
    public function actionReadNct() {
        $filexml = 'csv/ClinicalTrials/xxx/NCT03539549.xml';
        $rawdata = simplexml_load_string(file_get_contents($filexml));
        $jsondata = json_encode($rawdata);
        $ArrayData = json_decode($jsondata, true);
        if(isset($ArrayData['primary_outcome'][0])){
            $count = count($ArrayData['primary_outcome']);
            VarDumper::dump($count);
        }

    }
}
