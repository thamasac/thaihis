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
use backend\modules\core\classes\CoreQuery;

/**
 * PostsController implements the CRUD actions for CorePosts model.
 */
class PostsController extends Controller {

    public function behaviors() {
	return [
	    'access' => [
		'class' => AccessControl::className(),
		'rules' => [
		    [
			'allow' => true,
			'actions' => ['index', 'view'],
			'roles' => ['?', '@'],
		    ],
		    [
			'allow' => true,
			'actions' => ['view', 'create', 'update', 'delete', 'deletes'],
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
    public function actionIndex() {
	$type = (isset($_GET['type']) ? $_GET['type'] : 'post');
	$status = (isset($_GET['status']) ? $_GET['status'] : '');

	$searchModel = new CorePostsSearch();
	$searchModel->post_type = $type;
	$searchModel->post_status = $status;

	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

	return $this->render('index', [
		    'searchModel' => $searchModel,
		    'dataProvider' => $dataProvider,
		    'type' => $type,
		    'status' => $status,
	]);
    }

    /**
     * Displays a single CorePosts model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
	if (Yii::$app->getRequest()->isAjax) {
	    return $this->renderAjax('view', [
			'model' => $this->findModel($id),
	    ]);
	} else {
	    return $this->render('view', [
			'model' => $this->findModel($id),
	    ]);
	}
    }

    /**
     * Creates a new CorePosts model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
	$type = (isset($_GET['type']) ? $_GET['type'] : 'post');

	$model = new PostsForm();
	$model->comment_status = true;
	$model->ping_status = true;
	$model->post_type = $type;
	$model->post_format = 0;

	if ($model->load(Yii::$app->request->post())) {
	    $model->post_author = Yii::$app->user->id;
	    $model->post_date = date("Y-m-d H:i:s");
	    $model->post_date_gmt = gmdate("Y-m-d H:i:s");
	    $model->post_modified = date("Y-m-d H:i:s");
	    $model->post_modified_gmt = gmdate("Y-m-d H:i:s");
	    $model->comment_status = CoreFunc::valueToName($model->comment_status);
	    $model->ping_status = CoreFunc::valueToName($model->ping_status);

	    $modelPost = new CorePosts;
	    $modelPost->attributes = $model->attributes;

	    if ($modelPost->save()) {
		if (in_array($type, Yii::$app->controller->module->hasParentPost)) {
		    if ($model->page_template != '') {
			CoreFunc::savePostMeta($modelPost->ID, 'page_template', $model->page_template);
		    }
		} else {
		    if ($model->sticky_posts) {
			CoreFunc::saveStickyPost($modelPost->ID, $model->sticky_posts);
		    }

		    if (is_array($model->categories)) {
			CoreFunc::addTermRelationships($modelPost->ID, $model->categories, 'category');
		    }

		    if ($model->tags_id != '') {
			$tagsArr = explode(',', $model->tags_id);
			CoreFunc::addTermRelationships($modelPost->ID, $tagsArr, 'post_tag');
		    }

		    if ($model->post_format != '') {
			CoreFunc::savePostMeta($modelPost->ID, 'post_format', $model->post_format);
		    }
		}

		Yii::$app->session->setFlash('alert', [
		    'body' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
		    'options' => ['class' => 'alert-success']
		]);
		return $this->redirect(['index']);
	    } else {
		Yii::$app->session->setFlash('alert', [
		    'body' => SDHtml::getMsgError() . Yii::t('app', 'Can not create the data.'),
		    'options' => ['class' => 'alert-danger']
		]);
	    }
	} else {
	    return $this->render('create', [
			'model' => $model,
			'type' => $type,
	    ]);
	}
    }

    /**
     * Updates an existing CorePosts model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
	$type = (isset($_GET['type']) ? $_GET['type'] : 'post');

	$modelPost = $this->findModel($id);

	if ($modelPost->post_status == 'trash') {
	    throw new \yii\web\HttpException(404, Yii::t('app', 'You can’t edit this item because it is in the Trash. Please restore it and try again.'));
	}

	$model = new PostsForm();
	$model->attributes = $modelPost->attributes;
	$model->comment_status = CoreFunc::nameToValue($model->comment_status);
	$model->ping_status = CoreFunc::nameToValue($model->ping_status);

	if (in_array($type, Yii::$app->controller->module->hasParentPost)) {
	    $model->page_template = CoreFunc::getPostMetaValue($modelPost->ID, 'page_template');
	} else {
	    $model->sticky_posts = CoreFunc::getStickyPost($modelPost->ID);
	    $model->categories = CoreFunc::getTermRelationships($modelPost->ID, 'category');
	    $model->tags_id = CoreFunc::getTermRelationships($modelPost->ID, 'post_tag', false);
	    $model->post_format = CoreFunc::getPostMetaValue($modelPost->ID, 'post_format');
	}

	if ($model->load(Yii::$app->request->post())) {

	    $model->post_modified = date("Y-m-d H:i:s");
	    $model->post_modified_gmt = gmdate("Y-m-d H:i:s");
	    $model->comment_status = CoreFunc::valueToName($model->comment_status);
	    $model->ping_status = CoreFunc::valueToName($model->ping_status);

	    $modelPost->attributes = $model->attributes;

	    if ($model->save()) {
		if ($modelPost->post_author != Yii::$app->user->id) {
		    CoreFunc::savePostMeta($modelPost->ID, 'modified_author', Yii::$app->user->id);
		}

		if (in_array($type, Yii::$app->controller->module->hasParentPost)) {
		    if ($model->page_template != '') {
			CoreFunc::savePostMeta($modelPost->ID, 'page_template', $model->page_template);
		    }
		} else {
		    if ($model->sticky_posts) {
			CoreFunc::saveStickyPost($modelPost->ID, $model->sticky_posts);
		    }

		    if (is_array($model->categories)) {
			CoreFunc::addTermRelationships($modelPost->ID, $model->categories, 'category');
		    }

		    if ($model->tags_id != '') {
			$tagsArr = explode(',', $model->tags_id);
			CoreFunc::addTermRelationships($modelPost->ID, $tagsArr, 'post_tag');
		    }

		    if ($model->post_format != '') {
			CoreFunc::savePostMeta($modelPost->ID, 'post_format', $model->post_format);
		    }
		}

		Yii::$app->session->setFlash('alert', [
		    'body' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
		    'options' => ['class' => 'alert-success']
		]);
		return $this->redirect(['index', 'id' => $id]);
	    } else {
		Yii::$app->session->setFlash('alert', [
		    'body' => SDHtml::getMsgError() . Yii::t('app', 'Can not create the data.'),
		    'options' => ['class' => 'alert-danger']
		]);
	    }
	} else {
	    return $this->render('update', [
			'model' => $model,
			'type' => $type,
	    ]);
	}
    }

    /**
     * Deletes an existing CorePosts model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    $type = (isset($_GET['type']) ? $_GET['type'] : 'post');

	    try {
		CoreFunc::deletePostByTrash($id, $type);
		$result = [
		    'status' => 'success',
		    'action' => 'update',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		    'data' => $id,
		];
		return $result;
	    } catch (\yii\db\Exception $e) {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
		    'data' => $id,
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    public function actionDeletes() {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    if (isset($_POST['selection'])) {
		$type = (isset($_GET['type']) ? $_GET['type'] : 'post');

		foreach ($_POST['selection'] as $id) {
		    CoreFunc::deletePostByTrash($id, $type);
		}
		$result = [
		    'status' => 'success',
		    'action' => 'deletes',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		    'data' => $_POST['selection'],
		];
		return $result;
	    } else {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
		    'data' => $id,
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    public function actionRestore($id) {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    $model = $this->findModel($id);
	    $modelMeta = CoreQuery::getPostMetaByPostKey($id, 'trash_meta_status');
	    $model->post_status = isset($modelMeta->meta_value) ? $modelMeta->meta_value : 'publish';

	    if ($model->save()) {
		CoreFunc::deleteTrashMetaByPostId($id);

		$result = [
		    'status' => 'success',
		    'action' => 'update',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		    'data' => $id,
		];
		return $result;
	    } else {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
		    'data' => $id,
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    public function actionRestores() {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    if (isset($_POST['selection'])) {
		foreach ($_POST['selection'] as $id) {
		    $model = $this->findModel($id);
		    $modelMeta = CoreQuery::getPostMetaByPostKey($id, 'trash_meta_status');
		    $model->post_status = isset($modelMeta->meta_value) ? $modelMeta->meta_value : 'publish';

		    if ($model->save()) {
			CoreFunc::deleteTrashMetaByPostId($id);
		    }
		}
		$result = [
		    'status' => 'success',
		    'action' => 'deletes',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		    'data' => $_POST['selection'],
		];
		return $result;
	    } else {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
		    'data' => $id,
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    public function actionRenderTab() {
	if (Yii::$app->getRequest()->isAjax) {
	    $type = (isset($_GET['type']) ? $_GET['type'] : 'post');
	    $status = (isset($_GET['status']) ? $_GET['status'] : '');

	    $html = $this->renderPartial('/posts/_tab', [
		'type' => $type,
		'status' => $status,
		    ]
	    );

	    $result = [
		'status' => 'success',
		'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Loading Completed.'),
		'content' => $html,
	    ];
	    return $result;
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    public function actionPreview($id) {
	if (Yii::$app->getRequest()->isAjax) {
	    try {
		$model = $this->findModel($id);

		$html = $this->renderAjax('/posts/_view', array(
		    'model' => $model,
			), TRUE);

		$result = [
		    'status' => 'success',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Loading Completed.'),
		    'content' => $html,
		];
		return $result;
	    } catch (\yii\db\Exception $e) {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not load the data.'),
		    'data' => $id,
		];
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Finds the CorePosts model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CorePosts the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
	if (($model = CorePosts::findOne($id)) !== null) {
	    return $model;
	} else {
	    throw new NotFoundHttpException('The requested page does not exist.');
	}
    }

}
