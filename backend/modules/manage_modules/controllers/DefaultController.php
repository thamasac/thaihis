<?php

namespace backend\modules\manage_modules\controllers;

use yii\web\Controller;
use Yii;
use backend\modules\manage_modules\classes\ManageModuleFunc;
use yii\db\Exception;
use backend\modules\ezmodules\models\Ezmodule;
use yii\data\ActiveDataProvider;
/**
 * Default controller for the `manage_modules` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        
        $status = !empty($_GET['status']) ? $_GET['status'] : 'view';
        $module_id = !empty($_GET['module_id']) ? $_GET['module_id'] : '';
        \Yii::$app->session['module_id'] = $module_id;
        if(!empty(\Yii::$app->session['manage_module'])){
            $status = \Yii::$app->session['manage_module'];
        }
        return $this->renderAjax('index',[
            'status'=>$status,
            'module_id'=>$module_id
        ]);
    }
    public function actionUpdate(){
        $status = isset($_GET['status']) ? $_GET['status'] : 'view';
        \Yii::$app->session['manage_module'] = $status;
         if(!empty(\Yii::$app->session['manage_module'])){
             return \backend\modules\manageproject\classes\CNMessage::getSuccess('Success');
         }else{
             return \backend\modules\manageproject\classes\CNMessage::getError('error');
         }
    }
    public function actionGetModule()
    {
        $query = Ezmodule::find()->where('active=1'); 
        $term = isset($_GET['term']) ? $_GET['term'] : '';
        
        if($term != ''){
            $query->andWhere('ezm_name LIKE :term',[":term"=>"%{$term}%"]);
            //$query->orWhere('ezm_name LIKE :term', [":term"=>"%{$term}%"]);            
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
                'sort' => [
                    'defaultOrder' => [
                    'created_at' => SORT_DESC
                    ]
                ],
            'pagination' => ['pageSize' => 20],
        ]);
        
        return $this->renderAjax('get-module', [                     
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionGetManageModule()
    {
        $term= isset($_GET['term']) ? $_GET['term'] : 1;
        $list = (new \yii\db\Query())
              ->select('*')
              ->from('zdata_manage_modules')
              ->where('rstat not in(0,3)')
              ->andWhere('enableds=:enableds', [":enableds"=>$term])  
              ->orderBy(['order_by'=>SORT_ASC])  
              ->all();
         
        return $this->renderAjax('get-manage-module', [
            'list'=>$list
        ]);
    }     
    public function actionOrder(){
        try{
            $listOrder = isset($_POST['list_order']) ? $_POST['list_order'] : '';
            $list = explode(',' , $listOrder);
            $i = 1 ;
            $ezf_id='1528936267089555700'; 
            foreach($list as $id) {
                $initdata=[
                    'order_by'=>$i
                ];
                $dataCreate = (new \yii\db\Query())
                            ->createCommand()
                            ->update('zdata_manage_modules', $initdata, ['id'=>$id])->execute();
                $i++;
            }
            return  \backend\modules\manageproject\classes\CNMessage::getSuccess('Success');
        } catch (Exception $error) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
        }
         
    }
    
   //save modules    
    
   public function actionCreate(){
      $ezm_id = isset($_POST['ezm_id']) ? $_POST['ezm_id'] : '';
      $dataModule = ManageModuleFunc::getModuleById($ezm_id);
      $counts = (new \yii\db\Query())
              ->select('count(*) as c')
              ->from('zdata_manage_modules')->where('rstat not in(0,3)')->scalar();
      if(empty($counts)){$counts=0;}
      //print_r($dataModule->ezm_icon);return; 
      if(!empty($dataModule)){
        
        $ezf_id='1528936267089555700';
        $initdata=[
            'id'=> \appxq\sdii\utils\SDUtility::getMillisecTime(),
            'rstat'=>'1',
            'module_id'=> isset($dataModule->ezm_id) ? $dataModule->ezm_id : '',
            'image'=> isset($dataModule->ezm_icon) ? $dataModule->ezm_icon : '',
            'url_default'=> isset($dataModule->ezm_icon) ? $dataModule->ezm_icon : '',
            'module_name'=> isset($dataModule->ezm_name) ? $dataModule->ezm_name : '',
            'detail'=> isset($dataModule->ezm_detail) ? $dataModule->ezm_detail : '',
            'enableds'=>1,
            'order_by'=>$counts+1,
            'user_create'=> \cpn\chanpan\classes\CNUser::getUserId(),
            'create_date'=>date('Y-m-d H:i:s'),
            'sitecode'=> isset(Yii::$app->user->identity->profile->sitecode) ? Yii::$app->user->identity->profile->sitecode : ''
        ];
        $create = Yii::$app->db->createCommand()->insert("zdata_manage_modules", $initdata)->execute();
         
        if($create){
          return  \backend\modules\manageproject\classes\CNMessage::getSuccess('Success');
        }else{
            return \backend\modules\manageproject\classes\CNMessage::getError('Error');
        }
      }
      
   }
   public function actionDelete(){
       $id= isset($_POST['id']) ? $_POST['id'] : '';
       $dataDelete = (new \yii\db\Query())
                   ->createCommand()
                   ->update('zdata_manage_modules', ['rstat'=>3], ['id'=>$id])->execute();
       if($dataDelete){
          return  \backend\modules\manageproject\classes\CNMessage::getSuccess('Success');
        }else{
            return \backend\modules\manageproject\classes\CNMessage::getError('Error');
        }
   }
   
   
   //view module
   public function actionViewModule()
    {
        $term= isset($_GET['term']) ? $_GET['term'] : 1;
        $list = (new \yii\db\Query())
                ->select(['ezm.ezm_type','ezm.ezm_link','zmm.id','zmm.module_id','ezm.ezm_short_title as module_name','zmm.detail','zmm.image','zmm.module_icon','zmm.order_by','zmm.view_mode','zmm.url_default','zmm.color'])
                ->from('zdata_manage_modules as zmm')
                ->innerJoin('ezmodule as ezm' , 'zmm.module_id = ezm.ezm_id')
                ->where('zmm.rstat not in(0,3)')
                ->andWhere('zmm.enableds=:enableds', [":enableds"=>$term])  
                ->orderBy(['zmm.order_by'=>SORT_ASC])  
                ->all();
        
        return $this->renderAjax('view-module', [
            'list'=>$list
        ]);
    }
    
    
    
    //permission
    public function actionPermission(){
        $module_id = isset($_GET['id']) ? $_GET['id'] : '';
        return $this->renderAjax("permission/index", compact('module_id'));
    }
    public function actionGetModuleAll(){
        $query = (new \yii\db\Query())
              ->select('*')
              ->from('zdata_manage_modules')
              ->where('rstat not in(0,3)')->all();
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $query,
            'sort' => [
                'attributes' => ['module_name'],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        return $this->renderAjax('permission/get-module-all', [
            'dataProvider'=>$dataProvider
        ]);
    }
    
    
    //getModuleById json
    public function actionGetModuleById()
    {
        if(!empty($_GET)){
            $id= isset($_GET['id']) ? $_GET['id'] : 1;
            $data = (new \yii\db\Query())
                  ->select('*')
                  ->from('ezmodule')
                  ->where('ezm_id=:id',[':id'=>$id]) 
                  ->one();

          $html = "
            <h3>Selected Module</h3>  
            <div class='media-body'> 
                <h4 class='list-group-item-heading'>
                    <span>
                         <img src='".$data['icon_base_url']."/".$data['ezm_icon']."' 
                         class='img-rounded' width='30' height='30'>

                    </span> 
                        ".$data['ezm_name']."     
                </h4>
            </div>  
          ";
          echo $html;
        }
    }
    
    public function actionTest(){
        return $this->render("text");
    }
    public function actionSort(){
        try{
            $data = isset($_GET['data']) ? $_GET['data'] : '';
            $data = explode(",",$data);
            $forder = 1;
            $status=[];
            foreach($data as $key=>$d){
                $update = (new \yii\db\Query())
                    ->createCommand()
                    ->update('zdata_create_project', ['forder'=>$forder], ['id'=>$d])->execute();
                if($update){
                    array_push($status, 1);
                }
                $forder ++;                
            }
            if (in_array(1, $status)) {
                return \backend\modules\manageproject\classes\CNMessage::getSuccess("Sort success");
            } else {
                return \backend\modules\manageproject\classes\CNMessage::getError("Error");
            }
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return \backend\modules\manageproject\classes\CNMessage::getError("Error");
        }
        
    }
    
    
    public function actionViewMode(){
        try{
           $view_mode = isset($_GET['view_mode']) ? $_GET['view_mode'] : '';
           $initdata = ['view_mode'=>$view_mode]; 
           $dataUpdate = (new \yii\db\Query())
                            ->createCommand()
                            ->update('zdata_manage_modules', $initdata)->execute(); 
            return  \backend\modules\manageproject\classes\CNMessage::getSuccess('Success');
        } catch (Exception $error) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
        }
         
    }
}
