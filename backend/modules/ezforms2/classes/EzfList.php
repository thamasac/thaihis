<?php
namespace backend\modules\ezforms2\classes;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * Description of EzfList
 *
 * @author appxq
 */
class EzfList {
    //put your code here
//    $params = [
//    'field'=>$field,
//    'data'=>$model
//];
    
    public static function getEzFormList($params=[]) {
        $items = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevAll();
        
        return ArrayHelper::map($items,'ezf_id','ezf_name');
    }
    
    public static function getEzModuleList($params=[]) {
        $items = \backend\modules\ezmodules\classes\ModuleQuery::getModuleMyAllAddon(Yii::$app->user->id);
        
        return ArrayHelper::map($items,'ezm_id','ezm_name');
    }
    
    public static function getWorkingUnit($params=[]) {
        $sitecode = Yii::$app->user->identity->profile->sitecode;
        
        $sql = "SELECT u.id AS id, concat(u.unit_code, ' ', u.unit_name) AS text
		FROM zdata_working_unit u
                Where u.rstat not in(0,3) AND xsourcex=:sitecode
		";
	
	$data = Yii::$app->db->createCommand($sql, [':sitecode' => $sitecode])->queryAll();
        
        return ArrayHelper::map($data,'id','text');
    }
    
    public static function getEzFieldList($params=[]) {
                
        if(isset($params['field']['ezf_id'])){
            
            $items = \backend\modules\ezforms2\classes\EzfQuery::getFieldsListVersion($params['field']['ezf_id'], $params['field']['ezf_version']);
            
            return ArrayHelper::map($items,'ezf_field_name','name');
        }
        return [];
    }
    
}
