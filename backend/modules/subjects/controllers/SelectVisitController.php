<?php

namespace backend\modules\subjects\controllers;

use yii\web\Controller;
use backend\modules\gantt\models\InvProject;
use Yii;
use appxq\sdii\helpers\SDHtml;
use yii\web\Response;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfQuery;

class OpenActivityController extends Controller {

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex() {
        $mid = Yii::$app->request->get('module_id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $subject_profile_ezf = Yii::$app->request->get('subject_profile_ezf');
        $subject_detail_ezf = Yii::$app->request->get('subject_detail_ezf');
        $profile_column = Yii::$app->request->get('profile_column');
        $detail_column = Yii::$app->request->get('detail_column');
        $detail_column2 = Yii::$app->request->get('detail_column2');
        $field_subject = Yii::$app->request->get('field_subject');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $modal = Yii::$app->request->get('modal');

        $profile_form = EzfQuery::getEzformOne($subject_profile_ezf);
        $detail_form = EzfQuery::getEzformOne($subject_detail_ezf);

        $activityQuery = SubjectManagementQuery::GetTableActivity($profile_form, $detail_form->ezf_table, $detail_column);
        $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $activityQuery,
            'pagination' => [
                'pageSize' => 50,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
            'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
                'defaultOrder' => [
                //'create_date' => SORT_DESC
                ]
            ]
        ]);

        return $this->renderAjax('index', [
                    'reloadDiv' => $reloadDiv,
                    'module_id' => $mid,
                    'subject_profile_ezf' => $subject_profile_ezf,
                    'subject_detail_ezf' => $subject_detail_ezf,
                    'dataProvider' => $dataProvider,
                    'profile_column' => $profile_column,
                    'detail_column' => $detail_column,
                    'detail_column2' => $detail_column2,
                    'field_subject' => $field_subject,
                    'schedule_id' => $schedule_id,
                    'visitSchedule'=>$visitSchedule,
                    'modal' => $modal,
        ]);
    }

    public function actionViewSchedule() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $visit_id = Yii::$app->request->get('visit_id');
        $actual_date = Yii::$app->request->get('actual_date');
        $target = Yii::$app->request->get('data_id');
        $group_id = Yii::$app->request->get('group_id');

        return $this->renderAjax('_view-schedule', [
                    'schedule_id' => $schedule_id,
                    'visit_id' => $visit_id,
                    'actual_this_date' => $actual_date,
                    'target' => $target,
                    'group_id'=>$group_id,
        ]);
    }

    public function actionActivityDetail() {
        $mid = Yii::$app->request->get('id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $subject_profile_ezf = Yii::$app->request->get('subject_profile_ezf');
        $subject_detail_ezf = Yii::$app->request->get('subject_detail_ezf');
        $data_id = Yii::$app->request->get('data_id');
        $field_subject = Yii::$app->request->get('field_subject');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $profile_column = \appxq\sdii\utils\SDUtility::string2Array(base64_decode(Yii::$app->request->get('profile_column')));
        $detail_column = \appxq\sdii\utils\SDUtility::string2Array(base64_decode(Yii::$app->request->get('detail_column')));
        $detail_column2 = \appxq\sdii\utils\SDUtility::string2Array(base64_decode(Yii::$app->request->get('detail_column2')));

        $modal = Yii::$app->request->get('modal');
        $profile_form = EzfQuery::getEzformOne($subject_profile_ezf);
        $detail_form = EzfQuery::getEzformOne($subject_detail_ezf);
        $tb2_column = [];
        foreach ($detail_column as $key => $val) {
            $tb2_column[] = $detail_form->ezf_table . '.' . $val;
        }
        $profileQuery = SubjectManagementQuery::GetTableQuery($profile_form, ['id' => $data_id], 'one');

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $profileQuery,
            'pagination' => [
                'pageSize' => 50,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
            'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
                'defaultOrder' => [
                //'create_date' => SORT_DESC
                ]
            ]
        ]);

        return $this->renderAjax('activity-detail', [
                    'reloadDiv' => $reloadDiv,
                    'dataProvider' => $dataProvider,
                    'subject_profile_ezf' => $subject_profile_ezf,
                    'subject_detail_ezf' => $subject_detail_ezf,
                    'profile_column' => $profile_column,
                    'detail_column' => $detail_column,
                    'detail_column2' => $detail_column2,
                    'field_subject' => $field_subject,
                    'schedule_id' => $schedule_id,
                    'data_id' => $data_id,
                    'modal' => $modal,
        ]);
    }

    public function actionGridDetail() {
        $mid = Yii::$app->request->get('id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $subject_profile_ezf = Yii::$app->request->get('subject_profile_ezf');
        $subject_detail_ezf = Yii::$app->request->get('subject_detail_ezf');
        $field_subject = Yii::$app->request->get('field_subject');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $data_id = Yii::$app->request->get('data_id');
        $modal = Yii::$app->request->get('modal');
        $profile_column = \appxq\sdii\utils\SDUtility::string2Array(base64_decode(Yii::$app->request->get('profile_column')));
        $detail_column = \appxq\sdii\utils\SDUtility::string2Array(base64_decode(Yii::$app->request->get('detail_column')));
        $detail_column2 = \appxq\sdii\utils\SDUtility::string2Array(base64_decode(Yii::$app->request->get('detail_column2')));
        $modal = Yii::$app->request->get('modal');

        $profile_form = EzfQuery::getEzformOne($subject_profile_ezf);
        $detail_form = EzfQuery::getEzformOne($subject_detail_ezf);
        $tb2_column = [];
        foreach ($detail_column as $key => $val) {
            $tb2_column[] = $detail_form->ezf_table . '.' . $val;
        }
        $detailQuery = SubjectManagementQuery::GetTableQuery($detail_form, ['target' => $data_id]);
        $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $detailQuery,
            'pagination' => [
                'pageSize' => 50,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
            'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
                'defaultOrder' => [
                //'create_date' => SORT_DESC
                ]
            ]
        ]);

        return $this->renderAjax('_grid-detail', [
                    'reloadDiv' => $reloadDiv,
                    'dataProvider' => $dataProvider,
                    'subject_profile_ezf' => $subject_profile_ezf,
                    'subject_detail_ezf' => $subject_detail_ezf,
                    'profile_column' => $profile_column,
                    'detail_column' => $detail_column,
                    'field_subject' => $field_subject,
                    'detail_column2' => $detail_column2,
                    'schedule_id' => $schedule_id,
                    'visitSchedule'=>$visitSchedule,
                    'data_id' => $data_id,
                    'modal' => $modal,
                    'modal' => $modal,
        ]);
    }

    public function actionGetGroup() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $group_id = Yii::$app->request->get('group_id');
        $dataGroup = SubjectManagementQuery::getVisitScheduleByEzf($schedule_id, $group_id);
        return json_encode($dataGroup);
    }

}
