<?php

namespace common\modules\user\classes;

use Yii;
use yii\web\Response;
use yii\web\UploadedFile;   

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminClasses
 *
 * @author damasa
 */
class AdminClasses {

    //put your code here

    public static function GetResponse() {
        return \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public static function GetError($id) {
        $result = [
            'status' => 'error',
            'action' => 'alert',
            'message' => '<strong><i class="glyphicon glyphicon-warning-sign"></i> Error!</strong> ' . Yii::t('user', 'You can not action your own account.'),
            'data' => $id,
        ];
        return $result;
    }

    public static function GetSuccess($id, $msg="") {
        if($msg == ""){
            $msg = '<strong><i class="glyphicon glyphicon-ok-sign"></i> Success!</strong> ' . Yii::t('user', 'User has been remove manager.');
        }
        $result = [
            'status' => 'success',
            'action' => 'update',
            'message' => $msg,
            'data' => $id,
        ];
        return $result;
    }
    public static function UploadFiles($models, $files){
        $images = \yii\web\UploadedFile::getInstance($models, $files);
        $path = \Yii::getAlias('@storage') . "/web/source/";
        if(!empty($models->$files)){
            unlink($path.$models->$files);
        }
        $filename = \Date('YmdHis') . rand(9999, 9999999) . "." . $images->extension;
        if($images->saveAs($path . $filename)){
            return $filename;
        }
    }
    public static function getImages($urlStorage){
        return "<div class=\"text-center\"><a href='".$urlStorage."' target='_blank'><img class=\"img img-responsive img-thumbnail\" src=\"$urlStorage\" ></a></div>";
    }
 

}
