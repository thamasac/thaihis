<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\ezforms2\controllers;

/**
 * Description of RandomizationController
 *
 * @author AR Soft
 */
class RandomizationController extends \yii\web\Controller {

    //put your code here

    public function actionAddCode() {
        return $this->renderAjax('_add-code');
    }

    public function actionForm() {
        $dataSitecode = (new \yii\db\Query())
                ->select(['site_name as id', 'CONCAT(site_detail,\' (\',site_name,\')\') as sitecode'])
                ->from('zdata_sitecode')
                ->where('site_detail is not null AND site_name is not null')
                ->all();
        return $this->renderAjax('_form', ['dataSitecode' => $dataSitecode]);
    }

    public function actionAdd() {
        $post = \Yii::$app->request->post();
        if (!empty($post) && is_array($post)) {
            try {
                (new \yii\db\Query())->createCommand()->insert('random_code', $post)->execute();
                return TRUE;
            } catch (\yii\db\Exception $error) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
                return FALSE;
            }
        }
    }
    public function actionUpdate() {
        $post = \Yii::$app->request->post();
        if (!empty($post) && is_array($post)) {
            try {
                (new \yii\db\Query())->createCommand()->update('random_code', $post,['id'=>$post['id']])->execute();
                return TRUE;
            } catch (\yii\db\Exception $error) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
                return FALSE;
            }
        }
    }
    
    public function actionUpdateCode() {
        
        $id= \Yii::$app->request->get('id','');
        $data = (new \yii\db\Query())->select('*')->from('random_code')->where(['id'=>$id])->one();
        return $this->renderAjax('_add-code',['data' => $data]);
    }

    public function actionSelect($q = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
        $data = (new \yii\db\Query())->select('id,name')->from('random_code')->where('name LIKE :q', [':q' => "%$q%"])->limit(20)->all();

        foreach ($data as $value) {
            $out["results"][] = ['id' => "{$value['id']}", 'text' => $value['name']];
        }

//        if ($id > 0) {
//            $out['results'] = ['id' => $id, 'text' => City::find($id)->name];
//        }

        return $out;
    }

}
