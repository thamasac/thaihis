<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DrawingController extends Controller {

    public function actionPerview($value, $bg, $width, $height) {
        $line = '';
        
        $fileArr = explode(',', $value);
        
        if(count($fileArr)>1){
                $fileName = $fileArr[0];
                $fileBg = $fileArr[1];

                
                if (isset($fileName) && !empty($fileName) && stristr($fileName, '.png') == TRUE){
                    $line = Yii::getAlias('@storageUrl/ezform/drawing/data/') . $fileName;
                }
                
                if (isset($fileBg) && !empty($fileBg) && stristr($fileBg, '.png') == TRUE){
                    $bg = Yii::getAlias('@storageUrl/ezform/drawing/bg/') . $fileBg;
                }else{
                    $bg = Yii::getAlias('@storageUrl/ezform/drawing/bg/') . $bg;
                }
                
        }
        
        return $this->render('/widgets/perview_drawing', [
                    'line' => $line,
                    'bg' => $bg,
                    'width' => $width,
                    'height' => $height,
        ]);
    }
    
    public function actionCanvasImage() {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            // input is in format: data:image/png;base64,...
            //chmod(Yii::$app->basePath . '/../backend/web/drawing/', 0777);

            $im = imagecreatefrompng($_POST['image']);
            imagesavealpha($im, true);
            // Fill the image with transparent color
            $color = imagecolorallocatealpha($im, 0x00, 0x00, 0x00, 127);
            imagefill($im, 0, 0, $color);
            
            $idName = '';
            if ($_SERVER["REMOTE_ADDR"] == '::1' || $_SERVER["REMOTE_ADDR"] == '127.0.0.1') {
                $idName = 'mycom';
            } else {
                $idName = str_replace('.', '_', $_SERVER["REMOTE_ADDR"]);
            }
            //date("YmdHis")
            $nowFileName = $idName . '_' . $_POST['name'] . '_tmp.png';
            $fullPath = Yii::getAlias('@storage/web/ezform/print/') . $nowFileName;            

            $success = imagepng($im, $fullPath);
            if ($success) {
                //chmod(Yii::$app->basePath . '/../backend/web/drawing/'. $nowFileName, 0777);
                $result = [
                    'status' => 'success',
                    'action' => 'upload',
                    'message' => '<strong><i class="glyphicon glyphicon-info-sign"></i> Success!</strong> ' . Yii::t('app', 'File upload completed.'),
                    'path'=>Yii::getAlias('@storageUrl/ezform/print/'),
                    'data' => $nowFileName,
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
            imagedestroy($im);
        } else {
            throw new NotFoundHttpException('Ajax only.');
        }
    }
    
    public function actionSaveImage() {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            // input is in format: data:image/png;base64,...
            //chmod(Yii::$app->basePath . '/../backend/web/drawing/', 0777);

            $im = imagecreatefrompng($_POST['image']);
            imagesavealpha($im, true);
            // Fill the image with transparent color
            $color = imagecolorallocatealpha($im, 0x00, 0x00, 0x00, 127);
            imagefill($im, 0, 0, $color);
            
            $idName = '';
            if ($_SERVER["REMOTE_ADDR"] == '::1' || $_SERVER["REMOTE_ADDR"] == '127.0.0.1') {
                $idName = 'mycom';
            } else {
                $idName = str_replace('.', '_', $_SERVER["REMOTE_ADDR"]);
            }
            //date("YmdHis")
            $nowFileName = $idName . '_' . $_POST['name'] . '_tmp.png';
            $fullPath = Yii::getAlias('@storage/web/ezform/drawing/') . $nowFileName;            

            $success = imagepng($im, $fullPath);
            if ($success) {
                //chmod(Yii::$app->basePath . '/../backend/web/drawing/'. $nowFileName, 0777);
                $result = [
                    'status' => 'success',
                    'action' => 'upload',
                    'message' => '<strong><i class="glyphicon glyphicon-info-sign"></i> Success!</strong> ' . Yii::t('app', 'File upload completed.'),
                    'data' => $nowFileName,
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
            imagedestroy($im);
        } else {
            throw new NotFoundHttpException('Ajax only.');
        }
    }

    public function actionBgImage() {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            //chmod(Yii::$app->basePath . '/../storage/web/drawing/', 0777);
            $uploadFile = \yii\web\UploadedFile::getInstanceByName('outline-bg');

            if (isset($_GET['oldfile'])) {
                unlink(Yii::getAlias('@storage/web/ezform/drawing/') . $_GET['oldfile']);
            }

            if ($uploadFile !== null) {
                $idName = '';
                if ($_SERVER["REMOTE_ADDR"] == '::1' || $_SERVER["REMOTE_ADDR"] == '127.0.0.1') {
                    $idName = 'mycom';
                } else {
                    $idName = str_replace('.', '_', $_SERVER["REMOTE_ADDR"]);
                }
                //date("YmdHis")
                $nowFileName = $idName . '_bg_' . $_GET['name'] . date('_His') . 'tmp.png';
                $fullPath = Yii::getAlias('@storage/web/ezform/drawing/') . $nowFileName;

                $uploadFile->saveAs($fullPath);
                //chmod(Yii::$app->basePath . '/../storage/web/drawing/'. $nowFileName, 0777);

                list($width, $height, $type, $attr) = getimagesize($fullPath);

                return ['files' => [
                        'name' => $nowFileName,
                        'type' => $uploadFile->type,
                        'size' => $uploadFile->size,
                        'url' => \Yii::getAlias('@storageUrl') . '/ezform/drawing/' . $nowFileName,
                        'width' => $width,
                        'height' => $height,
                        'newurl' => \yii\helpers\Url::to(['//ezforms2/drawing/bg-image', 'name' => $_GET['name'], 'oldfile' => $nowFileName]),
                ]];
            }
        } else {
            throw new NotFoundHttpException('Ajax only.');
        }
    }

    public function actionOptionImage() {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            //chmod(Yii::$app->basePath . '/../storage/web/drawing/', 0777);
            $uploadFile = \yii\web\UploadedFile::getInstanceByName('option-bg');

            if (isset($_GET['oldfile'])) {
                unlink(Yii::getAlias('@storage/web/ezform/drawing/') . $_GET['oldfile']);
            }

            if ($uploadFile !== null) {
                $idName = '';
                if ($_SERVER["REMOTE_ADDR"] == '::1' || $_SERVER["REMOTE_ADDR"] == '127.0.0.1') {
                    $idName = 'mycom';
                } else {
                    $idName = str_replace('.', '_', $_SERVER["REMOTE_ADDR"]);
                }
                //date("YmdHis")
                $nowFileName = $idName . '_option_' . $_GET['name'] . date('_His') . 'tmp.png';
                $fullPath = Yii::getAlias('@storage/web/ezform/drawing/') . $nowFileName;

                $uploadFile->saveAs($fullPath);
                //chmod(Yii::$app->basePath . '/../storage/web/drawing/'. $nowFileName, 0777);

                list($width, $height, $type, $attr) = getimagesize($fullPath);
                
                return ['files' => [
                        'name' => $nowFileName,
                        'type' => $uploadFile->type,
                        'size' => $uploadFile->size,
                        'url' => \Yii::getAlias('@storageUrl') . '/ezform/drawing/' . $nowFileName,
                        'width' => $width,
                        'height' => $height,
                        'newurl' => \yii\helpers\Url::to(['//ezforms2/drawing/option-image', 'name' => $_GET['name'], 'oldfile' => $nowFileName]),
                ]];
            }
        } else {
            throw new NotFoundHttpException('Ajax only.');
        }
    }

}

?>
