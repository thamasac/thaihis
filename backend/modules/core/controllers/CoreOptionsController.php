<?php

namespace backend\modules\core\controllers;

use Yii;
use backend\modules\core\models\CoreOptions;
use backend\modules\core\models\CoreOptionsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use backend\modules\core\classes\CoreQuery;
use backend\modules\core\classes\CoreFunc;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;

/**
 * CoreOptionsController implements the CRUD actions for CoreOptions model.
 */
class CoreOptionsController extends Controller {

	public function behaviors() {
		return [
			
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
			if (in_array($action->id, array('create', 'update'))) {
				
			}
			return true;
		} else {
			return false;
		}
	}

	public function actionConfig() {
		$term = isset($_GET['term']) ? $_GET['term'] : 'company';

		$modelFields = CoreQuery::getAllOptions($term);
		$model = CoreFunc::setDynamicModel($modelFields);

		if ($model->load(Yii::$app->request->post())) {
			foreach ($model->attributes as $key => $value) {
				CoreQuery::updateOptions($key, $value);
			}

			Yii::$app->session->setFlash('alert', [
				'body' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
				'options' => ['class' => 'alert-success']
			]);
		}

		return $this->render('config', [
					'model' => $model,
					'modelFields' => $modelFields,
					'term' => $term,
		]);
	}
        
	/**
	 * Lists all CoreOptions models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new CoreOptionsSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                
		return $this->render('index', [
					'searchModel' => $searchModel,
					'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single CoreOptions model.
	 * @param integer $id
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
	 * Creates a new CoreOptions model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		if (Yii::$app->getRequest()->isAjax) {
			$model = new CoreOptions();
			
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
	 * Updates an existing CoreOptions model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id) {
		if (Yii::$app->getRequest()->isAjax) {
			$model = $this->findModel($id);
			
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
	 * Deletes an existing CoreOptions model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id) {
		if (Yii::$app->getRequest()->isAjax) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			if ($this->findModel($id)->delete()) {
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

	/**
	 * Finds the CoreOptions model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return CoreOptions the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = CoreOptions::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

}
