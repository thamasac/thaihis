<?php

namespace backend\modules\subjects\controllers;

use yii\web\Controller;
use backend\modules\gantt\models\InvProject;
use Yii;
use appxq\sdii\helpers\SDHtml;
use yii\web\Response;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfQuery;
use appxq\sdii\utils\SDdate;

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

        return $this->renderAjax('index', [
                    'reloadDiv' => $reloadDiv,
                    'module_id' => $mid,
                    'subject_profile_ezf' => $subject_profile_ezf,
                    'subject_detail_ezf' => $subject_detail_ezf,
                    'profile_column' => $profile_column,
                    'detail_column' => $detail_column,
                    'detail_column2' => $detail_column2,
                    'field_subject' => $field_subject,
                    'schedule_id' => $schedule_id,
                    'profile_ezf' => $subject_profile_ezf,
                    'detail_ezf' => $subject_detail_ezf,
                    'modal' => $modal,
        ]);
    }

    public function actionOpenActivity() {
        $module_id = Yii::$app->request->get('module_id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $subject_profile_ezf = Yii::$app->request->get('subject_profile_ezf');
        $subject_detail_ezf = Yii::$app->request->get('subject_detail_ezf');
        $profile_column = Yii::$app->request->get('profile_column');
        $detail_column = Yii::$app->request->get('detail_column');
        $detail_column2 = Yii::$app->request->get('detail_column2');
        $field_subject = Yii::$app->request->get('field_subject');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $subject_status = Yii::$app->request->get('subject_status');
        $modal = Yii::$app->request->get('modal');
        $number = Yii::$app->request->get('number');
        $group_id = Yii::$app->request->get('group_id');
        $visit_id = Yii::$app->request->get('visit_id');
        $date_consent = Yii::$app->request->get('date_consent');
        $next_date = Yii::$app->request->get('next_date');
        $export = Yii::$app->request->get('export');
        if (isset($date_consent) && !empty($date_consent))
            $date_consent = SDdate::phpThDate2mysqlDate($date_consent);
        if (isset($next_date) && !empty($next_date))
            $next_date = SDdate::phpThDate2mysqlDate($next_date);

        $profile_form = EzfQuery::getEzformOne($subject_profile_ezf);
        $detail_form = EzfQuery::getEzformOne($subject_detail_ezf);
        $where = null;
        $having = null;
        if ($number != '') {
            $where = "  " . $profile_form->ezf_table . ".subject_number LIKE '%{$number}%'";
        }
        if ($group_id != '' && $group_id != '1' && $group_id != '0') {
            if ($where != null)
                $where .= " AND " . $detail_form->ezf_table . ".group_name ='{$group_id}'";
            else {
                $where .= "  " . $detail_form->ezf_table . ".group_name = '{$group_id}'";
            }
        }
        if ($visit_id != '' && $visit_id != 'Loading ...') {
            if ($having != null)
                $having .= " AND visit_name ='{$visit_id}'";
            else
                $having .= "visit_name='{$visit_id}'";
        }
        if ($date_consent != '') {
            if ($having != null)
                $having .= " AND DATE(inform_date)='{$date_consent}'";
            else
                $having .= "DATE(inform_date)='{$date_consent}'";
        }
        if ($next_date != '') {
            if ($having != null)
                $having .= " AND DATE(next_visit_date)='{$next_date}'";
            else
                $having .= "DATE(next_visit_date)='{$next_date}'";
        }

        if (isset($subject_status) && $subject_status != '0') {
            if ($having != null)
                $having .= " AND type_visit='{$subject_status}'";
            else
                $having .= "  type_visit='{$subject_status}'";
        }

        array_push($detail_column, 'type_visit');

        if (isset($export) && $export == true) {
            $activityQuery = SubjectManagementQuery::GetTableActivity($profile_form, $detail_form->ezf_table, $detail_column, $where, $having);
            $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id);
            $result = $activityQuery->all();
            $type_data = EzfQuery::getFieldByName($detail_form['ezf_id'], "type_visit");
            $type_data = \appxq\sdii\utils\SDUtility::string2Array($type_data['ezf_field_data']);
            $type_data = $type_data['items'];

            $headers = [];
            $resultAdd = [];
            $profileArr = Yii::$app->db->createCommand("SELECT user_id,firstname,lastname FROM profile ", [])->queryAll();
            $profileKeyValue = [];
            foreach ($profileArr as $val) {
                $profileKeyValue[$val['user_id']] = $val;
            }
            foreach ($result as $val) {

                foreach ($val as $key => $valDat) {
                    if ($key == 'type_visit') {
                        if ($valDat != null)
                            $val[$key] = $type_data[$valDat];
                    }else if ($key == 'visit_name' || $key == 'next_visit_name') {
                        if ($valDat != null)
                            $val[$key] = $visitSchedule[$valDat]['visit_name'];
                    }
                    else if ($key == 'user_create' || $key == 'user_update') {
                        if ($valDat != null) {
                            $val[$key] = $profileKeyValue[$valDat]['firstname'] . ' ' . $profileKeyValue[$valDat]['lastname'];
                        }
                    } else if ($key == 'subject_number') {
                         $val['screening_number'] = $val[$key];
                    } else if ($key == 'subject_no') {
                        $val['subject_number'] = $val[$key];
                    } 
                }

                $resultAdd[] = $val;
            }
            
            $result = $resultAdd;
            if ($result) {
                foreach ($result[0] as $key => $val) {
                    if ($key == 'subject_number') {
                        $headers['screening_number'] = $val;
                    } else if ($key == 'subject_no') {
                        $headers['subject_number'] = $val;
                    } else {
                        $headers[$key] = $val;
                    }
                }
            }

            $fileName = \backend\modules\subjects\classes\ExportexcelFunc::ExportExcelTbdata('Subjects Management System', 'Subjects Management System', $headers, $result);
            $this->redirect(Yii::getAlias('@web/print/') . $fileName);
        } else {
            $activityQuery = SubjectManagementQuery::GetTableActivity($profile_form, $detail_form->ezf_table, $detail_column, $where, $having);
            $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id);

            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => $activityQuery,
                'pagination' => [
                    'pageSize' => 50,
                ],
                'sort' => [
                    'attributes' => $profile_column,
                    'defaultOrder' => [
                    //'create_date' => SORT_DESC
                    ]
                ]
            ]);

            return $this->renderAjax('open-activity', [
                        'reloadDiv' => $reloadDiv,
                        'module_id' => $module_id,
                        'subject_profile_ezf' => $subject_profile_ezf,
                        'subject_detail_ezf' => $subject_detail_ezf,
                        'dataProvider' => $dataProvider,
                        'profile_column' => $profile_column,
                        'detail_column' => $detail_column,
                        'detail_column2' => $detail_column2,
                        'field_subject' => $field_subject,
                        'schedule_id' => $schedule_id,
                        'visitSchedule' => $visitSchedule,
                        'profile_ezf' => $subject_profile_ezf,
                        'detail_ezf' => $subject_detail_ezf,
                        'modal' => $modal,
            ]);
        }
    }

    public function actionExportOpenActivity() {
        if (Yii::$app->getRequest()->isAjax) {
            $module_id = Yii::$app->request->get('module_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $subject_profile_ezf = Yii::$app->request->get('subject_profile_ezf');
            $subject_detail_ezf = Yii::$app->request->get('subject_detail_ezf');
            $profile_column = Yii::$app->request->get('profile_column');
            $detail_column = Yii::$app->request->get('detail_column');
            $detail_column2 = Yii::$app->request->get('detail_column2');
            $field_subject = Yii::$app->request->get('field_subject');
            $schedule_id = Yii::$app->request->get('schedule_id');
            $subject_status = Yii::$app->request->get('subject_status');
            $modal = Yii::$app->request->get('modal');

            $export = Yii::$app->request->get('export');

            //=== Export Data ===
            $url = \yii\helpers\Url::to(['/subjects/open-activity/open-activity',
                        'reloadDiv' => $reloadDiv,
                        'module_id' => $module_id,
                        'subject_profile_ezf' => $subject_profile_ezf,
                        'subject_detail_ezf' => $subject_detail_ezf,
                        'profile_column' => $profile_column,
                        'detail_column' => $detail_column,
                        'detail_column2' => $detail_column2,
                        'field_subject' => $field_subject,
                        'schedule_id' => $schedule_id,
                        'profile_ezf' => $subject_profile_ezf,
                        'detail_ezf' => $subject_detail_ezf,
                        'modal' => $modal,
                        'export' => true,
            ]);

            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $html = '<iframe src="' . $protocol . getenv('HTTP_HOST') . $url . '" width="100%" height="200px" />';

            $result = [
                'status' => 'success',
                'action' => 'report',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Load completed.'),
                'html' => $html,
            ];

            return \yii\helpers\Json::encode($result);
        } else {
            throw new \yii\web\NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionViewSchedule() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $visit_id = Yii::$app->request->get('visit_id');
        $last_visit_id = Yii::$app->request->get('last_visit_id');
        $actual_date = Yii::$app->request->get('actual_date');
        $target = Yii::$app->request->get('data_id');
        $group_id = Yii::$app->request->get('group_id');

        return $this->renderAjax('_view-schedule', [
                    'schedule_id' => $schedule_id,
                    'visit_id' => $visit_id,
                    'last_visit_id' => $last_visit_id,
                    'actual_this_date' => $actual_date,
                    'target' => $target,
                    'group_id' => $group_id,
        ]);
    }

    public function actionCheckSchedulePeriod() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $visit_id = Yii::$app->request->get('visit_id');
        $last_visit_id = Yii::$app->request->get('last_visit_id');
        $date_visit = Yii::$app->request->get('date_visit');
        $target = Yii::$app->request->get('data_id');
        $group_id = Yii::$app->request->get('group_id');

        $schedule_widget = SubjectManagementQuery::getWidgetById($schedule_id);
        $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id, $group_id);
        $val = $visitSchedule[$last_visit_id];
        $visit_cal = "";
        $ezform = null;
        if ($last_visit_id == '22222' || $last_visit_id == '11111') {
            $ezform = EzfQuery::getEzformOne($visitSchedule['11111']['ezf_id']);
            $visit_cal = '11111';
        } else {
            $ezform = EzfQuery::getEzformOne($val['ezf_id']);
            if (!$ezform)
                $ezform = EzfQuery::getEzformOne($visitSchedule['11111']['ezf_id']);
            $visit_cal = $val['visit_cal_date'];
        }
        $data = [];
        if ($ezform) {
            $data = SubjectManagementQuery::GetTableData($ezform, ['target' => $target, 'visit_name' => $visit_cal], 'one');
        }
        if ($val['actual_date'] == null)
            $val['actual_date'] = $visitSchedule['11111']['actual_date'];

        $actual_date = $data[$val['actual_date']];
        $date = new \DateTime($actual_date);
        $planDate = "";
        if (isset($val['plan_date'])) {
            $planDate = $date->modify('+' . $val['plan_date'] . ' day');
            $planDate = $date->format('Y-m-d');
        }

        $pdate = new \DateTime($planDate);
        $latestDate = $pdate->modify('+' . $val['latest_date'] . ' day');
        $latestDate = $pdate->format('Y-m-d');

        $pdate = new \DateTime($planDate);
        $earDate = $pdate->modify('+' . $val['earliest_date'] . ' day');
        $earDate = $pdate->format('Y-m-d');

        $result = 'true';

        $date_vs = date('Y-m-d', strtotime($date_visit));
        if ($date_vs > $latestDate || $date_vs < $earDate) {
            $result = 'false';
        }

        return $result;
    }

    public function actionActivityDetail() {
        $module_id = Yii::$app->request->get('module_id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $subject_profile_ezf = Yii::$app->request->get('subject_profile_ezf');
        $subject_detail_ezf = Yii::$app->request->get('subject_detail_ezf');
        $data_id = Yii::$app->request->get('data_id');
        $field_subject = Yii::$app->request->get('field_subject');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $inform_date = Yii::$app->request->get('inform_date');
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
                'pageSize' => 1,
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
                    'inform_date' => $inform_date,
                    'data_id' => $data_id,
                    'modal' => $modal,
                    'module_id' => $module_id,
        ]);
    }

    public function actionGridDetail() {
        $module_id = Yii::$app->request->get('module_id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $subject_profile_ezf = Yii::$app->request->get('subject_profile_ezf');
        $subject_detail_ezf = Yii::$app->request->get('subject_detail_ezf');
        $field_subject = Yii::$app->request->get('field_subject');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $data_id = Yii::$app->request->get('data_id');
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
                    'module_id' => $module_id,
                    'dataProvider' => $dataProvider,
                    'subject_profile_ezf' => $subject_profile_ezf,
                    'subject_detail_ezf' => $subject_detail_ezf,
                    'profile_column' => $profile_column,
                    'detail_column' => $detail_column,
                    'field_subject' => $field_subject,
                    'detail_column2' => $detail_column2,
                    'schedule_id' => $schedule_id,
                    'visitSchedule' => $visitSchedule,
                    'data_id' => $data_id,
                    'modal' => $modal,
        ]);
    }

    public function actionGetGroup() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $group_id = Yii::$app->request->get('group_id');
        $data_id = Yii::$app->request->get('data_id');
        if ($group_id == '') {
            $sql = " SELECT * FROM zdata_subject_detail WHERE target='$data_id' AND visit_name='22222' ";
            $result = \Yii::$app->db->createCommand($sql)->queryOne();
            $group_id = $result['group_name'];
        }
        //$dataGroup = SubjectManagementQuery::getVisitScheduleByInput($param1, $group_id);
        $dataGroup = SubjectManagementQuery::getVisitScheduleByInput($schedule_id, $group_id);
        return json_encode($dataGroup);
    }

    public function actionSaveNotifySchedule() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $next_date_visit = Yii::$app->request->get('next_date_visit');
        $next_visit_id = Yii::$app->request->get('next_visit_id');
        $date_visit = Yii::$app->request->get('visit_date');
        $visit_id = Yii::$app->request->get('visit_id');
        $target = Yii::$app->request->get('target');
        $module_id = Yii::$app->request->get('module_id');
        $group_id = Yii::$app->request->get('group_id');
        $status_verify = Yii::$app->request->get('status_verify');

        $email = \Yii::$app->user->identity->email;
        $sitecode = \Yii::$app->user->identity->profile->sitecode;
        $user = \Yii::$app->user->id;

        $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id);
        $scheduleOptions = \appxq\sdii\utils\SDUtility::string2Array(SubjectManagementQuery::getWidgetById($schedule_id)['options']);
        $val = $visitSchedule[$visit_id];
        $visit_name = $val['visit_name'];
        $profileData = [];

        $url_curr = \cpn\chanpan\classes\CNServerConfig::getDomainName();
        $projectData = \backend\modules\subjects\classes\ReportQuery::getProjectData($url_curr);
        $user_pi = $projectData['user_create'];

        $profileForm = EzfQuery::getEzformOne($scheduleOptions['subject_ezf_id']);
        $detailForm = EzfQuery::getEzformOne($scheduleOptions['11111']['main_ezf_id']);
        if ($profileForm)
            $profileData = SubjectManagementQuery::GetTableData($profileForm, ['id' => $target], 'one');

        $detailData = SubjectManagementQuery::GetTableData($detailForm, ['target' => $target, 'visit_name' => $visit_id], 'one');
        $action = "";
        $delay_date = "";
        $head = "";
        $msg_mail = "";
        $send_notify = false;

        if ($status_verify == 'period') {
            if ($next_date_visit != '')
                $next_date_visit = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($next_date_visit, '-');
            $head = "Schedule visit out of window!!!!  Study: " . $projectData['projectacronym'] . " Site: " . $sitecode . " ";
            $action['type'] = "peroid-" . $visit_id;
            $strDate = strtotime($date_visit);
            $this_date = date('Y-m-d', $strDate);
            $msg_mail = $this->renderAjax('msg_schedule_mail', [
                'subject_number' => $profileData['subject_number'],
                'schedule_id' => $schedule_id,
                'birth_date' => $profileData['birth_date'],
                'visit_name' => $visit_name,
                'next_visit_date' => $next_date_visit,
                'detail_form' => $detailForm,
                'visit_id' => $visit_id,
                'next_visit_id' => $next_visit_id,
                'visit_data' => $val,
                'target' => $target,
                'date_visit' => $this_date,
                'group_id' => $group_id,
                'url' => '/ezmodules/ezmodule/view?id=' . $module_id . '&dataid=' . base64_encode($target),
            ]);

            $send_notify = true;
            $query = new \yii\db\Query();

            $update = \Yii::$app->db->createCommand()
                    ->update("notify_email", ['status' => '3'], "data_id='$target' AND action='$action' AND  DATE(delay_date)='$this_date'")
                    ->execute();
            $delay_date = $this_date;
        } else {
            $head = "Reminder your schedule visit is in the window period!! Study: " . $projectData['projectacronym'] . " Site: " . $sitecode . " ";
            $action = "warning-" . $next_visit_id;
            $strDate = strtotime($next_date_visit);
            $this_date = date('Y-m-d', $strDate);
            if ($next_date_visit != '') {
                $send_notify = true;
                $next_date_visit = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($next_date_visit, '-');
            }

            $msg_mail = $this->renderAjax('msg_schedule_warning', [
                'subject_number' => $profileData['subject_number'],
                'schedule_id' => $schedule_id,
                'birth_date' => $profileData['birth_date'],
                'visit_name' => $visit_name,
                'next_visit_date' => $next_date_visit,
                'detail_form' => $detailForm,
                'visit_id' => $visit_id,
                'next_visit_id' => $next_visit_id,
                'visit_data' => $val,
                'target' => $target,
                'date_visit' => $this_date,
                'group_id' => $group_id,
                'url' => '/ezmodules/ezmodule/view?id=' . $module_id . '&dataid=' . base64_encode($target),
            ]);

            $query = new \yii\db\Query();

            $update = \Yii::$app->db->createCommand()
                    ->update("notify_email", ['status' => '3'], "data_id='$target' AND action='$action' AND  DATE(delay_date)='$this_date'")
                    ->execute();

            $delay_date = $next_date_visit ;
        }
        $user_ass = null;
        if ($user_pi != $user) {
            $user_ass = [$user, $user_pi];
        } else {
            $user_ass = $user;
        }

        if ($send_notify) {
            \dms\aomruk\classese\Notify::setNotify()->notify($head)
                    ->detail($msg_mail)
                    ->data_id($target)
                    ->action($action)
                    ->delay_date($delay_date)
                    ->send_email(true)
                    //->send_system(false)
                    ->assign($user_ass)
                    ->sendRedirect('/ezmodules/ezmodule/view?id=' . $module_id . '&dataid=' . base64_encode($target));
//
            \dms\aomruk\classese\Notify::setNotify()->notify($head)
                    ->detail($msg_mail)
                    ->data_id($target)
                    ->action($action)
                    ->send_system(false)
                    ->delay_date($delay_date)
                    ->send_line(true)
                    ->assign($user_ass)
                    ->sendRedirect('/ezmodules/ezmodule/view?id=' . $module_id . '&dataid=' . base64_encode($target));

            return 'success';
        }
    }

}
