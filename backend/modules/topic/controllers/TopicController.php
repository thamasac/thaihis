<?php

namespace backend\modules\topic\controllers;

use Yii;
use backend\modules\topic\models\Topic;
use backend\modules\topic\models\TopicSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;

/**
 * TopicController implements the CRUD actions for Topic model.
 */
class TopicController extends Controller
{
    public function behaviors()
    {
        return [
/*	    'access' => [
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
	    ],*/
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
    
    public function actionCheckTopic(){
        $options = isset($_GET['options']) ? $_GET['options'] : "";
        return $this->renderAjax("check-topic",['options'=>$options]);
    }
    /**
     * Lists all Topic models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->getRequest()->isAjax) {
            $options = isset($_GET['options']) ? $_GET['options'] : "";
            $data = Topic::find()
                    ->where([
                                'status'=>'1',
                                'module_id'=>$options['module_id'],
                                'widget_id'=>$options['widget_id']
                            ])
                    ->andWhere('rstat <> 3')
                    ->one(); 
            $status = 0;
            if(empty($data)){
                $status = 1;
            }                
            $options = isset($_GET['options']) ? $_GET['options'] : '';         
            return $this->renderAjax('index', [
                'options'=>$options,
                'status'=>$status
            ]);
        }
    }

    /**
     * Displays a single Topic model.
     * @param integer $id
     * @return mixed
     */
    public function actionGetTopicAll()
    {
        $options = isset($_GET['options']) ? $_GET['options'] : "";
	$model = Topic::find()
                    ->where([
                                'status'=>'1',
                                'module_id'=>$options['module_id'],
                                'widget_id'=>$options['widget_id'],
                                'create_by'=> \Yii::$app->user->id
                            ])
                    ->andWhere('rstat <> 3')
                    ->one();
            //\appxq\sdii\utils\VarDumper::dump(\Yii::$app->user->id);
        //order by rand() limit 1
        return $this->renderAjax('topic-all',[
            'value'=>$model,
            'options'=>$options
        ]);
    }

    /**
     * Creates a new Topic model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $model = new Topic();
            $model->module_id = isset($_GET['options']['module_id']) ? $_GET['options']['module_id'] : '';
            $model->widget_id = isset($_GET['options']['widget_id']) ? $_GET['options']['widget_id'] : '';
            $model->rstat = 1;
            $model->status = 1;
            $model->create_by = Yii::$app->user->id;
            $model->create_at = Date('Y-m-d H:i:s');
            $options = isset($_GET['options']) ? $_GET['options'] : "";
            
	    if ($model->load(Yii::$app->request->post())) {
                $model->icon = $options['icon'];
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
                    'options'=>$options
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Updates an existing Topic model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $model = $this->findModel($id);
            $options = isset($_GET['options']) ? $_GET['options'] : "";
	    if ($model->load(Yii::$app->request->post())) {
                $model->icon = $options['icon'];
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
                    'options'=>$options
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Deletes an existing Topic model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
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
    public function actionSetShow(){
        $show = isset($_POST['show']) ? $_POST['show'] : 1;
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        //$status = isset($_POST['status']) ? $_POST['status'] : '';
        $data=[
            'show'=>$show
        ];
        if(Yii::$app->db->createCommand()->update('topic', $data, ['id'=>$id])->execute()){
            return $show;
        }else{
            return $show;
        }
        
    }


    /**
     * Finds the Topic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Topic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Topic::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionDeleteRole(){
        $role_name = isset($_POST['role_name']) ? $_POST['role_name'] : '';
        $data = (new \yii\db\Query())->select('*')->from('zdata_role')->where('role_name=:role_name and rstat = 3', [':role_name'=>$role_name])->one();
        if(!empty($data)){
            if(Yii::$app->db->createCommand()->delete('zdata_role', ['role_name'=>$role_name])->execute()){
                return \backend\modules\manageproject\classes\CNMessage::getSuccess('Success');
            }else{
                return \backend\modules\manageproject\classes\CNMessage::getError("Error");
            }
        }
    }
    
    
}
