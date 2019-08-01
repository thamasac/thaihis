<?php
namespace backend\modules\ezforms2\controllers;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfUiFunc;
use appxq\sdii\utils\SDUtility;
 
class SelectSiteController extends Controller{
    
     
    public static function initSite($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        if (!is_array($code)) {
            $code = SDUtility::string2Array($code);
        }
        $id = join(',', $code);
        $str = [];
        if ($id != '') {
            $data = (new \yii\db\Query())->select(["site_name as id" , "CONCAT(site_detail,'(',site_name,')') as name"])
                    ->from('zdata_sitecode')
                    ->where("site_name IN ({$id})")
                    ->all();
//            $sql = "SELECT * FROM `const_icd9` WHERE `code`=:code";
//            $data = Yii::$app->db->createCommand($sql, [':code'=>$code])->queryOne();
//            \appxq\sdii\utils\VarDumper::dump($data);
            foreach ($data as $value) {

                $str[] = $value['name'];
            }
        }
//        $str = join(',', $str);
//        \appxq\sdii\utils\VarDumper::dump($str);
        return $str;
        
    }
    public static function getSiteValue($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
         //\appxq\sdii\utils\VarDumper::dump($model['sitecode_data']);
        if (!is_array($code)) {
            $code = SDUtility::string2Array($code);
        }
       
        $id = join(',', $code);
        $str = [];
        if ($id != '') {
            $data = (new \yii\db\Query())->select(["site_name as id" , "CONCAT(site_detail,'(',site_name,')') as name"])
                    ->from('zdata_sitecode')
                    ->where("site_name IN ({$id})")
                    ->all(); 
            foreach ($data as $value) {
                $str[] = $value['name'];
            }
        }
        $str = join(' , ', $str);
      
        return $str;
    }
    public function actionGetSite($q = null, $id = null)
    {
        $sitecode = isset(\Yii::$app->user->identity->profile->sitecode) ? \Yii::$app->user->identity->profile->sitecode : '';      
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
            $sql = "SELECT site_name as id, CONCAT(site_detail,'(',site_name,')') as name FROM zdata_sitecode WHERE CONCAT(site_detail,'(',site_name,')') LIKE :q  LIMIT 0,50";
            $data = Yii::$app->db->createCommand($sql, [':q'=>"%$q%"])->queryAll();
            $i = 0;
            foreach($data as $value){
                $out["results"][$i] = ['id'=>$value['id'],'text'=>$value["name"]];
                $i++;
            }
      
        return $out;
    }
    
     
}
