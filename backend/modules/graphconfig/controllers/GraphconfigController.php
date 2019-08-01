<?php

namespace backend\modules\graphconfig\controllers;
use appxq\sdii\utils\VarDumper;
use Yii;
use yii\db\Query;
use yii\web\Response;

class GraphconfigController extends \yii\web\Controller {

    public function actionIndex() {
        return $this->renderAjax('index');
    }
    public function actionConfig(){
        $proj_id = Yii::$app->request->get('id',0);
        $userId = Yii::$app->user->id;

        if (Yii::$app->request->post()) {

            // save data
            $forms =  Yii::$app->request->post('forms');

            $status = 0;
            $action = true ;
            $conf_order = 1;
            foreach($forms['id'] as $keys => $value){
                //set value
                //$conf_order = isset($forms['order'][$value]) ? $forms['order'][$value] : 0;
                $report_type = isset($forms['reporttype'][$value])  ? $forms['reporttype'][$value] : 0;
                $sql_command = isset($forms['sqlsetting'][$value]) != '' ? $forms['sqlsetting'][$value] : '';
                $config_array = [];
                $config_array['selectdate']=isset($forms['selectdate'][$value]) ? $forms['selectdate'][$value] : '';
                $config_array['selectsite']=isset($forms['selectsite'][$value]) ? $forms['selectsite'][$value] : '';
                $config_array['width']=isset($forms['width'][$value]) ? $forms['width'][$value] : '';
                $config_array['reporttypeval']=isset($forms['reporttypeval'][$value]) ? $forms['reporttypeval'][$value] : '';
                $config_array['reporttypevariable']=isset($forms['reporttypevariable'][$value]) ? $forms['reporttypevariable'][$value] : '';
                $config_array['graphname']=isset($forms['graphname'][$value]) ? $forms['graphname'][$value] : '';
                $config_array['textbox']=isset($forms['textbox'][$value]) ? $forms['textbox'][$value] : '';
                $config_array['selectsitedef']=isset($forms['selectsitedef'][$value]) ? $forms['selectsitedef'][$value] : '';
                $config_array['showselectsite']=isset($forms['showselectsite'][$value]) ? $forms['showselectsite'][$value] : '';
                $config_array['day']=isset($forms['day'][$value]) ? $forms['day'][$value] : '';
                $config_array['month']=isset($forms['month'][$value]) ? $forms['month'][$value] : '';
                $config_array['year']=isset($forms['year'][$value]) ? $forms['year'][$value] : '';

                $config_json = json_encode($config_array);
                $checkword = 0;
                $checkword = $this->checkSqlCommand($sql_command);
                if($checkword == 1){
                    $sql_command = '';
                }
                $q = new \yii\db\Query();
                $q->select('id')
                    ->from('advance_report_config')
                    ->where('id = :id', [':id'=>$value]);
                $data = $q->createCommand()->queryOne();
                if($data){
                    // update
                    try{
                        /* $sql = new \yii\db\Query();
                         $result = $sql->createCommand()->update('advance_report_config',
                                 [
                                     'conf_order'=>$conf_order,
                                     'report_type'=>$report_type,
                                     'sql_command'=>$sql_command,
                                     'config_json'=>$config_json,
                                     'user_update'=>$userId
                                 ],
                                 [':id'=>$value])->execute();
                         *
                          */
                        $sql = "update advance_report_config set conf_order = :conf_order, report_type = :report_type, sql_command = :sql_command, config_json = :config_json,
                            user_update = :user_update, update_date = NOW() where id = :id " ;
                        $result = Yii::$app->db->createCommand($sql, [':conf_order'=>$conf_order, ':report_type'=>$report_type, ':sql_command'=>$sql_command,
                            ':config_json'=>$config_json, ':user_update'=>$userId, ':id'=>$value])->execute();

                        if(!$result){
                            $status = 1;
                            $action = false;
                        }
                    } catch (\yii\db\Exception $e) {
                        \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                        $status = 1;
                        $action = false;
                    }


                }else{
                    try{
                        $sql = "insert into advance_report_config VALUES (:id, :proj_id, :conf_order, :report_type, :sql_command,:config_json, "
                            . ":user_create, NOW(), :user_update, NOW(),0)" ;
                        $result = Yii::$app->db->createCommand($sql, [':id'=>$value,':proj_id'=>$proj_id ,':conf_order'=>$conf_order, ':report_type'=>$report_type, ':sql_command'=>$sql_command,
                            ':config_json'=>$config_json, ':user_create'=>$userId, ':user_update'=>$userId])->execute();
                        if(!$result){
                            $status = 1;
                            $action = false;
                        }
                    } catch (\yii\db\Exception $e) {
                        \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                        $status = 1;
                        $action = false;
                    }

                }
                $conf_order++;
            }
            // save report config

            if ($status == 0) {
                Yii::$app->getSession()->setFlash('alert', [
                    'body'=> Yii::t('app', 'Data completed.'),
                    'options'=>['class'=>'alert-success']
                ]);

                if($action){
                    return $this->redirect($forms['referrer']);
                } else {
                    return $this->redirect(['/graphconfig/graphconfig/config', 'id'=>$proj_id]);
                }
            } else {
                Yii::$app->getSession()->setFlash('alert', [
                    'body'=> Yii::t('app', 'Can not create the data.'),
                    'options'=>['class'=>'alert-danger']
                ]);
            }
        }
        $sql = 'select * from advance_report_config where proj_id = :proj_id and status = 0 order by conf_order';
        $data = Yii::$app->db->createCommand($sql, [':proj_id'=>$proj_id])->queryAll();
        return $this->render('_config', [
            'data' => $data,
            'proj_id'=>$proj_id,
        ]);
    }
    public function actionGetWidget() {
        if (Yii::$app->getRequest()->isAjax) {
            $widgetnum = Yii::$app->request->post('widgetnum',0);
            $list = $this->setDropdownArray($this->sitecodeQueryList());
            $view = Yii::$app->getView();
            return $view->renderAjax('/graphconfig/_config_widget', ['id'=>Yii::$app->request->post('id',''), 'widgetnum' => $widgetnum, 'conftype'=>1, 'list'=> $list]);

        }
    }
    public function actionRemoveConfig(){
        $id = Yii::$app->request->post('id','');
        $success ='';
        try{
            $sql = 'update advance_report_config set status = 1 where id = :id';
            $result = Yii::$app->db->createCommand($sql, [':id'=>$id])->execute();
            $success = Yii::t('app', 'Delete config success.');
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            $success = Yii::t('app', 'Delete config error.');
        }
        return $success;
    }
    private function checkSqlCommand($sql){
        // set to lower case
        $sql = strtolower($sql);
        $array = ['alter ','create ','delete ', 'drop ', 'insert ','truncate ', 'update '];
        $hasWord = 0;
        foreach($array as $item){
            if (strpos($sql, $item) !== false){
                $hasWord = 1;
            }
        }
        return $hasWord;
    }
    public function actionGetReportOption() {
        if (Yii::$app->getRequest()->isAjax) {

            $type = Yii::$app->request->post('type','');
            $sql = Yii::$app->request->post('sql','') ;
            $val = Yii::$app->request->post('val','');
            $val2 = Yii::$app->request->post('val2','');
            $error = 0 ;
            try{
                // test query
                // string replace
                $usersitecode = $userProfile = Yii::$app->user->identity->profile->sitecode;//Yii::$app->user->identity->userProfile->sitecode;
                $sitecode ='00';
                $startdate = '2017-01-01';
                $stopdate = date("Y-m-d");
                $array = ['_USERSITECODE_'=>$usersitecode,
                    '_SITECODE_'=>$sitecode,
                    '_STARTDATE_'=>$startdate,
                    '_STOPDATE_'=>$stopdate,
                    'where ' => 'where 1 or ',
                    'WHERE '=> 'WHERE 1 or '
                ];
                $sql = $this->setStrReplace($array, $sql);

                $checkword = $this->checkSqlCommand($sql);
                if($checkword == 1){
                    return Yii::t('graphconfig', 'Can use only select command!');
                }

                $data = Yii::$app->db->createCommand($sql)->queryAll();

                $list = [];
                foreach($data[0] as $key => $value){
                    $list[$key] = $key;
                }
            } catch (\yii\db\Exception $e){
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                $list = null;
                $error = 1;

            }
            $parentid = Yii::$app->request->post('parentid','');
            $html = $this->renderAjax('_report_option', ['type'=>$type, 'parentid'=>$parentid, 'list'=>$list, 'error'=>$error, 'val'=>$val, 'val2'=>$val2]);
            return $html;
        }
    }

    public function actionGetReportData() {
        //if (Yii::$app->getRequest()->isAjax) {

        $parentid = Yii::$app->request->get('parentid','');
        $report_type=Yii::$app->request->get('report_type','') ;
        $selectbox= Yii::$app->request->get('selectbox','') ;
        $onwith= Yii::$app->request->get('onwith','') ;
        $valueRow= Yii::$app->request->get('valueRow','') ;
        $valueCol= Yii::$app->request->get('valueCol','') ;
        $modal = Yii::$app->request->get('modal',0);
        $title = Yii::$app->request->get('title','');
        $newpage = Yii::$app->request->get('newpage',0);
        /*
        $selectsitedef=$_GET['sitechoose'];
        $xsitechoose= (explode(" ",$selectsitedef));
        $sitechoose=$xsitechoose[0];
        */

        $end_date = Yii::$app->request->get('end_date','') ;
        $start_date = Yii::$app->request->get('start_date','') ;
        $site_code = Yii::$app->request->get('site_code','');

        $site_codex= (explode("|",$site_code));
        $site_codes=$site_codex[0];
        $error = 0 ;
        $dataname =[];
        //try{
        $usersitecode = $userProfile = Yii::$app->user->identity->profile->sitecode;
        $sql = "SELECT sql_command FROM advance_report_config Where id = $parentid";
        $sqldata = Yii::$app->db->createCommand($sql)->queryScalar();

        $con_sql = "SELECT report_type, config_json FROM advance_report_config Where id = $parentid";
        $con_data = Yii::$app->db->createCommand($con_sql)->queryOne();
        $config = json_decode($con_data['config_json'], true);
        $variable = $config['reporttypevariable'];
        $val = $config['reporttypeval'];
        $report_type = $con_data['report_type'];
        // value to replace
        $array = ['_USERSITECODE_'=>$usersitecode,
            '_SITECODE_'=>$site_codes,
            '_STARTDATE_'=>$start_date,
            '_STOPDATE_'=>$end_date,
        ];

        $sqldata = $this->setStrReplace($array, $sqldata);

        $dataQuery = Yii::$app->db->createCommand($sqldata)->queryAll();
        $selectbox = $this->setStrReplace($array, $selectbox);
        $num2=0;
        $datattype = [];
        $datachart =[];
        $datain =[];
        $dataline =[];

        if($report_type == 2 || $report_type == 3) {
            if($dataQuery){

                $keyarray = [];
                foreach ($dataQuery as $v){
                    $datattype[] = (string)$v[$variable] == '' || (string)$v[$variable] == null ? Yii::t('graphconfig', 'Null') : (string)$v[$variable];
                    foreach($v as $key=>$val){
                        if($key != $variable && !in_array($key, $keyarray)){
                            $keyarray[] = $key;
                        }
                    }
                }
                foreach($keyarray as $keyname){
                    if($report_type == 3){
                        $buffarray = ['data'=>[], 'name'=>$keyname, 'type' => 'column'];
                    }else{
                        $buffarray = ['data'=>[], 'name'=>$keyname];
                    }

                    foreach($dataQuery as $v){
                        $buffarray['data'][] = (integer)$v[$keyname];
                    }
                    $dataline[] = $buffarray;
                }
            }
        }else{
            if($dataQuery){
                foreach($dataQuery[0] as $key2=>$val){
                    $datattype[]=(string)$key2;//ประเภทข้อมูล เช่น เพศ อายุ
                }
            }
            foreach ($dataQuery as $key => $value ) {
                $num=0;
                $first=true;
                foreach ($value as $key1=>$item) {
                    if($variable == $key1) { // variable
                        $datain['name'] = $item == '' || $item == null ?   Yii::t('graphconfig', 'Null') : $item;
                    }else{ /// number
                        $datain['y'] = (float)($item);
                    }
                    /*
                    if (is_numeric($item)) {
                        $datain[$num] = $first ? ($item) : intval($item);
                        $dataline[] = intval($item);//ข้อมูล 222,34
                    }else{
                        $datain[$num] = $item == '' || $item == null ?   Yii::t('graphconfig', 'Null') : $item;
                        $datattype[0]=(string)$key1;//ประเภทข้อมูล เช่น เพศ อายุ

                    }
                    */
                    $first = false;
                    $num++;
                } $datachart[$num2]=$datain;
                $num2++;

            }
        }
        //} catch (\yii\db\Exception $e){
        //    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        //     $error = 1;
        // }
        $html = $this->renderAjax('/graph/_advance_chart',
            ['error'=>$error,
                'report_type'=>$report_type,
                'parentid'=>$parentid,
                'onwith'=>$onwith,
                'datachart'=>$datachart,
                'dataline'=>$dataline,
                'datattype'=>$datattype,
                'selectbox'=>$selectbox,
                'dataQuery'=>$dataQuery,
                'valueRow'=>$valueRow,
                'valueCol'=>$valueCol,
                'modal'=>$modal,
                'title'=>$title,
                'newpage'=>$newpage,
            ]);
        return $html;
        //}
    }
    public function actionSitecodeList(){
        $data = $this->sitecodeQueryList();
        unset($out);
        foreach($data as $value){
            $out["results"][] = ['id'=>$value['sitecode'].'|'.$value["sitename"],'text'=> $value["sitecode"] ."|" .$value["sitename"]];
        }
        if(!$data){
            $out = ['results' => []];
        }
        return json_encode($out);
    }
    private function sitecodeQueryList(){
        // select sitecode ต้องแก้

        $query = 'select site_name as sitecode, site_detail as sitename from zdata_sitecode where rstat not in (0,3)';
        $data = Yii::$app->db->createCommand($query)->queryAll();

        return $data;
    }
    private function setDropdownArray($data){
        $list = [];
        foreach ($data as $value){
            $list[$value['sitecode'].'|'.$value['sitename']]=$value['sitecode'].'|'.$value['sitename'];
        }
        return $list;
    }
    private function setStrReplace($array, $query){
        foreach($array as $key => $val){
            $query = str_replace($key, $val, $query);
        }
        return $query;
    }
    // new update
    public function actionGetAdvanceReportData(){
        $id = Yii::$app->request->get('id');
        $module = Yii::$app->request->get('module');
        $data = Yii::$app->db->createCommand('select * from advance_report_config where id = :id', [':id'=>$id])->queryOne();
        return $this->renderAjax('_config_widget', ['data'=> $data, 'module'=>$module]);
        //return json_encode($data);
    }
    public function actionAdvanceReportOrder(){
        $proj_id = Yii::$app->request->get('module');
        $data = Yii::$app->db->createCommand('select id, conf_order from advance_report_config where proj_id = :proj_id order by conf_order asc', [':proj_id'=>$proj_id])->queryAll();
        // check order format => 10 20 30 40
        $status = 0;
        foreach($data as $d){
            if($d['conf_order']%10 !=0){
                $status = 1;
                break;
            }
        }
        if($status == 1){
            // re order
            $order = $this->reOrder($proj_id);
            return $order;
        }
    }
    private function reOrder($module){
        $data = Yii::$app->db->createCommand('select id, conf_order from advance_report_config where proj_id = :proj_id and status = 0 order by conf_order asc', [':proj_id'=>$module])->queryAll();
        // start @ 10
        $i = 1;
        foreach($data as $d){
            try{
                $sql = 'update advance_report_config set conf_order = :order where id = :id';
                $result = Yii::$app->db->createCommand($sql, [':order'=> $i*10 ,':id'=>$d['id']])->execute();
            } catch (\yii\db\Exception $e) {
                return 'fault';
            }
            $i++;
        }
        return 'success';
    }
    public function actionReportList(){
        $proj_id = Yii::$app->request->get('module');
        $parentid = Yii::$app->request->get('parentid');
        // search data by id
        $dataid =  Yii::$app->db->createCommand("select conf_order from advance_report_config where id = :id ", [':id'=>$parentid])->queryScalar();
        unset($out);
        // set 3 choice
        if($dataid){
            $out["data"][] = ['id'=>(int)$dataid,'text'=> Yii::t('graphconfig', 'Old value')];
        }
        $out["data"][] = ['id'=>0,'text'=> Yii::t('graphconfig', 'Top')];
        $out["data"][] = ['id'=>99999,'text'=> Yii::t('graphconfig', 'Last')];

        $sql = "SELECT conf_order, config_json from advance_report_config where proj_id = :proj_id and status = 0 and id <> :id order by  conf_order asc";
        $data = Yii::$app->db->createCommand($sql, [':proj_id'=>$proj_id, ':id' =>$parentid])->queryAll();


        foreach($data as $value){
            $config_json = json_decode($value['config_json']);
            $out["data"][] = ['id'=>(int)$value['conf_order']+5,'text'=> Yii::t('graphconfig', 'Behind') .$config_json->graphname];
        }
        return json_encode($out);
    }
    public function actionAdvanceReportSave(){
        $userId = Yii::$app->user->id;
        if (Yii::$app->request->post()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            // check sql command
            $conf_order = Yii::$app->request->post('forms-order');
            $report_type = Yii::$app->request->post('forms-reporttype');
            $sql_command = Yii::$app->request->post('forms-sqlsetting');
            $checkword = 0;
            $checkword = $this->checkSqlCommand($sql_command);
            if($checkword == 1){
                return json_encode(['status'=>'error']);
            }

            // save data
            $id = Yii::$app->request->post('forms-id');
            $proj_id = Yii::$app->request->post('forms-module');

            $config_array = [];

            $config_array['selectdate']= Yii::$app->request->post('forms-selectdate') != null ? Yii::$app->request->post('forms-selectdate') :'';
            $config_array['selectsite']= Yii::$app->request->post('forms-selectsite') != null ? Yii::$app->request->post('forms-selectsite') :'';
            $config_array['width']= Yii::$app->request->post('forms-width')!= null ? Yii::$app->request->post('forms-width') :'';
            $config_array['reporttypeval']= Yii::$app->request->post('forms-reporttypeval')!= null ? Yii::$app->request->post('forms-reporttypeval') :'';
            $config_array['reporttypevariable']= Yii::$app->request->post('forms-reporttypevariable')!= null ? Yii::$app->request->post('forms-reporttypevariable') :'';
            $config_array['graphname']= Yii::$app->request->post('forms-graphname')!= null ? Yii::$app->request->post('forms-graphname') :'';
            $config_array['textbox']= Yii::$app->request->post('forms-textbox')!= null ? Yii::$app->request->post('forms-textbox') :'';
            $config_array['selectsitedef']= Yii::$app->request->post('forms-selectsitedef')!= null ? Yii::$app->request->post('forms-selectsitedef') :'';
            $config_array['showselectsite']= Yii::$app->request->post('forms-showselectsite')!= null ? Yii::$app->request->post('forms-showselectsite') :'';
            $config_array['day']=Yii::$app->request->post('forms-day')!= null ? Yii::$app->request->post('forms-day') :'';
            $config_array['month']=Yii::$app->request->post('forms-month')!= null ? Yii::$app->request->post('forms-month') :'';
            $config_array['year']=Yii::$app->request->post('forms-year')!= null ? Yii::$app->request->post('forms-year') :'';

            $config_json = json_encode($config_array);

            $q = 'select id from advance_report_config where id = :id';
            $data = Yii::$app->db->createCommand($q, [':id'=>$id])->queryOne();

            if($data){
                // update
                try{
                    $sql = "update advance_report_config set conf_order = :conf_order, report_type = :report_type, sql_command = :sql_command, config_json = :config_json,
                                user_update = :user_update, update_date = NOW() where id = :id " ;
                    $result = Yii::$app->db->createCommand($sql, [':conf_order'=>$conf_order, ':report_type'=>$report_type, ':sql_command'=>$sql_command,
                        ':config_json'=>$config_json, ':user_update'=>$userId, ':id'=>$id])->execute();
                    $order = $this->reOrder($proj_id);
                    return $result ? json_encode(['status'=>'success', 'id'=>$id,'message'=>Yii::t('graphconfig', 'Update data success!')])
                        : json_encode(['status'=>'error', 'id'=>$id, 'message'=>Yii::t('graphconfig', 'Update data error!')]) ;
                } catch (\yii\db\Exception $e) {
                    return json_encode(['status'=>'error', 'id'=>$id, 'message'=>Yii::t('graphconfig', 'Update data error!')]) ;
                }
            }else{
                // insert
                // insert into advance_report_config(id, module_id, conf_order, report_type, sql_command,config_json, user_create, create_date, user_update, update_date) VALUES ()
                try{
                    $sql = "insert into advance_report_config VALUES (:id, :proj_id, :conf_order, :report_type, :sql_command,:config_json, "
                        . ":user_create, NOW(), :user_update, NOW(),0)" ;
                    $result = Yii::$app->db->createCommand($sql, [':id'=>$id,':proj_id'=>$proj_id ,':conf_order'=>$conf_order, ':report_type'=>$report_type, ':sql_command'=>$sql_command,
                        ':config_json'=>$config_json, ':user_create'=>$userId, ':user_update'=>$userId])->execute();
                    $order = $this->reOrder($proj_id);
                    return $result ? json_encode(['status'=>'success','id'=>$id, 'message'=>Yii::t('graphconfig', 'Update data error!')])
                        : json_encode(['status'=>'error', 'id'=>$id, 'message'=>Yii::t('graphconfig', 'Insert data error!')]) ;
                } catch (\yii\db\Exception $e) {
                    return json_encode(['status'=>'error', 'id'=>$id, 'message'=>Yii::t('graphconfig', 'Insert data error!')]) ;
                }
            }
            // end

        }
    }
    public function actionSinglegraphReportOption() {
        if (Yii::$app->getRequest()->isAjax) {

            $type = Yii::$app->request->post('type','');
            $sql = Yii::$app->request->post('sql','') ;
            $val = Yii::$app->request->post('val','');
            $val2 = Yii::$app->request->post('val2','');
            $error = 0 ;
            try{
                // test query
                // string replace
                $usersitecode = $userProfile = Yii::$app->user->identity->profile->sitecode;//Yii::$app->user->identity->userProfile->sitecode;
                $sitecode ='00';
                $startdate = '2017-01-01';
                $stopdate = date("Y-m-d");
                $array = ['_USERSITECODE_'=>$usersitecode,
                    '_SITECODE_'=>$sitecode,
                    '_STARTDATE_'=>$startdate,
                    '_STOPDATE_'=>$stopdate,
                    'where ' => 'where 1 or ',
                    'WHERE '=> 'WHERE 1 or '
                ];
                $sql = $this->setStrReplace($array, $sql);

                $checkword = $this->checkSqlCommand($sql);
                if($checkword == 1){
                    return Yii::t('graphconfig', 'Can use only select command!');
                }

                $data = Yii::$app->db->createCommand($sql)->queryAll();

                $list = [];
                foreach($data[0] as $key => $value){
                    $list[$key] = $key;
                }
                $errortext='';
            } catch (\yii\db\Exception $e){
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                $list = null;
                $error = 1;
                $errortext = $e;
            }
            $html = $this->renderAjax('_single_report_option',
                ['type'=>$type, 'list'=>$list, 'error'=>$error, 'val'=>$val, 'val2'=>$val2,'errortext'=>$errortext]);
            return $html;
        }
    }

    public function actionGetSinglegraphReportData() {
        if (Yii::$app->getRequest()->isAjax) {

            $parentid = Yii::$app->request->get('parentid','');
            $report_type=Yii::$app->request->get('report_type','') ;
            $selectbox= Yii::$app->request->get('selectbox','') ;
            $onwith= Yii::$app->request->get('onwith','') ;
            $valueRow= Yii::$app->request->get('valueRow','') ;
            $valueCol= Yii::$app->request->get('valueCol','') ;
            $modal = Yii::$app->request->get('modal',0);
            $title = Yii::$app->request->get('title','');
            $newpage = Yii::$app->request->get('newpage',0);

            $end_date = Yii::$app->request->get('end_date','') ;
            $start_date = Yii::$app->request->get('start_date','') ;
            $site_code = Yii::$app->request->get('site_code','');
            $sqldata = base64_decode(Yii::$app->request->get('sql','')) ;
            $variable = $valueCol;

            $site_codex= (explode("|",$site_code));
            $site_codes=$site_codex[0];
            $error = 0 ;
            $dataname =[];
            //try{
            $usersitecode = $userProfile = Yii::$app->user->identity->profile->sitecode;

            // value to replace
            $array = ['_USERSITECODE_'=>$usersitecode,
                '_SITECODE_'=>$site_codes,
                '_STARTDATE_'=>$start_date,
                '_STOPDATE_'=>$end_date,
            ];

            $sqldata = $this->setStrReplace($array, $sqldata);
            // check sql
            $checkword = $this->checkSqlCommand($sqldata);
            if($checkword == 1){
                return $this->renderAjax('/graph/sqlcheck',
                    ['error'=>Yii::t('graphconfig', 'Can use only select command!')]);
            }

            $dataQuery = Yii::$app->db->createCommand($sqldata)->queryAll();
            $selectbox = $this->setStrReplace($array, $selectbox);
            $num2=0;
            $datattype = [];
            $datachart =[];
            $datain =[];
            $dataline =[];
            $datatnum = [];

            if($report_type == 2 || $report_type == 3) {
                if($dataQuery){

                    $keyarray = [];
                    foreach ($dataQuery as $v){
                        $datattype[] = (string)$v[$variable] == '' || (string)$v[$variable] == null ? Yii::t('graphconfig', 'Null') : (string)$v[$variable];
                        foreach($v as $key=>$val){
                            if($key != $variable && !in_array($key, $keyarray)){
                                $keyarray[] = $key;
                            }
                        }
                    }
                    foreach($keyarray as $keyname){
                        if($report_type == 3){
                            $buffarray = ['data'=>[], 'name'=>$keyname, 'type' => 'column'];
                        }else{
                            $buffarray = ['data'=>[], 'name'=>$keyname];
                        }

                        foreach($dataQuery as $v){
                            $buffarray['data'][] = (integer)$v[$keyname];
                        }
                        $dataline[] = $buffarray;
                    }
                }
            }else{
                if($dataQuery){
                    foreach($dataQuery[0] as $key2=>$val){
                        $datattype[]=(string)$key2;//ประเภทข้อมูล เช่น เพศ อายุ
                    }
                }
                foreach ($dataQuery as $key => $value ) {
                    $num=0;
                    $first=true;
                    foreach ($value as $key1=>$item) {

                        if($variable == $key1) { // variable
                            $datain['name'] = $item == '' || $item == null ?   Yii::t('graphconfig', 'Null') : $item;
                        }else{ /// number
                            $datain['y'] = (float)($item);
                        }
                        $first = false;
                        $num++;
                    } $datachart[$num2]=$datain;
                    $num2++;

                }
            }

            //} catch (\yii\db\Exception $e){
            //    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            //     $error = 1;
            // }
            $html = $this->renderAjax('/graph/_advance_chart',
                ['error'=>$error,
                    'report_type'=>$report_type,
                    'parentid'=>$parentid,
                    'onwith'=>$onwith,
                    'datachart'=>$datachart,
                    'dataline'=>$dataline,
                    'dataname'=>$dataname,
                    'datatnum'=>$datatnum,
                    'datattype'=>$datattype,
                    'selectbox'=>$selectbox,
                    'dataQuery'=>$dataQuery,
                    'valueRow'=>$valueRow,
                    'valueCol'=>$valueCol,
                    'modal'=>$modal,
                    'title'=>$title,
                    'newpage'=>$newpage,
                ]);
            return $html;
        }
    }

    public function actionDisplayGraph(){
        $title = Yii::$app->request->post('title');
        $visit = Yii::$app->request->post('visit');
        $ezfid = Yii::$app->request->post('ezfid');
        $fieldstxt = Yii::$app->request->post('fields');
        $fieldexplode = explode(',', $fieldstxt);
        $fields = $fieldexplode;
        $fields[]='update_date';
        // get table name
        $tablename = (new \yii\db\Query())
            ->select('ezf_table')
            ->from('ezform')
            ->where(['ezf_id' => $ezfid])
            ->scalar();
        // select ptid from visit id from visit table
        $ptid = (new \yii\db\Query())
            ->select('ptid')
            ->from('zdata_visit')
            ->where(['id' => $visit])
            ->scalar();
        // select dataset with create data
        /*
        $dataset = (new \yii\db\Query())
            ->select($fields)
            ->from($tablename)
            ->where(['ptid' => $ptid])
            ->andWhere('rstat not in (0,3)')
            ->orderBy('update_date asc')
            ->all();
        */
        // select by subquery
        $subquery = (new \yii\db\Query())
            ->select($fields)
            ->from($tablename)
            ->where(['ptid' => $ptid])
            ->andWhere('rstat not in (0,3)')
            ->orderBy('update_date desc')
            ->limit('50');
        // reorder dataset
        $dataset = (new \yii\db\Query())
            ->select('*')
            ->from(['a' =>$subquery])
            ->orderBy('update_date')
            ->all();
        // get ezf_field label
        $fieldlabel = (new \yii\db\Query())
            ->select(['ezf_field_name', 'ezf_field_label'])
            ->from('ezform_fields')
            ->where(['ezf_id'=>$ezfid])
            ->andwhere(['ezf_field_name'=>$fieldexplode])
            ->all();

        $datacategory= [];
        $databuffer = [];
        foreach ($dataset as $data){
            $datacategory[] = $data['update_date'];
            foreach ($data as $keys => $item){
                if($keys != 'update_date'){
                    $databuffer[$keys][] = (float)$item;
                }
            }
        }
        // set dataseries into highchart format
        $dataseries = [];
        foreach($databuffer as $keys => $data){
            $dataseries[] = ['name'=>$this->keynameMatching($keys, $fieldlabel),'data'=>$data];
        }

        return $this->renderAjax('_box_content_graph',
            ['title'=>$title, 'category'=>$datacategory, 'series'=>$dataseries, 'visit'=>$visit]);
    }
    private function keynameMatching($name, $array){
        $keyname = '';
        foreach($array as $a ){
            if($a['ezf_field_name'] == $name){
                $keyname = $a['ezf_field_label'];
            }
        }
        return $keyname;
    }
    public function actionBuilderLoad(){
        if (Yii::$app->getRequest()->isAjax) {
            $id = Yii::$app->request->post('id',0);
            $result = (new Query())->select('sql_raw')->from('zdata_ezsql')
                ->where(['id'=>$id])->createCommand()->queryScalar();
            return json_encode([
                'sql' => $result
            ]);
        }
    }
}
