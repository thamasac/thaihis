<?php

namespace backend\modules\core\controllers;

use Yii;
use backend\modules\core\models\TablesFields;
use backend\modules\core\models\TablesFieldsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use backend\modules\core\classes\CoreFunc;
use appxq\sdii\helpers\SDHtml;

/**
 * TablesFieldsController implements the CRUD actions for TablesFields model.
 */
class TablesFieldsController extends Controller {

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
	 * Lists all TablesFields models.
	 * @return mixed
	 */
	public function actionIndex() {
		$table = $this->getTable($_GET['table']);

		$searchModel = new TablesFieldsSearch();
		$searchModel->table_name = $table;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
					'searchModel' => $searchModel,
					'dataProvider' => $dataProvider,
					'table' => $table,
		]);
	}

	/**
	 * Displays a single TablesFields model.
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
	 * Creates a new TablesFields model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		if (Yii::$app->getRequest()->isAjax) {
			$table = $this->getTable($_GET['table']);

			$model = new TablesFields();
			$model->table_name = $table;
			
			if ($model->load(Yii::$app->request->post())) {
				Yii::$app->response->format = Response::FORMAT_JSON;
				$model->table_name = $table;

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
	 * Updates an existing TablesFields model.
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
	 * Deletes an existing TablesFields model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id) {
		if (Yii::$app->getRequest()->isAjax) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			$model = $this->findModel($id);
			if ($model->delete()) {
				
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
	 * Finds the TablesFields model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return TablesFields the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = TablesFields::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	protected function getTable($table) {
		$tableAlias = CoreFunc::itemAlias('tables_fields', isset($table) ? $table : '');
		if ($tableAlias) {
			return $table;
		} else {
			throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
		}
	}

}
