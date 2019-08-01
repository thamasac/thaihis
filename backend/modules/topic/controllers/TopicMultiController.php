<?php
 
namespace backend\modules\topic\controllers;
use Yii;
use yii\web\Controller;
use backend\modules\topic\classes\ResponseData;
use backend\modules\topic\models\Topic;

class TopicMultiController extends Controller{
    public function actionIndex(){
        if (Yii::$app->getRequest()->isAjax) {
            $options = isset($_GET['options']) ? $_GET['options'] : "";
            return $this->renderAjax('index',['options'=>$options]);
        }
    }//หน้าแรกของ topicmulti
    public function actionGetData(){
        $module_id = isset($_GET['options']['module_id']) ? $_GET['options']['module_id'] : '';
        $widget_id = isset($_GET['options']['widget_id']) ? $_GET['options']['widget_id'] : '';
        $sql="SELECT * FROM topic WHERE module_id=:module_id AND widget_id=:widget_id AND status = 2 AND rstat <> 3 ORDER BY RAND() LIMIT 1";
        $params=[
            ':module_id' => $module_id,
            ':widget_id' => $widget_id
        ];
        $data = Yii::$app->db->createCommand($sql,$params)->queryOne();        
        $options = isset($_GET['options']) ? $_GET['options'] : "";
        return $this->renderAjax('get-data',[
            'data'=>$data,
            'options'=>$options
        ]);
    }//โหลดข้อมูลเพื่อนำมาแสดง 
    public function actionManage(){
        $options = isset($_GET['options']) ? $_GET['options'] : "";
        return $this->renderAjax("manage",[
            'options'=>$options
        ]);
    }//ทำหน้าที่จัดการข้อมูล topic
    
    public function actionGetForm(){
        if (Yii::$app->getRequest()->isAjax) {
            $options = isset($_GET['options']) ? $_GET['options'] : "";
            
	    $model =    Topic::find()->where(['status'=>'2','module_id'=>$options['module_id'],'widget_id'=>$options['widget_id']])->andWhere('rstat <> 3')->orderBy(['id'=>SORT_DESC])->all();
            $model->module_id = isset($_GET['options']['module_id']) ? $_GET['options']['module_id'] : '';
            $model->widget_id = isset($_GET['options']['widget_id']) ? $_GET['options']['widget_id'] : '';
            $model->rstat = 1;
            $model->create_by = Yii::$app->user->id;
            $model->create_at = Date('Y-m-d H:i:s');
            return $this->renderAjax('form', [
		    'model' => $model,
                    'options'=>$options
            ]);
        }
    }
    public function actionSaveForm(){
        if (Yii::$app->getRequest()->isAjax) {
            $options = isset($_GET['options']) ? $_GET['options'] : "";
	    $model = new  Topic();
            $model->module_id = isset($_GET['options']['module_id']) ? $_GET['options']['module_id'] : '';
            $model->widget_id = isset($_GET['options']['widget_id']) ? $_GET['options']['widget_id'] : '';
            $model->rstat = 1;
            $model->status = 2;
            $model->create_by = Yii::$app->user->id;
            $model->create_at = Date('Y-m-d H:i:s');
            $model->update_by = Yii::$app->user->id;
            $model->update_at = Date('Y-m-d H:i:s');
            $model->name = "";
            $model->icon = $options['icon'];
            $model->detail = "";
            if($model->save()){
               return ResponseData::Success(Yii::t('chanpan','Save success'));
            }else{
               return ResponseData::Error(Yii::t('chanpan','Save error'));
            }
        }
    }
    public function actionUpdate(){
        if (Yii::$app->getRequest()->isAjax) {
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $name = isset($_POST['name']) ? $_POST['name'] : '';
            $value = isset($_POST['value']) ? $_POST['value'] : '';
            $model =  Topic::findOne($id);
            $model[$name] = $value;
             
            if($model->save()){
               return  ResponseData::Success(Yii::t('chanpan','Save success'));
            }else{
               return  ResponseData::Error(Yii::t('chanpan','Save error'));
            }
        }
    }
    public function actionDelete($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $model =  Topic::findOne($id);
	    if ($model->delete()) {
		return  ResponseData::Success(Yii::t('chanpan','Delete success'));
	    } else {
		return  ResponseData::Error(Yii::t('chanpan','Save error'));
	    }
	} else {
	    throw new \yii\web\NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
}
