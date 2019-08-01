<?php
 
namespace backend\modules\manageproject\classes;
use yii\db\Exception;
use Yii;
class CNSettingProjectFunc {
    public static function MyProjectByid($dataid){
        $user_id = \cpn\chanpan\classes\CNUser::getUserId();
        return CNSettingProjectQuery::classNames()
                ->setDb(\Yii::$app->db_main)
                ->setUserId($user_id)
                ->setDataId($dataid)
                ->getMyProjectById();        
    }
    public static function MyProjectByid2($dataid){
        $user_id = \cpn\chanpan\classes\CNUser::getUserId();
        return CNSettingProjectQuery::classNames()
                ->setDb(\Yii::$app->db_main)
                ->setUserId($user_id)
                ->setDataId($dataid)
                ->getMyProjectById2();        
    }
    public static function MyProjectByidNoUser($dataid){
        $user_id = \cpn\chanpan\classes\CNUser::getUserId();
        return CNSettingProjectQuery::classNames()
                ->setDb(\Yii::$app->db_main)
//                ->setUserId($user_id)
                ->setDataId($dataid)
                ->getMyProjectByIdNoUsers();        
    }
    
    public static function MyProject(){
        $user_id = \cpn\chanpan\classes\CNUser::getUserId();
        return CNSettingProjectQuery::classNames()
                ->setDb(\Yii::$app->db_main)
                ->setUserId($user_id)
                ->getMyProject();         
    }
    public static function MyProjectDeleteByid($dataid){
        $user_id = \cpn\chanpan\classes\CNUser::getUserId();
        return CNSettingProjectQuery::classNames()
                ->setDb(\Yii::$app->db_main)
                ->setUserId($user_id)
                ->setDataId($dataid)
                ->getMyProjectDeleteById();        
    }
    public static function DeleteMyProject($id){
        $user_id = \cpn\chanpan\classes\CNUser::getUserId();
        $status=CNSettingProjectQuery::classNames()
                ->setDb(\Yii::$app->db_main)
                ->setUserId($user_id)
                ->setDataId($id)
                ->deleteProject();
         
        if($status){
            return CNMessage::getSuccess('Delete Project success.');
        }else{
            return CNMessage::getError('Server Error');
        }       
    }
    public static function RestoreMyProject($id){
        $user_id = \cpn\chanpan\classes\CNUser::getUserId();
        $status=CNSettingProjectQuery::classNames()
                ->setDb(\Yii::$app->db_main)
                ->setUserId($user_id)
                ->setDataId($id)
                ->restoreProject();         
        if($status){
            return CNMessage::getSuccess('Delete Project success.');
        }else{
            return CNMessage::getError('Server Error');
        }       
    }
    public static function UploadImage($files){
        
        $fType =['png','jpeg','gif','jpg'];
        $target_dir = \Yii::getAlias('@storage').'/web/ezform/fileinput/';        
        $fName = basename($files['name']['projecticon']);
        $tName = $files['tmp_name']['projecticon']; 
        $imageFileType = strtolower(pathinfo($fName,PATHINFO_EXTENSION));
         
        if(!in_array(strtolower($imageFileType) , $fType)){
           return CNMessage::getError('Check type file'); 
        }
        $reName = date('YmdHis').'_'.md5(time().rand()).".".$imageFileType;       
        if(@move_uploaded_file($tName, $target_dir.$reName)){
            return ['status'=>'success','fileName'=>$reName];
        }else{
            return ['status'=>'error','message'=>'Upload image error']; 
        }
    }
    
    public static function UpdateProject($data){ 
        try{
           $dataDynamic = ['proj_name'=>$data['projectname']]; 
           $execDynamic= \Yii::$app->db_main->createCommand()
                    ->update('dynamic_db', $dataDynamic, ['data_id'=>$data['dataid']])
                    ->execute();
            if($execDynamic){
                $dataCreateProject=[
                    'tctrno'=>$data['tctrno'],
                    'projectname'=>$data['projectname'],                
                    'pi_name'=>$data['pi_name'],
                    'sharing'=>$data['sharing'],
                    'useTemplate'=>$data['useTemplate'],
                    'briefsummary'=>$data['briefsummary'],               
                    'detail'=>$data['detail'], 
                    'proj_home'=>$data['proj_home'], 
                ];
                try{
                   
                    $dataSaveCreateProject = \Yii::$app->db_main->createCommand()
                        ->update('zdata_create_project', $dataCreateProject, ['id'=>$dataid['dataid']])
                        ->execute();
                    if($dataSaveCreateProject){
                        return CNMessage::getSuccess('success');
                    }else{
                        return CNMessage::getError('error');
                    }
                } catch (Exception $ex) {
                   return CNMessage::getError(json_encode($ex));
                }
            }
             
        } catch (Exception $ex) {
            
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }
    }
    
     
    public static function getThemes(){
        try{
            $data = (new \yii\db\Query())
                ->select('*')
                ->from('zdata_themes')
                ->where('rstat not in(0,3)')
                ->orderBy(['id'=>SORT_DESC])
                ->one();
            if(!$data){
                return FALSE;
            }
            return $data;
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return FALSE;
        }          
    }
    
    public static function getProjectAll($search = ''){
        return CNSettingProjectQuery::classNames()
                ->setDb(\Yii::$app->db)->getProjectAll($search);
                 
    }
    
    /* get dynamic_db */
    public static function getDynamicDbByDataId($dataid){
        return CNSettingProjectQuery::classNames()
                ->setDb(\Yii::$app->db)->getDynamicDbByDataId($dataid);
                 
    }
    
    
    /* save zdata_create_project */
    public static function create($columns){
        return CNSettingProjectQuery::classNames()
                ->setDb(\Yii::$app->db)->create($columns);       
    }
    
    
    

}
