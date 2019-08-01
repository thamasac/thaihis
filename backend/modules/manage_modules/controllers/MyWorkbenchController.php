<?php
 

namespace backend\modules\manage_modules\controllers;
 
class MyWorkbenchController extends \yii\web\Controller{
    public function actionIndex()
    {
        $module_id = isset($_GET['module_id']) ? $_GET['module_id'] : '';
        $user_id = \cpn\chanpan\classes\CNUser::getUserId();
//        echo $user_id;
        $dataPermission=(new \yii\db\Query())
              ->select('*')
              ->from('zdata_permission_module')
              ->where('(user_id LIKE :user_id1 OR member=1)',
                      [
                          ':user_id1'=>"%$user_id%",
//                          ':user_id2'=>"%$user_id%"
                      ])
              
              ->andWhere('rstat not in(0,3)')
              ->all();
       $out=[];
       $per1 = [];
       $per2 =[];
       
       if(!empty($dataPermission)){
           foreach($dataPermission as $key=>$value){            
                $out[$key] = [
                    'user_id'=>\appxq\sdii\utils\SDUtility::string2Array($value['user_id']),
                    'module_id'=>$value['module_id'],
                    'permission_type'=>$value['permission_type'], //read , read/write/ manage
                    'role_name'=>$value['role_name'],
                    'member'=>[
                        'type'=>$value['member'],
                        'user_read'=>\appxq\sdii\utils\SDUtility::string2Array($value['user_read'])
                     ]
                 ];
            }
            //\appxq\sdii\utils\VarDumper::dump($out);
            foreach($out as $key=>$value){
              if($value['member']['type'] == 1){ //every one
                 if(\cpn\chanpan\classes\CNUser::canAdmin()){
                     $per1[] = $value;
                 }else{
                     if(!in_array($user_id, $value['member']['user_read'])){
                         $per1[] = $value;
                     }
                 }

              }else{//restricted             
                 if(in_array($user_id, $value['user_id'])){
                         $per2[] = $value;
                 }
              }  
            }

            $modules = \yii\helpers\ArrayHelper::merge($per1, $per2);
            
       }else{
           $modules = '';           
       }
       
       
       return $this->renderAjax("index",[
                'modules'=>$modules,
                'module_id'=>$module_id
       ]);
                
    }//func
}
