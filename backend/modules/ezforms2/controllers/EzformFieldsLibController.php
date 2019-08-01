<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use backend\modules\ezforms2\models\EzformFieldsLib;
use backend\modules\ezforms2\models\EzformFieldsLibSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezbuilder\classes\EzBuilderFunc;

/**
 * EzformFieldsLibController implements the CRUD actions for EzformFieldsLib model.
 */
class EzformFieldsLibController extends Controller {

    public function behaviors() {
        return [
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
     * Lists all EzformFieldsLib models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new EzformFieldsLibSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EzformFieldsLib model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        if (Yii::$app->getRequest()->isAjax) {


            return $this->renderAjax('view', [
                        'model' => $this->findModel($id),
            ]);
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    /**
     * Creates a new EzformFieldsLib model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        if (Yii::$app->getRequest()->isAjax) {
            $model = new EzformFieldsLib();
            $action = Yii::$app->request->get('action');

            if ($model->load(Yii::$app->request->post())) {
                if ($action == 'submit') {
                    $user_id = Yii::$app->user->id;
                    $oldModel = EzformFieldsLib::findOne(['ezf_field_id' => $model->ezf_field_id, 'created_by' => $user_id]);
                    //find old EzformFieldsLib
                    if ($oldModel) {
                        $model = $oldModel;
                        //load data ezform_field
                        $modelField = \backend\modules\ezforms2\models\EzformFields::findOne(['ezf_field_id' => $model->ezf_field_id]);
                        $model->attributes = $modelField->attributes;
                    } else {
                        //load data ezform_field
                        $modelField = \backend\modules\ezforms2\models\EzformFields::findOne(['ezf_field_id' => $model->ezf_field_id]);
                        $model->attributes = $modelField->attributes;
                        $model->field_lib_id = SDUtility::getMillisecTime();
                    }

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
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return \yii\widgets\ActiveForm::validate($model);
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
     * Updates an existing EzformFieldsLib model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        if (Yii::$app->getRequest()->isAjax) {
            $mode = Yii::$app->request->get('mode');
            $model = $this->findModel($id);
            $action = Yii::$app->request->get('action');

            if ($model->load(Yii::$app->request->post())) {
                if ($action == 'submit') {
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
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return \yii\widgets\ActiveForm::validate($model);
                }
            } else {
                return $this->renderAjax('update', [
                            'model' => $model,
                            'mode' => $mode
                ]);
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionQuickCreate($ezf_id, $ezf_field_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $model = new EzformFieldsLib();

            $model->ezf_id = Yii::$app->request->get('ezf_id');
            $model->ezf_field_id = Yii::$app->request->get('ezf_field_id');
            $model->field_lib_share = 2;
            $model->field_lib_status = 1;

            $user_id = Yii::$app->user->id;
            $oldModel = EzformFieldsLib::findOne(['ezf_field_id' => $model->ezf_field_id, 'created_by' => $user_id]);
            //find old EzformFieldsLib
            if ($oldModel) {
                $model = $oldModel;
                //load data ezform_field
                $modelField = \backend\modules\ezforms2\models\EzformFields::findOne(['ezf_field_id' => $model->ezf_field_id]);
                $model->attributes = $modelField->attributes;
            } else {
                //load data ezform_field
                $modelField = \backend\modules\ezforms2\models\EzformFields::findOne(['ezf_field_id' => $model->ezf_field_id]);
                $model->attributes = $modelField->attributes;
                $model->field_lib_id = SDUtility::getMillisecTime();

                if (empty($model->field_lib_name)) {
                    $model->field_lib_name = ($model->ezf_field_label ? $model->ezf_field_label . ' library' : $model->ezf_field_name . ' library') . date('s');
                }                
            }

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
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    /**
     * Deletes an existing EzformFieldsLib model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
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
     * Finds the EzformFieldsLib model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return EzformFieldsLib the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = EzformFieldsLib::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionApprove($id) {
        $model = $this->findModel($id);
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {
                if ($model->field_lib_approved == 1) {
                    $model->field_lib_approved = 0;
                } else {
                    $model->field_lib_approved = 1;
                }

                if ($model->save()) {
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                        'data' => $model,
                    ];
                    return $result;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                        'data' => $model,
                    ];
                    return $result;
                }
            } catch (\yii\db\Exception $e) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                    'data' => $model,
                ];
                return $result;
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionGetFields($q = null) {
        $ezf_id = Yii::$app->request->get('ezf_id', 0);
        $v = Yii::$app->request->get('v', '');

        $sqladdon = '';
        $params = [':q' => "%$q%"];

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }

        if ($v != '') {
            $sqladdon = ' AND (ezf_version = :v OR ezf_version="all") ';
            $params[':v'] = $v;
        }

        if ($ezf_id) {
            $params[':id'] = $ezf_id;
            $sqladdon .= " AND `ezf_id` = :id";
        }

        $sql = "SELECT ezf_field_id AS `id`,  
            IF(ezf_field_label<>'' OR ezf_field_label<>Null,ezf_field_label,ezf_field_name) AS`name` , 
            ezf_version 
            FROM `ezform_fields` 
            WHERE table_field_type <> 'none' AND ezf_field_type <> 0 AND ezf_version <> 'all'
            AND CONCAT(`ezf_field_name`, `ezf_field_label`) LIKE :q $sqladdon 
            ORDER BY ezf_version, ezf_field_order LIMIT 0,50";
//        \appxq\sdii\utils\VarDumper::dump(Yii::$app->db->createCommand($sql, $params)->rawSql, 1, 0);
        $data = Yii::$app->db->createCommand($sql, $params)->queryAll();
        $i = 0;

        foreach ($data as $value) {
            $out["results"][$i] = ['id' => "{$value['id']}", 'text' => $value["name"] . " [{$value['ezf_version']}]"];
            $i++;
        }

        return $out;
    }

    public function actionGetLibGroup($q = null) {
        $user_id = Yii::$app->user->id;
        $out = ['results' => []];

        $data = \backend\modules\ezforms2\models\EzformFieldsLibGroup::find()
                ->where(['LIKE', 'lib_group_name', "$q"])
                ->andWhere(['created_by' => $user_id])
                ->all();

        $i = 0;
        foreach ($data as $value) {
            $out["results"][$i] = ['id' => "{$value['lib_group_id']}", 'text' => $value["lib_group_name"]];
            $i++;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $out;
    }

    public function actionBtnAddEdit() {
        $group_id = Yii::$app->request->get('group_id');

        $html = '';
        if ($group_id) {
            $html .= Html::button('<i class="glyphicon glyphicon-eye-open"></i> ', [
                        'data-toggle' => 'tooltip',
                        'title' => Yii::t('ezform', 'Open Form'),
                        'class' => 'btn btn-primary btn-open-lib btn-edit',
                        'data-url' => Url::to(['/ezforms2/ezform-fields-lib/update-group',
                ])]) . ' ';
        }

        $html .= Html::button('<i class="glyphicon glyphicon-plus"></i> ', [
                    'data-toggle' => 'tooltip',
                    'title' => Yii::t('app', 'New'),
                    'class' => 'btn btn-success btn-open-lib btn-add',
                    'data-url' => Url::to(['/ezforms2/ezform-fields-lib/create-group'
            ])]) . ' ';

        return $html;
    }

    public static function itemAlias($code, $key = NULL) {
        $items = [
            'status' => [
                '0' => Yii::t('ezform', 'Disable'),
                '1' => Yii::t('ezform', 'Enable'),
            ],
            'public' => [
                '2' => Yii::t('ezform', 'Private'),
                '3' => Yii::t('ezform', 'Everyone in site'),
                '1' => Yii::t('ezform', 'Public User'),
                '4' => Yii::t('ezform', 'Public Common(Admin select)'),
            ],
            'approved' => [
                Yii::t('ezform', 'Waiting for approval.'),
                Yii::t('ezform', 'Approved'),
            ],
        ];

        $return = $items[$code];

        if (isset($key)) {
            return isset($return[$key]) ? $return[$key] : [];
        } else {
            return isset($return) ? $return : [];
        }
    }

    public function actionShowLibLists($ezf_id, $v) {
        $searchModel = new EzformFieldsLibSearch();

        return $this->renderAjax('_show_lib', [
                    'searchModel' => $searchModel,
                    'ezf_id' => $ezf_id,
                    'v' => $v
        ]);
    }

    public function actionLibLists($ezf_id, $v) {
        if (Yii::$app->getRequest()->isAjax) {
            $searchModel = new EzformFieldsLibSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->post(), 'modal');

            return $this->renderAjax('_lib_lists', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'ezf_id' => $ezf_id,
                        'v' => $v
            ]);
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionShowLib($ezf_id, $ezf_field_id, $v) {
        if (Yii::$app->getRequest()->isAjax) {
            $user_id = Yii::$app->user->id;
            $data = EzformFieldsLibSearch::getLibrary($ezf_field_id, $user_id);

            return $this->renderAjax('_lib_show', [
                        'data' => $data,
                        'ezf_id' => $ezf_id,
                        'ezf_field_id' => $ezf_field_id,
            ]);
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionAddLibInput($ezf_id, $lib_id, $v) {
        if (Yii::$app->getRequest()->isAjax) {
            $auto = isset($_GET['auto']) ? (int) $_GET['auto'] : 0;
            $show = isset($_GET['show']) ? $_GET['show'] : 0;
            Yii::$app->session['show_varname'] = (int) $show;

            $modelClone = EzformFieldsLib::findOne($lib_id);

            $model = new EzformFields();
            $model->attributes = $modelClone->attributes;

            $model->ezf_id = $ezf_id;
            $model->ezf_version = $v;

//            $model->ezf_field_id = SDUtility::getMillisecTime();
            $model->ezf_field_name = EzfFunc::generateFieldName($model->ezf_id);
            $model->ezf_field_label = $modelClone->ezf_field_label . '_library_clone';
            $model->ezf_field_order = EzfQuery::getFieldsCountById($model->ezf_id);

            $model->ezf_field_data = [];
            $model->ezf_field_options = SDUtility::string2Array($modelClone->ezf_field_options);
            $model->ezf_field_specific = SDUtility::string2Array($modelClone->ezf_field_specific);
            $model->ezf_field_validate = SDUtility::string2Array($modelClone->ezf_field_validate);
            $model->ref_field_desc = SDUtility::string2Array($modelClone->ref_field_desc);
            $model->ref_field_search = SDUtility::string2Array($modelClone->ref_field_search);

            $modelFields = EzfQuery::getFieldAll($model->ezf_id, $v);
            $modelEzf = EzfQuery::getEzformOne($model->ezf_id);

            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $dataEzf = $modelEzf->attributes;

                if (in_array($model->ezf_field_type, [79, 81])) {
                    $model->ezf_version = 'all';
                }

                $model->ezf_field_id = SDUtility::getMillisecTime();
                $model->ref_field_desc = SDUtility::array2String($model->ref_field_desc);
                $model->ref_field_search = SDUtility::array2String($model->ref_field_search);
                $model->ezf_field_lenght = $modelClone->ezf_field_lenght;

                //Yii::$app->session['show_varname'] = 1;
                $dataInput;

                if (isset(Yii::$app->session['ezf_input'])) {
                    $dataInput = EzfFunc::getInputByArray($model->ezf_field_type, Yii::$app->session['ezf_input']);
                }

                $data = isset($_POST['data']) ? $_POST['data'] : [];
                $options = isset($_POST['options']) ? $_POST['options'] : [];
                $validate = isset($_POST['validate']) ? $_POST['validate'] : [];

                $result = EzBuilderFunc::saveEzField($model, $model, $dataEzf, $dataInput, $data, $options, $validate, 1);
                return $result;
            } else {
                return $this->renderAjax('/../../ezbuilder/views/ezform-fields/update', [
                            'model' => $model,
                            'auto' => $auto,
                            'modelFields' => $modelFields,
                            'modelEzf' => $modelEzf,
                ]);
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionCheckQuestName($q) {
        if (Yii::$app->getRequest()->isAjax) {
            $searchModel = new EzformFieldsLibSearch();
            $data = $searchModel->searchName($q);

            return $data;
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    /**
     * Creates a new EzformFieldsLibGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateGroup() {
        if (Yii::$app->getRequest()->isAjax) {
            $model = new \backend\modules\ezforms2\models\EzformFieldsLibGroup();

            if ($model->load(Yii::$app->request->post())) {
                $model->lib_group_id = \appxq\sdii\utils\SDUtility::getMillisecTime();

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
                            'action' => 'group',
                ]);
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    /**
     * Updates an existing EzformFieldsLibGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateGroup($id) {
        if (Yii::$app->getRequest()->isAjax) {
            $model = \backend\modules\ezforms2\models\EzformFieldsLibGroup::findOne(['lib_group_id' => $id]);

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
                            'action' => 'group',
                ]);
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

}
