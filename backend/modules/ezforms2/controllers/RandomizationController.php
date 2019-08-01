<?php

namespace backend\modules\ezforms2\controllers;

use appxq\sdii\utils\SDUtility;
use appxq\sdii\utils\VarDumper;
use Yii;
use backend\modules\ezforms2\models\RandomCode;
use backend\modules\ezforms2\models\RandomCodeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use appxq\sdii\helpers\SDHtml;

/**
 * RandomizationController implements the CRUD actions for RandomCode model.
 */
class RandomizationController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all RandomCode models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!\Yii::$app->user->isGuest) {
            $searchModel = new RandomCodeSearch();
            $dataSearch = Yii::$app->request->queryParams;
            $query = $searchModel->find();
            $searchModel->search(Yii::$app->request->queryParams);
            if (Yii::$app->user->can("administrator") || Yii::$app->user->can("adminsite")) {
                $query->leftJoin('random_match', 'random_code.id = random_match.random_id')->where(['random_code.user_create' => Yii::$app->user->id])->orWhere('random_match.sitecode = :sitecode OR random_match.sitecode = ""', [':sitecode' => Yii::$app->user->identity->profile->sitecode]);
            } else {
                $query->leftJoin('random_match', 'random_code.id = random_match.random_id')->where('random_match.sitecode = :sitecode OR random_match.sitecode = ""', [':sitecode' => Yii::$app->user->identity->profile->sitecode]);
            }
            if (is_array($dataSearch) && isset($dataSearch['RandomCodeSearch'])) {
                foreach ($dataSearch['RandomCodeSearch'] as $key => $value) {
                    if ($value != '') {
                        $query->andWhere(['LIKE', $key, $value]);
                    }
                }
            }
//            \appxq\sdii\utils\VarDumper::dump($query->all());
            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => $query
            ]);
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->redirect(Yii::$app->user->loginUrl);
        }
    }

    /**
     * Displays a single RandomCode model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new RandomCode model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RandomCode();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing RandomCode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing RandomCode model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        try {
            $id = Yii::$app->request->post('id', '');
//            VarDumper::dump($id);
            if ($id != '') {
                $this->findModel($id)->delete();
                (new \yii\db\Query())->createCommand()->delete('random_code_site', ['random_id' => $id])->execute();
                return true;
            } else {
                return false;
            }

        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }

    public function actionDeletes()
    {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (isset($_POST['selection'])) {
                foreach ($_POST['selection'] as $id) {
                    $this->findModel($id)->delete();
                    (new \yii\db\Query())->createCommand()->delete('random_code_site', ['random_id' => $id])->execute();
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
                    'data' => '',
                ];
                return $result;
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    /**
     * Finds the RandomCode model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RandomCode the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RandomCode::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAddCode()
    {
        $sitecode = \Yii::$app->request->get('sitecode', '');
        $ezf_id = Yii::$app->request->get('ezf_id', '');
        return $this->renderAjax('_add-code', ['ezf_id' => $ezf_id, 'sitecode' => $sitecode]);
    }

    public function actionForm()
    {
        $ezf_id = Yii::$app->request->get('ezf_id', '');
//        var_dump($ezf_id);
        $dataSitecode = (new \yii\db\Query())
            ->select(['site_name as id', 'CONCAT(site_detail,\' (\',site_name,\')\') as sitecode'])
            ->from('zdata_sitecode')
            ->where('site_detail is not null AND site_name is not null AND rstat not in (0,3)')
            ->all();
        return $this->renderAjax('_form-add', ['dataSitecode' => $dataSitecode, 'ezf_id' => $ezf_id]);
    }

    public function actionAdd()
    {
        $post = \Yii::$app->request->post();
        $post['display_code'] = SDUtility::array2String($post['display_code']);
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

    public function actionUpdateData()
    {
        $post = \Yii::$app->request->post();
        if (!empty($post) && is_array($post)) {
            try {
                $post['display_code'] = isset($post['display_code']) ? SDUtility::array2String($post['display_code']) : '';
                (new \yii\db\Query())->createCommand()->update('random_code', $post, ['id' => $post['id']])->execute();
                return TRUE;
            } catch (\yii\db\Exception $error) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
                return FALSE;
            }
        }
    }

    public function actionUpdateCode()
    {
        $ezf_id = Yii::$app->request->get('ezf_id', '');
        $id = \Yii::$app->request->get('id', '');
        $sitecode = \Yii::$app->request->get('sitecode', '');
        $data = (new \yii\db\Query())->select('*')->from('random_code')->where(['id' => $id])->one();
        return $this->renderAjax('_add-code', ['data' => $data, 'sitecode' => $sitecode, 'ezf_id' => $ezf_id]);
    }

    public function actionSelect($q = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ezf_id = Yii::$app->request->post('ezf_id', '');
        $out = ['results' => []];
        $random = '';
        if (is_null($q)) {
            $q = '';
        }
        $dataFrom = \Yii::$app->request->post('data_from', []);
        foreach ($dataFrom as $key => $value) {
            if ($value['name'] == 'options[options][random_code][]') {
                if ($value['value'] != '') {
                    $random .= $value['value'] . ' ,';
                }
//            }else if ($value['name'] == 'options[options][ezf_id]') {
//                if ($value['value'] != '') {
//                    $ezf_id = $value['value'];
//                }
            }
        }
        if ($random != '') {
            $random = substr($random, 0, strlen($random) - 1);
        }
//        \appxq\sdii\utils\VarDumper::dump($random);
        $query = (new \yii\db\Query())->select('id,name')->from('random_code')->where('ezf_id = :ezf_id AND user_create = :user_create AND name LIKE :q', [':q' => "%$q%", ':user_create' => \Yii::$app->user->id, ':ezf_id' => $ezf_id]);
        if ($random != '') {
            $query->andWhere('id NOT IN (' . $random . ')');
        }
        $data = $query->limit(20)->all();
        foreach ($data as $value) {
            $out["results"][] = ['id' => "{$value['id']}", 'text' => $value['name']];
        }

//        if ($id > 0) {
//            $out['results'] = ['id' => $id, 'text' => City::find($id)->name];
//        }

        return $out;
    }

    public function actionViewData()
    {
        $random_id = \Yii::$app->request->get('random_id', '');
        $sitecode = \Yii::$app->request->get('sitecode', '');
        $have_modal = \Yii::$app->request->get('have-modal', '');
//        $ezf_id = \Yii::$app->request->get('ezf_id', '');
        $dataCode = RandomCode::findOne($random_id);
        $data = [];
        if ($dataCode) {
            $strData = $dataCode['code_random'];
            $arrData = preg_split("/\r\n|\n|\r/", $strData);
            if (is_array($arrData)) {
                foreach ($arrData as $key => $vData) {
                    $code = explode(',', $vData);
                    if (is_array($code) && $code[0] != '') {
                        $dataId = \backend\modules\ezforms2\models\RandomCodeSite::findAll(['random_id' => $random_id, 'key' => $key]); //, 'sitecode' => \Yii::$app->user->identity->profile->sitecode]);
                        $valId = '';
                        if ($dataId) {

                            foreach ($dataId as $key => $value) {
                                $valId .= $value['sitecode'] . "," . $value['data_id'] . "|";
                            }
                            $valId = substr($valId, 0, strlen($valId) - 1);
                        }
                        array_push($data, ['code_random' => $code[$dataCode['code_index'] - 1], 'data_id' => $valId, 'ezf_id' => $dataCode['ezf_id']]);
                    }
                }
            }
        }
        if (empty($data)) {
            array_push($data, ['code_random' => '', 'data_id' => '', 'ezf_id' => '']);
        }
//        $dataRandom = \backend\modules\ezforms2\models\RandomCodeSite::find();
//        if ($sitecode != '') {
//            $dataRandom->where(['random_id' => $random_id, 'sitecode' => $sitecode]);
//        } else {
//            $dataRandom->where(['random_id' => $random_id]);
//        }
//        $dataRandom = $dataRandom->groupBy(['ezf_id'])->all();
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $data,
            'pagination' => ['defaultPageSize' => 30]
        ]);
//        \appxq\sdii\utils\VarDumper::dump($dataProvider);
        return $this->renderAjax('_view-data', [
//            'dataRandom' => $dataRandom, 
            'dataCode' => $dataCode,
            'sitecode' => $sitecode,
            'dataProvider' => $dataProvider,
            'have_modal' => $have_modal]);
    }

    public function actionGetData()
    {
        $random_id = \Yii::$app->request->get('random_id', '');
        $sitecode = \Yii::$app->request->get('sitecode', '');
        $ezf_id = \Yii::$app->request->get('ezf_id', '');
        $dataUrl = \Yii::$app->request->get('url', '');
        $reloadDiv = \Yii::$app->request->get('reloadDiv', '');
        $dataRandom = \backend\modules\ezforms2\models\RandomCodeSite::find();
        if ($sitecode != '') {
            $dataRandom->where(['random_id' => $random_id, 'sitecode' => $sitecode, 'ezf_id' => $ezf_id]);
        } else {
            $dataRandom->where(['random_id' => $random_id, 'ezf_id' => $ezf_id]);
        }
        $dataRandom = $dataRandom->all();
        $dataCode = RandomCode::findOne($random_id);
        return $this->renderAjax('_get-data', ['dataRandom' => $dataRandom, 'dataCode' => $dataCode, 'dataUrl' => $dataUrl, 'reloadDiv' => $reloadDiv]);
    }

    public function actionDeleteRandomcode()
    {
        $id = \Yii::$app->request->get('id', '');
        $ezf_id = \Yii::$app->request->get('ezf_id', '');
        $data_id = \Yii::$app->request->get('data_id', '');
        try {

            $data = \backend\modules\ezforms2\models\RandomCodeSite::findOne($id);
            if ($data) {
                $data->delete();
            }
            $dataTable = new \backend\modules\ezforms2\models\TbdataAll();
            $ezf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
            $dataTable->setTableName($ezf['ezf_table']);
            $dataEzf = \backend\modules\ezforms2\classes\EzfUiFunc::deleteDataRstat($dataTable, $ezf['ezf_table'], $ezf_id, $data_id);
            return TRUE;
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return FALSE;
        }
    }

//    public function

    public function actionImportScv()
    {
        $file = $_FILES;
        $head = \Yii::$app->request->post('head', '');
        $out = [];
        if ($file != '') {
            $code = file_get_contents($_FILES['file']['tmp_name']);
            $code = str_replace('"', "", $code);
            $code = str_replace("'", "", $code);
            $code = preg_split("/\r\n|\n|\r/", $code);
//            if ($head != '') {
//                $out['data_head'] = $code[0];
//                unset($code[0]);
//                $out['data_text'] = implode("\n", $code);
//            } else {
//                unset($code[0]);
                $out['data_text'] = implode("\n", $code);
//            }

            return json_encode($out);
        } else {
            return false;
        }
    }

    public function actionGetRandomCode()
    {
        $options['seed'] = \Yii::$app->request->get('seed', '');
        $options['list_length'] = \Yii::$app->request->get('list_length', '');
        $options['block_size'] = \Yii::$app->request->get('block_size', '');
        $options['treatment'] = \Yii::$app->request->get('treatment', '');
        $status = \Yii::$app->request->get('status', '');
//VarDumper::dump(mt_rand());
        echo \backend\modules\ezforms2\classes\RandomizationFunc::getRandomBlock($options, $status);
    }

    public function actionSaveMatch()
    {
        try {
            $options = Yii::$app->request->post('options', []);
            $ezfField = Yii::$app->request->post('EzformFields', '');
            $ezf_field_id = isset($ezfField['ezf_field_id']) ? $ezfField['ezf_field_id'] : '';
            $random_code = isset($options['options']['random_code']) ? $options['options']['random_code'] : [];
            $sitecode = isset($options['options']['sitecode']) ? $options['options']['sitecode'] : [];
            $check_sitecode = isset($options['options']['check_sitecode']) ? $options['options']['check_sitecode'] : 1;
            $query = new \yii\db\Query();
            $query->createCommand()->delete('random_match', ['ezf_field_id' => $ezf_field_id, 'user_create' => \Yii::$app->user->id])->execute();
            foreach ($random_code as $key => $value) {
                if ($value != '') {
                    try {
                        $query->createCommand()->insert('random_match', [
                            'random_id' => $value,
                            'sitecode' => $check_sitecode == 1 ? $sitecode[$key] : '',
                            'user_create' => \Yii::$app->user->id,
                            'type_site' => $check_sitecode,
                            'ezf_field_id' => $ezf_field_id
                        ])->execute();
                    } catch (\yii\db\Exception $ex) {

                    }
                }
            }
            return 'success';
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return 'error';
        }
    }

    public function actionDeleteMatch()
    {
        try {
            $random = Yii::$app->request->get('random', '');
            $sitecode = Yii::$app->request->get('sitecode', '');
            $query = new \yii\db\Query();
            $query->createCommand()->delete('random_match', ['random_id' => $random])->execute();
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }
    }

    public function actionPreviewCode(){
        $display_code = Yii::$app->request->post('display_code','');
        $code_index = Yii::$app->request->post('code_index','');
        $start_row = Yii::$app->request->post('start_row','1');
        $code = Yii::$app->request->post('code','');
        $modalID = Yii::$app->request->post('modalID','modal-preview');
        return $this->renderAjax('_preview_code',[
            'display_code' => $display_code,
            'code_index'=>$code_index,
            'start_row'=>$start_row,
            'code' => $code,
            'modalID' => $modalID
        ]);
    }

}
