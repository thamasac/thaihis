<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;

/**
 * GridController implements the CRUD actions for EzformInput model.
 */
class GridController extends Controller
{
    
    public function actionRow()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    
	    $row = isset($_POST['row'])?$_POST['row']:0;
            $col = isset($_POST['col'])?$_POST['col']:0;
            $attr = isset($_POST['attr'])?$_POST['attr']:'';
            $header = isset($_POST['header'])?$_POST['header']:[];
            
            $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            
	    $html = $this->renderAjax('//../modules/ezbuilder/views/widgets/grid/_form_row', [
		'row' => $row,
                'col'=>$col,
                'attr' => $attr,
                'id' => $id,
                'header'=>$header,
	    ]);
	    
	    $result = [
		'status' => 'success',
		'action' => 'create',
		'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
		'html' => $html,
                'id' => $id,
	    ];
	    return $result;
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionCol()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    
	    $row = isset($_POST['row'])?$_POST['row']:0;
            $col = isset($_POST['col'])?$_POST['col']:0;
            $attr = isset($_POST['attr'])?$_POST['attr']:'';
            $id = isset($_POST['id'])?$_POST['id']:0;
            
            
	    $html = $this->renderAjax('//../modules/ezbuilder/views/widgets/grid/_form_col', [
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
    
    public function actionFields()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    
	    $row = isset($_POST['row'])?$_POST['row']:0;
            $col = isset($_POST['col'])?$_POST['col']:0;
            $attr = isset($_POST['attr'])?$_POST['attr']:'';
            $id = isset($_POST['id'])?$_POST['id']:0;
            
	    $html = $this->renderAjax('//../modules/ezbuilder/views/widgets/grid/_form_fields', [
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
