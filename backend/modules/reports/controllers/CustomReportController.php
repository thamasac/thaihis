<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\reports\controllers;
use Yii;

/**
 * Description of CustomReportController
 *
 * @author chanpan
 */
use common\lib\tcpdf\SDPDF;
class CustomReportController extends \yii\web\Controller{
    //put your code here
    public function actionIndex(){
       $ezfId       = \Yii::$app->request->get('ezf_id', '1503378440057007100');
       $dataId      = \Yii::$app->request->get('data_id', '1528879530068152000');
      
       $dataSet      = \Yii::$app->request->get('data_set', '0');
       if($dataSet == '1'){
            if(Yii::$app->user->can('administrator')){
                return $this->render('report_dataset',[]);
            }
       }
       
       $dataTemplate = \backend\modules\reports\models\CustomPrint::find()
               ->where('ezf_id=:ezf_id AND `default`=10 AND rstat not in(0,3)',[
                   ':ezf_id'=>$ezfId
               ])->one();
       $data = \backend\modules\reports\classes\CustomReport::getEzfData($ezfId, "");
       $view = "index";
       $params = [
           'data'=>$data,
           //'output'=>$data['output'],
           //"dataKey"=>$data['dataKey'], 
           'ezf_id'=>$ezfId, 
           'data_id'=>$dataId, 
           "dataTemplate"=>$dataTemplate
       ];
       if(\Yii::$app->request->isAjax){
           return $this->renderAjax($view,$params);
       }
       
       return $this->render($view,$params);
    }
    public function actionProcessCommand(){
        $sql_command = Yii::$app->request->post('sql_command', '');
        if($sql_command != ''){
            $exec = Yii::$app->db->createCommand($sql_command)->queryOne();
            if($exec){
                return \cpn\chanpan\classes\CNResponse::getSuccess('Process complete', $exec);
            }
        }
    }
    public function actionPrintCommand(){
        $template    = \Yii::$app->request->post('template', '');
        $layout    = \Yii::$app->request->post('layout', '');
        $page_size    = \Yii::$app->request->post('page_size', '');
        $sql_command        = \Yii::$app->request->post('sql_command', '');
        $title    = \Yii::$app->request->post('title', '');
        if($sql_command != ''){
            $output = Yii::$app->db->createCommand($sql_command)->queryOne();
            $path = [];
            foreach ($output as $key => $value) {
                $path["{" . $key . "}"] = $value;
            }
            $template = strtr($template, $path); 
        }
        //\appxq\sdii\utils\VarDumper::dump($page_size);
        return \backend\modules\reports\classes\CustomReport::printPDF('2',$layout, $page_size, $title, $template, '', '');
    }

    public function actionPrint(){
        $origin = "*";
        if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
            $origin = $_SERVER['HTTP_ORIGIN'];
        }
        header("Access-Control-Allow-Origin: $origin", true);
        header("Access-Control-Allow-Credentials: true");
        /**
         * CORS
         */
        if (Yii::$app->getRequest()->getMethod() === 'OPTIONS') {
            Yii::$app->end();
        }

       $ezfId       = \Yii::$app->request->get('ezf_id', '1523071255006806900');
       $dataId      = \Yii::$app->request->get('data_id', '1528879530068152000');
       $template    = \Yii::$app->request->get('template', '');
       $print       = \Yii::$app->request->get('print', '1');
       
       $patient     = \Yii::$app->request->get('patient', '0');
       $visit_id    = \Yii::$app->request->get('visit_id', '1537683022033912900');
       $template_id       = \Yii::$app->request->get('template_id', ''); //template id
       $user_id     = isset(Yii::$app->user->id)?Yii::$app->user->id:'';   
       
       $layout      = \Yii::$app->request->get('layout', 'P'); //P แนวตั้ง L แนวนอน
       $paperSize   = \Yii::$app->request->get('paper_size', 'A4');//string x,y x=white y=height
       $paperSize   = (explode(",",$paperSize));
       $hn = '';
       if(count($paperSize) == 2){
           $paperSize = [$paperSize[0], $paperSize[1]];
       }else{
           $paperSize   = $paperSize[0];
       }
       
       if($template_id){
           $template = \backend\modules\reports\models\CustomPrint::findOne($template_id);
       }else{
           $template = \backend\modules\reports\models\CustomPrint::find()->where("rstat not in(0,3) AND `default`='10'")->one();
       }
       $dataId = ($dataId != "") ? $dataId : '1528879530068152000';
       $age = '';
        if($patient == "1" && $visit_id != ""){
            $output = \backend\modules\reports\classes\CustomReport::getPatientprofile($visit_id);
            if($output == false){
                $out = [
                    'success'=>false,
                    'msg'=>'ไม่พบข้อมูลสิทธ์ กรุณาติดต่อห้องตรวจสิทธิ์!'
                ];
                return json_encode($out);
            }
            $title = "";
            $hn  = isset($output['hn']) ? $output['hn'] : '';
            $age = isset($output['pt_bdate']) ? $output['pt_bdate'] : '';
            $output['age']=$age;
            $out['hn']=$hn;
            
            
        }else{
            $data = \backend\modules\reports\classes\CustomReport::getEzfData($ezfId, $dataId);
            $dataKey = isset($data['dataKey']) ? $data['dataKey'] : '';
            $output  = isset($data['output']) ? $data['output'] : '';
            
            $title   = isset($data['ezfStruc']['ezf_name']) ? $data['ezfStruc']['ezf_name'] : '';
            $hn      = isset($data['output']['pt_hn']) ? $data['output']['pt_hn'] : '';
            $age     = isset($data['output']['pt_bdate']) ? $data['output']['pt_bdate'] : '';
            $cid     = isset($data['output']['pt_cid']) ? $data['output']['pt_cid'] : ''; 
            $output['hn']=$hn;
            $output['cid']=$cid;
            $output['firstname']= isset($output['pt_firstname']) ? $output['pt_firstname'] : '';
            $output['lastname']= isset($output['pt_firstname']) ? $output['pt_firstname'] : '';
            
            $output['fullname'] = "{$output['firstname']} {$output['lastname']}";
            
        } 
        //$department = Yii::$app->user->identity->profile->department;
        if(isset($user_id)){
            $department = \common\modules\user\classes\CNDepartment::getDepartmentValue($user_id);
            $output['department']=$department;
        }
        
        //select * from user_print_queue where rstat = 1 AND visit_id=1545010153071772800 AND user_id=1
        try{
            $price = '';
            $data_queue = (new \yii\db\Query())->select('*')->from('user_print_queue')
                    ->where('rstat = 1 AND visit_id=:visit_id AND user_id=:user_id',[
                        ':visit_id'=>$visit_id,
                        ':user_id'=>$user_id
                    ])->one();
     
            $price = isset($data_queue['data']) ? $data_queue['data'] : '';
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }

        $unit_name = '';
        if(isset($visit_id) && $visit_id != ''){
           $unit_name = \backend\modules\patient\classes\PatientQuery::getDestinationDepartment($visit_id);
        }
        
        $path = [];
        if($age != ''){
           $age = \appxq\sdii\utils\SDdate::getAgeMysqlDate($age);
        }
       
        $output['age'] = $age;
       
        $output['price']= $price != '' ? $price:'';
//        if(Yii::$app->user->id = '1'){
//            //debug price
//            \appxq\sdii\utils\VarDumper::dump($output['price']);
//        }
        $output['unit_name'] = $unit_name;
        foreach ($output as $key => $value) {
            $path["{" . $key . "}"] = $value;
        }
        $template = strtr($template['template'], $path); 
        return \backend\modules\reports\classes\CustomReport::printPDF($print,$layout, $paperSize, $title, $template, $ezfId, $dataId, $hn);
       
        
    }
    
    
    
    public function actionSave(){
        $post = \Yii::$app->request->post();
//        \appxq\sdii\utils\VarDumper::dump($post);
        
        if(!empty($post)){
            //\appxq\sdii\utils\VarDumper::dump($post);
            $model = \backend\modules\reports\models\CustomPrint::findOne($post['id']);
            //\appxq\sdii\utils\VarDumper::dump($post);
            $model->default = $post['defaults'];
            $model->template_id = $post['template_id'];
            $model->template = $post['template'];
            if($model->save()){
                $out =['success'=>true, ['data'=>['message'=>'Save success!']]];
                return \backend\modules\manageproject\classes\CNMessage::getSuccess('Success');
                //return json_encode($out);
            }
        }
        
    }
    
    
    //custom print by array
    public function actionDesignTemplate(){
        //$template    = \Yii::$app->request->post('template', '');
        $user_id    = isset(Yii::$app->user->id) ? Yii::$app->user->id : '';
        $model = \backend\modules\reports\models\CustomPrint::find()->where(['user_create'=>$user_id, 'default'=>10])->one();
        if(!$model){
            $model = new \backend\modules\reports\models\CustomPrint();
        }
        return $this->render('design-template',[
            'model'=>$model
        ]);
    }
    public function actionPrintPdfByData(){
        
    }
    
}
