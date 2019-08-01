<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dms\aomruk\classese;

/**
 * Description of DowloadJob
 *
 * @author AR Soft
 */
class DowloadJob extends Notify implements \yii\queue\JobInterface{
    //put your code here
    public $url;
    public $file; 
    public $model;
    public $ezf_field;
    public $version;
    public function execute($queue) {
        \Yii::$app->db->createCommand("INSERT INTO test (name) VALUE ('1234')")->execute();
        $this->sendByEzfModel($this->model, $this->ezf_field, $this->version);
        
    }
    
    
   

}
