<?php

namespace backend\modules\core\controllers;

use Yii;
use backend\modules\core\models\CoreGenerate;
use backend\modules\core\models\CoreGenerateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use backend\modules\core\models\GenerateFields;
use backend\modules\core\classes\CoreFunc;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;

/**
 * CoreGenerateController implements the CRUD actions for CoreGenerate model.
 */
class CoreGenerateController extends Controller {

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
						'actions' => ['view', 'create', 'update', 'delete', 'deletes', 'create-field', 'update-field', 'delete-field', 'reset-field', 'generate', 'clone'],
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
					'delete-field' => ['post'],
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
	 * Lists all CoreGenerate models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new CoreGenerateSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		$model = new GenerateFields();
		$model->resetData();
		
		return $this->render('index', [
					'searchModel' => $searchModel,
					'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single CoreGenerate model.
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

	public function actionCreateField() {
		if (Yii::$app->getRequest()->isAjax) {
			$session = Yii::$app->session;

			$model = new GenerateFields();
			$model->defaultAttributes();

			if ($model->load(Yii::$app->request->post())) {
				Yii::$app->response->format = Response::FORMAT_JSON;
				
				$model->saveData();

				$result = [
					'status' => 'success',
					'action' => 'create',
					'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
					'data' => $model,
				];
				return $result;
			} else {
				return $this->renderAjax('_form-ui', [
							'model' => $model,
				]);
			}
		} else {
			throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
		}
	}

	public function actionUpdateField($id) {
		if (Yii::$app->getRequest()->isAjax) {
			$session = Yii::$app->session;
			$model = new GenerateFields();
			$model->loadDataOne($id);

			if ($model->load(Yii::$app->request->post())) {
				Yii::$app->response->format = Response::FORMAT_JSON;
				
				$model->saveData($id);

				$result = [
					'status' => 'success',
					'action' => 'update',
					'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
					'data' => $model,
				];
				return $result;
			} else {
				return $this->renderAjax('_form-ui', [
							'model' => $model,
				]);
			}
		} else {
			throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
		}
	}

	public function actionDeleteField($id) {
		if (Yii::$app->getRequest()->isAjax) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			$model = new GenerateFields();
			$model->deleteData($id);
			
			$result = [
				'status' => 'success',
				'action' => 'update',
				'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
				'data' => $id,
			];
			return $result;
		} else {
			throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
		}
	}

	public function actionResetField() {
		if (Yii::$app->getRequest()->isAjax) {
			Yii::$app->response->format = Response::FORMAT_JSON;

			$model = new GenerateFields();
			$model->resetData();
			
			$result = [
				'status' => 'success',
				'action' => 'update',
				'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Reset data completed.'),
				'data' => '',
			];
			return $result;
		} else {
			throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
		}
	}

	public function actionGenerate($id) {
		$modelFields = $this->findModel($id);
		
		$modelFields->gen_ui = $modelFields->gen_ui;
		
		$model = CoreFunc::setDynamicModel($modelFields->gen_ui);

		if (isset($_POST['DynamicModel'])) {
			$model->attributes = $_POST['DynamicModel'];
			$fields = $_POST['DynamicModel'];

			Yii::$app->session->setFlash('alert', [
				'body' => SDHtml::getMsgSuccess() . Yii::t('app', 'Generate completed.'),
				'options' => ['class' => 'alert-success']
			]);
		}

		return $this->render('generate', [
					'model' => $model,
					'modelFields' => $modelFields,
		]);
	}

	public function actionClone($id) {
		$model = new CoreGenerate();
		$modelUi = new GenerateFields();
		$modelFields = $this->findModel($id);
		
		$model->attributes = $modelFields->attributes;
		
		$modelUi->loadDataAll($model->gen_ui);

		if ($model->load(Yii::$app->request->post())) {
			if ($model->save()) {
				Yii::$app->session->setFlash('alert', [
					'body' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
					'options' => ['class' => 'alert-success']
				]);
				return $this->redirect(['index', 'id' => $model->gen_id]);
			} else {
				Yii::$app->session->setFlash('alert', [
					'body' => SDHtml::getMsgError() . Yii::t('app', 'Can not create the data.'),
					'options' => ['class' => 'alert-danger']
				]);
			}
		} else {
			return $this->render('create', [
						'model' => $model,
						'modelUi' => $modelUi,
			]);
		}
	}

	/**
	 * Creates a new CoreGenerate model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new CoreGenerate();
		$modelUi = new GenerateFields();

		if ($model->load(Yii::$app->request->post())) {
			if ($model->save()) {
				Yii::$app->session->setFlash('alert', [
					'body' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
					'options' => ['class' => 'alert-success']
				]);
				return $this->redirect(['index', 'id' => $model->gen_id]);
			} else {
				Yii::$app->session->setFlash('alert', [
					'body' => SDHtml::getMsgError() . Yii::t('app', 'Can not create the data.'),
					'options' => ['class' => 'alert-danger']
				]);
			}
		} else {
			return $this->render('create', [
						'model' => $model,
						'modelUi' => $modelUi,
			]);
		}
	}

	/**
	 * Updates an existing CoreGenerate model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id) {
		$model = $this->findModel($id);
		$modelUi = new GenerateFields();
		$modelUi->loadDataAll($model->gen_ui);
		
		if ($model->load(Yii::$app->request->post())) {
			if ($model->save()) {
				Yii::$app->session->setFlash('alert', [
					'body' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
					'options' => ['class' => 'alert-success']
				]);
			} else {
				Yii::$app->session->setFlash('alert', [
					'body' => SDHtml::getMsgError() . Yii::t('app', 'Can not create the data.'),
					'options' => ['class' => 'alert-danger']
				]);
			}
                        return $this->redirect(['index', 'id' => $model->gen_id]);
		} else {
			return $this->render('update', [
						'model' => $model,
						'modelUi' => $modelUi,
			]);
		}
	}

	/**
	 * Deletes an existing CoreGenerate model.
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
	 * Finds the CoreGenerate model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return CoreGenerate the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = CoreGenerate::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

}
