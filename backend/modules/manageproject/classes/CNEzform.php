<?php
namespace backend\modules\manageproject\classes;
 
class CNEzform {
    /**
     * 
     * @param type string || integer $ezfId
     * @return type array data ezform
     */
    public static function getEzformByEzfId($ezfId){
        $data= (new \yii\db\Query())
                ->select("*")
                ->from("ezform")
                ->where(["ezf_id"=>$ezfId])
                ->one();
        return $data;
    }
    
    /**
     * 
     * @param type string || integer $ezfId
     * @return type string ezf_table
     */
    public static function getEzfTableName($ezfId){
        $data= CNEzform::getEzformByEzfId($ezfId);
        return $data['ezf_table'];
    }
    
    /**
     * 
     * @param type string $table table name
     * @param type array $where [name=>'xxx']
     * @return type array data
     */ 
    public static function getDynamicTableAll($table, $where=[], $dbType=""){
        if(empty($dbType)){
            $dataStr= (new \yii\db\Query())
                ->select("*")
                ->from($table)->where("rstat not in(0,3)");
            if(!empty($where)){
                $dataStr->andWhere($where);
            }
            $data = $dataStr->orderBy(['forder'=>SORT_ASC]);
            $data = $dataStr->all();   
            return $data;
        }else{
            $sql="SELECT * FROM {$table} WHERE id={$where['id']} AND (rstat not in(0,3))";
            return \Yii::$app->db_main->createCommand($sql)->queryOne();
        } 
        
    }
    public static function getUserProject(){
        $user_id = \Yii::$app->user->identity->id;
        $sql="SELECT * FROM user_project WHERE user_id=:user_id GROUP BY data_id";
        $data = \Yii::$app->db_main->createCommand($sql, [':user_id'=>$user_id])->queryAll();
//         \appxq\sdii\utils\VarDumper::dump($output);
        $output = [];
        if(!empty($data)){
            foreach($data as $key => $value){
                $sql2="SELECT * FROM zdata_create_project WHERE (id=:id) AND (rstat not in(0,3)) ORDER BY forder asc";
                $data2 = \Yii::$app->db_main->createCommand($sql2, [':id'=>$value['data_id']])->queryOne();
                
                if(!empty($data2)){
                    array_push($output, $data2);
                }

            }
        }
        return $output;        
    }
    
    public static function getTrashProject(){
        try{
            $user_id = \cpn\chanpan\classes\CNUser::getUserId();
            $data = (new \yii\db\Query())
                    ->select('*')
                    ->from('zdata_create_project')
                    ->where('rstat=3')
                    ->andWhere('user_create=:user_id',[':user_id'=>$user_id])
                    ->all(\Yii::$app->db_main);
            return $data;
            
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }
    }
    
    /**
     * 
     * @param string $table table name
     * @param string $dataId id
     * @return array data one
     */
    public static function getDynamicTableByDataId($table, $dataId){
        $data= (new \yii\db\Query())
                ->select("*")
                ->from($table)
                ->where(['id'=>$dataId])
                ->andWhere("rstat not in (0,3)")
                ->one();
        return $data;
    }
}
