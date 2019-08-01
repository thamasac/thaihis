<?php

namespace backend\modules\random\controllers;
use appxq\sdii\utils\SDUtility;
use Yii;
use yii\web\Controller;
class RandomizationController extends Controller{
    public function actionSaveSession(){
//       if(!empty($_POST)){
//           $options = isset($_POST['options']) ? $_POST['options'] : '';
//           \Yii::$app->session['options'] = $options;
//       }
    }
    public function actionIndex(){
        $reloadDiv = Yii::$app->request->get('reloadDiv','random-'.SDUtility::getMillisecTime());
        $color = Yii::$app->request->get('color','panel-primary');
        $options = isset($_GET['options']) ? $_GET['options'] : '';
//        $options = isset(\Yii::$app->session['options']) ? \Yii::$app->session['options'] : $options;
        return $this->renderAjax('index', ['options'=>$options,'color'=>$color,'reloadDiv'=>$reloadDiv]);
    }
    
    public function actionSetting(){
        $reloadDiv = Yii::$app->request->get('reloadDiv','random-'.SDUtility::getMillisecTime());
        $color = Yii::$app->request->get('color','panel-primary');
        $options = isset($_GET['options']) ? $_GET['options'] : '';
        //\yii\helpers\VarDumper::dump($options, 10 , true);
//        $options = isset(\Yii::$app->session['options']) ? \Yii::$app->session['options'] : $options;
        return $this->renderAjax('setting', ['options'=>$options,'color'=>$color,'reloadDiv'=>$reloadDiv]);
    }
    public function actionSuccess(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'status' => 'success',
            'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('app', 'Success.'),
        ];
        return $result;
       
    }
    public function actionDownloadcsv(){
        
       $data = isset($_POST['value']) ? $_POST['value'] : "";
       $type = isset($_POST['type']) ? $_POST['type'] : "0";
       $table_id = isset($_POST['table_id']) ? $_POST['table_id'] : "data-table";
       $data = preg_split("/\r\n|\n|\r/", $data);//explode('|', $data);
       $out=[];
       $n=0;

       foreach($data as $k=>$d){
           try {
               $data_str = explode(',', $d);
               if(isset($data_str[0]) && isset($data_str[1]) && isset($data_str[2]) && isset($data_str[3]) && isset($data_str[4]) && isset($data_str[5])){
                $out[$n] = ['No.'=>(int)$data_str[0],'block'=>(int)$data_str[1], 'block_size'=>$data_str[2], 'block_num'=>$data_str[3], 'group'=>$data_str[4], 'code'=>$data_str[5]];
                $n++;
               }
           } catch (\Exception $ex) {
               \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
           }
           
           // \yii\helpers\VarDumper::dump($out, 10, true);
       } 
        //exit();
       return $this->renderAjax('downloadcsv', [
           'out'=>$out,
           'type' =>$type,
           'table_id' =>$table_id
       ]);
    }
    
    public function actionGetRandomCode(){
        $options['seed'] = \Yii::$app->request->get('seed','');
        $options['list_length'] = \Yii::$app->request->get('list_length','');
        $options['block_size'] = \Yii::$app->request->get('block_size','');
        $options['treatment'] = \Yii::$app->request->get('treatment','');
        $status = \Yii::$app->request->get('status','1');
        
        echo \backend\modules\random\classes\CNRandom::getRandomBlock($options, $status);
    }
    
    public function actionGetUpload($id = 'file-upload-random'){
        return $this->renderAjax('upload',['id'=>$id]);
    }
}
