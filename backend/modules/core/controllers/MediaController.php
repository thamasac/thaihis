<?php

namespace backend\modules\core\controllers;

use Yii;
use backend\modules\core\models\CorePosts;
use backend\modules\core\models\CorePostsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use backend\modules\core\models\PostsForm;
use backend\modules\core\classes\CoreFunc;

/**
 * PostsController implements the CRUD actions for CorePosts model.
 */
class MediaController extends Controller
{
    public function behaviors()
    {
        return [
	    'access' => [
		'class' => AccessControl::className(),
		'rules' => [
		    [
			'allow' => true,
			'actions' => ['index', 'media'], 
			'roles' => ['@'],
		    ],
		],
	    ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    
    /**
     * Lists all CorePosts models.
     * @return mixed
     */
    public function actionIndex()
    {
	return $this->render('index');
    }

    public function actionMedia() {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    $html = '<iframe width="100%" height="650" src="'.Yii::getAlias('@storageUrl').'/filemanager/dialog.php?type=0'.'">';
	    $result = [
		'status' => 'success',
		'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Load completed.'),
		'html' => $html,
	    ];
	    return $result;
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
}
