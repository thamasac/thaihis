<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\api\v1\controllers;

/**
 * Description of LogController
 *
 * @author chanpan
 */
class LogController extends \yii\web\Controller{
    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->enableCsrfValidation = false;
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin,Content-Type,Authorization,user_id,application,platform,uuid,version");
        return parent::beforeAction($action);
    }
    //put your code here
    public function actionIndex(){
      try{
            
            $page = \Yii::$app->request->get('page','');
            $perPage = \Yii::$app->request->get('per-page','');
            $term = \Yii::$app->request->get('term');
            $dynamicDb = \backend\modules\log\models\DynamicDb::find()->where("rstat not in(0,3) AND url <> ''");
            if ($term != '') {
                $dynamicDb = $dynamicDb->andWhere("url LIKE :url", [':url'=>"%{$term}%"]);
            }
            if($page != '' && $perPage != ''){ 
                $dynamicDb = $dynamicDb->limit($perPage)->offset($page-1);
            }
            $dynamicDb = $dynamicDb->all();
            $output  = [
                'success'=>true,
                'data'=>[]
            ];
            foreach($dynamicDb as $k=>$v){
                try{ 
                   
                    $output['data'][$k]['aconym'] = isset($v['aconym'])?$v['aconym']:'';
                    $output['data'][$k]['projectName'] = isset($v['proj_name'])?$v['proj_name']:'';
                    $output['data'][$k]['url'] = isset($v['url'])?$v['url']:''; 
                    $dbname = isset($v['dbname']) ? $v['dbname'].'.' : '';
                    $sql = "SELECT count(*) FROM {$dbname}system_error"; 
                    $data = \Yii::$app->db->createCommand($sql)->queryScalar();
                    $output['data'][$k]['count'] = $data;
                } catch (\yii\db\Exception $ex) {

                }
             }
             return $output;
      } catch (Exception $ex) {

      }
       
    }//
}
