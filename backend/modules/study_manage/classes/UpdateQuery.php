<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\study_manage\classes;

/**
 * Description of UpdateQuery
 *
 * @author Admin
 */
class UpdateQuery {
    //put your code here
    public static function getLatestDate($ezf_id){
        $query = new \yii\db\Query();
        $query->select('max(create_date) as latestDate')
                ->from('ezform_update_log')
                ->where(['ezf_id'=>$ezf_id]);
        
        $result = $query->one();
        
        return $result;
                
    }
}
