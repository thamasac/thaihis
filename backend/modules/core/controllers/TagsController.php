<?php

namespace backend\modules\core\controllers;

use Yii;
use backend\modules\core\models\CoreTerms;
use backend\modules\core\models\CoreTermsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use backend\modules\core\classes\CoreFunc;
use backend\modules\core\classes\CoreQuery;
use backend\modules\core\models\TagsForm;

/**
 * CoreTermsController implements the CRUD actions for CoreTerms model.
 */
class TagsController extends Controller
{
    public function behaviors()
    {
        return [
	    'access' => [
		'class' => AccessControl::className(),
		'rules' => [
		    [
			'allow' => true,
			'actions' => ['index', 'view', 'terms', 'search-lookup'], 
			'roles' => ['?', '@'],
		    ],
		    [
			'allow' => true,
			'actions' => ['widget', 'view', 'create', 'update', 'delete', 'deletes'], 
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

    public function beforeAction($action) {
	if (parent::beforeAction($action)) {
	    if (in_array($action->id, array('create', 'update', 'terms'))) {
		
	    }
	    return true;
	} else {
	    return false;
	}
    }
    
    /**
     * Lists all CoreTerms models.
     * @return mixed
     */
    public function actionIndex()
    {
	$taxonomy = (isset($_GET['taxonomy']) ? $_GET['taxonomy'] : 'category');
	
        $searchModel = new CoreTermsSearch();
        $dataProvider = $searchModel->dataProvider(Yii::$app->request->queryParams, $taxonomy);
	
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
	    'taxonomy' => $taxonomy,
        ]);
    }

    /**
     * Displays a single CoreTerms model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
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
     * Creates a new CoreTerms model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $taxonomy = (isset($_GET['taxonomy']) ? $_GET['taxonomy'] : 'category');
	    
	    $model = new TagsForm();
	    $model->taxonomy = $taxonomy;
	    
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
		if ($model->save()) {
		    $result = [
			'status' => 'success',
			'action' => 'create',
			'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
			'data' => $model,
		    ];
		    return $result;
		} else {
		    $result = [
			'status' => 'error',
			'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not create the data.'),
			'data' => $model,
		    ];
		    return $result;
		}
	    } else {
		return $this->renderAjax('create', [
		    'model' => $model,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Updates an existing CoreTerms model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $model = new TagsForm;
	    $model->attributes = CoreQuery::getTaxonomyById($id);
	    
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
		
		if ($model->save()) {
		    $result = [
			'status' => 'success',
			'action' => 'update',
			'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
			'data' => $model,
		    ];
		    return $result;
		} else {
		    $result = [
			'status' => 'error',
			'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not update the data.'),
			'data' => $model,
		    ];
		    return $result;
		}
	    } else {
		return $this->renderAjax('update', [
		    'model' => $model,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Deletes an existing CoreTerms model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    try {
		$this->findModel($id)->delete();
		
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
		foreach ($_POST['selection'] as $id) {
		    $this->findModel($id)->delete();
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
    
    public function actionTerms() {
	if (Yii::$app->getRequest()->isAjax) {
	    try {
		Yii::$app->response->format = Response::FORMAT_JSON;
		$taxonomy = (isset($_GET['taxonomy']) ? $_GET['taxonomy'] : 'category');
		$terms = CoreFunc::getTaxonomyDropDownList(0, $taxonomy);
		
		$html = '<option value="">none</option>';
		foreach ($terms as $key => $value) {
		    $html .= '<option value="' . $key . '">' . $value . '</option>';
		}

		$result = [
		    'status' => 'success',
		    'action' => 'deletes',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		    'data' => $terms,
		    'content' => $html,
		];
		return $result;
	    } catch (\yii\db\Exception $e) {
		    $result = [
			'status' => 'error',
			'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
		    ];
		    return $result;
	    }
	} else {
	    throw new HttpException(400, Yii::t('app', 'Invalid request. Please do not repeat this request again.'));
	}
    }
    
    public function actionWidget() {
	if (Yii::$app->getRequest()->isAjax) {
	    try {
		Yii::$app->response->format = Response::FORMAT_JSON;
		$taxonomy = (isset($_GET['taxonomy']) ? $_GET['taxonomy'] : 'post_tag');

		$model = new TagsForm;
		$model->taxonomy = $taxonomy;

		if (isset($_POST['TagsForm']['term_id']) && $_POST['TagsForm']['term_id'] > 0) {
		    $model->attributes = CoreQuery::getTaxonomyById($_POST['TagsForm']['term_id']);
		}

		if ($model->load(Yii::$app->request->post())) {
		    $tagsArr = explode(',', $model->name);
		    $data = [];
		    
		    foreach ($tagsArr as $value) {
			$model->name = strip_tags(trim($value));

			$modelTerm = CoreFunc::saveTerm($model->attributes);

			$data[] = ['id' => $modelTerm['term_taxonomy_id'], 'text' => $model->name];
		    }
		    $result = [
			'status' => 'success',
			'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
			'data' => $data,
		    ];
		    return $result;
		} else {
		    $html = $this->renderAjax('/tags/_widget', array(
			'model' => $model,
		    ));

		    $result = [
			'status' => 'success',
			'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Loading Completed.'),
			'content' => $html,
		    ];
		    return $result;
		}
	    } catch (\yii\db\Exception $e) {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . $e->getCode() . ' : ' . $e->getMessage(),
		];
		return $result;
	    }
	} else {
	    throw new HttpException(400, Yii::t('app', 'Invalid request. Please do not repeat this request again.'));
	}
    }

    public function actionSearchLookup($q = null, $id = null) {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	    $out = ['results' => []];
	    if ($id > 0) {
		$model = CoreTerms::find($id);
		$out['results'] = ['id' => $id, 'text' => $model->name];
	    } else {
		$data = CoreQuery::getTaxonomySelect2($q, 'post_tag');
		$out['results'] = array_values($data);
	    }
	    return $out;
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    /**
     * Finds the CoreTerms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CoreTerms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CoreTerms::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
