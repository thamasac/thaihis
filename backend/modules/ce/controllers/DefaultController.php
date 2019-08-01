<?php

namespace backend\modules\ce\controllers;

use yii\web\Controller;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\models\TbdataAll;
use backend\modules\ezforms2\classes\EzfQuery;
use Yii;
use yii\helpers\Url;
use yii\db\Query;

/**
 * Default controller for the `ce` module
 */
class DefaultController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        if (\Yii::$app->getRequest()->isAjax) {
            try {
                $ezf_id_cat = isset($_GET['ezf_id_cat']) ? $_GET['ezf_id_cat'] : 0;
                $ezf_id_subcat = isset($_GET['ezf_id_subcat']) ? $_GET['ezf_id_subcat'] : 0;
                $ezf_id_event = isset($_GET['ezf_id_event']) ? $_GET['ezf_id_event'] : 0;
                $popup = isset($_GET['popup']) ? $_GET['popup'] : 0;
                $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
                $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
                $target = isset($_GET['target']) ? $_GET['target'] : '';
                $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
                $data_column = isset($_GET['data_column']) ? $_GET['data_column'] : '';
                $disabled = isset($_GET['disabled']) ? $_GET['disabled'] : 0;
                $addbtn = isset($_GET['addbtn']) ? $_GET['addbtn'] : 1;
                $status = isset($_GET['status']) ? $_GET['status'] : '';
                $page_size = isset($_GET['page_size']) ? $_GET['page_size'] : '20';
                $complete_date = isset($_GET['complete_date']) ? $_GET['complete_date'] : '';
                $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
                $status_view = isset($_GET['status_view']) ? $_GET['status_view'] : '';
                $module = isset($_GET['module']) ? $_GET['module'] : '';
                $db2 = isset($_GET['db2']) ? $_GET['db2'] : 0;
                $default_column = isset($_GET['default_column']) ? $_GET['default_column'] : 0;
                $data_column = EzfFunc::stringDecode2Array($data_column);
                

                $ezform = EzfQuery::getEzformOne($ezf_id_event);
//            \appxq\sdii\utils\VarDumper::dump($_GET);
                $searchModel = NULL;
                $dataProvider = NULL;

                if ($popup == 0) {
                    $searchModel = new TbdataAll();
                    $searchModel->setTableName($ezform->ezf_table);
                    $query = $searchModel->find()->where('rstat not in(0,3)');


                    if ($targetField == '') {
                        $modelTarget = \backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezform->ezf_id);
                        if ($modelTarget) {
                            $targetField = $modelTarget['ezf_field_name'];
                        }
                    }

                    if ($target != '') {
                        $searchModel[$targetField] = $target;
                    }
//                \appxq\sdii\utils\VarDumper::dump($searchModel,0);
                    $dataProvider = \backend\modules\ezforms2\classes\EzfUiFunc::modelSearch($searchModel, $ezform, $targetField, $data_column, $data_column, \Yii::$app->request->queryParams);
                }
                $dataProvider->pagination->pageSize = $page_size;
//            \appxq\sdii\utils\VarDumper::dump($dataProvider,0);
//                $view = $popup ? '_view-popup' : '_view';

                return $this->renderAjax('index', [
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
                            'ezf_id_cat' => $ezf_id_cat,
                            'ezf_id_subcat' => $ezf_id_subcat,
                            'ezf_id_event' => $ezf_id_event,
                            'user_id' => $user_id,
                            'default_column' => $default_column,
                            'db2' => $db2,
                            'module' => $module
                ]);
            } catch (\yii\db\Exception $ex) {
                EzfFunc::addErrorLog($ex);
            }
        } else {
            throw new \yii\web\NotFoundHttpException(\Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionView() {
        if (Yii::$app->getRequest()->isAjax) {
            try {
//            \appxq\sdii\utils\VarDumper::dump($_GET);
                $ezf_type_id = isset($_GET['ezf_type_id']) ? $_GET['ezf_type_id'] : '';
                $ezf_name_id = isset($_GET['ezf_name_id']) ? $_GET['ezf_name_id'] : '';
                $ezf_detail_id = isset($_GET['ezf_detail_id']) ? $_GET['ezf_detail_id'] : '';
                $data_column_type = isset($_GET['data_column_type']) ? $_GET['data_column_type'] : '';
                $data_column_name = isset($_GET['data_column_name']) ? $_GET['data_column_name'] : '';
                $data_column_detail = isset($_GET['data_column_detail']) ? $_GET['data_column_detail'] : '';
//                $order_column_type = isset($_GET['order_column_type']) ? $_GET['order_column_type'] : '';
//                $order_column_name = isset($_GET['order_column_name']) ? $_GET['order_column_name'] : '';
//                $order_column_detail = isset($_GET['order_column_detail']) ? $_GET['order_column_detail'] : '';
                $type_field_value = isset($_GET['type_field_value']) ? $_GET['type_field_value'] : '';
                $type_field_label = isset($_GET['type_field_label']) ? $_GET['type_field_label'] : '';
                $ref_form_detail = isset($_GET['ref_form_detail']) ? $_GET['ref_form_detail'] : '';
                $popup = isset($_GET['popup']) ? $_GET['popup'] : 0;
                $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
                $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
                $target = isset($_GET['target']) ? $_GET['target'] : '';
                $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
                $disabled = isset($_GET['disabled']) ? $_GET['disabled'] : 0;
                $addbtn = isset($_GET['addbtn']) ? $_GET['addbtn'] : 1;
                $default_column = isset($_GET['default_column']) ? $_GET['default_column'] : 1;
                $pageSize = isset($_GET['pageSize']) ? $_GET['pageSize'] : 50;
                $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 4;
                $type_id = isset($_GET['type_id']) ? $_GET['type_id'] : 0;
                $data_id = isset($_GET['data_id']) ? $_GET['data_id'] : 0;
                $module = isset($_GET['module']) ? $_GET['module'] : '';



                $data_column_detail = EzfFunc::stringDecode2Array($data_column_detail);
                $data_column_name = EzfFunc::stringDecode2Array($data_column_name);
//                $order_column_detail = EzfFunc::stringDecode2Array($order_column_detail);

                $ezform_type = EzfQuery::getEzformOne($ezf_type_id);
                $ezform_name = EzfQuery::getEzformOne($ezf_name_id);
                $ezform_detail = EzfQuery::getEzformOne($ezf_detail_id);

                $query = new Query();
                $result = $query->select($type_field_value . ',' . $type_field_label)
                        ->from($ezform_type['ezf_table'])
                        ->where("rstat not in(0,3)")
                        ->orderBy(['order' => SORT_ASC])
                        ->all();
                $items = [];

                $data_url = Url::to(['/ce/default/sub-view',
                            'ezf_type_id' => $ezf_type_id,
                            'ezf_name_id' => $ezf_name_id,
                            'ezf_detail_id' => $ezf_detail_id,
                            'modal' => $modal,
                            'reloadDiv' => $reloadDiv,
                            'data_column_type' => $data_column_type,
                            'data_column_name' => EzfFunc::arrayEncode2String($data_column_name),
                            'data_column_detail' => EzfFunc::arrayEncode2String($data_column_detail),
                            'popup' => 0,
                            'disabled' => $disabled,
                            'default_column' => $default_column,
//                            'order_column_type' => $order_column_type,
//                            'order_column_name' => $order_column_name,
//                            'order_column_detail' => EzfFunc::arrayEncode2String($order_column_detail),
                            'ref_form_detail' => $ref_form_detail,
                            'type_field_value' => $type_field_value,
                            'type_field_label' => $type_field_label,
                            'pageSize' => $pageSize,
                            'orderby' => $orderby,
                            'module' => $module
                ]);
                $url = Url::to(['/ce/default/view',
                            'ezf_type_id' => $ezf_type_id,
                            'ezf_name_id' => $ezf_name_id,
                            'ezf_detail_id' => $ezf_detail_id,
                            'modal' => $modal,
                            'reloadDiv' => $reloadDiv,
                            'data_column_type' => $data_column_type,
                            'data_column_name' => EzfFunc::arrayEncode2String($data_column_name),
                            'data_column_detail' => EzfFunc::arrayEncode2String($data_column_detail),
                            'popup' => 0,
                            'disabled' => $disabled,
                            'default_column' => $default_column,
//                            'order_column_type' => $order_column_type,
//                            'order_column_name' => $order_column_name,
//                            'order_column_detail' => EzfFunc::arrayEncode2String($order_column_detail),
                            'ref_form_detail' => $ref_form_detail,
                            'type_field_value' => $type_field_value,
                            'type_field_label' => $type_field_label,
                            'pageSize' => $pageSize,
                            'orderby' => $orderby,
                            'module' => $module
                ]);

                $items[] = [
                    'label' => 'All Categories of Event',
                    'active' => $type_id == 0 ? true : false,
                    'headerOptions' => [
                        'data-id' => '0',
                        'data-url' => $url,
                        'class' => 'tabHeader'],
                    'content' => '',
                ];
                foreach ($result as $key => $val) {
                    $class = 'tabHeader editTab';
                    if ($val[$type_field_value] == '1521529930028891900') {
                        $class = 'tabHeaderLink';
                    }

                    $items[] = [
                        'label' => $val[$type_field_label] != '' ? $val[$type_field_label] : '',
                        'active' => $type_id == $val[$type_field_value] ? true : false,
                        'headerOptions' => [
                            'data-id' => $val[$type_field_value],
                            'data-url' => $url,
                            'class' => $class],
                        'content' => '',
                    ];
                }
//            
                $data_column = [];
//                $data_column[] = $data_column_type;
                foreach ($data_column_name as $value) {
                    $data_column[] = $value;
                }
                $column_detail = '';
//                $data_column_detail[] = 'target';
//                $data_column_detail[] = 'check_user';
//                \appxq\sdii\utils\VarDumper::dump($ezform_detail);
                foreach ($data_column_detail as $value) {
                    $data_column[] = $value;
                    $column_detail .= "zdata_final." . $value . " ,";
                }
                $column_detail .= 'zdata_final.update_date, zdata_final.target ,zdata_final.ecstep1,zdata_final.ecstep2,zdata_final.ecstep3,zdata_final.ecstep4,zdata_final.ecstep5,zdata_final.ecstep6 ,';
                $column_detail = substr($column_detail, 0, strlen($column_detail) - 1);
                $query = new Query();

                $query->select(
                                $ezform_name['ezf_table'] . '.*,'
                                . 'zdata_ec_event.*,'
//                                . '' . $ezform_type['ezf_table'] . '.' . $data_column_type
                        )
                        ->from('`' . $ezform_name['ezf_table'] . '`')
                        ->leftJoin('( 
                        SELECT ' . $column_detail . '
                        FROM ' . $ezform_detail['ezf_table'] . ' as zdata_final 
                        WHERE zdata_final.update_date=(SELECT max(zde.update_date)  FROM ' . $ezform_detail['ezf_table'] . ' as zde WHERE zde.target=zdata_final.target AND zde.rstat not in(0,3))

                        GROUP BY zdata_final.target 
                        )as zdata_ec_event', $ezform_name['ezf_table'] . '.id = zdata_ec_event.target')
                        ->leftJoin($ezform_type['ezf_table'], $ezform_name['ezf_table'] . '.target = ' . $ezform_type['ezf_table'] . '.id')
                        ->where($ezform_name['ezf_table'] . ".rstat not in(0,3)")
                        ->andWhere($ezform_name['ezf_table'] . '.xsourcex = :xsourcex', [':xsourcex' => \Yii::$app->user->identity->profile->sitecode]);
                if ($type_id != 0) {
                    $query->andWhere("{$ezform_name['ezf_table']}.target = :target", [':target' => $type_id]);
//                          
                }
                if ($data_id != 0) {
                    $query->andWhere("{$ezform_name['ezf_table']}.id = :id", [':id' => $data_id]);
//                          
                }

                $dataProvider = new \yii\data\ActiveDataProvider([
                    'query' => isset($_GET['sort']) ? $query : $query->orderBy([$ezform_detail['ezf_table'] . '.update_date' => SORT_DESC]),
                    'pagination' => [
                        'pageSize' => $pageSize,
                    //'route' => '/ezforms2/fileinput/grid-update',
                    ],
                    'sort' => [
                        //'route' => '/ezforms2/fileinput/grid-update',
                        'attributes' => $data_column
                    ]
                ]);
//            \appxq\sdii\utils\VarDumper::dump($dataProvider->getModels());
                $view = $popup ? '_view-popup' : '_view';
                return $this->renderAjax($view, [
                            'items' => $items,
//                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'ezf_type_id' => $ezf_type_id,
                            'ezf_name_id' => $ezf_name_id,
                            'ezf_detail_id' => $ezf_detail_id,
                            'modal' => $modal,
                            'reloadDiv' => $reloadDiv,
                            'data_column' => $data_column,
                            'target' => $target,
                            'targetField' => $targetField,
                            'disabled' => $disabled,
                            'addbtn' => $addbtn,
                            'default_column' => $default_column,
                            'pageSize' => $pageSize,
                            'orderby' => $orderby,
                            'type_id' => $type_id,
                            'data_url' => $data_url,
                            'ezform_type' => $ezform_type,
                            'ezform_name' => $ezform_name,
                            'ezform_detail' => $ezform_detail,
                            'module' => $module
                ]);
            } catch (yii\db\Exception $ex) {
                EzfFunc::addErrorLog($ex);
                return "<code>เกิดข้อผิดพลาดในการโหลดข้อมูล</code>";
            }
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionSubView() {
        if (Yii::$app->getRequest()->isAjax) {
            try {
//            \appxq\sdii\utils\VarDumper::dump($_GET);
                $ezf_type_id = isset($_GET['ezf_type_id']) ? $_GET['ezf_type_id'] : '';
                $ezf_name_id = isset($_GET['ezf_name_id']) ? $_GET['ezf_name_id'] : '';
                $ezf_detail_id = isset($_GET['ezf_detail_id']) ? $_GET['ezf_detail_id'] : '';
                $data_column_type = isset($_GET['data_column_type']) ? $_GET['data_column_type'] : '';
                $data_column_name = isset($_GET['data_column_name']) ? $_GET['data_column_name'] : '';
                $data_column_detail = isset($_GET['data_column_detail']) ? $_GET['data_column_detail'] : '';
//                $order_column_type = isset($_GET['order_column_type']) ? $_GET['order_column_type'] : '';
//                $order_column_name = isset($_GET['order_column_name']) ? $_GET['order_column_name'] : '';
//                $order_column_detail = isset($_GET['order_column_detail']) ? $_GET['order_column_detail'] : '';
                $type_field_value = isset($_GET['type_field_value']) ? $_GET['type_field_value'] : '';
                $type_field_label = isset($_GET['type_field_label']) ? $_GET['type_field_label'] : '';
                $ref_form_detail = isset($_GET['ref_form_detail']) ? $_GET['ref_form_detail'] : '';
                $popup = isset($_GET['popup']) ? $_GET['popup'] : 0;
                $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
                $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
                $target = isset($_GET['target']) ? $_GET['target'] : '';
                $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
                $disabled = isset($_GET['disabled']) ? $_GET['disabled'] : 0;
                $addbtn = isset($_GET['addbtn']) ? $_GET['addbtn'] : 1;
                $default_column = isset($_GET['default_column']) ? $_GET['default_column'] : 1;
                $pageSize = isset($_GET['pageSize']) ? $_GET['pageSize'] : 50;
                $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 4;
                $data_id = isset($_GET['data_id']) ? $_GET['data_id'] : 0;
                $type_id = isset($_GET['type_id']) ? $_GET['type_id'] : 0;
                $module = isset($_GET['module']) ? $_GET['module'] : '';

                $subModal = $reloadDiv . '-sub-modal';

                $data_column_detail = EzfFunc::stringDecode2Array($data_column_detail);
                $data_column_name = EzfFunc::stringDecode2Array($data_column_name);
//                $order_column_detail = EzfFunc::stringDecode2Array($order_column_detail);

                $data_url = Url::to(['/ce/default/sub-view',
                            'ezf_type_id' => $ezf_type_id,
                            'ezf_name_id' => $ezf_name_id,
                            'ezf_detail_id' => $ezf_detail_id,
                            'modal' => $modal,
                            'reloadDiv' => $reloadDiv,
                            'data_column_type' => $data_column_type,
                            'data_column_name' => EzfFunc::arrayEncode2String($data_column_name),
                            'data_column_detail' => EzfFunc::arrayEncode2String($data_column_detail),
                            'popup' => 0,
                            'disabled' => $disabled,
                            'default_column' => $default_column,
//                            'order_column_type' => $order_column_type,
//                            'order_column_name' => $order_column_name,
//                            'order_column_detail' => EzfFunc::arrayEncode2String($order_column_detail),
                            'ref_form_detail' => $ref_form_detail,
                            'type_field_value' => $type_field_value,
                            'type_field_label' => $type_field_label,
                            'pageSize' => $pageSize,
                            'orderby' => $orderby,
                            'data_id' => $data_id,
                            'type_id' => $type_id,
                            'module' => $module
                ]);

                $ezform_type = EzfQuery::getEzformOne($ezf_type_id);
                $ezform_name = EzfQuery::getEzformOne($ezf_name_id);
                $ezform_detail = EzfQuery::getEzformOne($ezf_detail_id);

                $data_column = [];
//            foreach ($data_column_type as $value) {
//                $data_column[] = $data_column_type;
//            }
                $colum_name = '';
                foreach ($data_column_name as $value) {
                    $data_column[] = $value;
                    $colum_name .= $ezform_name['ezf_table'] . "." . $value . ",";
                }
                $colum_name = substr($colum_name, 0, strlen($colum_name) - 1);
//                 \appxq\sdii\utils\VarDumper::dump($colum_name);
//            $data_column_detail[] = 'target';
                foreach ($data_column_detail as $value) {
                    $data_column[] = $value;
                }

                $query = new Query();
                $query->select($ezform_detail['ezf_table'] . '.*,' . $colum_name)
                        ->from($ezform_detail['ezf_table'])
                        ->leftJoin($ezform_name['ezf_table'], $ezform_detail['ezf_table'] . '.target=' . $ezform_name['ezf_table'] . '.id')
//                        ->leftJoin($ezform_type['ezf_table'], $ezform_name['ezf_table'] . '.target = ' . $ezform_type['ezf_table'] . '.id')
                        ->where($ezform_detail['ezf_table'] . ".rstat not in(0,3)")
                        ->andWhere($ezform_name['ezf_table'] . '.xsourcex = :xsourcex', [':xsourcex' => \Yii::$app->user->identity->profile->sitecode]);
//                            ->andWhere($ezform_name['ezf_table'] . '.sitecode = :sitecode', [':sitecode' => \Yii::$app->user->identity->profile->sitecode])
                ;
                if ($data_id != 0) {
                    $query->andWhere($ezform_detail['ezf_table'] . '.target = :target', [':target' => $data_id]);
                }

                $dataProvider = new \yii\data\ActiveDataProvider([
                    'query' => isset($_GET['sort']) ? $query : $query->orderBy([$ezform_detail['ezf_table'] . '.create_date' => SORT_DESC]),
                    'pagination' => [
                        'pageSize' => $pageSize,
                    //'route' => '/ezforms2/fileinput/grid-update',
                    ],
                    'sort' => [
                        //'route' => '/ezforms2/fileinput/grid-update',
                        'attributes' => $data_column
                    ]
                ]);
//\appxq\sdii\utils\VarDumper::dump($dataProvider->getModels());

                $view = $popup ? '_view-popup' : '_grid';
                return $this->renderAjax($view, [
//                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'ezf_type_id' => $ezf_type_id,
                            'ezf_name_id' => $ezf_name_id,
                            'ezf_detail_id' => $ezf_detail_id,
                            'modal' => $modal,
                            'reloadDiv' => $reloadDiv,
                            'data_column' => $data_column,
                            'data_column_name' => $data_column_name,
                            'data_column_type' => $data_column_type,
                            'target' => $target,
                            'targetField' => $targetField,
                            'disabled' => $disabled,
                            'addbtn' => $addbtn,
                            'default_column' => $default_column,
                            'pageSize' => $pageSize,
                            'orderby' => $orderby,
                            'type_id' => $type_id,
                            'data_url' => $data_url,
                            'data_id' => $data_id,
                            'ezform_type' => $ezform_type,
                            'ezform_name' => $ezform_name,
                            'ezform_detail' => $ezform_detail,
//                        'data_column_detail' => $data_column_detail,
                            'subModal' => $subModal,
                            'module' => $module
                ]);
            } catch (yii\db\Exception $ex) {
                EzfFunc::addErrorLog($ex);
                return "<code>เกิดข้อผิดพลาดในการโหลดข้อมูล</code>";
            }
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionGetForm() {
        if (\Yii::$app->getRequest()->isAjax) {
            try {
                $ezf_id_cat = isset($_GET['ezf_id_cat']) ? $_GET['ezf_id_cat'] : 0;
                $ezf_id_subcat = isset($_GET['ezf_id_subcat']) ? $_GET['ezf_id_subcat'] : 0;
                $ezf_id_event = isset($_GET['ezf_id_event']) ? $_GET['ezf_id_event'] : 0;
                $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
                $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
                $target = isset($_GET['target']) ? $_GET['target'] : '';
                $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
                $dataCat = [];
                if ($ezf_id_cat != 0) {
                    $ezform = EzfQuery::getEzformOne($ezf_id_cat);
                    $model = new TbdataAll();
                    $model->setTableName($ezform->ezf_table);
                    $dataCat = $model->find()->all();
                    $dataCat = \yii\helpers\ArrayHelper::map($dataCat, 'id', 'category');
                }
                return $this->renderAjax('_get-form', [
                            'modal' => $modal,
                            'reloadDiv' => $reloadDiv,
                            'target' => $target,
                            'targetField' => $targetField,
                            'ezf_id_cat' => $ezf_id_cat,
                            'ezf_id_subcat' => $ezf_id_subcat,
                            'ezf_id_event' => $ezf_id_event,
                            'dataCat' => $dataCat
                ]);
            } catch (\yii\db\Exception $ex) {
                EzfFunc::addErrorLog($ex);
            }
        } else {
            throw new \yii\web\NotFoundHttpException(\Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionGetSubcat() {
        $out = [];
        if (\Yii::$app->request->post('depdrop_parents')) {
            $post = \Yii::$app->request->post('depdrop_parents');
            if ($post != null) {
                $query = new \yii\db\Query();
                $dataSub = $query->select(['id', 'sub_cat'])->from('zdata_ec_subcat')->where('target = :target', [':target' => $post[0]])->all();
                $selected = null;
                foreach ($dataSub as $value) {
                    $out[] = ['id' => $value['id'], 'name' => $value['sub_cat']];
                }

                return \yii\helpers\Json::encode(['output' => $out, 'selected' => $selected]);
            }
        }
        return \yii\helpers\Json::encode(['output' => '', 'selected' => '']);
    }

}
