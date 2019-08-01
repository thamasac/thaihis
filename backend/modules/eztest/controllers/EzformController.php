<?php

namespace backend\modules\eztest\controllers;

use Yii;
use backend\modules\ezforms2\models\Ezform;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml; 
use appxq\sdii\utils\SDUtility;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfForm;

/**
 * Controller for the `eztest` module
 */
class EzformController extends Controller {

//data test insert,update
    private $data = [
        'Ezform' => [
            'ezf_id' => '1502179235012345500',
            'ezf_name' => 'Test Insert Form',
            'category_id' => '2',
            'co_dev' => [
                0 => '1501123070097609300'
            ],
            'ezf_detail' => 'Detail EzForm',
            'shared' => '2',
            'assign' => [
                0 => '1501129103092435800'
            ],
            'field_detail' => '',
            'status' => '1',
            'public_listview' => '0',
            'public_edit' => '0',
            'public_delete' => '0',
            'query_tools' => '1',
            'unique_record' => '1',
            'consult_tools' => '1',
            'consult_telegram' => '',
            'consult_users' => '',
            'ezf_sql' => '',
            'ezf_js' => '',
            'xsourcex' => '',
            'ezf_table' => '',
            'ezf_error' => '',
            'ezf_options' => '',
            'ezf_version' => '',
            'updated_at' => '',
            'updated_by' => '',
            'created_at' => '',
            'created_by' => '',
        ]
    ];

    /**
     * Creates a new Ezform model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Ezform();
        $model->attributes = $this->data; //datatest to model
        $result = EzfForm::saveEzfForm($model);
        return $this->render('index', ['result' => $result,]);
    }

    /**
     * Updates an existing Ezform model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate() {
        $model = $this->findModel($this->data['Ezform']['ezf_id']); //find ezf_id by datatest;
        $model->load($this->data);
        $result = EzfForm::saveEzfForm($model);
        return $this->render('index', ['result' => $result,]);
    }

    /**
     * Deletes an existing Ezform model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionTrash($id) {
        $model = $this->findModel($id);
        $result = EzfForm::trashEzfForm($model);
        return $result;
    }

    public function actionDelete($id) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($id);
            $result = EzfForm::deleteEzfForm($model);
            return $result;
    }

    /**
     * Finds the Ezform model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Ezform the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Ezform::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
