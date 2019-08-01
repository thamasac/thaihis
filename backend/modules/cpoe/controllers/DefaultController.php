<?php

namespace backend\modules\cpoe\controllers;

use yii\web\Controller;
use Yii;
use backend\modules\cpoe\classes\CpoeFunc;
use backend\modules\thaihis\classes\ThaiHisQuery;

class DefaultController extends Controller {

    public function actionIndex() {
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $pt_id = Yii::$app->request->get('ptid');
        $action = Yii::$app->request->get('action');
        $ezfProfile_id = \backend\modules\patient\Module::$formID['profile'];

        $visit_type = '';
        $action_id = '';
        $visit_tran_id = '';
        if ($action == 'que') {
            $action_id = Yii::$app->request->get('visitid');
            $visit_type = Yii::$app->request->get('visit_type');
            $visit_tran_id = Yii::$app->request->get('visit_tran_id');
        } elseif ($action == 'appoint') {
            $action_id = Yii::$app->request->get('appid');
        }

        return $this->renderAjax('index', [
                    'pt_id' => $pt_id,
                    'action' => $action,
                    'visit_type' => $visit_type,
                    'action_id' => $action_id,
                    'ezfProfile_id' => $ezfProfile_id,
                    'visit_tran_id' => $visit_tran_id
        ]);
    }

    public function actionQueueView() {
        $reloadDiv = Yii::$app->request->get('reloadDiv', 'queue');
        $userProfile = Yii::$app->user->identity->profile->attributes;
        $pt_id = Yii::$app->request->get('ptid', '');
        $options = Yii::$app->request->get('options', '');
        $options = \appxq\sdii\utils\SDUtility::string2Array($options);
        $options['target'] = $pt_id;
        $ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : '';
        $current_url = isset($options['current_url']) ? $options['current_url'] : \yii\helpers\Url::base();
        $que_type = Yii::$app->request->get('que_type', '1');
        $template_content = isset($options['template_content']) ? $options['template_content'] : '';

        $fields = isset($options['fields']) ? $options['fields'] : '';

        isset($options['image_field']) && $options['image_field'] != '' ? array_push($fields, $options['image_field']) : '';

//        \appxq\sdii\utils\VarDumper::dump($options);
        $modelFields = ThaiHisQuery::getEzformFields2($fields, $ezf_id);
        $fields = implode(',', $fields);
//        \appxq\sdii\utils\VarDumper::dump($fields);
        $data = (new \yii\db\Query())
                ->select(['ezf_field_name'])
                ->from('ezform_fields')
                ->where('ezf_field_id IN (' . $fields . ')')
                ->all();
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = $value['ezf_field_name'];
        }
        $txtField = implode(',', $fields);
        $date = date('Y-m-d');
        if ($que_type == '3') {
            $dataProviderQue = CpoeFunc::getVisitOpdAll($userProfile['department'], $date);
        } elseif ($que_type == '1') {
            $dataProviderQue = CpoeFunc::getVisitQue($options, $userProfile['department'], $modelFields);
            $dataProviderQue = CpoeFunc::getVisitDept($userProfile['department'], $date, $txtField);
        } elseif ($userProfile['position'] == '2') {
            $dataProviderQue = CpoeFunc::getVisitDoctor($userProfile['user_id'], $date);
        } else {
            $dataProviderQue = CpoeFunc::getVisitDept($userProfile['department'], $date);
        }
//        \appxq\sdii\utils\VarDumper::dump($current_url);
        return $this->renderAjax('_que', [
                    'dataProviderQue' => $dataProviderQue,
                    'reloadDiv' => $reloadDiv,
                    'pt_id' => $pt_id,
                    'que_type' => $que_type,
                    'ezf_id' => $options['ezf_id'],
                    'image_field' => $options['image_field'],
                    'bdate_field' => $options['bdate_field'],
                    'template_custom' => $template_content,
                    'modelFields' => $modelFields,
                    'current_url' => $current_url
        ]);
    }

    public function actionAppointView() {
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $userProfile = Yii::$app->user->identity->profile->attributes;
        $doc_id = '';
        if ($userProfile['position'] == '2') {
            $doc_id = $userProfile['user_id'];
        }
        $dataProviderAppoint = CpoeFunc::getAppDept($userProfile['department'], date('Y-m-d'), $doc_id);

        return $this->renderAjax('_appoint', [
                    'dataProviderAppoint' => $dataProviderAppoint,
                    'reloadDiv' => $reloadDiv,
        ]);
    }

}
