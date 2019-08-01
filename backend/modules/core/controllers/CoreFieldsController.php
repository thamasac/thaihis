<?php

namespace backend\modules\core\controllers;

use Yii;
use backend\modules\core\models\CoreFields;
use backend\modules\core\models\CoreFieldsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;

/**
 * CoreFieldsController implements the CRUD actions for CoreFields model.
 */
class CoreFieldsController extends Controller {

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

	public function beforeAction($action) {
		if (parent::beforeAction($action)) {
			if (in_array($action->id, array('create', 'update'))) {
				
			}
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Lists all CoreFields models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new CoreFieldsSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		return $this->render('index', [
					'searchModel' => $searchModel,
					'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single CoreFields model.
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
	 * Creates a new CoreFields model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		if (Yii::$app->getRequest()->isAjax) {
			$model = new CoreFields();
			
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
	 * Updates an existing CoreFields model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param string $id
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
	 * Deletes an existing CoreFields model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param string $id
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
	 * Finds the CoreFields model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param string $id
	 * @return CoreFields the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = CoreFields::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

}
