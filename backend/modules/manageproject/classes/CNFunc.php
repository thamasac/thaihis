<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\manageproject\classes;

/**
 * Description of CNFunc
 *
 * @author chanpan
 */
class CNFunc {
  /**
    *  
    * @param type $detail detali action
    * @return boolean true|false
    */
    public static function addLog($detail){
       try{
            if(isset(\Yii::$app->params['enabled_dbmain']) && \Yii::$app->params['enabled_dbmain'] === true){
                $module = isset(\Yii::$app->controller->module->id)?\Yii::$app->controller->module->id:''; 
                $controller = isset(\Yii::$app->controller->id)?\Yii::$app->controller->id:'';
                $action = isset(\Yii::$app->controller->action->id)?\Yii::$app->controller->action->id:'';
                $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
                $user_id = isset(\Yii::$app->user->id)?\Yii::$app->user->id:'';
                $columns=[
                    'id'=>$id,
                    'create_date'=>date('Y-m-d H:i:s'),
                    'create_by'=>$user_id,
                    'action'=>"{$controller}/{$action}",
                    'detail'=>$detail
                ];
                $portalDB = 'ncrc';
                $table='system_log';
                $data = \Yii::$app->db->createCommand()->insert($table, $columns)->execute();
                if($data){
                    return true;
                }
            }//enabled db_main
            
       } catch (\yii\db\Exception $ex) {

       }
    }//end addLog
}
