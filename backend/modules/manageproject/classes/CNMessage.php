<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\manageproject\classes;

/**
 * Description of CNMessage
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class CNMessage {
  public static function setResponse(){
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
  }
  /**
   * 
   * @param type string $message
   * @return json format status=success
   */
  public static function getSuccess($message, $data=""){
      CNMessage::setResponse();
      $result = [
            'data'=>$data,
            'status' => 'success',
            'action' => 'create',
            'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . \Yii::t('chanpan', $message), //Data completed.
        ];
        return $result;
 }
   /**
   * 
   * @param type string $message
   * @return json format status=error
   */
  public static function getError($message, $data=""){
      CNMessage::setResponse();
     $result = [
            'status' => 'error',
            'action' => 'create',
            'message' => \appxq\sdii\helpers\SDHtml::getMsgError() . \Yii::t('chanpan', $message), //Already have this user.
            'data' => $data
        ];
        return $result;
    }
    
   /**
   * 
   * @param type string $message
   * @return json format status=success
   */
  public static function getSuccessObj($message, $data){
      CNMessage::setResponse();
      $result = [
            'data'=>$data,
            'status' => 'success',
            'action' => 'create',
            'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . \Yii::t('chanpan', $message), //Data completed.
        ];
        return $result;
 }  
}
