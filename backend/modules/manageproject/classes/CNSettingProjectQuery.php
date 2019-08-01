<?php
namespace backend\modules\manageproject\classes;
use yii\db\Exception;
use Yii;
use yii\base\Component;
class CNSettingProjectQuery extends Component{
    public $mydb='';
    public $user_id='';
    public $dataid='';
    public $table = 'zdata_create_project';
    public $dynamic_db = 'dynamic_db';
    public $create_project = 'zdata_create_project';
            
    /**
     * @inheritdoc
     * @return CNSettingProjectQuery the newly created [[CNSettingProjectQuery]] instance.
     */
    public static function classNames() {
        return Yii::createObject(CNSettingProjectQuery::className());  
    }
    /**
     * 
     * @param string $mydb
     * @return $this
     */
    public function setDb($mydb){
        $this->mydb = $mydb;
        return $this;
    }
     /**
     * 
     * @param string $user_id
     * @return $this
     */
    public function setUserId($user_id){
        $this->user_id = $user_id;
        return $this;
    }
    /**
     * 
     * @param string $dataid
     * @return $this
     */
    public function setDataId($dataid){
        $this->dataid = $dataid;
        return $this;
    }
    
    public function getProjectAll($search = ''){
        try{ 
            $data = (new \yii\db\Query())
                    ->select("*")
                    ->from($this->table)
                    ->where('rstat <> 0');
            if($search != ''){
                $data->andWhere('projectname LIKE :name OR projectacronym LIKE :acronym OR projurl LIKE :projurl OR projdomain LIKE :projdomain OR pi_name LIKE :pi_name',[
                    ':name'=>"%$search%",
                    ':acronym'=>"%$search%",
                    ':projurl'=>"%$search%",
                    ':projdomain'=>"%$search%",    
                    ':pi_name'=>"%$search%", 
                    
                ]);
            }
            $data->orderBy(['rstat'=>SORT_ASC]);              
            return $data->all($this->mydb);
      } catch (Exception $ex) {
          \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
      }
    }   

    public function getMyProject(){ 
        try{ 
            $data = (new \yii\db\Query())
                    ->select("*")
                    ->from($this->table)
                    ->where('rstat=1 OR rstat = 3')
                    ->andWhere('user_create = :user_id',[':user_id'=> $this->user_id])
                    ->orderBy(['rstat'=>SORT_ASC])
                    ->all($this->mydb);
            return $data;
      } catch (Exception $ex) {
          \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
      }
    }
    public function getMyProjectById(){
        try{ 
            $data = (new \yii\db\Query())
                    ->select("*")
                    ->from($this->table)
//                    ->where('rstat=1')
                    ->andWhere('user_create = :user_id',[':user_id'=> $this->user_id])
                    ->andWhere('id=:id', [":id"=> $this->dataid])
                    ->all($this->mydb);
            return $data;
      } catch (Exception $ex) {
          \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
          return false;
      }
    }
    public function getMyProjectByIdNoUsers(){
        try{ 
            $data = (new \yii\db\Query())
                    ->select("*")
                    ->from($this->table)
                    ->where('rstat=1')
//                    ->andWhere('user_create = :user_id',[':user_id'=> $this->user_id])
                    ->andWhere('id=:id', [":id"=> $this->dataid])
                    ->all($this->mydb);
            return $data;
      } catch (Exception $ex) {
          \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
          return false;
      }
    }
    public function getMyProjectDeleteById(){
        try{ 
            $data = (new \yii\db\Query())
                    ->select("*")
                    ->from($this->table)
                    ->where('rstat=3')
                    ->andWhere('user_create = :user_id',[':user_id'=> $this->user_id])
                    ->andWhere('id=:id', [":id"=> $this->dataid])
                    ->all($this->mydb);
            return $data;
      } catch (Exception $ex) {
          \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
          return false;
      }
    }
    
    public function restoreProject(){
        try{
            $status = (new \yii\db\Query())
                    ->createCommand($this->mydb)
                    ->update($this->table, ['rstat'=>1], ['id'=>$this->dataid])
                    ->execute();
            $this->updateDbDynamic(1);
            return $status;
         } catch (Exception $ex) {
             \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
             return false;
        }
    }
    public function deleteProject(){
        try{
            //$this->updateDbDynamic(3);return;
            $status = (new \yii\db\Query())
                    ->createCommand($this->mydb)
                    ->update($this->table, ['rstat'=>3], ['id'=>$this->dataid])
                    ->execute();
            $this->updateDbDynamic(3);
            return $status;
         } catch (Exception $ex) {
             \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
             return false;
        }
    }
    public function updateDbDynamic($rstat){     
        //url_change
        //\appxq\sdii\utils\VarDumper::dump($rstat);
        if ($rstat == 3) {
            $data = $this->getDynamicDbByDataId($this->dataid);
            $url = $data['url'];
            $updateUrl = (new \yii\db\Query())
                    ->createCommand($this->mydb)
                    ->update($this->dynamic_db, ['rstat' => $rstat, 'url_change'=>$url], ['data_id' => $this->dataid])
                    ->execute();
        } else {
            return (new \yii\db\Query())
                            ->createCommand($this->mydb)
                            ->update($this->dynamic_db, ['rstat' => $rstat], ['data_id' => $this->dataid])
                            ->execute();
        }
    }
   
    /* get dynamic_db for dataid*/
    public function getDynamicDbByDataId($dataid){        
        return \cpn\chanpan\classes\CNServerConfig::getServerModelDynamicDb($id);
//        return (new \yii\db\Query())->select('*')->from($this->dynamic_db)->where([
//            'data_id'=>$dataid
//        ])->one();
    }
   
   
   
   public function getMyProjectById2(){
        try{ 
            $data = (new \yii\db\Query())
                    ->select("*")
                    ->from($this->table)  
                    ->andWhere('id=:id', [":id"=> $this->dataid])
                    ->all($this->mydb);
            return $data;
      } catch (Exception $ex) {
          \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
          return false;
      }
    }
   
    /* save and update*/
    /**
     * 
     * @param type $columns array columns table create project
     * @return boolean
     */
    public function create($columns){
       try{
          $create = Yii::$app->db->createCommand()
                  ->insert($this->table, $columns)
                  ->execute();
          if($create){return true;}return false;
       } catch (Exception $ex) {
          \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
          return false;
       } 
    }
    
    
    /**
    * 
    * @param type $data array ['projicon'=>'']
    * @return type
    */  
   public function updateCreateProject($data){
       return (new \yii\db\Query())
                    ->createCommand($this->mydb)
                    ->update($this->create_project,$data, ['id'=>$this->dataid])
                    ->execute();
   }
   
   /**
    * 
    * @param type $url | String
    * @param type $data_id | Bigint id zdata_create_project
    * @param type $db | Database name | ''
    * @return type boolean true|false
    */
   public static function update_url_for_dynamic_db($url,$data_id, $db=''){
       try{
           $sql="UPDATE {$db}.dynamic_db SET url=:url WHERE data_id=:data_id";
           $params=[
               ':url'=>$url,
               ':data_id'=>$data_id
            ];
           return Yii::$app->db->createCmmand($sql, $params)->execute();
       } catch (Exception $ex) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
           return false;
       }
   }
   
   /**
    * 
    * @param type $table_name string 'zdata_create_project'
    * @param type $dbname string 'ncrc'
    * @return boolean true|false
    */
   public static function drop_table($table_name, $dbname){
       try{
           $sql="DROP TABLE IF EXISTS {$dbname}.{$table_name}";
           return Yii::$app->db_main->createCmmand($sql, $params)->execute();
       } catch (Exception $ex) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
           return false;
       }
   }
   
    
    
}
