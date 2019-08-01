<?php

namespace backend\modules\tmf\controllers;

use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\VarDumper;
use common\models\User;
use common\modules\user\models\Profile;
use Yii;
use yii\db\Exception;
use yii\web\Controller;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use yii\db\Query;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfUiFuncTmf;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\models\TbdataAll;
use yii\web\NotFoundHttpException;

class TmfController extends Controller {

    public function actionEmail() {
        $link = Url::to(['/tmf/tmf/index']);
        Yii::$app->mailer->compose()
                ->setFrom(['ncrc.damasac@gmail.com' => \Yii::$app->name . ''])
                ->setTo('ncrc.damasac@gmail.com')
                ->setSubject('คำถามของคุณที่ ' . \Yii::$app->name)
                ->setTextBody('หัวข้อ  ติดตามคำถามของคุณได้ที่ : ' . $link) //เลือกอยางใดอย่างหนึ่ง
                ->setHtmlBody('หัวข้อ  ติดตามคำถามของคุณได้ที่ : ' . $link) //เลือกอยางใดอย่างหนึ่ง
                ->send();
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

//                \dms\aomruk\classese\Notify::setNotify()
//                        ->assign('1')
//                        ->notify('การแจ้งเตือน')
//                        ->detail('กรุณาบันทึกข้อมูล')
//                        ->sendSaveForm('1521627828021602700');
//                \dms\aomruk\classese\Notify::setNotify()
//                        ->assign('1')
//                        ->notify('การแจ้งเตือน')
//                        ->detail('กรุณาแก้ไขข้อมูล')
//                        ->sendEditForm('1521627828021602700', '1521874490083614800');
//                \dms\aomruk\classese\Notify::setNotify()
//                        ->assign('1')
//                        ->notify('การแจ้งเตือน')
//                        ->detail('ตรวจสอบข้อมูล')
//                        ->sendViewForm('1521627828021602700', '1521874490083614800');
//                \dms\aomruk\classese\Notify::setNotify()
//                        ->assign('1')
//                        ->notify('การแจ้งเตือน')
//                        ->detail('ตรวจสอบข้อมูล')
//                        ->sendRedirect('http://backend.nhis.test/ezforms2/data-lists/index?ezf_id=1521627828021602700');

                $data_column_detail = EzfFunc::stringDecode2Array($data_column_detail);
                $data_column_name = EzfFunc::stringDecode2Array($data_column_name);
//                $order_column_detail = EzfFunc::stringDecode2Array($order_column_detail);

                $ezform_type = EzfQuery::getEzformOne($ezf_type_id);
                $ezform_name = EzfQuery::getEzformOne($ezf_name_id);
                $ezform_detail = EzfQuery::getEzformOne($ezf_detail_id);

                $query = new \yii\db\Query();
                $result = $query->select($type_field_value . ',' . $type_field_label)
                        ->from($ezform_type['ezf_table'])
                        ->where("rstat not in(0,3)")
                        ->orderBy(['order' => SORT_ASC])
                        ->all();
                $items = [];

                $data_url = Url::to(['/tmf/tmf/sub-view',
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
                $url = Url::to(['/tmf/tmf/view',
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
                    'label' => 'Document Log',
                    'active' => $type_id == 0 ? true : false,
                    'headerOptions' => [
                        'data-id' => '0',
                        'data-url' => $url,
                        'class' => 'tabHeader'],
                    'content' => '',
                ];
                foreach ($result as $key => $val) {
                    $items[] = [
                        'label' => $val[$type_field_label] != '' ? $val[$type_field_label] : '',
                        'active' => $type_id == $val[$type_field_value] ? true : false,
                        'headerOptions' => [
                            'data-id' => $val[$type_field_value],
                            'data-url' => $url,
                            'class' => 'tabHeader editTab'],
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
//                \appxq\sdii\utils\VarDumper::dump($data_column_detail);
                foreach ($data_column_detail as $value) {
                    $data_column[] = $value;
                    $column_detail .= "zdata_final." . $value . " ,";
                }
                $column_detail .= 'zdata_final.id AS detail_id,zdata_final.update_date, zdata_final.check_user,zdata_final.target ,';
                $column_detail = substr($column_detail, 0, strlen($column_detail) - 1);
                $query = new Query();
                $query->select(
                                $ezform_name['ezf_table'] . '.*,'
                                . 'zdata_doc_detail.*,'
                    . ' zdata_document_type.id AS type_id,zdata_document_name.id AS name_id'
//                                . '' . $ezform_type['ezf_table'] . '.' . $data_column_type
                        )
                        ->from('`' . $ezform_name['ezf_table'] . '`')
                        ->leftJoin('( 
                        SELECT ' . $column_detail . '
                        FROM ' . $ezform_detail['ezf_table'] . ' as zdata_final 
                        WHERE zdata_final.rstat not in (0,3) AND zdata_final.update_date=(SELECT max(zde.update_date)  FROM ' . $ezform_detail['ezf_table'] . ' as zde WHERE zde.target=zdata_final.target AND zde.rstat not in(0,3))

                        GROUP BY zdata_final.target 
                        )as zdata_doc_detail', $ezform_name['ezf_table'] . '.id = zdata_doc_detail.target')
                        ->leftJoin($ezform_type['ezf_table'], $ezform_name['ezf_table'] . '.target = ' . $ezform_type['ezf_table'] . '.id AND '.$ezform_type['ezf_table'].'.rstat not in (0,3)')
                        ->where($ezform_name['ezf_table'] . ".rstat not in(0,3) AND ".$ezform_type['ezf_table'].'.rstat not in (0,3) ')
                        ->andWhere($ezform_name['ezf_table'] . '.xsourcex = :xsourcex OR '.$ezform_name['ezf_table'] . '.user_create = :user_create', [
                            ':xsourcex' => \Yii::$app->user->identity->profile->sitecode,
                            ':user_create' => '1530521953014158200'
                        ]);

                if ($type_id != 0) {
                    $query->andWhere("{$ezform_name['ezf_table']}.target = :target", [':target' => $type_id]);
//                          
                }
                
                if ($data_id != 0) {
                    $query->andWhere("{$ezform_name['ezf_table']}.id = :id", [':id' => $data_id]);
//                          
                }
//VarDumper::dump($query->createCommand()->rawSql);
                $dataProvider = new \yii\data\ActiveDataProvider([
                    'query' => isset($_GET['sort'])?$query : $query->orderBy(['zdata_doc_detail.update_date' => SORT_DESC]),
                    'pagination' => [
                        'pageSize' => $pageSize,
                    //'route' => '/ezforms2/fileinput/grid-update',
                    ],
                    'sort' => [
                        //'route' => '/ezforms2/fileinput/grid-update',
                        'attributes' => $data_column
                    ]
                ]);
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
                return "<div class='alert alert-danger text-center'>".Yii::t('tmf', 'Load data error')."</div>";
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

                $data_url = Url::to(['/tmf/tmf/sub-view',
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
                        ->andWhere($ezform_name['ezf_table'] . '.xsourcex = :xsourcex', [':xsourcex' => \Yii::$app->user->identity->profile->sitecode])
                    ->orWhere($ezform_name['ezf_table'] . '.user_create = :user_create', [':user_create' => '1530521953014158200'])
//                            ->andWhere($ezform_name['ezf_table'] . '.sitecode = :sitecode', [':sitecode' => \Yii::$app->user->identity->profile->sitecode])
                        ->orderBy([$ezform_detail['ezf_table'] .'.update_date' => SORT_DESC]);
                if ($data_id != 0) {
                    $query->andWhere($ezform_detail['ezf_table'] . '.target = :target', [':target' => $data_id]);
                }
//            \appxq\sdii\utils\VarDumper::dump($result);
                $dataProvider = new \yii\data\ActiveDataProvider([
                    'query' => $query,
                    'pagination' => [
                        'pageSize' => $pageSize,
                    //'route' => '/ezforms2/fileinput/grid-update',
                    ],
                    'sort' => [
                        //'route' => '/ezforms2/fileinput/grid-update',
                        'attributes' => $data_column
                    ]
                ]);


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
                return "<div class='alert alert-danger text-center'>".Yii::t('tmf', 'Load data error')."</div>";
            }
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionGetUser() {
        $user_id = Yii::$app->request->get('user_id','');
        $id = '';
        $name = '';
        if($user_id != ''){
            $user = Profile::find()->where(['user_id'=>$user_id])->one();
            if($user){
                $id = $user_id;
                $name = $user['firstname']." ".$user['lastname'];
            }
        }else{
            $id = \Yii::$app->user->id;
            $name = \Yii::$app->user->identity->profile->firstname . " " . \Yii::$app->user->identity->profile->lastname;
        }

        return \yii\helpers\Json::encode(['id' => (string)$id, 'name' => $name]);
//        return $name;
    }

    public function actionSetStatus() {
        try {
            $ezf_name = Yii::$app->request->get('ezf_name');
            $data_id = Yii::$app->request->get('data_id');
            Yii::$app->db->createCommand("UPDATE {$ezf_name} SET acknowledge_result = :status WHERE id = :id")->bindValues([':status' => '1', ':id' => $data_id])->execute();
        } catch (\yii\db\Exception $ex) {
            EzfFunc::addErrorLog($ex);
        }
    }

    public function actionViewAssign() {
        try {
            $data_id = EzfFunc::stringDecode2Array(Yii::$app->request->get('data-id', ''));
//            $name = SDUtility::string2Array(Yii::$app->request->get('data-name', []));
            $data_check = SDUtility::string2Array(Yii::$app->request->get('data-check', []));
            $data_approve = SDUtility::string2Array(Yii::$app->request->get('data-approve', []));
            $data_action = Yii::$app->request->get('data-action', '');
//            \appxq\sdii\utils\VarDumper::dump($data_approve);
//            $data_id = [];
            $query = new \yii\db\Query();
//        if (!empty($role)) {
//            $query->select('user_id')->from('zdata_matching')->where('1');
//            foreach ($role as $key => $vRole) {
//                if ($key == 0) {
//                    $query->andWhere('role_name = ' . $vRole);
//                } else {
//                    $query->orWhere('role_name = ' . $vRole);
//                }
//            }
//            $data_id = $query->andWhere('user_id != "" OR user_id IS NOT NULL')->all();
//        }


            $i = 0;
            $query->select('profile.user_id,profile.firstname,profile.lastname')->from('profile')
                    ->leftJoin('zdata_matching', 'zdata_matching.user_id LIKE concat(\'%"\',profile.user_id ,\'"%\')')
//                    ->leftJoin(',zdata_role.role_detail zdata_role', 'zdata_role.role_name = zdata_matching.role_name')
                    ->where('1');
//            foreach ($role as $key => $vRole) {
//                if ($i == 0) {
//                    $query->andWhere('zdata_matching.role_name = :role',[':role'=>$vRole]);
//                    $i++;
//                } else {
//                    $query->orWhere('zdata_matching.role_name = :role',[':role'=>$vRole]);
//                }
//            }
//            $query->andWhere('zdata_matching.user_id != "" OR zdata_matching.user_id IS NOT NULL');
//        foreach ($data_id as $vUser) {
////            foreach ($vUser as $vId) {
//                if ($i == 0) {
//                    $query->andWhere('user_id = ' . $vUser['user_id']);
//                    $i++;
//                } else {
//                    $query->orWhere('user_id = ' . $vUser['user_id']);
//                }
////            }
//            
//        }
//        
            foreach ($data_id as $vId) {
                if ($i == 0) {
                    $query->andWhere('profile.user_id = :id' . $i, [':id' . $i => $vId]);
                    $i++;
                } else {
                    $query->orWhere('profile.user_id = :id' . $i, [':id' . $i => $vId]);
                    $i++;
                }
            }
            if (empty($data_id) || count($data_id) < 0) {
                $query->andWhere('profile.user_id = 00000');
            }
            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => $query->groupBy('profile.user_id'),
            ]);
            return $this->renderAjax('_view-assign', [
                        'dataProvider' => $dataProvider,
                        'data_check' => $data_check,
                        'data_approve' => $data_approve,
                        'data_action' => $data_action
            ]);
        } catch (\yii\db\Exception $ex) {
            EzfFunc::addErrorLog($ex);
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
            $data_role = isset($find->final_role) ? SDUtility::string2Array($find->final_role) : [];
            $data_user = isset($find->final_name) ? SDUtility::string2Array($find->final_name) : [];
            $data_role = \backend\modules\tmf\classes\TmfFn::getRole($data_role);
            $data_check = isset($find->check_user) ? SDUtility::string2Array($find->check_user) : [];
            $data_approve = isset($find->approve_status) ? SDUtility::string2Array($find->approve_status) : [];
            if (!in_array($user_id, $data_check)) {
                if ($find->status == '2') {
                    $find->status = '3';
                }
                if ($value == 2) {
                    $data_approve[$user_id] = "1";
                } else if ($value == 3) {
                    $data_approve[$user_id] = "0";
                }
                array_push($data_check, "{$user_id}");
            }

            $find->check_user = SDUtility::array2String($data_check);
            $find->approve_status = SDUtility::array2String($data_approve);
            if ($find->status == '1' || $find->status == '4') {

                $dataAll = array_merge($data_user, $data_role);
                $result = array();
                foreach ($dataAll as $value) {
                    if (!isset($result[$value]))
                        $result[$value] = $value;
                }
                if (sizeof($data_check) == sizeof($result)) {
                    $find->status = $find->status + 1;
                }
            }
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
//        }
            if ($find->update()) {
                return TRUE;
            } else {
                return TRUE;
            }
//            
        } catch (\yii\db\Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return false;
        }
    }

    public function actionDelete(){
        try{
            $dataid_type = Yii::$app->request->post('dataid_type','');
            $dataid_name = Yii::$app->request->post('dataid_name','');
            $dataid_detail = Yii::$app->request->post('dataid_detail','');
//            Yii::$app->db->createCommand()->update('zdata_document_type',['rstat' => '3'],['id'=>$dataid_type])->execute();
            Yii::$app->db->createCommand()->update('zdata_document_name',['rstat' => '3'],['target'=>$dataid_type])->execute();
            Yii::$app->db->createCommand()->update('zdata_document_detail',['rstat' => '3'],['target'=>$dataid_name])->execute();
            $result = [
                'status' => 'success',
                'message' => SDHtml::getMsgSuccess(),
                'data' => '',
            ];

        }catch (Exception $ex){
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError(),
                'data' => '',
            ];
            EzfFunc::addErrorLog($ex);
        }
        return json_encode($result);
    }

}
