<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;

/**
 * Select2Controller implements the CRUD actions for EzformInput model.
 */
class TextEditorController extends Controller
{
    public $enableCsrfValidation = false;
    
    public function actions()
    { 
        $fuser = 'guest';
        if(!Yii::$app->user->isGuest){
            $fuser = Yii::$app->user->id;
        }
        
        return [
            'image-upload' => [
                'class' => 'vova07\imperavi\actions\UploadFileAction',
                'url' => Yii::getAlias('@storageUrl') . '/ezform/editor-upload', // Directory URL address, where files are stored.
                'path' => '@storage/web/ezform/editor-upload', // Or absolute path to directory where files are stored.
                    'validatorOptions' => [
                      'maxWidth' => 3000,
                      'maxHeight' => 3000
                  ],
            ],
            'file-upload' => [
                'class' => 'vova07\imperavi\actions\UploadFileAction',
                'url' => Yii::getAlias('@storageUrl') . '/ezform/editor-upload', // Directory URL address, where files are stored.
                'path' => '@storage/web/ezform/editor-upload', // Or absolute path to directory where files are stored.
                'uploadOnlyImage' => false,
                'validatorOptions' => [
                    'maxSize' => 500000000
                ]
            ],
            'files-get' => [
                'class' => 'appxq\sdii\action\GetAction',
                'url' => Yii::getAlias('@storageUrl') . '/ezform/editor-upload/'.$fuser, // Directory URL address, where files are stored.
                'path' => '@storage/web/ezform/editor-upload/'.$fuser, // Or absolute path to directory where files are stored.
                'type' => \appxq\sdii\action\GetAction::TYPE_FILES,
            ],
            'images-get' => [
                'class' => 'appxq\sdii\action\GetAction',
                'url' => Yii::getAlias('@storageUrl') . '/ezform/editor-upload/'.$fuser, // Directory URL address, where files are stored.
                'path' => '@storage/web/ezform/editor-upload/'.$fuser, // Or absolute path to directory where files are stored.
                'type' => \appxq\sdii\action\GetAction::TYPE_IMAGES,
            ],
            'image-upload-froala' => [
                'class' => '\appxq\sdii\action\UploadFileAction',
                'url' => Yii::getAlias('@storageUrl') . '/ezform/editor-upload/'.$fuser.'/', // Directory URL address, where files are stored.
                'path' => '@storage/web/ezform/editor-upload/'.$fuser.'/', // Or absolute path to directory where files are stored.
                    'validatorOptions' => [
                      'maxWidth' => 3000,
                      'maxHeight' => 3000
                  ],
            ],
            'file-upload-froala' => [
                'class' => '\appxq\sdii\action\UploadFileAction',
                'url' => Yii::getAlias('@storageUrl') . '/ezform/editor-upload/'.$fuser.'/', // Directory URL address, where files are stored.
                'path' => '@storage/web/ezform/editor-upload/'.$fuser.'/', // Or absolute path to directory where files are stored.
                'uploadOnlyImage' => false,
                'validatorOptions' => [
                    'maxSize' => 500000000
                ]
            ],
        ];        
    }
    
    public function actionFileDelete() {
        if (Yii::$app->getRequest()->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $file_name = isset($_POST['title'])?$_POST['title']:'';
            
            $fuser = 'guest';
            if(!Yii::$app->user->isGuest){
                $fuser = Yii::$app->user->id;
            }

            if($file_name!=''){
                @unlink(Yii::getAlias('@storage/web/ezform/editor-upload/'.$fuser.'/') . $file_name);
            }
            
            $result = [
                    'status' => 'success',
                    'action' => 'update',
                    'message' =>  Yii::t('app', 'Deleted completed.'),
            ];
            return $result;
        } else {
                throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }
}
