<?php

namespace backend\modules\manageproject\controllers;

use dms\aomruk\classese\Notify;
use Yii;
use yii\db\Exception;
use yii\httpclient\Client;
use yii\web\Response;

class CloneProjectController extends \yii\web\Controller { 


    public function actionGetProjectAll() {
        \backend\modules\manageproject\classes\CNFunc::addLog('View All My Projects');
        $status = isset($_GET['status']) ? $_GET['status'] : '1';
        $where = ['user_create'=> \cpn\chanpan\classes\CNUser::getUserId()];
        
        $data = \backend\modules\manageproject\classes\CNEzform::getDynamicTableAll("zdata_create_project",$where);
        $output = \backend\modules\manageproject\classes\CNEzform::getUserProject();
         
        if(empty($data) && empty($output)){
           return \cpn\chanpan\classes\CNResponse::notFoundAlert();
        }
            
        foreach($data as $k1=>$v1){
            foreach($output as $k2=>$v2){
                if($v1['id'] == $v2['id']){
                    unset($output[$k2]);
                }
            }
        }
        
        
        
        if($status == '1'){
           $data = \yii\helpers\ArrayHelper::merge($data, $output);
        }else{
            if(empty($data)){
                return \cpn\chanpan\classes\CNResponse::notFoundAlert();
             }
        }
        $user_id = \cpn\chanpan\classes\CNUser::getUserId();
        foreach($data as $key=>$value){
            if ($data[$key]['user_create']==$user_id){
                $data[$key]['mode']='all';
            } else {
                $data[$key]['mode']='assign';
            }
                
        }
        
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels'=>$data,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);  
        //VarDumper::dump($dataProvider);
        return $this->renderAjax("get-project-all", [
           'data' => $data,
           'dataProvider'=>$dataProvider,
           'status'=>'all'
        ]);
    }

    public function actionGetCollaborationProjectView() {
        // 0 mean all , 1 mean only approve  , 2 only automatic
        $type = isset($_GET['type']) ? $_GET['type'] : 0;
        $where = []; 
        switch ($type){
            case 0:
                $where = [ "collaboration"=>[1,2]];
                break;
            case 1:
                $where = [ "collaboration"=>1];
                break;
            case 2:
                $where = [ "collaboration"=>2];
                break;
        }
        $data = \backend\modules\manageproject\classes\CNEzform::getDynamicTableAll("zdata_create_project",$where);
        foreach($data as $key=>$value){
           $data[$key]['mode']='seek';
        }  
        if(empty($data)){
            return \cpn\chanpan\classes\CNResponse::notFoundAlert();
        }
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels'=>$data,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]); 
        return $this->renderAjax("get-project-all", [
            'data' => $data,
            'dataProvider'=>$dataProvider,
            'status'=>'seek'
        ]);
    }
      
    public function actionGetUserProject() {
         $output = \backend\modules\manageproject\classes\CNEzform::getUserProject();
        
         if(empty($output)){
            return \cpn\chanpan\classes\CNResponse::notFoundAlert();
        }
         
        return $this->renderAjax("get-user-project", [
           'data' => $output
        ]);
        
        //db_main
    }
    
    public function actionGetTrashProject() {
        \backend\modules\manageproject\classes\CNFunc::addLog('View Project Trash');
         $output = \backend\modules\manageproject\classes\CNEzform::getTrashProject();
        
         if(empty($output)){
            return \cpn\chanpan\classes\CNResponse::notFoundAlert();
         }
         foreach($output as $key=>$value){
           $output[$key]['mode']='trash';
         }
         $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels'=>$output,
            'pagination' => [
                'pageSize' => 100,
                
            ],
        ]);  
         return $this->renderAjax("get-trash-project", [
           'data' => $output,
           'dataProvider'=>$dataProvider,
           'status'=>'trash'
        ]);
        
        //db_main
    }
    public function actionCheckUrl(){
        try{
            $status = 0;
//            $url = isset($_GET['url']) ? $_GET['url'] : '';
//            $sql = 'SELECT * FROM dynamic_db WHERE url=:url AND rstat <> 3';
//            $params = [':url'=>$url];
            $data = \cpn\chanpan\classes\CNServerConfig::getServerModelDynamicDb();//\Yii::$app->db_main->createCommand($sql, $params)->queryOne();
            
            if(!$data){
                $status = 1;
            }
            return $status;
        } catch (\yii\db\Exception $error) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
            return 0;
        }
    }
    //Tab
    public function actionAllProject(){
        $isPortal = \cpn\chanpan\classes\CNServerConfig::isPortal();
        if($isPortal){
            return $this->renderAjax("all-project");
        }
        return $this->renderAjax("all-project");
        /** @noinspection PhpUnhandledExceptionInspection */
        throw new \yii\web\NotFoundHttpException(\Yii::t('ezform', 'Page not found in this project.'));
    }
    public function actionCollaborationProject(){
        \backend\modules\manageproject\classes\CNFunc::addLog('View Project Collaboration');
        $isPortal = \cpn\chanpan\classes\CNServerConfig::isPortal();
        if($isPortal){
            return $this->renderAjax("collaboration-project");
        }
        /** @noinspection PhpUnhandledExceptionInspection */
        throw new \yii\web\NotFoundHttpException(\Yii::t('ezform', 'Page not found in this project.'));
    }  
            
    public function actionCoCreatorProject(){
        \backend\modules\manageproject\classes\CNFunc::addLog('View Project Co-Creator');
        $user_id = isset(\Yii::$app->user->id) ? \Yii::$app->user->id : '';
        $dataStr = (new \yii\db\Query())
            ->select("pro.*")
            ->from("zdata_create_project pro")
            ->innerJoin("zdata_1550237431020291300 co", 'pro.id = co.target')
            ->where("pro.rstat not in (0,3)")
            ->andWhere("co.rstat not in (0,3)")
            ->andWhere(["co_user"=> $user_id])
            ->orderBy(['forder'=>SORT_ASC]);
        if (empty($user_id)) {
            $dataStr->andWhere('0');
        }
        $data = $dataStr->all();
        if(empty($data)){
            return \cpn\chanpan\classes\CNResponse::notFoundCoCreator();
        }
        foreach($data as $key=>$value){
            $data[$key]['mode']='co';
        }
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels'=>$data,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        $isPortal = \cpn\chanpan\classes\CNServerConfig::isPortal();
        if($isPortal){
            return $this->renderAjax("co-creator-project", [
                'data' => $data,
                'status'=>'co',
                'dataProvider'=>$dataProvider,
            ]);
            
        }else{
            return '<div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> You have no project!</div>';
        }
        /** @noinspection PhpUnhandledExceptionInspection */
        throw new \yii\web\NotFoundHttpException(\Yii::t('ezform', 'Page not found in this project.'));
    }
    public function actionMyOwn(){
        \backend\modules\manageproject\classes\CNFunc::addLog('View Project Created by me');
        $isPortal = \cpn\chanpan\classes\CNServerConfig::isPortal();
        if($isPortal)
            return $this->renderAjax("my-own");
        /** @noinspection PhpUnhandledExceptionInspection */
        throw new \yii\web\NotFoundHttpException(\Yii::t('ezform', 'Page not found in this project.'));
    }
    public function actionMyAssign(){
        \backend\modules\manageproject\classes\CNFunc::addLog('View Project Assigned to me');
        $isPortal = \cpn\chanpan\classes\CNServerConfig::isPortal();
        if($isPortal){
            $output = \backend\modules\manageproject\classes\CNEzform::getUserProject();        
            if(empty($output)){
                return \cpn\chanpan\classes\CNResponse::notFoundAlert();
            }
             
            foreach($output as $key=>$value){
                $output[$key]['mode']='assign';
            }
            
            $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels'=>$output,
                'pagination' => [
                    'pageSize' => 100,                
                ],
            ]);
            return $this->renderAjax("my-assign",[
                'dataProvider'=>$dataProvider,
                'status'=>'assign',
                'data'=>$output
            ]);
        }
            
        /** @noinspection PhpUnhandledExceptionInspection */
        throw new \yii\web\NotFoundHttpException(\Yii::t('ezform', 'Page not found in this project.'));
    }

    public function actionJoinProjectView($project_id){
        return $this->renderAjax("join-request",['project_id' => $project_id]);
    }

    public function actionSendJoinProject(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $project_id = Yii::$app->request->post("project_id","");
        $compose = Yii::$app->request->post("compose","");
        try {
            $userData = Yii::$app->db->createCommand("SELECT * FROM user WHERE id = :user_id", [":user_id" => Yii::$app->user->id])->queryOne();
            $userProfileData = Yii::$app->db->createCommand("SELECT * FROM profile WHERE user_id = :user_id",[":user_id" => Yii::$app->user->id])->queryOne();
            $projectData = \backend\modules\manageproject\classes\CNEzform::getDynamicTableAll("zdata_create_project",["id"=>$project_id]);
            $projectData = $projectData[0];
            $userCreateData = Yii::$app->db->createCommand("SELECT * FROM user WHERE id = :user_id",[":user_id" => $projectData["user_create"]])->queryOne();

        } catch (Exception $e) {
            $message = $e->getMessage();
            $result = [
                'status' => 'error',
                'action' => 'create',
                'message' => \appxq\sdii\helpers\SDHtml::getMsgError() . Yii::t('chanpan', "$message."),
            ];
            return $result;
        }

        $projectUrl = $projectData["projurl"];
        $projDomain = $projectData["projdomain"];
        $url = "https://".$projectUrl.".".$projDomain."/api/ncrc-project/request-join-project";
        $package = [
            "userData" => json_encode($userData),
            "userProfileData" => json_encode($userProfileData),
            "projectData" => json_encode($projectData),
            "projectId" => $project_id,
            "compose" => $compose
        ];

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->addHeaders(["Authorization"=>"Bearer ".$userCreateData["auth_key"]])
            ->setUrl($url)//.$url
            ->setData($package)
            ->setOptions([
                CURLOPT_CONNECTTIMEOUT => 5, // connection timeout
                CURLOPT_TIMEOUT => 5, // data receiving timeout
            ])
            ->send();
        if ($response->isOk) {
             $res = $response->getData();
             if($res["success"]){
                 switch ($res["collaboration"]){
                     case 2:
                         $data = [
                             'url'=> \cpn\chanpan\classes\CNServerConfig::getDomainName(),
                             'user_id'=>$userData["id"],
                             'create_by'=> "1",
                             'create_at'=>Date('Y-m-d'),
                             'data_id'=> $project_id
                         ];
                         \Yii::$app->db->createCommand()->insert('user_project', $data)->execute();
                         // Send notify to user for request is sent
                         $projectWebUrl="https://".$projectUrl.".".$projDomain;
                         Notify::setNotify()
                             ->send_email(true)
                             ->notify("Join project request")
                             ->detail("<h3>Your request is approved in ".$projectData['projectname']."</h3><a href='$projectWebUrl'>$projectWebUrl</a>")
                             ->assign([$userData["id"]])
                             ->sendStatic();
                         $result = [
                             'status' => 'success',
                             'action' => 'create',
                             'debug' => $res,
                             'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', "Sent collaboration project Request.\nResult will send to you in email."),
                         ];
                         return $result;
                     case 1:
                         $result = [
                             'status' => 'success',
                             'action' => 'create',
                             'debug' => $res,
                             'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', "(Project cause) Request collaboration project is pending to approve."),
                         ];
                         return $result;
                     case 0:
                         $result = [
                             'status' => 'success',
                             'action' => 'create',
                             'debug' => $res,
                             'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', "This project not open for Collaboration."),
                         ];
                         return $result;
                 }
             }
            $result = [
                'status' => 'error',
                'action' => 'create',
                'debug' => $res,
                'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', "(Project cause) Request collaboration project process was failed."),
            ];
            return $result;
        }else{
            $result = [
                'status' => 'error',
                'action' => 'create',
                'message' => \appxq\sdii\helpers\SDHtml::getMsgError() . Yii::t('chanpan', "Request collaboration project process was failed."),
            ];
            return $result;
        }
    }

}
