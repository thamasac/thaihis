<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;

/**
 * ScaleController implements the CRUD actions for EzformInput model.
 */
class ScaleController extends Controller
{
    
    public function actionCreate()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    
	    $row = isset($_POST['row'])?$_POST['row']:0;
            $col = isset($_POST['col'])?$_POST['col']:0;
            $attr = isset($_POST['attr'])?$_POST['attr']:'';
            
	    $html = $this->renderAjax('//../modules/ezbuilder/views/widgets/scale/_form_field', [
		'row' => $row,
                'col'=>$col,
                'attr' => $attr,
	    ]);
	    
	    $result = [
		'status' => 'success',
		'action' => 'create',
		'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
		'html' => $html,
	    ];
	    return $result;
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionLvl()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    
	    $row = isset($_POST['row'])?$_POST['row']:0;
            $col = isset($_POST['col'])?$_POST['col']:0;
            $attr = isset($_POST['attr'])?$_POST['attr']:'';
            $id = isset($_POST['id'])?$_POST['id']:0;
            
	    $html = $this->renderAjax('//../modules/ezbuilder/views/widgets/scale/_form_lvl', [
		'row' => $row,
                'col'=>$col,
                'attr' => $attr,
                'id'=>$id,
	    ]);
	    
	    $result = [
		'status' => 'success',
		'action' => 'create',
		'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
		'html' => $html,
	    ];
	    return $result;
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

}
