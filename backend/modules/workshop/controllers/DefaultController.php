<?php

namespace backend\modules\workshop\controllers;


use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\models\TbdataAll;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\Controller;
use yii\web\Response;

/**
 * Default controller for the `purify` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionWorkshopView(){

        $workshopData = new TbdataAll();
        $workshopData->setTableName("zdata_1532334018067132600");
        $query = $workshopData->find()->where('share_target LIKE "%\"1531423223042693400\"%"');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
        return $this->renderAjax('_workshop_grid',["dataProvider"=>$dataProvider]);
    }

    public function actionReloadData( $id , $lastUpdate){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $ezfId = "1532334018067132600";
        $ezfTable = "zdata_1532334018067132600";

        // GET Preset
        $preset = \Yii::$app->db->createCommand("SELECT * FROM zdata_workshop WHERE id = :id",[":id" => $id])->queryOne();
        $fieldList = SDUtility::string2Array($preset['show_fields']);
        array_push($fieldList,"id");
        array_push($fieldList,"share_target as editable_users");
        array_push($fieldList,"user_create as create_user_id");
        array_push($fieldList,"rstat");
        $query = (new Query())->select($fieldList)->from($ezfTable)->where(['show_collaborative'=>1]);
        $queryField = (new Query())->select(["ezf_field_name as fieldName","ezf_field_label  as fieldLabel"])->from("ezform_fields")->where(['ezf_id'=>$ezfId])->all();
        if($lastUpdate != null && $lastUpdate != 'null'){
            $lastUpdate = date('Y-m-d H:i:s', strtotime('-2 seconds', strtotime($lastUpdate)));
            $query->andWhere([">=" ,"update_date",$lastUpdate]);
            $query->andWhere("rstat NOT IN (0)");
        }else{
            $query->andWhere("rstat NOT IN (0,3)");
        }
        $queryResult = $query->all();
        foreach ($queryResult as $key=>$value){
            $queryResult[$key]["editable_users"] =  SDUtility::string2Array($queryResult[$key]["editable_users"]);
        }
        $now = (new Query())->select("NOW() as lastUpdate")->from("zdata_1532334018067132600")->scalar();
        return ["results" => $queryResult , "lastUpdate"=>$now,"fields"=>$queryField];
    }
    public function actionWorkshopData(){
        Yii::$app->response->format = Response::FORMAT_JSON;
//{"results":[{"id":"1","text":"Admin Service"}]}
        $result = \Yii::$app->db->createCommand("SELECT id,title as text FROM zdata_workshop")->queryAll();
        return ["results" => $result];
    }
}
