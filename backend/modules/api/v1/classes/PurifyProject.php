<?php

/*
 * ตรวจสอบเกี่ยวกับ ความเกี่ยวของของสิทธิ์ สำหรับการใช้งานข้อมูลในมิติต่างๆ
 *      project authen
 *      data authen
 */

namespace backend\modules\api\v1\classes;

use Yii;
use backend\modules\api\v1\classes\PurifyData;
/**
 * Description of PurifyProject
 *
 * @author chaiwat
 */
class PurifyProject {
    //put your code here
    public $url;
    public $sec;
    public $data;
    public $dbex;
    public $user_id;
    public $hsitecode;
    public $ezf_id;
    public $ezf_table;
    public $table;          //  ต่างจาก ezf_table ตรงที่รับมาจาก $_GET
    public $page;           // Current page start at 0 -> n
    public $limit;          // Perpage
    
    public $table_reg;
    public $table_purifylog;
    public $purify_session;
    public $purify_token;
    private $tableExist;


    public $id;
    
    public function projSection(){
        $this->table_reg = 'tb_data_1';
        $this->table_purifylog = 'purify_log';
        
        
        $this->projectDetail();
        if( $this->sec == 'pls' ){
            $this->projList();
        }else if($this->sec == 'yrtb' ){
            $this->tableYourTable();
        }else if($this->sec == 'sctb' ){
            $this->tableSqlCreateTable();
        }else if($this->sec == 'destb' ){
            unset($this->data);
            $this->tableRecordDescription();
        }else if($this->sec == 'ldtb' ){
            // Load data from table (version 1)
            $this->tableLoadAllDataFromTable();
        }
    }

    public function projectDetail(){
        if( strlen($this->url)>0 ){
            $this->setProjectDetailByURL();
        }   
    }
    private function setProjectDetailByURL(){
        # fix database
        Try{
            $sql = 'select * from ncrc.dynamic_db where url=:url limit 1 ';
            //$sql = 'select * from dynamic_db limit 3,1 ';
            $params = [
                ':url' => $this->url,
            ];
            $this->data = Yii::$app->db->createCommand($sql, $params)->queryOne();
            $this->dbex = $this->data['dbname'];
        } catch (Exception $ex) {

        }
    }
    
    private function projList(){
        // select user id from token
        
        // list project your pi
    }
    
    
    private function tableYourTable(){
        Try{
            $sql = 'SELECT * FROM `'.$this->dbex.'`.`ezform` WHERE shared<>"4" and (created_by=:user_id OR updated_by=:user_update) ';
//            $sql = 'SELECT `ezf_id`,`ezf_version`,`ezf_name`,`ezf_detail`,`xsourcex`,`ezf_table`,`created_by` ';
//            $sql.= ',`created_at`,`updated_by`,`updated_at`,`status`,`shared`,`public_listview` ';
//            $sql.= ',`public_edit`,`public_delete`,`co_dev`,`assign`,`category_id`,`field_detail`,`ezf_sql` ';
//            $sql.= ',`ezf_error`,`query_tools`,`unique_record`,`consult_tools`,`consult_users`,`consult_telegram` ';
//            $sql.= ',`ezf_options`,`enable_version`,`ezf_icon`,`ezf_crf`,`ezf_db2` ';
//            
//            $sql = 'SELECT `ezf_id`,`ezf_version`,`ezf_name`,`ezf_table`,`created_at`,`updated_by`,`updated_at`,`status` ';
//            $sql.= 'FROM `'.$this->dbex.'`.`ezform` WHERE created_by=:user_id ';
            //$sql = 'select * from dynamic_db limit 3,1 ';
            $params = [
                ':user_id' => $this->user_id,
                ':user_update' => $this->user_id,
            ];
            $this->data = Yii::$app->db->createCommand($sql, $params)->queryAll();
            //$this->data[0]['user_id'] = $this->user_id;
            //$this->dbex = $this->data['dbname'];
        } catch (Exception $ex) {
        }
    }
    
    // แสดงคำสั่ง create table
    private function tableSqlCreateTable(){
        Try{
            $sql = 'select * from `'.$this->dbex.'`.`ezform` ';
            $sql.= 'where ezf_id=:ezf_id ';
            $sql.= 'limit 1 ';
            $params = [':ezf_id' => $this->ezf_id,];
            $result = Yii::$app->db->createCommand($sql, $params)->queryOne();
            if( strlen($result['ezf_table'])>0 ){
                $this->ezf_table = $result['ezf_table'];
            }
            $this->data = $result;
        } catch (Exception $ex) {
        }
        if( strlen($this->ezf_table)>0 ){
            Try{
                $sql = 'SHOW CREATE TABLE `'.$this->dbex.'`.`'.$this->ezf_table.'` ';
                $this->data = Yii::$app->db->createCommand($sql)->queryOne();
            } catch (Exception $ex) {
            }
        }
    }
    
    
    // Load all data from table
    // table -> มาจากการส่งเข้ามา
    // dbex -> มาจากการเช็คจาก url
    //  เงื่อจไขการดึงข้อมูงจาก table
    //      public_listview = 0     Private                             ฟอร์ม ส่วนตัว มองเห็นแค่ตัวเอง ดึงข้อมูลได้ทั้งหมด โดยอัตโนมัติ
    //      public_listview = 1     Public                              ฟอร์มสาธารณะ การดึงข้อมูล สามารถดึงได้ทั้งหมด (ภายใน site ตัวเอง)
    //      public_listview = 2     All members within the same site    ดึงได้เฉพาะ ในรหัส site เรา
    //      public_listview = 3     All members within the same unit    ดึงได้เฉพาะ ในหน่วยงาน
    //      การดึงข้อมูล จะ ดึงตามเงื่อนไข ของแต่ละตัว ตามข้อกำหนด
    private function tableLoadAllDataFromTable(){
        Try{
            $sql = 'select * from `'.$this->dbex.'`.`'.$this->table.'` ';
//            $sql.= 'where ezf_id=:ezf_id ';
            $sql.= 'limit 1000 ';
            $params = [':ezf_id' => $this->ezf_id,];
            $this->data = Yii::$app->db->createCommand($sql, $params)->queryAll();
        } catch (Exception $ex) {
        }
    }
    
    
    
    private function checkTableExist($db,$tb){
        $this->tableExist = FALSE;
        try{
            $sql = 'SELECT count(*) as numberoftable FROM information_schema.TABLES WHERE (TABLE_SCHEMA = :db) AND (TABLE_NAME = :tb)';
            $params = [
                    ':db' => $db,
                    ':tb' => $tb,
                ];
            $result = Yii::$app->db->createCommand($sql, $params)->queryOne();
            if ($result['numberoftable']>0 ){
                $this->tableExist = TRUE;
            }
        } catch (Exception $ex) {

        }
    }

    private function logForLoadData($commandname, $sql, $ctype='0'){
        // check table if not exist;
        $this->checkTableExist($this->dbex, $this->table_purifylog);  
        // execute table
        if($this->tableExist){
            try{
                $log = 'insert ignore into `'.$this->dbex.'`.`'.$this->table_purifylog.'` ';
                $log.= 'set dadd=NOW() ,command_name=:commandname ';
                $log.= ',purify_session=:usr ,purify_sql=:sql ';
                $log.= ',user_id=:user_id ,token=:token ';
                $log.= ',command_type=:ctype ';
                $params = [
                        ':commandname' => $commandname,
                        ':usr' => $this->purify_session,
                        ':sql' => $sql,
                        ':user_id' => $this->user_id,
                        ':token' => $this->purify_token,
                        ':ctype' => $ctype,
                    ];
                Yii::$app->db->createCommand($log, $params)->execute();
            } catch (Exception $ex) {

            }
        }
    }
    
    private function tableRecordDescription(){
        $this->tableCountAllRecord();
        $this->tableCountAllRecordGroupbyRstat();
        $this->tableCountAllRecordBySite();
        $this->tableCountAllRecordGroupbyRstatBySite();
    }
    private function tableCountAllRecord(){
        Try{
            $sql = 'select count(*) as recs from `'.$this->dbex.'`.`'.$this->table.'` ';
            $sql.= 'where rstat in (1,2,4) ';
            $params = [];
            $sqlRaw = Yii::$app->db->createCommand($sql, $params)->rawSql;
            $result = Yii::$app->db->createCommand($sql, $params)->queryOne();
            $this->data['all_records'] = $result['recs'];
            
            // log for load data
            $this->logForLoadData('นับจำนวน Record : '.$this->table.' => ทั้งหมด ',$sqlRaw,'1');
        } catch (Exception $ex) {
            $this->data['error'] = 'fail';
            $this->data['mysql_error'] = $ex->getMessage();
            // store error into log_catch -> getMessage
        }
    }
    private function tableCountAllRecordBySite(){
        if( strlen($this->hsitecode)>0 ){
            Try{
//                if( $this->table=='tb_data_1' ){
//                    // Register
//                    $sql = 'select count(distinct a.id) as recs from `'.$this->dbex.'`.`'.$this->table.'` a ';
//                    //$sql.= 'inner join `'.$this->dbex.'`.`'.$this->table_reg.'` b on b.ptid=a.ptid ';
//                    $sql.= 'where a.rstat in (1,2,4) and a.hsitecode=:hsitecode ';
//                }else{
//                    $sql = 'select count(distinct a.id) as recs from `'.$this->dbex.'`.`'.$this->table.'` a ';
//                    $sql.= 'inner join `'.$this->dbex.'`.`'.$this->table_reg.'` b on b.ptid=a.ptid ';
//                    $sql.= 'where a.rstat in (1,2,4) and b.hsitecode=:hsitecode ';
//                }
                $sql = 'select count(distinct a.id) as recs from `'.$this->dbex.'`.`'.$this->table.'` a ';
                $sql.= 'where a.rstat in (1,2,4) and a.hsitecode=:hsitecode ';
                $params = [':hsitecode' => $this->hsitecode ];
                
                $sqlRaw = Yii::$app->db->createCommand($sql, $params)->rawSql;
                $result = Yii::$app->db->createCommand($sql, $params)->queryOne();
                
                $this->data['hsitecode_records'] = $result['recs'];
                
                
                // log for load data
                $this->logForLoadData('นับจำนวน Record : '.$this->table.' => ใน site ['.$this->hsitecode.'] ',$sqlRaw,'1');
            } catch (Exception $ex) {
                $this->data['error'] = 'fail';
                $this->data['mysql_error'] = $ex->getMessage();
                // store error into log_catch -> getMessage
            }
        }
    }
    private function tableCountAllRecordGroupbyRstat(){
        Try{
            $sql = 'select a.rstat,count(distinct a.id) as recs from `'.$this->dbex.'`.`'.$this->table.'` a ';
            $sql.= 'where a.rstat in (1,2,4) '; 
            $sql.= 'group by a.rstat ';
            $params = [];
            $sqlRaw = Yii::$app->db->createCommand($sql, $params)->rawSql;
            $result = Yii::$app->db->createCommand($sql, $params)->queryAll();
            if(count($result)>0){
                foreach($result as $v){
                    if($v['rstat']=='1'){
                        $this->data['rstat_all_1'] = $v['recs'];
                    }else if($v['rstat']=='2'){
                        $this->data['rstat_all_2'] = $v['recs'];
                    }else if($v['rstat']=='3'){
                        $this->data['rstat_all_3'] = $v['recs'];
                    }else if($v['rstat']=='4'){
                        $this->data['rstat_all_4'] = $v['recs'];
                    }
                }
            }
            
            // log for load data
            $this->logForLoadData('นับจำนวน Record : '.$this->table.' ตาม rstat => ของทุกหน่วยบริการ ',$sqlRaw,'1');
        } catch (Exception $ex) {
            $this->data['error'] = 'fail';
            $this->data['mysql_error'] = $ex->getMessage();
            // store error into log_catch -> getMessage
        }
    }
    private function tableCountAllRecordGroupbyRstatBySite(){
        Try{
            $sql = 'select rstat,count(*) as recs from `'.$this->dbex.'`.`'.$this->table.'` ';
            $sql.= 'where rstat in (1,2,4) and hsitecode=:hsitecode ';
            $sql.= 'group by rstat ';
            $params = [':hsitecode' => $this->hsitecode ];
            $sqlRaw = Yii::$app->db->createCommand($sql, $params)->rawSql;
            $result = Yii::$app->db->createCommand($sql, $params)->queryAll();
            if(count($result)>0){
                foreach($result as $v){
                    if($v['rstat']=='1'){
                        $this->data['rstat_bysite_1'] = $v['recs'];
                    }else if($v['rstat']=='2'){
                        $this->data['rstat_bysite_2'] = $v['recs'];
                    }else if($v['rstat']=='3'){
                        $this->data['rstat_bysite_3'] = $v['recs'];
                    }else if($v['rstat']=='4'){
                        $this->data['rstat_bysite_4'] = $v['recs'];
                    }
                }
            }
            
            // log for load data
            $this->logForLoadData('นับจำนวน Record : '.$this->table.' ตาม rstat => ใน site ['.$this->hsitecode.'] ',$sqlRaw,'1');
        } catch (Exception $ex) {
            $this->data['error'] = 'fail';
            $this->data['mysql_error'] = $ex->getMessage();
            // store error into log_catch -> getMessage
        }
    }
    
    
    
    
    
    
    
}
