<?php

namespace backend\modules\webboard\controllers;

use yii\web\Controller;

/**
 * Default controller for the `webboard` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function actionGetWebboard(){
        if(\Yii::$app->request->isAjax){
            $data = (new \yii\db\Query())
                    ->select(['w.id','w.title','w.create_date', "concat(p.`firstname`,' ', p.`lastname`) as name"])
                    ->from('zdata_webboard as w')
                    ->innerJoin('profile as p', 'w.user_create=p.user_id')
                    ->where('w.rstat <> 3 and w.rstat <> 0')
                    ->orderBy(['id'=>SORT_DESC])->all();
            $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels'=>$data,
                'sort' => [
                    'attributes' => ['id', 'title', 'name'],
                ],
                'pagination' => [
                    'pageSize' => 50,
                ],
            ]); 
            return $this->renderAjax('get-webboard',['dataProvider'=>$dataProvider]); 
        }
    }
    public function actionView()
    {        
        try{
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            $view_post = 0;
            $view_post_str = (new \yii\db\Query())->select('view_post')->from('zdata_webboard')->where('id=:id',[':id'=>$id])->one();
            $view_post = isset($view_post_str['view_post']) ? $view_post_str['view_post'] : 0;
            $view_post += 1;
            \Yii::$app->db->createCommand()->update('zdata_webboard', ['view_post'=>$view_post],['id'=>$id])->execute();
            $data = (new \yii\db\Query())
                        ->select(['w.title','w.id','w.detail','w.create_date', 'p.firstname', 'p.lastname'])
                        ->from('zdata_webboard as w')
                        ->innerJoin('profile as p', 'w.user_create=p.user_id')
                        ->where('w.rstat <> 3 and w.rstat <> 0')
                        ->andWhere('id=:id',[':id'=>$id])
                        ->one();
        } catch (\yii\base\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex->getMessage());
        }
        return $this->render('view',['model'=>$data, 'id'=>$id]); 

    }
    public function actionDelete(){
       if(\Yii::$app->request->isAjax){
           $id = isset($_GET['id']) ? $_GET['id'] : '';
           if(\Yii::$app->db->createCommand()->update('zdata_webboard', ['rstat'=>3],['id'=>$id])->execute()){
               return \backend\modules\manageproject\classes\ChanpanMessage::getSuccess('Success');
           }else{
               return \backend\modules\manageproject\classes\ChanpanMessage::getError('Error');
           }
       }
    }
     
}
