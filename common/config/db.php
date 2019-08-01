<?php
class CNDynamicDatabaseConnection{
     private $dbname="";
    private $ncrc_db;
    public function setDbnameDynamic($servername,$username, $password,$dbname, $backendUrl, $frontendUrl){ 
        $this->ncrc_db = $dbname; 
        
       try{
            $url = $_SERVER['SERVER_NAME'];
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("SELECT dbname,rstat FROM dynamic_db WHERE url=:url LIMIT 1"); 
             
            $stmt->bindParam(':url', $url,  PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $row = $stmt->fetch();
            
            $main_url = '';//\backend\modules\core\classes\CoreFunc::getParams('main_url','url');
            $portal_site = '';//\backend\modules\core\classes\CoreFunc::getParams('portal_site', 'url');
            
            if($stmt->rowCount()==0){
                if($url== $backendUrl){
                    $row['dbname'] = $this->ncrc_db;                    
                }else{                    
                    header("Location: https://{$backendUrl}");
                    exit();//ถ้าไม่ exit จะไม่ redirect
                }
            
            }
            if($stmt->rowCount() > 0){              
                
                if($url== $backendUrl){
                    $row['dbname'] = $this->ncrc_db;                    
                }
                $this->dbname = $row['dbname'];                
                if($row['rstat'] == 3){
                   echo "
                       <style>body{    background: #f2dede;}</style>
                        <div style='
                                    padding:20px;
                                    margin-top:10%;text-align:center;color: #31708f;
                                    color: #a94442;
                                    background-color: #f2dede;
                                    border-color: #ebccd1;'>
                            <h1>This project has been deleted. Please contact the project owner.</h1>
                            <a href='https://".$frontendUrl."'>
                                <i class='fa fa-home'></i> 
                                <span class='title'>nCRC Central Site</span>
                            </a> | 
                            <a href='https://".$backendUrl."'>
                                <i class='fa fa-home'></i> 
                                <span class='title'>All My Projects</span>
                            </a>
                        </div>
                   ";
                   exit();//ถ้าไม่ exit จะไม่ echo ค่าใดๆ
                }else if($portal_site == '1' && $row['rstat'] != 3){
                    $this->dbname = $dbname;
                }
            } 


        } catch (PDOException $ex) {
            echo $ex->getMessage();
        } 
    }
    public function getDbname(){
        return $this->dbname;
    }
}
