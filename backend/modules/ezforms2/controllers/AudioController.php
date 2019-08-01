<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class AudioController extends Controller
{
    public $enableCsrfValidation = false;
    
    public function actionSave(){
	Yii::$app->response->format = Response::FORMAT_JSON;
	
        $fileName = $_POST["audio-filename"];
        
	$rootpath = Yii::getAlias('@storage/web/ezform/audio/');
	if (!is_dir($rootpath)) {
		mkdir($rootpath, 0777, true);
		chmod($rootpath, 0777);
		//throw new CHttpException(500, "{$this->path} does not exists.");
	} else if (!is_writable($rootpath)) {
		chmod($rootpath, 0777);
		//throw new CHttpException(500, "{$this->path} is not writable.");
	}
	
        $path = $rootpath . '/'. $_POST["id"].'_'.Yii::$app->user->id;
        if (!is_dir($path)) {
		mkdir($path, 0777, true);
		chmod($path, 0777);
		//throw new CHttpException(500, "{$this->path} does not exists.");
	} else if (!is_writable($path)) {
		chmod($path, 0777);
		//throw new CHttpException(500, "{$this->path} is not writable.");
	}
	$fn = explode('.', $fileName);
	$fileNameCover = $fn[0].'.mp3';
	
	$uploadDirectoryConver = "$path/audiorec_$fileNameCover";
        $uploadDirectory = "$path/audiorec_$fileName";
	
	$linkAudio = Yii::getAlias('@storageUrl').'/audio/'.$_POST["id"].'_'.Yii::$app->user->id.'/audiorec_'.$fileNameCover;

        array_map('unlink', glob($path."/*"));

	$success = move_uploaded_file($_FILES["audio-blob"]["tmp_name"], $uploadDirectory);
        
	exec("/usr/bin/avconv -y -i $uploadDirectory $uploadDirectoryConver");
	
        if ($success) {
	    chmod($uploadDirectory, 0777);
	    $result = [
		'status' => 'success',
		'action' => 'upload',
		'message' => '<strong><i class="glyphicon glyphicon-info-sign"></i> Success!</strong> ' . Yii::t('app', 'File upload completed.'),
		'data' => $uploadDirectoryConver,
		'link'=>$linkAudio,
		'name' => "audiorec_$fileNameCover",
	    ];
	    return $result;
	} else {
	    $result = [
		'status' => 'error',
		'action' => 'upload',
		'message' => '<strong><i class="glyphicon glyphicon-warning-sign"></i> Error!</strong> ' . Yii::t('yii', 'File upload failed.'),
	    ];
	    return $result;
	}

    }

}
?>
