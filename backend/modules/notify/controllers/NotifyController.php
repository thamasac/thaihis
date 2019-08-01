<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\notify\controllers;

use appxq\sdii\utils\VarDumper;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\models\TbdataAll;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezmodules\classes\ModuleQuery;
use yii\db\Exception;
use yii\db\Expression;
use appxq\sdii\helpers\SDHtml;
use Yii;
use yii\db\Query;
use yii\web\Response;

/**
 * Description of NotifyController
 *
 * @author AR9
 */
class NotifyController extends \yii\web\Controller {

    public function actionIndex() {
        return $this->renderAjax('index', ['get' => $_GET]);
    }

    public function actionView($ezf_id) {
//        \appxq\sdii\utils\VarDumper::dump(\Yii::$app->request->queryParams,0);
        if (\Yii::$app->getRequest()->isAjax) {
            try {
                $popup = isset($_GET['popup']) ? $_GET['popup'] : 0;
                $actionRequire = isset($_GET['actionRequire']) ? $_GET['actionRequire'] : 0;
                $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
                $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
                $target = isset($_GET['target']) ? $_GET['target'] : '';
                $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
                $data_column = isset($_GET['data_column']) ? $_GET['data_column'] : '';
                $disabled = isset($_GET['disabled']) ? $_GET['disabled'] : 0;
                $hide_tab = isset($_GET['hide_tab']) ? $_GET['hide_tab'] : false;
                $addbtn = isset($_GET['addbtn']) ? $_GET['addbtn'] : 1;
                $status = isset($_GET['status']) ? $_GET['status'] : '';
                $page_size = isset($_GET['page_size']) ? $_GET['page_size'] : '20';
                $complete_date = isset($_GET['complete_date']) ? $_GET['complete_date'] : '';
                $status_view = isset($_GET['status_view']) ? $_GET['status_view'] : '';
                $module = isset($_GET['module']) ? $_GET['module'] : '';
                $data_column = EzfFunc::stringDecode2Array($data_column);
                $tab = isset($_GET['tab']) ? $_GET['tab'] : 'to_me';
                $data_id = isset($_GET['data_id']) ? $_GET['data_id'] : '';
                $notify_id = isset($_GET['notify_id']) ? $_GET['notify_id'] : '';
                $fillter = Yii::$app->request->get('TbdataAll', '');


                $ezform = EzfQuery::getEzformOne($ezf_id);
//            \appxq\sdii\utils\VarDumper::dump($_GET);
                $searchModel = NULL;
                $dataProvider = NULL;
                if ($tab == 'to_me' || $tab == 'all') {
                    if ($popup == 0) {
                        $searchModel = new TbdataAll();
                        $searchModel->setTableName($ezform->ezf_table);
                        $query = $searchModel->find()->where('rstat not in(0,3)  AND IFNULL(TIMESTAMP(delay_date) <= NOW() ,true) AND IFNULL(TIMESTAMP(due_date_assign) <= NOW() ,true) ');
                        if ($complete_date != '') {
                            $query->andWhere('(complete_date is not null AND complete_date != \'\') AND status_view = 1');
                            $status_view = '3';
                        }
                        if ($status_view != '' && $status_view == 0) {
                            $query->andWhere('(complete_date is null OR complete_date = \'\') AND status_view = :status_view', [':status_view' => $status_view]);
                            $status_view = '1';
                        } else if ($status_view != '' && $status_view == 1) {
                            $query->andWhere('(complete_date is null OR complete_date = \'\') AND (action is not null AND action != \'\') AND status_view = :status_view', [':status_view' => $status_view]);
                            $status_view = '2';
                        }
                        if ($tab == 'to_me') {
//                            $query->andWhere('assign_to = :assign_to AND xsourcex = :xsourcex', [':assign_to' => \Yii::$app->user->id,':xsourcex' => Yii::$app->user->identity->profile->sitecode]);
                            $query->andWhere('assign_to = :assign_to', [':assign_to' => \Yii::$app->user->id]);
                        } else {
                            $query->andWhere('assign_to != :assign_to', [':assign_to' => \Yii::$app->user->id]);
//                            $query->andWhere('assign_to != :assign_to AND xsourcex = :xsourcex', [':assign_to' => \Yii::$app->user->id,':xsourcex' => Yii::$app->user->identity->profile->sitecode]);
                        }
                        if ($actionRequire) {
                            $query->andWhere('(action is not null AND action <> \'\')');
                        }
                        if ($notify_id != '') {
                            $query->andWhere('id = :id', [':id' => $notify_id]);
                        }
                        if ($data_id != '') {
                            $query->andWhere('data_id = :id', [':id' => $data_id]);
                        }
                        if ($fillter != '') {
                            foreach ($fillter as $key => $value) {
                                if ($value != '' && !is_array($value)) {
                                    $query->andWhere($key . ' LIKE :q', [':q' => "%$value%"]);
                                } else if (is_array($value)) {
                                    $param = '';
                                    foreach ($value as $v) {
                                        $param .= "'$v' ,";
                                    }
                                    $param = substr($param, 0, strlen($param) - 1);
                                    $query->andWhere($key . ' IN (' . $param . ')');
                                }
                            }
                        }
                        $dataProvider = new \yii\data\ActiveDataProvider([
                            'query' => $query, //->orderBy(['update_date' => SORT_DESC, 'due_date_assign' => SORT_DESC]),
                            'pagination' => [
                                'pageSize' => $page_size,
                            //'route' => '/ezforms2/fileinput/grid-update',
                            ],
                            'sort' => [
                                //'route' => '/ezforms2/fileinput/grid-update',
                                'defaultOrder' => ['update_date' => SORT_DESC,'delay_date' => SORT_DESC, 'due_date_assign' => SORT_DESC]
//                            'attributes' => $data_column
                            ]
                        ]);

//                    $dataProvider->sort->defaultOrder = ['update_date' => SORT_DESC, 'due_date_assign' => SORT_DESC];
//                if ($targetField == '') {
//                    $modelTarget = \backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezform->ezf_id);
//                    if ($modelTarget) {
//                        $targetField = $modelTarget['ezf_field_name'];
//                    }
//                }
//
//                if ($target != '') {
//                    $searchModel[$targetField] = $target;
//                }
////                \appxq\sdii\utils\VarDumper::dump($searchModel,0);
//                $dataProvider = \backend\modules\ezforms2\classes\EzfUiFunc::modelSearch($searchModel, $ezform, $targetField, $data_column, $data_search, \Yii::$app->request->queryParams);
                    }
                } else if('tool') {
                    $searchModel = new \backend\modules\ezforms2\models\EzformSearch();
                    $ezfSearch = \Yii::$app->request->get('EzformSearch', []);
                    $param = [];

//                    $tab = \Yii::$app->request->get('tab', 1);
                    $query = $searchModel->find()
                            ->leftJoin('ezform_fields', 'ezform.ezf_id = ezform_fields.ezf_id')
                            ->where(['ezform_fields.ezf_field_type' => '912', 'ezform.status' => '1'])
                            ->andWhere('(ezform.ezf_id in (SELECT ezf_id FROM ezform_assign WHERE user_id = :user_id) '
                            . ' OR ezform.created_by = :user_id'
                            . ' OR ezform.shared = 1)', [':user_id' => \Yii::$app->user->id]);
                    foreach ($ezfSearch as $key => $value) {
                        if ($value != '')
                            $query->andFilterWhere(['like', 'ezform.' . $key, $value]);
                    }
                    $dataProvider = new \yii\data\ActiveDataProvider([
                        'query' => $query, //->orderBy(['update_date' => SORT_DESC, 'due_date_assign' => SORT_DESC]),
                        'pagination' => [
                            'pageSize' => $page_size,
                        //'route' => '/ezforms2/fileinput/grid-update',
                        ],
                        'sort' => [
                        //'route' => '/ezforms2/fileinput/grid-update',
//                            'defaultOrder' => ['update_date' => SORT_DESC, 'due_date_assign' => SORT_DESC]
//                            'attributes' => $data_column
                        ]
                    ]);
                }
//            $dataProvider->pagination->pageSize = $page_size;

                return $this->renderAjax('_view', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'ezform' => $ezform,
                            'modal' => $modal,
                            'reloadDiv' => $reloadDiv,
                            'data_column' => $data_column,
                            'target' => $target,
                            'targetField' => $targetField,
                            'disabled' => $disabled,
                            'addbtn' => $addbtn,
                            'ezf_id' => $ezf_id,
                            'module' => $module,
                            'tab' => $tab,
                            'status_view' => $status_view,
                            'hide_tab' => $hide_tab
                ]);
            } catch (\yii\db\Exception $ex) {
                EzfFunc::addErrorLog($ex);
            }
        } else {
            throw new \yii\web\NotFoundHttpException(\Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionViewRequired($ezf_id = '1520530564093708000') {
//        \appxq\sdii\utils\VarDumper::dump(\Yii::$app->request->queryParams,0);
        if (\Yii::$app->getRequest()->isAjax) {
            try {
                $popup = isset($_GET['popup']) ? $_GET['popup'] : 0;
                $modal = isset($_GET['modal']) ? $_GET['modal'] : 'modal-ezform-main';
                $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
                $target = isset($_GET['target']) ? $_GET['target'] : '';
                $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
                $data_column = ['ezf_id', 'data_id', 'action', 'notify', 'detail', 'sender', 'file_upload', 'readonly', 'assign_to'];
                $disabled = isset($_GET['disabled']) ? $_GET['disabled'] : 0;
                $addbtn = isset($_GET['addbtn']) ? $_GET['addbtn'] : 1;
                $status = isset($_GET['status']) ? $_GET['status'] : '';
                $page_size = isset($_GET['page_size']) ? $_GET['page_size'] : '20';
                $complete_date = isset($_GET['complete_date']) ? $_GET['complete_date'] : '';
                $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : \Yii::$app->user->id;
                $status_view = isset($_GET['status_view']) ? $_GET['status_view'] : '';
                $module = isset($_GET['module']) ? $_GET['module'] : '';


                $ezform = EzfQuery::getEzformOne($ezf_id);
//            \appxq\sdii\utils\VarDumper::dump($_GET);
                $searchModel = NULL;
                $dataProvider = NULL;

                if ($popup == 0) {
                    $searchModel = new TbdataAll();
                    $searchModel->setTableName($ezform->ezf_table);
                    $query = $searchModel->find()->where('rstat not in(0,3) AND (action is not null AND action != \'\')  AND IFNULL(TIMESTAMP(delay_date) <= NOW() ,true) AND IFNULL(TIMESTAMP(due_date_assign) <= NOW() ,true) ');
                    if ($complete_date != '') {
                        $query->andWhere('(complete_date is not null AND complete_date != \'\') AND status_view = 1');
                        $status_view = '3';
                    }
                    if ($status_view != '' && $status_view == 0) {
                        $query->andWhere('(complete_date is null OR complete_date = \'\') AND status_view = :status_view', [':status_view' => $status_view]);
                        $status_view = '1';
                    } else if ($status_view != '' && $status_view == 1) {
                        $query->andWhere('(complete_date is null OR complete_date = \'\') AND (action is not null AND action != \'\') AND status_view = :status_view', [':status_view' => $status_view]);
                        $status_view = '2';
                    }
//                    if ($user_id != '') {
                    $query->andWhere('assign_to = :assign_to', [':assign_to' => $user_id]);
//                    }
                    $dataProvider = new \yii\data\ActiveDataProvider([
                        'query' => $query, //->orderBy(['update_date' => SORT_DESC, 'due_date_assign' => SORT_DESC]),
                        'pagination' => [
                            'pageSize' => $page_size,
                        //'route' => '/ezforms2/fileinput/grid-update',
                        ],
                        'sort' => [
                            //'route' => '/ezforms2/fileinput/grid-update',
                            'defaultOrder' => ['update_date' => SORT_DESC,'delay_date' => SORT_DESC, 'due_date_assign' => SORT_DESC]
//                            'attributes' => $data_column
                        ]
                    ]);
//                    $dataProvider->sort->defaultOrder = ['update_date' => SORT_DESC, 'due_date_assign' => SORT_DESC];
//                if ($targetField == '') {
//                    $modelTarget = \backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezform->ezf_id);
//                    if ($modelTarget) {
//                        $targetField = $modelTarget['ezf_field_name'];
//                    }
//                }
//
//                if ($target != '') {
//                    $searchModel[$targetField] = $target;
//                }
////                \appxq\sdii\utils\VarDumper::dump($searchModel,0);
//                $dataProvider = \backend\modules\ezforms2\classes\EzfUiFunc::modelSearch($searchModel, $ezform, $targetField, $data_column, $data_search, \Yii::$app->request->queryParams);
                }
//            $dataProvider->pagination->pageSize = $page_size;
//            \appxq\sdii\utils\VarDumper::dump($dataProvider,0);
//                \appxq\sdii\utils\VarDumper::dump($reloadDiv);
                $view = '_view_required';

                return $this->renderAjax($view, [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'ezform' => $ezform,
                            'modal' => $modal,
                            'reloadDiv' => $reloadDiv,
                            'data_column' => $data_column,
                            'target' => $target,
                            'targetField' => $targetField,
                            'disabled' => $disabled,
                            'addbtn' => $addbtn,
                            'ezf_id' => $ezf_id,
                            'user_id' => $user_id,
                            'module' => $module,
                            'status_view' => $status_view
                ]);
            } catch (\yii\db\Exception $ex) {
                EzfFunc::addErrorLog($ex);
            }
        } else {
            throw new \yii\web\NotFoundHttpException(\Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionDetail() {
        $id = \Yii::$app->request->get('id', '');
        $modal = \Yii::$app->request->get('modal', 'default');
        $sub_modal = \Yii::$app->request->get('sub_modal', 'default');
        $reloadDiv = \Yii::$app->request->get('reloadDiv', 'default');
        if ($id != '') {
            try {
                $data = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData('zdata_notify', $id);
                $dataUser = \common\modules\user\models\Profile::findOne(['user_id' => $data ? $data['sender'] : '']);

                return $this->renderAjax('detail', [
                            'data' => $data,
                            'sub_modal' => $sub_modal,
                            'modal' => $modal,
                            'reloadDiv' => $reloadDiv,
                            'dataUser' => $dataUser
                ]);
            } catch (Exception $ex) {
                return \yii\helpers\Html::tag('div', \Yii::t('app', 'No results found.'), ['class' => 'alert alert-danger']);
            }
        } else {
            
        }
    }

    public function actionGridComplete() {
//        if (\Yii::$app->getRequest()->isAjax) {
        try {
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : '';
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $data_form = isset($_GET['data_form']) ? $_GET['data_form'] : '';
            $data_ezf_id = isset($_GET['data-ezf_id']) ? $_GET['data-ezf_id'] : '';

            $ezform = EzfQuery::getEzformOne($ezf_id);
            $searchModel = NULL;
            $dataProvider = NULL;
            $data_column = ['action', 'notify', 'effective_date', 'due_date', 'complete_date', 'assign_to'];
            $searchModel = new TbdataAll();
            $searchModel->setTableName($ezform->ezf_table);
            $query = $searchModel->find()
                    ->where('rstat not in(0,3) 
                            AND (complete_date is not null && complete_date != \'\') 
                            AND action is not null 
                            AND action <> \'\'
                            AND data_id = :data_form 
                            AND ezf_id = :data_ezf_id
                            ', [
                ':data_form' => $data_form, ':data_ezf_id' => $data_ezf_id
            ]);
            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => isset($_GET['sort']) ? $query : $query->orderBy(['complete_date' => SORT_DESC]),
                'pagination' => [
                    'pageSize' => 10,
                //'route' => '/ezforms2/fileinput/grid-update',
                ],
                'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
//                    'attributes' => $data_column
                ]
            ]);
//                
//            $dataProvider->pagination->pageSize = $page_size;
//            \appxq\sdii\utils\VarDumper::dump($dataProvider,0);

            return $this->renderAjax('_grid-data-complete', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'ezform' => $ezform,
                        'reloadDiv' => $reloadDiv,
                        'ezf_id' => $ezf_id,
                        'data_column' => $data_column,
                        'modal' => $modal,
            ]);
        } catch (\yii\db\Exception $ex) {
            EzfFunc::addErrorLog($ex);
        }
//        } else {
//            throw new \yii\web\NotFoundHttpException(\Yii::t('ezform', 'Do not allow this way.'));
//        }
    }

    public function actionGetForm() {
        $getData = \Yii::$app->request->get();
        return $this->render('_form', ['ezf_id' => $getData['ezf_id'], 'options' => $getData['options'], 'id' => $getData['id']]);
    }

    public function actionWebBoard() {
//        \appxq\sdii\utils\VarDumper::dump($_POST);
        try {
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $status = isset($_POST['status']) ? $_POST['status'] : '';
            $txt = isset($_POST['txt']) ? $_POST['txt'] : '';
            $reloadDiv = isset($_POST['reloadDiv']) ? $_POST['reloadDiv'] : '';
            $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : '';
            $data = \Yii::$app->db->createCommand("
                    SELECT fp.reply_comment as reply_comment,uf.avatar_base_url as avatar_base_url,
                    uf.avatar_path as avatar_path, uf.name as name, uf.public_email as public_email,
                    f.forum_title as forum_title,fp.created_at
                    FROM forum_reply AS fp 
                    JOIN forum AS f ON fp.forum_id = f.id
                    JOIN profile AS uf on uf.user_id = fp.created_by
                    WHERE notify_id = :id
            ", [':id' => $id])->queryAll();
            $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels' => $data,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
            return $this->renderAjax('_webboard', [
                        'dataProvider' => $dataProvider,
                        'status' => $status,
                        'id' => $id,
                        'txt' => $txt,
                        'reloadDiv' => $reloadDiv,
                        'ezf_id' => $ezf_id
            ]);
        } catch (\yii\db\Exception $ex) {
            EzfFunc::addErrorLog($ex);
        }
    }

    public function actionUpdateAction() {
        try {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $ezf_id = \Yii::$app->request->post('ezf_id', '');
            $id = \Yii::$app->request->post('id', '');
            $status = \Yii::$app->request->post('status', '2');
            $modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
            $searchModel = new \backend\modules\ezforms2\models\TbdataAll();
            $searchModel->setTableName($modelEzf->ezf_table);
            $model = $searchModel->findOne(['id' => $id]);
            if ($model->noti_status != 3) {
                $model->noti_status = $status;
                try {
                    $model->save();
                    return $resule = [
                        'status' => 'success'
                    ];
                } catch (\yii\db\Exception $ex) {
                    return $resule = [
                        'status' => 'error'
                    ];
                }
            } else {
                return $resule = [
                    'status' => 'no update'
                ];
            }
        } catch (\yii\db\Exception $ex) {
            EzfFunc::addErrorLog($ex);
        }
    }

    public function saveNotify($ezf_id) {
        
    }

    public function actionCountNotify() {
        try {
            $query = new \yii\db\Query();
            $dataCount = $query->select('id')
                    ->from('zdata_notify')
                    ->where('rstat not in (0,3) AND IFNULL(TIMESTAMP(delay_date) <= NOW() ,true) AND IFNULL(TIMESTAMP(due_date_assign) <= NOW() ,true) AND status_view = 0 AND assign_to = :assign_to ', [':assign_to' => \Yii::$app->user->id])
                    ->count();
            return $dataCount;
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($ex);
        }
    }

    public function actionGetNotify() {
        try {
            $reloadDiv = \Yii::$app->request->get('reloadDiv', 'notify');
            $notifyId = \Yii::$app->request->get('notifyId', 'notifyId');
            $query = new \yii\db\Query();
            $data = $query->select('*,IF(update_date,TIME(update_date),TIME(create_date)) time_notify')->from('zdata_notify')
                    ->where('rstat not in (0,3) AND IFNULL(TIMESTAMP(delay_date) <= NOW() ,true) AND IFNULL(TIMESTAMP(due_date_assign) <= NOW() ,true)  AND assign_to = :assign_to ', [':assign_to' => \Yii::$app->user->id])
                    ->limit(10)
                    ->orderBy(['update_date' => SORT_DESC,'delay_date' => SORT_DESC, 'due_date_assign' => SORT_DESC])
                    ->all();

            if ($data) {
                return $this->renderAjax('_get-notify', [
                            'data' => $data,
                            'reloadDiv' => $reloadDiv,
                            'notifyId' => $notifyId
                ]);
            } else {
                return '<div class="col-md-12 text-center"><code>' . \Yii::t('app', 'No results found.') . '</code></div>';
            }
        } catch (\yii\db\Exception $ex) {
//            return '<div class="col-md-12 text-center" style="width:250px;"><code>' . \Yii::t('app', 'No results found.') . '</code></div>';;
            EzfFunc::addErrorLog($ex);
        }
    }

    public function actionDeletes() {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (isset($_POST['selection'])) {
                foreach ($_POST['selection'] as $id) {
                    try {
                        (new \yii\db\Query())->createCommand()->update('zdata_notify', ['rstat' => '3'], ['id' => $id])->execute();
                    } catch (Exception $error) {
                        EzfFunc::addErrorLog($error);
                    }
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
//                    'data' => $id,
                ];
                return $result;
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionDelete() {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (isset($_POST['id'])) {
                try {
                    (new \yii\db\Query())->createCommand()->update('zdata_notify', ['rstat' => '3'], ['id' => $_POST['id']])->execute();
                    $result = [
                        'status' => 'success',
                        'action' => 'deletes',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
                        'data' => $_POST['id'],
                    ];
                    return $result;
                } catch (Exception $error) {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
//                    'data' => $id,
                    ];
                    EzfFunc::addErrorLog($error);
                    return $result;
                }
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
//                    'data' => $id,
                ];
                return $result;
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionViewed($id) {
        try {
            $query = new \yii\db\Query();
            $data = $query->createCommand()->update('zdata_notify', ['status_view' => '1'], 'id = :id', [':id' => $id])->execute();
////        \appxq\sdii\utils\VarDumper::dump($data);
//        return $this->renderAjax('_get-notify',[
//            'data'=>$data
//        ]);
        } catch (\yii\db\Exception $ex) {
            EzfFunc::addErrorLog($ex);
        }
    }

    public function actionUpdateResult() {
        try {

            $id = \Yii::$app->user->id;
            $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : '';
            $data_id = isset($_POST['id']) ? $_POST['id'] : '';
            $field = EzfQuery::getFieldByName($ezf_id, 'notify');
            $options = \appxq\sdii\utils\SDUtility::string2Array($field['ezf_field_options']);
            if ($ezf_id != '') {
                $ezform = EzfQuery::getEzformOne($ezf_id);
                $model = new TbdataAll();
                $model->setTableName($ezform['ezf_table']);
                $find = $model->findOne(['id' => $data_id]);
                $find->complete_date = new Expression('CURDATE()');
                if ($find->save()) {
                    $delayModel = $model->find()
                                    ->where('ezf_id = :ezf_id AND data_id = :data_id AND action = :action AND assign_to = :assign_to AND due_date_assign is not null AND due_date_assign <> \'\' ', [
                                        ':ezf_id' => $find->ezf_id,
                                        ':data_id' => $find->data_id,
                                        ':action' => $find->action,
                                        ':assign_to' => $find->assign_to
                                    ])->one();
                    if ($delayModel) {
                        $delayModel->rstat = 3;
                        $delayModel->save();
                    }

                    $model->setTableName('notify_email');
                    $dataEmail = $model->find()
                                    ->where('ezf_id = :ezf_id AND data_id = :data_id AND action = :action AND assign_to = :assign_to AND due_date_assign is not null AND due_date_assign <> \'\'', [
                                        ':ezf_id' => $find->ezf_id,
                                        ':data_id' => $find->data_id,
                                        ':action' => $find->action,
                                        ':assign_to' => $find->assign_to
                                    ])->one();
                    if ($dataEmail) {
                        $dataEmail->delete();
                    }


                    $model->setTableName('notify_line');
                    $dataLine = $model->find()
                                    ->where('ezf_id = :ezf_id AND data_id = :data_id AND action = :action AND assign_to = :assign_to AND due_date_assign is not null AND due_date_assign <> \'\'', [
                                        ':ezf_id' => $find->ezf_id,
                                        ':data_id' => $find->data_id,
                                        ':action' => $find->action,
                                        ':assign_to' => $find->assign_to
                                    ])->one();
                    if ($dataLine) {
                        $dataLine->delete();
                    }
                    $user = \common\modules\user\models\Profile::findOne(['user_id' => $find->assign_to]);
//                    
                    if ($find->sender != \Yii::$app->user->id) {
                        if ($find->action == 'Review') {
                            $find->action .= 'ed';
                        } else {
                            $find->action .= 'd';
                        }
                        $notify = new \dms\aomruk\classese\Notify([
                            'data_id' => $find->data_id,
                            'ezf_id' => $find->ezf_id,
                            'module_id' => $find->module_id,
                            'readonly' => false,
                            'assign' => $find->sender,
                            'version' => $find->ezf_version,
                            'notify' => 'Responded Successfully',
                            'detail' => $user['firstname'] . " " . $user['lastname'] . " " . $find->action . " successfully.",
                            'complete_date' => new Expression('CURDATE()'),
                            'delay_date' => new Expression('CURDATE()'),
                            'due_date' => new Expression('CURDATE()'),
                            'type_link' => $find->type_link
                        ]);
//                        \dms\aomruk\classese\Notify::setNotify()
//                                ->data_id($find->data_id)
//                                ->ezf_id($find->ezf_id)
//                                ->module_id($find->module_id)
//                                ->readonly(false)
//                                ->assign($find->sender)
//                                ->rstat(1)
//                                ->version($find->ezf_version)
//                                ->notify('New Notification')
//                                ->detail($user['firstname'] . " " . $user['lastname'] . " " . $find->action . " successfully.")
//                                ->status_view(0)
//                                ->complete_date(new Expression('CURDATE()'))
//                                ->delay_date(new Expression('CURDATE()'))
//                                ->type_link($find->type_link)
//                                ->sendStatic();
                        $notify->sendStatic();
                        if (isset($options['options']['send_email']) && $options['options']['send_email'] == true) {
                            $user = \common\modules\user\models\Profile::findOne(['user_id' => $find->sender]);
                            if (isset($user['email']) && $user['email'] != '')
                                $notify->SendMailTemplate($user['email']);
                        }
                        if (isset($options['options']['send_line']) && $options['options']['send_line'] == true) {
                            $notify->saveDataLine();
                        }
                    }
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\yii\db\Exception $ex) {
            EzfFunc::addErrorLog($ex);
//            \appxq\sdii\utils\VarDumper::dump($ex);
            return false;
        }
    }

    public function actionSendNotify(){
        $modal = Yii::$app->request->get('modal','modal');
        $reloadDiv = Yii::$app->request->get('reloadDiv','reloadDiv');
        $items_role = (new yii\db\Query())->select(['role_name', 'CONCAT(role_detail,\' (\',role_name,\')\') as role_detail'])->from('zdata_role')->all();
        $items_user = \common\modules\user\models\Profile::find()->select(['user_id', 'CONCAT(firstname,\' \',lastname) as name'])->where('sitecode = :sitecode', [':sitecode' => Yii::$app->user->identity->profile->sitecode])->all();
        return $this->renderAjax('_send-notify',[
            'items_role' => $items_role,
            'items_user' => $items_user,
            'reloadDiv' => $reloadDiv,
            'modal' => $modal
        ]);
    }

    public function actionSendData() {
        
    }

    public function actionTest() {
       echo  is_numeric ('3');
    }

}
