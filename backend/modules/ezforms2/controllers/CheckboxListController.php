<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;

/**
 * CheckboxListController implements the CRUD actions for EzformInput model.
 */
class CheckboxListController extends Controller
{
    
    public function actionCreate()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    
	    $row = isset($_POST['row'])?$_POST['row']:0;
	    $other = isset($_POST['other'])?$_POST['other']:'';
            $attr = isset($_POST['attr'])?$_POST['attr']:'';
            
	    $html = $this->renderAjax('//../modules/ezbuilder/views/widgets/checkboxlist/_formitem', [
		'row' => $row,
                'other' => $other,
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
    
    public function actionOther()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    
	    $row = isset($_POST['row'])?$_POST['row']:0;
            $other = isset($_POST['other'])?$_POST['other']:'';
	    $id = isset($_POST['id'])?$_POST['id']:0;
            $type = isset($_POST['type'])?$_POST['type']:'text';
            
	    $html = $this->renderAjax('//../modules/ezbuilder/views/widgets/checkboxlist/_other_item', [
		'row' => $row,
                'other' => $other,
                'id' => $id,
                'type' => $type,
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
