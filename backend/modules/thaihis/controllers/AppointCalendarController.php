<?php

namespace backend\modules\thaihis\controllers;

use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\VarDumper;
use backend\modules\ezforms2\models\EzformDynamic;
use backend\modules\ezforms2\models\EzformInput;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\thaihis\classes\ThaiHisQuery;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\db\Query;
use backend\modules\ezforms2\classes\EzfFunc;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\models\TbdataAll;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\ezforms2\classes\MyWorkbenchFunc;
use yii\web\Response;

class AppointCalendarController extends \backend\modules\ezforms2\controllers\FullCalendarController
{

    public function actionCalendar()
    {
        $request = \Yii::$app->request;
        $ezf_id = $request->get('ezf_id');
        $target = $request->get('target');
        $title = $request->get('title');
        $event_name = $request->get('event_name');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $allDay = $request->get('allDay');
        $reloadDiv = $request->get('reloadDiv', 'calendar-appoint-' . SDUtility::getMillisecTime());
        $modal_full_calendar = $request->get('modal-full-calendar');
        $modal_event_calendar = $request->get('modal-event-calendar');
        $select_all = Yii::$app->request->get('select_all',false);
        $now_date = isset($_GET['now_date']) ? $_GET['now_date'] : date('Y-m-d');

        return $this->renderAjax('calendar', [
            'ezf_id' => $ezf_id,
            'target' => $target,
            'reloadDiv' => $reloadDiv,
            'title' => $title,
            'event_name' => $event_name,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'allDay' => $allDay,
            'now_date' => $now_date,
            'modal_full_calendar' => $modal_full_calendar,
            'modal_event_calendar' => $modal_event_calendar,
            'select_all' => $select_all
        ]);
    }

    public function actionFeed()
    {
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
        $select_all = Yii::$app->request->get('select_all',false);



        $sdate = new \DateTime();
        $sdate->setTimestamp($start);
        $start = $sdate->format('Y-m-d');

        $edate = new \DateTime();
        $edate->setTimestamp($end);
        $end = $edate->format('Y-m-d');

        \Yii::$app->response->format = Response::FORMAT_JSON;
        $events = [];
        $color[] = '#3a87ad'; // default
        $color[] = '#1CE835'; // green
        $color[] = '#F52A1D'; // red
        $color[] = '#E8E820'; // yellow
        $nowDate = date('Y-m-d H:i:s');

        $setColor = $color[0];

        $ezform = EzfQuery::getEzformById($ezf_id);
        $where = [];
        if (Yii::$app->user->can('doctor')) {
            $where = ['app_doctor' => Yii::$app->user->id];
        } else {
//            $dataDept = EzfUiFunc::loadTbData('zdata_working_unit', Yii::$app->user->identity->profile->department);
//            $dataDept ? $where = ['app_dept' =>Yii::$app->user->identity->profile->department] : $where = ' false ';
            $where = ['app_dept' => Yii::$app->user->identity->profile->department];
        }

        $event_value = null;
        $event_data = null;
        $event_field = EzfQuery::getFieldByName($ezf_id, $event_name);

        $dataInput = EzformInput::findOne(['input_id' => $event_field['ezf_field_type']]);
        $zdataCalendar = self::getData($ezform, $where, '');
        if ($start != '') {
            $zdataCalendar->andWhere(['between', $start_date, $start, $end]);
        }
        if ($end != '') {
            $zdataCalendar->andWhere(['between', $end_date, $start, $end]);
        }

        if ($target != '' && $select_all == false) {
            $zdataCalendar->andWhere(['ptid' => $target]);
        }

        $zdataCalendar->andWhere('app_status = 1');
//        VarDumper::dump($zdataCalendar->createCommand()->rawSql);
        $zdataCalendar = $zdataCalendar->all();
        if ($zdataCalendar) {
            $txt = '';
            foreach ($zdataCalendar as $keyZdata => $valueZdata) {
                if ($valueZdata['app_status'] == '2') {
                    $setColor = $color[2];
                    $txt = '(ยกเลิกนัด)';
                } else {
                    $setColor = $color[0];
                }


//                    $dataInput = EzfFunc::getInputByArray($event_field['ezf_field_type'], $ezf_input);

                $event_value = EzfUiFunc::getValueEzform($dataInput, $event_field, $valueZdata);
//                if ($event_field['ezf_field_type'] == '80') {
//                    $ezform_ref = EzfQuery::getEzformById($event_field['ref_ezf_id']);
//                    $event_data = EzfQuery::getTarget($ezform_ref['ezf_table'], $valueZdata[$event_name]);
//                    $field_desc = SDUtility::string2Array($event_field['ref_field_desc']);
//                    foreach ($field_desc as $val) {
//                        if ($event_value)
//                            $event_value .= " : " . $event_data[$val];
//                        else
//                            $event_value = $event_data[$val];
//                    }
//                }

                $val_start_date = isset($valueZdata[$start_date]) && $valueZdata[$start_date] != '' ? $valueZdata[$start_date] : date('Y-m-d');
                $val_end_date = isset($valueZdata[$end_date]) && $valueZdata[$end_date] != '' ? $valueZdata[$end_date] : date('Y-m-d');
                $val_start_time = isset($valueZdata['app_time']) && $valueZdata['app_time'] != '' ? $valueZdata['app_time'] : '08:00';
                $val_end_time = isset($valueZdata['app_time_stop']) && $valueZdata['app_time_stop'] != '' ? $valueZdata['app_time_stop'] : '09:00';
                $val_start = $val_start_date . ' ' . $val_start_time;
                $val_end = $val_end_date . ' ' . $val_end_time;
                $event = new \yii2fullcalendar\models\Event();
                $event->id = 'ezform-' . $ezf_id . '-' . $valueZdata['id'] . '-' . $valueZdata['user_create'];
                $event->title = $event_value == '' ? $valueZdata[$event_name] . $txt : $event_value . $txt;
                $event->start = $val_start;
                $event->end = $val_end;
                $event->color = $setColor;
                $event->allDay = $allDay;
                $event->editable = 1;
                $event->startEditable = 1;
                $event->durationEditable = 1;
                $events[] = $event;
            }
        }
        $stopEvents = EzfQuery::getEventStop($start, $end);
        $stopEventsCustom = ThaiHisQuery::getEventStopCustom($start, $end);

        if ($stopEvents) {
            if ($stopEventsCustom) {
                $stopEvents = ArrayHelper::merge($stopEvents, $stopEventsCustom);
            }
//VarDumper::dump($stopEvents);
            foreach ($stopEvents as $key => $value) {
                $event = new \yii2fullcalendar\models\Event();
                $event->id = 'holiday-zdata_holiday-' . $value['id'] . '-' . $value['user_create'];
                $event->title = $value['hname'];
                $event->start = $value['ddate'] . ' 00:00:00';
                $event->color = 'rgb(255,255,255, 0)';
                $event->textColor = '#FF0000';
                $event->editable = 1;
                $event->startEditable = 1;
                $event->durationEditable = 1;
                $events[] = $event;
            }
        }

        return $events;
    }

    protected function getData($ezform, $where = null, $type = null, $limit = null, $order = null, $group = null)
    {
        if (isset($ezform->ezf_table) || isset($ezform['ezf_table']))
            $table = isset($ezform->ezf_table) ? $ezform->ezf_table : $ezform['ezf_table'];
        else
            $table = $ezform;

        $query = new \yii\db\Query();
        $query->select('*')
            ->from($table);

        if ($where != null)
            $query->where($where);
        else
            $query->where('1=1');

//        if (isset($ezform->ezf_table)) {
//            if ($ezform['public_listview'] == 2) {
//        $query->andWhere('xsourcex = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
//            }
//            if ($ezform['public_listview'] == 3) {
//                $query->andWhere('xdepartmentx = :unit', [':unit' => Yii::$app->user->identity->profile->department]);
//            }
//            if ($ezform['public_listview'] == 0) {
//                $query->andWhere("user_create=:created_by", [':created_by' => Yii::$app->user->id]);
//            }
//        } else {
//            $query->andWhere('sitecode = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
//        }

        $query->andWhere(" rstat NOT IN('0','3') ");

        if ($group != null)
            $query->groupBy($group);

        if ($order != null) {
            $orderby = isset($order['order']) ? $order['order'] : '';
            $query->orderBy($order['column'] . ' ' . $orderby);
        }
        if ($limit != null)
            $query->limit($limit);

        $result = null;

        try {
            if ($type == 'one') {
                $result = $query->one();
            } else if ($type == 'all') {
                $result = $query->all();
            } else {
                $result = $query;
            }
            return $result;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return false;
        }
    }

    public function actionSelectTarget()
    {
        return $this->render('_select-target', $_GET);
    }

    public function actionEnroll()
    {
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

    public function actionEditable()
    {
        if (Yii::$app->getRequest()->isAjax) {
            $start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
            $end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            $allDay = isset($_GET['allDay']) ? $_GET['allDay'] : 'false';
            $field_dstart = Yii::$app->request->get('field_dstart');
            $field_estart = Yii::$app->request->get('field_estart');
            $allDay = $allDay == 'true' ? 1 : 0;


            $sdate = new \DateTime();
            $sdate->setTimestamp($start);
            $start = $sdate->format('Y-m-d H:i:s');
            $startAllDay = $sdate->format('Y-m-d');

            $edate = new \DateTime();
            $edate->setTimestamp($end);
            $end = $edate->format('Y-m-d H:i:s');
            $endAllDay = $edate->format('Y-m-d');
//VarDumper::dump($start.' '.$end);
            Yii::$app->response->format = Response::FORMAT_JSON;
            $idenArry = explode('-', $id);
            $ezf_id = isset($idenArry[1]) ? $idenArry[1] : null;
            $dataid = isset($idenArry[2]) ? $idenArry[2] : null;
            if (isset($ezf_id) && isset($idenArry[0]) && $idenArry[0] == 'ezform') {
                $modelEzf = EzfQuery::getEzformById($ezf_id);
                if ($modelEzf) {
                    $dataTbCalendar = (new Query())->select('*')->from($modelEzf['ezf_table'])->where('rstat NOT IN(0,3)')->andWhere(['id' => $dataid])->one();
                    try {

                        if ($dataTbCalendar) {
                            if ((new Query())->select('id')
                                ->from($modelEzf['ezf_table'])
                                ->where(['target' => $dataTbCalendar['target'], 'DATE(' . $field_dstart . ')' => $startAllDay])
                                ->andWhere('rstat NOT IN (0,3) AND app_status = 1')
                                ->one()) {
                                return [
                                    'status' => 'error',
                                    'message' => SDHtml::getMsgError() . Yii::t('app', 'มีข้อมูลนัดของผู้รับบริการรายนี้แล้ว'),
                                ];
                            }
                            $visit_ezf_id = \backend\modules\patient\Module::$formID['visit'];
                            $visit_ezf_table = \backend\modules\patient\Module::$formTableName['visit'];
                            if (isset($dataTbCalendar['app_visit_id']) && $dataTbCalendar['app_visit_id'] == '') {
                                $dataVisit = (new Query())->select('id,visit_date')
                                    ->from($visit_ezf_table)
                                    ->where(['DATE(visit_date)' => $startAllDay, 'target' => $dataTbCalendar['target']])
                                    ->andWhere('rstat NOT IN (0,3)')->one();
                                if ($dataVisit) {
                                    Yii::$app->db->createCommand()->update($modelEzf['ezf_table'], [$field_dstart => $startAllDay, 'app_visit_id' => $dataVisit['id']], ['id' => $dataTbCalendar['id']])->execute();
                                } else {
                                    Yii::$app->db->createCommand()->update($modelEzf['ezf_table'], [$field_dstart => $startAllDay], ['id' => $dataTbCalendar['id']])->execute();
                                }
                            } else {
                                $dataVisit = (new Query())->select('id,visit_date')
                                    ->from($visit_ezf_table)
                                    ->where(['id' => $dataTbCalendar['app_visit_id'], 'target' => $dataTbCalendar['target']])
                                    ->andWhere('rstat NOT IN (0,3)')->one();
                                $dataVisitDate = (new Query())->select('id,visit_date')
                                    ->from($visit_ezf_table)
                                    ->where(['DATE(visit_date)' => $startAllDay, 'target' => $dataTbCalendar['target']])
                                    ->andWhere('rstat NOT IN (0,3)')->one();
                                if ($dataVisit) {
                                    if ($dataVisitDate && $dataVisitDate['id'] == $dataVisit['id']) {
                                        Yii::$app->db->createCommand()->update($visit_ezf_table, ['visit_date' => $startAllDay], ['id' => $dataVisit['id']])->execute();
                                        Yii::$app->db->createCommand()->update($modelEzf['ezf_table'], [$field_dstart => $startAllDay], ['id' => $dataTbCalendar['id']])->execute();
                                    } elseif ($dataVisitDate && $dataVisitDate['id'] != $dataVisit['id']) {
//                                        Yii::$app->db->createCommand()->update($visit_ezf_table, ['visit_date' => $startAllDay], ['id' => $dataVisit['id']])->execute();
                                        Yii::$app->db->createCommand()->update($visit_ezf_table, ['rstat' => '3'], ['id' => $dataTbCalendar['app_visit_id'], 'target' => $dataTbCalendar['target']])->execute();
                                        Yii::$app->db->createCommand()->update($modelEzf['ezf_table'], [$field_dstart => $startAllDay, 'app_visit_id' => $dataVisitDate['id']], ['id' => $dataTbCalendar['id']])->execute();

                                    } else {
                                        Yii::$app->db->createCommand()->update($visit_ezf_table, ['visit_date' => $startAllDay], ['id' => $dataVisit['id']])->execute();
                                        Yii::$app->db->createCommand()->update($modelEzf['ezf_table'], [$field_dstart => $startAllDay], ['id' => $dataTbCalendar['id']])->execute();
                                    }
                                } else {
                                    if ($dataVisitDate) {
                                        Yii::$app->db->createCommand()->update($modelEzf['ezf_table'], [$field_dstart => $startAllDay, 'app_visit_id' => $dataVisitDate['id']], ['id' => $dataTbCalendar['id']])->execute();

                                    } else {
                                        Yii::$app->db->createCommand()->update($modelEzf['ezf_table'], [$field_dstart => $startAllDay], ['id' => $dataTbCalendar['id']])->execute();
                                    }

//                                    Yii::$app->db->createCommand()->update($visit_ezf_table, ['rstat' => '3'], ['id' => $dataTbCalendar['app_visit_id'], 'target' => $dataTbCalendar['target']])->execute();
//                                        Yii::$app->db->createCommand()->update($visit_ezf_table, ['visit_date' => $startAllDay], ['id' => $dataVisit['id']])->execute();
//                                    Yii::$app->db->createCommand()->update($modelEzf['ezf_table'], [$field_dstart => $startAllDay, 'app_visit_id' => $dataVisitDate['id']], ['id' => $dataTbCalendar['id']])->execute();
                                }
                            }
                            $result = [
                                'status' => 'success',
                                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save the data success'),
                            ];
                        } else {
                            $result = [
                                'status' => 'error',
                                'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                            ];
                        }

                    } catch (Exception $ex) {
                        $result = [
                            'status' => 'error',
                            'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                        ];
                    }
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

    public function actionReplaceByGoogle()
    {
        if (Yii::$app->getRequest()->isAjax) {
            $events = isset($_POST['events']) ? $_POST['events'] : null;
            $events_over = isset($_POST['events_over']) ? $_POST['events_over'] : null;
            $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : '';
            $overwrite = isset($_POST['overwrite']) ? $_POST['overwrite'] : '';
            $convert = json_decode($events, true);
            $convert_old = json_decode($events_over, true);
            $modelEzf = EzfQuery::getEzformById($ezf_id);
            $model = new EzformDynamic($modelEzf['ezf_table']);
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

    public function actionCheckDataExist()
    {
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