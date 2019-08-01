<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\study_manage\classes;

/**
 * Description of StudyQuery
 *
 * @author Admin
 */
class StudyQuery {
    public static function getStudyDesignFix(){
        $study =  [
            '1'=>'Randomized controlled trial (RCT)',
            '2'=>'Experimental studies (not RCT)',
            '3'=>'Prospective cohort study',
            '4'=>'Disease registry',
            '5'=>'Retrospective cohort study',
            '6'=>'Other type of cohort study',
            '7'=>'Case-control study',
            '8'=>'Cross-sectional study',
            '9'=>'Survey/Poll',
            '99'=>'Others'
        ];
        return \appxq\sdii\utils\SDUtility::array2String($study);
    }
    //put your code here
    public static function getStudyDesign(){
        $query  =new \yii\db\Query();
        $query->select("id,study_design,acronym")
                ->from("zdata_study_module")
                ->where(' rstat NOT IN(0,3)');
        
        $result = $query->all();
        return $result;
    }
    
    public static function getModuleListOfStudy($study_design){
        $query  =new \yii\db\Query();
        $query->select("ezmodule_list")
                ->from("zdata_study_module")
                ->where(' rstat NOT IN(0,3)')
                ->andWhere(['study_design'=>$study_design]);
        
        $result = $query->one();
        return $result;
    }
    
    public static function getModuleOneOfStudy($study_design, $module_id){
        $result = self::getModuleListOfStudy($study_design);
        $module_list = \appxq\sdii\utils\SDUtility::string2Array($result['ezmodule_list']);
        
        if(is_array($module_list) && count($module_list) > 0 ){
            
            if(in_array($module_id, $module_list)){
                return true;
            }
        }else{
            
            if($module_id==$result['ezmodule_list']){
                return true;
            }
        }
        return false;
    }
    
    public static function checkAleadyExistStudy($study_design){
        $query  =new \yii\db\Query();
        $query->select("*")
                ->from("zdata_study_module")
                ->where(' rstat NOT IN(0,3)')
                ->andWhere(['study_design'=>$study_design]);
        $result = $query->one();
        if($result){
            return 'true';
        }
        
        return 'false';
    }
    
    public static function getModuleStudyTemplates($module_id){
        $query = new \yii\db\Query();
        $query->select('ezm_id,ezm_order')
                ->from('study_templates')
                ->where(['ezm_id'=>$module_id]);
        $result=$query->one();
        
        return $result;
    }
}
