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
use yii\web\Response;

class FullCalendarController extends Controller {

    public function actionIndex() {
        $request = Yii::$app->request;
        $ezf_id = $request->get('ezf_id');
        $title = $request->get('title');
        $event_name = $request->get('event_name');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $allDay = $request->get('allDay');

        return $this->render('index', [
                    'ezf_id' => $ezf_id,
                    'title' => $title,
                    'event_name' => $event_name,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'allDay' => $allDay,
        ]);
    }

    public function actionCalendar() {
        $request = \Yii::$app->request;
        $ezf_id = $request->get('ezf_id');
        $target = $request->get('target');
        $title = $request->get('title');
        $event_name = $request->get('event_name');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $allDay = $request->get('allDay');
        $reloadDiv = $request->get('reloadDiv');
        
        return $this->renderAjax('calendar', [
                    'ezf_id' => $ezf_id,
                    'target'=>$target,
                    'reloadDiv' => $reloadDiv,
                    'title' => $title,
                    'event_name' => $event_name,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'allDay' => $allDay,
        ]);
    }

    public function actionEnroll() {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data['id'] = $request->get('id');
        $data['ptid'] = $request->get('ptid');
        $data['hsitecode'] = $request->get('hsitecode');
        if ($request->get('v') == '0') {
            $data['v'] = '1';
            $data['enroll'] = TRUE;
        } else {
            $data['v'] = '0';
            $data['enroll'] = FALSE;
        }

        return $data;
    }

    public function actionFeed() {
        $start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
        $end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');
        $forms = isset($_GET['forms']) ? $_GET['forms'] : '';
        $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : '';
        $target = isset($_GET['target']) ? $_GET['target'] : '';
        $event_name = isset($_GET['event_name']) ? $_GET['event_name'] : '';
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
        $allDay = isset($_GET['allDay']) ? $_GET['allDay'] : '';

        $search_cal = isset($_GET['search_cal']) ? $_GET['search_cal'] : '';
        $cal = isset($_GET['cal']) ? $_GET['cal'] : '';

        $sdate = new \DateTime();
        $sdate->setTimestamp($start);
        $start = $sdate->format('Y-m-d H:i:s');

        $edate = new \DateTime();
        $edate->setTimestamp($end);
        $end = $edate->format('Y-m-d H:i:s');

        \Yii::$app->response->format = Response::FORMAT_JSON;
        $events = [];
        $color[] = '#3a87ad'; // default
        $color[] = '#1CE835'; // green
        $color[] = '#F52A1D'; // red
        $color[] = '#E8E820'; // yellow
        $nowDate = date('Y-m-d H:i:s');

        $setColor = $color[0];

        $ezform = EzfQuery::getEzformById($ezf_id);
        
        $zdataCalendar = \backend\modules\subjects\classes\SubjectManagementQuery::GetTableData($ezform,['target'=>$target]);

        if ($zdataCalendar) {
            foreach ($zdataCalendar as $keyZdata => $valueZdata) {
                if ($valueZdata['rstat'] == '0')
                    $setColor = $color[3];
                else
                    $setColor = $color[0];
                
                $event_value = null;
                $event_data = null;
                $event_field = EzfQuery::getFieldByName($ezf_id, $event_name);
                
                if($event_field['ezf_field_type']=='80'){
                    $ezform_ref = EzfQuery::getEzformById($event_field['ref_ezf_id']);
                    $event_data = EzfQuery::getTarget($ezform_ref['ezf_table'], $valueZdata[$event_name]);
                    $field_desc = SDUtility::string2Array($event_field['ref_field_desc']);
                    foreach ($field_desc as $val){
                        if($event_value)
                            $event_value.=" : ".$event_data[$val];
                        else
                            $event_value=$event_data[$val];
                    }
                }

                $event = new \yii2fullcalendar\models\Event();
                $event->id = 'ezform-' . $ezf_id . '-' . $valueZdata['id'] . '-' . $valueZdata['user_create'];
                $event->title = isset($event_value) && $event_value != null ? $event_value: $valueZdata[$event_name];
                $event->start = isset($valueZdata[$start_date]) && $valueZdata[$start_date] != '' ? $valueZdata[$start_date] : date('Y-m-d') . ' 00:00:00';
                $event->end = isset($valueZdata[$end_date]) && $valueZdata[$end_date] != '' ? $valueZdata[$end_date] : date('Y-m-d') . ' 23:59:00';
                $event->color = $setColor;
                $event->allDay = $allDay;
                $event->editable = 1;
                $event->startEditable = 1;
                $event->durationEditable = 1;
                $events[] = $event;
            }
        }

        return $events;
    }

    public function actionEditable() {
        if (Yii::$app->getRequest()->isAjax) {
            $start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
            $end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            $allDay = isset($_GET['allDay']) ? $_GET['allDay'] : 'false';

            $allDay = $allDay == 'true' ? 1 : 0;

            $sdate = new \DateTime();
            $sdate->setTimestamp($start);
            $start = $sdate->format('Y-m-d H:i:s');
            $startAllDay = $sdate->format('Y-m-d');

            $edate = new \DateTime();
            $edate->setTimestamp($end);
            $end = $edate->format('Y-m-d H:i:s');
            $endAllDay = $edate->format('Y-m-d');

            Yii::$app->response->format = Response::FORMAT_JSON;
            $idenArry = explode('-', $id);
            $ezf_id = isset($idenArry[1]) ? $idenArry[1] : null;
            $dataid = isset($idenArry[2]) ? $idenArry[2] : null;

            if (isset($ezf_id) && isset($idenArry[0]) && $idenArry[0] == 'ezform') {
                $modelEzf = EzfQuery::getEzformById($ezf_id);
                $model = new \backend\modules\ezforms\models\EzformDynamic($modelEzf['ezf_table']);
                //$model->find()->where('id = :id', ['id' => $dataid])->One();

                if ($model) {
                    $model->id = $dataid;
                    $model->start_date = $start;
                    $model->end_date = $end;
                    $model->user_update = \Yii::$app->user->identity->id;
                    $model->update_date = date('Y-m-d h:i:s');

                    if ($model->update()) {
                        $result = [
                            'status' => 'success',
                            'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                            'data' => $model,
                        ];
                    } else {
                        $result = [
                            'status' => 'error',
                            'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                            'data' => $model,
                        ];
                    }
                    return $result;
                } else {

                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                    ];
                }

                return $result;
            } else {
                throw new \yii\web\NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
            }
        }
    }

    public function actionReplaceByGoogle() {
        if (Yii::$app->getRequest()->isAjax) {
            $events = isset($_POST['events']) ? $_POST['events'] : null;
            $events_over = isset($_POST['events_over']) ? $_POST['events_over'] : null;
            $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : '';
            $overwrite = isset($_POST['overwrite']) ? $_POST['overwrite'] : '';
            $convert = json_decode($events, true);
            $convert_old = json_decode($events_over, true);
            $modelEzf = EzfQuery::getEzformById($ezf_id);
            $model = new \backend\modules\ezforms\models\EzformDynamic($modelEzf['ezf_table']);
            $xsourcex = Yii::$app->user->identity->userProfile->sitecode;
            $user_id = Yii::$app->user->id;
            $nowDate = date('Y-m-d h:i:s');

            $ids = [];
            foreach ($convert_old as $key => $val) {
                $ids[] = $val['id'];
            }
            //$model->find()->where('id = :id', ['id' => $dataid])->One();
            if ($overwrite == 'false') {
                foreach ($convert as $key => $val) {
                    $start_date = isset($val['start']['dateTime']) ? $val['start']['dateTime'] : $val['start']['date'];
                    $end_date = isset($val['start']['dateTime']) ? $val['end']['dateTime'] : $val['end']['date'];
                    $startDate = date('Y-m-d h:i:s', strtotime($start_date));
                    $endDate = date('Y-m-d h:i:s', strtotime($start_date));

                    if (!in_array($val['id'], $ids)) {
                        \Yii::$app->db->createCommand("REPLACE INTO {$modelEzf['ezf_table']}(id, xsourcex, event_name, event_detail, start_date, end_date, rstat,user_create,create_date, user_update, update_date)
                    VALUES('{$val['id']}','$xsourcex','{$val['summary']}','{$val['desription']}','{$startDate}','{$endDate}','1','{$user_id}','{$nowDate}','{$user_id}','{$nowDate}')")->execute();
                    }
                }
            } else {
                foreach ($convert as $key => $val) {
                    $start_date = isset($val['start']['dateTime']) ? $val['start']['dateTime'] : $val['start']['date'];
                    $end_date = isset($val['start']['dateTime']) ? $val['end']['dateTime'] : $val['end']['date'];
                    $startDate = date('Y-m-d h:i:s', strtotime($start_date));
                    $endDate = date('Y-m-d h:i:s', strtotime($start_date));

                    \Yii::$app->db->createCommand("REPLACE INTO {$modelEzf['ezf_table']}(id, xsourcex, event_name, event_detail, start_date, end_date, rstat,user_create,create_date, user_update, update_date)
                    VALUES('{$val['id']}','$xsourcex','{$val['summary']}','{$val['desription']}','{$startDate}','{$endDate}','1','{$user_id}','{$nowDate}','{$user_id}','{$nowDate}') ")->execute();
                }
            }
        }
    }

    public function actionCheckDataExist() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->getRequest()->isAjax) {
            $events = isset($_POST['events']) ? $_POST['events'] : null;
            $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : '';

            $convert = json_decode($events, true);
            $modelEzf = EzfQuery::getEzformById($ezf_id);
            //$model->find()->where('id = :id', ['id' => $dataid])->One();
            $ids = '';
            foreach ($convert as $key => $val) {
                if ($ids == '') {
                    $ids = "'" . $val['id'] . "'";
                } else {
                    $ids .= ",'" . $val['id'] . "'";
                }
            }

            $query = new \yii\db\Query();
            $data = $query->select('id')->from($modelEzf['ezf_table'])->where("id IN($ids)")->all();

            $result = null;
            if ($data) {
                $result = [
                    'status' => 'exist',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data Already exist.'),
                    'data' => $data,
                ];
            } else {
                $result = [
                    'status' => 'not_exist',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data Not Already exist.'),
                    'data' => $data,
                ];
            }

            return $result;
        }
    }

}
