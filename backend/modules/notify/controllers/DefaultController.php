<?php

namespace backend\modules\notify\controllers;

use appxq\sdii\utils\VarDumper;
use dms\aomruk\classese\Notify;
use Yii;
use backend\modules\ezforms2\models\EzformFields;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\modules\ezforms2\models\EzformSearch;
use yii\web\Response;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezbuilder\classes\EzBuilderFunc;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\models\Ezform;
use backend\modules\tmf\classes\TmfFn;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;

/**
 * Default controller for the `notify` module
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

    public function actionUpdateCapaStatus()
    {
        try {
            $capa_id = \Yii::$app->request->post('capa_res_id', '');
            (new \yii\db\Query())->createCommand()->update('zdata_capa_request', ['capa_status' => 2], ['id' => $capa_id])->execute();
            return TRUE;
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return FALSE;
        }
    }

    public function actionDetail($ezf_id, $v)
    {
        $modal = \Yii::$app->request->get('modal', 'notify');
        $reloadDiv = \Yii::$app->request->get('reloadDiv', 'reloadDiv');
        $ezf_fields = \backend\modules\ezforms2\models\EzformFieldsSearch::findAll(['ezf_id' => $ezf_id, 'ezf_version' => $v, 'ezf_field_type' => '912']);
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $ezf_fields, //->orderBy(['update_date' => SORT_DESC, 'due_date_assign' => SORT_DESC]),
        ]);
        return $this->renderAjax('_detail', [
            'ezf_id' => $ezf_id,
            'version' => $v,
            'modal' => $modal,
            'reloadDiv' => $reloadDiv,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionAdd()
    {
        $modal = \Yii::$app->request->get('modal', 'notify');
        $reloadDiv = \Yii::$app->request->get('reloadDiv', 'reloadDiv');
        $tab = Yii::$app->request->get('tab', '1');
        $searchModel = new EzformSearch();

        $query = Ezform::find()
            ->select('*')
            ->andWhere(['created_by' => Yii::$app->user->id])
            ->andWhere('ezf_crf != 1')
            ->andWhere('ezform.status > :status', [':status' => 0]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'fullname',
                    'ezf_name',
                    'ezf_detail',
                    'created_at'
                ],
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        $searchModel->load(Yii::$app->request->queryParams);
        $query->andFilterWhere([
            'ezf_id' => $searchModel->ezf_id,
            'created_by' => $searchModel->created_by,
            'category_id' => $searchModel->category_id,
            /*'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'shared' => $this->shared,
            'public_listview' => $this->public_listview,
            'public_edit' => $this->public_edit,
            'public_delete' => $this->public_delete,*/
        ]);

        $query->andFilterWhere(['like', 'ezf_name', $searchModel->ezf_name]);
//        $dataProvider = $searchModel->searchMyForm(Yii::$app->request->queryParams, $tab);
        return $this->renderAjax('add', [
            'modal' => $modal,
            'reloadDiv' => $reloadDiv,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionGetEzf($q = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        $dataEzf = (new \yii\db\Query())->select(['ezf_id as id', 'ezf_name as text'])
            ->from('ezform')
            ->where('ezform.status = 1 AND (ezform.ezf_id in (SELECT ezf_id FROM ezform_assign WHERE user_id = :user_id) '
                . ' OR ezform.created_by = :user_id'
                . ' OR ezform.shared = 1)', [':user_id' => \Yii::$app->user->id]);
        if (!is_null($q)) {

            $dataEzf->andWhere('ezf_name LIKE :q', [':q' => "%$q%"]);
        }
        $out['results'] = array_values($dataEzf->limit(20)->all());

        return $out;
    }

    public function actionCreate($ezf_id, $v = '')
    {
        $modal = \Yii::$app->request->get('modal', 'notify');
        $reloadDiv = \Yii::$app->request->get('reloadDiv', 'reloadDiv');
        if (Yii::$app->getRequest()->isAjax) {
            $dataEzf = Ezform::findOne(['ezf_id' => $ezf_id]);
            $model = new EzformFields();
            $model->ezf_id = $ezf_id;
            $model->ezf_version = $dataEzf->ezf_version;
            $model->ezf_field_id = SDUtility::getMillisecTime();
            $model->ezf_field_type = 912;
            $model->ezf_field_label = Yii::t('ezform', 'Notification question');
            $model->ezf_field_name = EzfFunc::generateFieldName($ezf_id);
            $model->ezf_field_name .= '_notify';
            $model->ezf_field_order = EzfQuery::getFieldsCountById($ezf_id);

            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                $modelEzf = EzfQuery::getEzformOne($ezf_id);
                $dataEzf = $modelEzf->attributes;

                //fix
                if (in_array($model->ezf_field_type, [79, 81])) {
                    $model->ezf_version = 'all';
                }
                $model->ezf_id = $ezf_id;
                $model->ref_field_desc = SDUtility::array2String($model->ref_field_desc);
                $model->ref_field_search = SDUtility::array2String($model->ref_field_search);

                $dataInput = null;

                if (isset(Yii::$app->session['ezf_input'])) {
                    $dataInput = EzfFunc::getInputByArray($model->ezf_field_type, Yii::$app->session['ezf_input']);
                }

                $data = isset($_POST['data']) ? $_POST['data'] : [];
                $options = isset($_POST['options']) ? $_POST['options'] : [];
                $validate = isset($_POST['validate']) ? $_POST['validate'] : [];

                //\appxq\sdii\utils\VarDumper::dump($_POST);
                $result = EzBuilderFunc::saveEzField($model, $model, $dataEzf, $dataInput, $data, $options, $validate);
                return $result;
            } else {
                return $this->renderAjax('create', [
                    'model' => $model,
                    'modal' => $modal,
                    'dataEzf' => $dataEzf,
                    'reloadDiv' => $reloadDiv,
                ]);
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionUpdate($id)
    {
        $modal = \Yii::$app->request->get('modal', 'notify');
        $reloadDiv = \Yii::$app->request->get('reloadDiv', 'reloadDiv');
        if (Yii::$app->getRequest()->isAjax) {
            $model = $this->findModel($id);
            $dataEzf = EzfQuery::getEzformOne($model->ezf_id);
            $model->ezf_field_data = SDUtility::string2Array($model->ezf_field_data);
            $model->ezf_field_options = SDUtility::string2Array($model->ezf_field_options);
            $model->ezf_field_specific = SDUtility::string2Array($model->ezf_field_specific);
            $model->ezf_field_validate = SDUtility::string2Array($model->ezf_field_validate);
            $model->ref_field_desc = SDUtility::string2Array($model->ref_field_desc);
            $model->ref_field_search = SDUtility::string2Array($model->ref_field_search);

            $oldModel = $model->attributes;

            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $modelEzf = EzfQuery::getEzformOne($model->ezf_id);
                $dataEzf = $modelEzf->attributes;

                if (in_array($model->ezf_field_type, [79, 81])) {
                    $model->ezf_version = 'all';
                }

                if ($model->ezf_version == '') {
                    $model->ezf_version = $modelEzf->ezf_version;
                }

                $model->ref_field_desc = isset($_POST['EzformFields']['ref_field_desc']) ? $_POST['EzformFields']['ref_field_desc'] : [];
                $model->ref_field_search = isset($_POST['EzformFields']['ref_field_search']) ? $_POST['EzformFields']['ref_field_search'] : [];

                $model->ref_field_desc = SDUtility::array2String($model->ref_field_desc);
                $model->ref_field_search = SDUtility::array2String($model->ref_field_search);

                $dataInput;
                if (isset(Yii::$app->session['ezf_input'])) {
                    $dataInput = EzfFunc::getInputByArray($model->ezf_field_type, Yii::$app->session['ezf_input']);
                }

                $data = isset($_POST['data']) ? $_POST['data'] : [];
                $options = isset($_POST['options']) ? $_POST['options'] : [];
                $validate = isset($_POST['validate']) ? $_POST['validate'] : [];

                $result = EzBuilderFunc::saveEzField($model, $oldModel, $dataEzf, $dataInput, $data, $options, $validate);

                return $result;
            } else {
                return $this->renderAjax('update', [
                    'model' => $model,
                    'modal' => $modal,
                    'dataEzf' => $dataEzf,
                    'reloadDiv' => $reloadDiv,
                ]);
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionSendNotify()
    {
        $notify = Yii::$app->request->post('notify', '');
        $detail = Yii::$app->request->post('detail', '');
        $name_fix = Yii::$app->request->post('name_fix', []);
        $role_fix = Yii::$app->request->post('role_fix', []);
        $send_system = Yii::$app->request->post('send_system', false);
        $send_email = Yii::$app->request->post('send_email', false);
        $send_line = Yii::$app->request->post('send_line', false);
        $type_url = Yii::$app->request->post('type_url', '3');
        $url = Yii::$app->request->post('url', '');
        $readonly = Yii::$app->request->post('readonly', '');
//        VarDumper::dump($_POST);
        if (!empty($role_fix)) {
            $role_fix = TmfFn::getRole($role_fix);
        }
        $data_users = array_merge($role_fix, $name_fix);
        $data_user = [];
        foreach ($data_users as $value) {
            $data_user[$value] = $value;
        }
        $notify = Notify::setNotify()->notify($notify)
            ->detail($detail)->assign($data_user)
            ->send_email($send_email)
            ->send_system($send_system)
            ->send_line($send_line)
            ->type_link($type_url)
            ->url($url)
            ->readonly($readonly)
            ->sendStatic();
        if ($notify) {
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }
    }

    protected function findModel($ezf_field_id)
    {
        if (($model = EzformFields::findOne(['ezf_field_id' => $ezf_field_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
