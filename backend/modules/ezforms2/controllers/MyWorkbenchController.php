<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\db\Query;
use backend\modules\ezforms2\classes\EzfFunc;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\models\TbdataAll;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\ezforms2\classes\MyWorkbenchFunc;

class MyWorkbenchController extends Controller {

    public function actionView() {
        $ezf_id = Yii::$app->request->get('ezf_id', '');
        $field_label = Yii::$app->request->get('field_label', '');
        $field_value = Yii::$app->request->get('field_value', '');
        $docTypeId = Yii::$app->request->get('docTypeId', '');
        $docNameId = Yii::$app->request->get('docNameId', '');
        $docDetailId = Yii::$app->request->get('docDetailId', '');
        $modal = Yii::$app->request->get('modal', '');
        $reloadDiv = Yii::$app->request->get('reloadDiv', '');
        $pageSize = Yii::$app->request->get('pageSize', '');
        $field_column = Yii::$app->request->get('field_column', '');
        $field_taget = Yii::$app->request->get('field_taget', '');

        $items = [];
        $items[] = [
            'label' => 'Open Activity',
            'headerOptions' => ['data-value' => '0', 'class' => 'tabHeader'],
            'content' => '',
        ];
        $items[] = [
            'label' => 'Study Conduct',
            'headerOptions' => ['data-value' => '2', 'class' => 'tabHeader'],
            'content' => '',
        ];
        $items[] = [
            'label' => 'Workbench',
            'headerOptions' => ['data-value' => '3', 'class' => 'tabHeader'],
            'content' => '',
            'active' => true,
        ];

        return $this->renderAjax('_view', [
                    'items' => $items,
                    'ezf_id' => $ezf_id,
                    'docTypeId' => $docTypeId,
                    'docNameId' => $docNameId,
                    'docDetailId' => $docDetailId,
                    'field_value' => $field_value,
                    'field_label' => $field_label,
                    'modal' => $modal,
                    'reloadDiv' => $reloadDiv,
                    'pageSize' => $pageSize,
                    'field_column' => $field_column,
                    'field_taget' => $field_taget
        ]);

//        return TabsX::widget([
//            'id'=>'eztabs',
//            'items'=>$items,
//        ]);
    }

    public function actionGridWorkbench($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $popup = isset($_GET['popup']) ? $_GET['popup'] : 0;
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $ezf_match_id = isset($_GET['ezf_match_id']) ? $_GET['ezf_match_id'] : '';
            $ezf_name_id = isset($_GET['ezf_name_id']) ? $_GET['ezf_name_id'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
            $data_column = isset($_GET['data_column']) ? $_GET['data_column'] : '';
            $disabled = isset($_GET['disabled']) ? $_GET['disabled'] : 0;
            $addbtn = isset($_GET['addbtn']) ? $_GET['addbtn'] : 1;
            $default_column = isset($_GET['default_column']) ? $_GET['default_column'] : 1;
            $pageSize = isset($_GET['pageSize']) ? $_GET['pageSize'] : 50;
            $order_column = isset($_GET['order_column']) ? $_GET['order_column'] : '';
            $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 4;
            $column_download = isset($_GET['column_download']) ? $_GET['column_download'] : '';
            $column_status = isset($_GET['column_status']) ? $_GET['column_status'] : '';

            $data_column = EzfFunc::stringDecode2Array($data_column);
            $order_column = EzfFunc::stringDecode2Array($order_column);


            $ezform = EzfQuery::getEzformOne($ezf_id);
            $ezf_match = EzfQuery::getEzformOne($ezf_match_id);
            $ezf_name = EzfQuery::getEzformOne($ezf_name_id);

            if (empty($data_column)) {

                $data_column = SDUtility::string2Array($ezform->field_detail);
            }

            $searchModel = NULL;
            $dataProvider = NULL;

            if ($popup == 0) {
                $searchModel = new TbdataAll();
                $searchModel->setTableName($ezform->ezf_table);

                if ($targetField == '') {
                    $modelTarget = EzfQuery::getTargetOne($ezform->ezf_id);
                    if ($modelTarget) {
                        $targetField = $modelTarget['ezf_field_name'];
                    }
                }

                if ($target != '') {
                    $searchModel[$targetField] = $target;
                }

                $dataProvider = MyWorkbenchFunc::modelSearch($searchModel, $ezform, $ezf_match, $ezf_name, $targetField, $data_column, Yii::$app->request->queryParams, $pageSize, $order_column, $orderby);
            }

            $view = $popup ? '_view-popup' : '_grid-workbench';

            return $this->renderAjax($view, [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'ezform' => $ezform,
                        'ezf_name' => $ezf_name,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'data_column' => $data_column,
                        'target' => $target,
                        'targetField' => $targetField,
                        'disabled' => $disabled,
                        'addbtn' => $addbtn,
                        'default_column' => $default_column,
                        'pageSize' => $pageSize,
                        'order_column' => $order_column,
                        'orderby' => $orderby,
                        'column_download' => $column_download,
                        'column_status' => $column_status,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionUpdateResult() {
        try {
            $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : '';
            $dataid = isset($_POST['id']) ? $_POST['id'] : '';
            $value = isset($_POST['value']) ? $_POST['value'] : '';
            $user_id = \Yii::$app->user->id;
            $ezform = EzfQuery::getEzformOne($ezf_id);
            $model = new TbdataAll();
            $model->setTableName($ezform['ezf_table']);
            $find = $model->findOne(['id' => $dataid]);
            $data_check = isset($find->check_user) ? SDUtility::string2Array($find->check_user) : [];
            if (!in_array($user_id, $data_check)) {
                array_push($data_check, "{$user_id}");
                
            }
            $find->check_user = SDUtility::array2String($data_check);
//            $data_user = isset($find->assign_name) ? SDUtility::string2Array($find->assign_name) : [];
//            $count_user = count($data_user);
//            $num = 0;
//            foreach ($data_user as $vUser) {
////                foreach ($data_check as $vCheck) {
//                if (in_array($vUser, $data_check)) {
//                    $num++;
//                }
////                }
//            }
            
            if ($value == 2 || $value == 3) {
                $find->approve_status = $value;
            }
//        }

            if ($find->update()) {
                return TRUE;
            } else {
                return FALSE;
            }
//            
        } catch (\yii\base\Exception $ex) {
//            \appxq\sdii\utils\VarDumper::dump($ex);
            return false;
        }
    }

}
