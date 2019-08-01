<?php

namespace backend\modules\cpoe\controllers;

use yii\web\Controller;
use Yii;
use yii\web\Response;
use yii\db\Query;
use backend\modules\patient\classes\PatientFunc;

/**
 * Default controller for the `modules` module
 */
class PatentConfigController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $ezf_id = \backend\modules\patient\Module::$formID['project'];

        return $this->render('index', [
                    'ezf_id' => $ezf_id,
        ]);
    }

    public function actionGetGridview() {
        $ezf_id = \backend\modules\patient\Module::$formID['project_patient_name'];
        $target = Yii::$app->request->get('target');
        $date_start_project = Yii::$app->request->get('date_start_project');
        $date_end_project = Yii::$app->request->get('date_end_project');
        $params = Yii::$app->request->get();
        $searchModel = \backend\modules\patient\classes\PatientFunc::getModel($ezf_id, '');
        $searchModel->load($params);
        $searchModel['target_project'] = isset($params['target']) ? $params['target'] : '';
        $searchModel['date_start_project'] = $params['date_start_project'];
        $searchModel['date_end_project'] = $params['date_end_project'];

        $dataProvider = \backend\modules\cpoe\classes\CpoeFunc::getPatientInProject($searchModel);
        if ($date_start_project && $date_end_project) {
            $initdata = ['date_start_project' => $date_start_project, 'date_end_project' => $date_end_project];
            $initdata = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($initdata);
        } else {
            $initdata = [];
        }

        return $this->renderAjax('gridview', [
                    'ezf_id' => $ezf_id,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                    'target' => $target,
                    'date_start_project' => $date_start_project,
                    'date_end_project' => $date_end_project,
                    'initdata' => $initdata
        ]);
    }

    public function actionDelete() {
        $id = Yii::$app->request->get('id');
        $ezfproject_patient_name_id = '1517227483007856300';
        $ezfproject_patient_name_tbname = 'zdata_project_patient_name';
        if ($id) {
            $data['rstat'] = '3';
            PatientFunc::saveDataNoSys($ezfproject_patient_name_id, $ezfproject_patient_name_tbname, $id, $data);
        }
    }

    public function actionGetFullnameByCid() {
        $cid = Yii::$app->request->get('cid');
        $dataRight = PatientFunc::getRightOnlineByNhso($cid);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!empty($dataRight['fname'])) {
            return [
                'fullname' => $dataRight['title_name'] . $dataRight['fname'] . ' ' . $dataRight['lname'],
                'subinscl_name'=>$dataRight['subinscl_name']
            ];
        } else {
            return [
                'success' => false
            ];
        }
    }

    public function actionGetList($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, project_name AS text')
                    ->from('zdata_project')
                    ->where(['like', 'project_name', $q])
                    ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => \backend\modules\cpoe\classes\CpoeQuery::getProjectNoByid($id)];
        }
        return $out;
    }

}
