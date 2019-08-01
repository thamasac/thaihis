<?php

namespace backend\modules\log\controllers;

use yii\web\Controller;

/**
 * Default controller for the `log` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $dynamicDb = \backend\modules\log\models\DynamicDb::find()->where("rstat not in(0,3) AND url <> ''");
        $search = \Yii::$app->request->get('term');
        $id = \Yii::$app->request->get('id');
        if($search){
            
            $dynamicDb = $dynamicDb->where('id=:id',[':id'=>$search]);
        } 
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $dynamicDb,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'defaultOrder' => [
                    'create_at' => SORT_DESC 
                ]
            ],
        ]); 
        $errorProvider = new \yii\data\ArrayDataProvider([
            'allModels' => [],
        ]);
        try{
            if($id != ''){
                $term_err = \Yii::$app->request->get('term_err');
                $model = \backend\modules\log\models\DynamicDb::findOne($id);
                $dbname = isset($model['dbname'])?$model['dbname']:'';
                $sql="SELECT * FROM `{$dbname}`.system_error";
                $params = [];

                if($term_err){
                    $sql = $sql." WHERE message LIKE :msg OR created_at LIKE :create";
                    $params = [
                        ':msg'=>"%{$term_err}%",
                        ':create'=>"%{$term_err}%"    
                    ];
                }
                $data = \Yii::$app->db->createCommand($sql,$params)->queryAll();

              $errorProvider = new \yii\data\ArrayDataProvider([
                'allModels' => $data,
                    'sort' => [
                        'attributes' => ['created_at','message'],
                    ],
                    'pagination' => [
                        'pageSize' => 100,
                    ],
                ]);
            }
        } catch (\yii\db\Exception $ex) {

        }
        return $this->render('index',[
            'dataProvider'=>$dataProvider,
            'errorProvider'=>$errorProvider
       ]);
        
    }
    
    public function actionView(){
         $id = \Yii::$app->request->get('id');
         $errorid = \Yii::$app->request->get('errorid');
         if($id != ''){
           try{
                $model = \backend\modules\log\models\DynamicDb::findOne($id);
                $dbname = isset($model['dbname'])?$model['dbname']:'';
                //\appxq\sdii\utils\VarDumper::dump($id);
                
                $sql="SELECT * FROM `{$dbname}`.system_error WHERE id=:id";
                $data = \Yii::$app->db->createCommand($sql,[':id'=>$errorid])->queryOne();
                
                
                return $this->renderAjax("view",['model'=>$data]);
                
           } catch (\yii\db\Exception $ex) {
               return \backend\modules\manageproject\classes\CNMessage::getError("Error {$ex->getMessage()}");
           }
         }
    }
    
    public function actionGetCount(){
        try{
            $id = \Yii::$app->request->get('id');
            $model = \backend\modules\log\models\DynamicDb::findOne($id);
            $dbname = isset($model['dbname']) ? $model['dbname'] : '';
            $sql = "SELECT count(*) FROM `{$dbname}`.system_error";
            $data = \Yii::$app->db->createCommand($sql)->queryScalar();
            return $data;
        } catch (\yii\db\Exception $ex) {

        }
    }
    
    public function actionDelete(){
         $id = \Yii::$app->request->post('id');
         $errorid = \Yii::$app->request->post('errorid');
         if($id != ''){
           try{
                $model = \backend\modules\log\models\DynamicDb::findOne($id);
                $dbname = isset($model['dbname'])?$model['dbname']:'';
                $sql="DELETE FROM `{$dbname}`.system_error WHERE id=:id";
                $data = \Yii::$app->db->createCommand($sql,[':id'=>$errorid])->execute();
                if($data){
                    return \backend\modules\manageproject\classes\CNMessage::getSuccess("Success");
                }else{
                    return \backend\modules\manageproject\classes\CNMessage::getError("Error");
                }
           } catch (\yii\db\Exception $ex) {
               return \backend\modules\manageproject\classes\CNMessage::getError("Error {$ex->getMessage()}");
           }
         }
    }
    
    
    public function actionClear(){
         $id = \Yii::$app->request->post('id');
         if($id != ''){
            try{
                $model = \backend\modules\log\models\DynamicDb::findOne($id);
                $dbname = isset($model['dbname'])?$model['dbname']:'';
                $sql="DELETE FROM `{$dbname}`.system_error";
                $data = \Yii::$app->db->createCommand($sql)->execute();
                if($data){
                    return \backend\modules\manageproject\classes\CNMessage::getSuccess("Success");
                }else{
                    return \backend\modules\manageproject\classes\CNMessage::getError("Error");
                }
            } catch (\yii\db\Exception $ex) {
                return \backend\modules\manageproject\classes\CNMessage::getError("Error");
            }
         }
         
     }
     
     public function actionGetUrl($q = '', $id = ''){
        try {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $out = ['results' => ['id' => '', 'text' => '']];
            if ($id == null) {
                $data_all = \backend\modules\log\models\DynamicDb::find()
                                ->select(['id', 'url'])
                                ->where('url LIKE :url', [":url" => "%{$q}%"])
                                ->limit(50)->all();
                $data = [];
                foreach ($data_all as $k => $c) {
                    $data[$k] = ['id' => $c['id'], 'text' => $c['url']];
                }
                $out['results'] = array_values($data);
            } else {
                $data_one = \backend\modules\log\models\DynamicDb::findOne($id);
                $out = ['id' => $data_one['id'], 'text' => $data_one['url']];
            }
            return $out;
        } catch (\yii\db\Exception $ex) {
            return false;
        }
    }
     
}
