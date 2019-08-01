<?php

namespace backend\modules\gantt\controllers;

use yii\web\Controller;
use backend\modules\gantt\models\InvProject;
use Yii;
use appxq\sdii\helpers\SDHtml;
use yii\web\Response;
use DateTime;
use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\helpers\Json;
use cpn\chanpan\classes\CNUser;
use backend\modules\gantt\classes\GanttQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;

class PmsCalendarController extends Controller {

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex() {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $viewYear = isset($_GET['viewYear']) ? $_GET['viewYear'] : '';
            $module_id = isset($_GET['module_id']) ? $_GET['module_id'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $maintask_ezf_id = isset($_GET['maintask_ezf_id']) ? $_GET['maintask_ezf_id'] : '';
            $subtask_ezf_id = isset($_GET['subtask_ezf_id']) ? $_GET['subtask_ezf_id'] : '';
            $task_ezf_id = isset($_GET['task_ezf_id']) ? $_GET['task_ezf_id'] : '';
            $response_ezf_id = isset($_GET['response_ezf_id']) ? $_GET['response_ezf_id'] : '';
            $response_actual_field = isset($_GET['response_actual_field']) ? $_GET['response_actual_field'] : '';
            $defaultView = isset($_GET['defaultView']) ? $_GET['defaultView'] : 'month';
            $forms = isset($_GET['forms']) ? $_GET['forms'] : '';
            $tab = isset($_GET['tab']) ? $_GET['tab'] : '';
            $projectid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            //$forms = EzfFunc::stringDecode2Array($forms);
            
            $startDate = date('Y-m-d H:i:s');
            $dateNow = new \DateTime($startDate);
            $dateNow = $dateNow->modify('+ 1 day');
            $dueDate = $dateNow->format('Y-m-d H:i:s');
            foreach ($forms as $key => $val){
                $task_ezf_id = $val['ezf_id'];
            }
            if (isset($projectid) && $projectid != '') {
                $_COOKIE['project_id'] = $target;
                $modelCate = \backend\modules\patient\classes\PatientFunc::backgroundInsert($subtask_ezf_id, '', $projectid, ['cate_name' => 'New Sub-task name']);
                //\appxq\sdii\utils\VarDumper::dump($modelCate);
                if (isset($modelCate['data']) && $modelCate['data']) {

                    $modelTask = \backend\modules\patient\classes\PatientFunc::backgroundInsert($task_ezf_id, '', $projectid, [
                                'task_name' => 'New task name', 'category_id' => $modelCate['data']['id'], 'start_date' => $startDate, 'finish_date' => $dueDate]);
                }
            }
            $view_menu = isset($_GET['view_menu']) ? $_GET['view_menu'] : '';

            if (empty($view_menu)) {
                $view_menu = ['month', 'agendaWeek', 'agendaDay'];
            }
            $view_menu = implode(',', $view_menu);

            $now_date = isset($_GET['now_date']) ? $_GET['now_date'] : date('Y-m-d');
            $eventSources = [];

            return $this->renderAjax('index', [
                        'modal' => $modal,
                        'viewYear'=>$viewYear,
                        'reloadDiv' => $reloadDiv,
                        'target' => $target,
                        'projectid'=>$projectid,
                        'now_date' => $now_date,
                        'forms' => $forms,
                        'eventSources' => $eventSources,
                        'defaultView' => $defaultView,
                        'view_menu' => $view_menu,
                        'tab' => $tab,
                        'response_ezf_id' => $response_ezf_id,
                        'subtask_ezf_id' => $subtask_ezf_id,
                        'maintask_ezf_id' => $maintask_ezf_id,
                        'response_actual_field' => $response_actual_field,
                        'module_id'=>$module_id,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionIndex2() {
        if (Yii::$app->getRequest()->isAjax) {
            $viewYear = isset($_GET['viewYear']) ? $_GET['viewYear'] : '';
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $module_id = isset($_GET['module_id']) ? $_GET['module_id'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $maintask_ezf_id = isset($_GET['maintask_ezf_id']) ? $_GET['maintask_ezf_id'] : '';
            $subtask_ezf_id = isset($_GET['subtask_ezf_id']) ? $_GET['subtask_ezf_id'] : '';
            $task_ezf_id = isset($_GET['task_ezf_id']) ? $_GET['task_ezf_id'] : '';
            $response_ezf_id = isset($_GET['response_ezf_id']) ? $_GET['response_ezf_id'] : '';
            $response_actual_field = isset($_GET['response_actual_field']) ? $_GET['response_actual_field'] : '';
            $defaultView = isset($_GET['defaultView']) ? $_GET['defaultView'] : 'month';
            $forms = isset($_GET['forms']) ? $_GET['forms'] : '';
            $tab = isset($_GET['tab']) ? $_GET['tab'] : '';
            $project_id = isset($_GET['project_id']) ? $_GET['project_id'] : '';

            $view_menu = isset($_GET['view_menu']) ? $_GET['view_menu'] : '';

            if (empty($view_menu)) {
                $view_menu = ['month', 'agendaWeek', 'agendaDay'];
            }
            $view_menu = implode(',', $view_menu);

            $now_date = isset($_GET['now_date']) ? $_GET['now_date'] : date('Y-m-d');
            $eventSources = [];
            
            return $this->renderAjax('index2', [
                        'modal' => $modal,
                        'viewYear'=>$viewYear,
                        'reloadDiv' => $reloadDiv,
                        'target' => $target,
                        'now_date' => $now_date,
                        'forms' => $forms,
                        'eventSources' => $eventSources,
                        'defaultView' => $defaultView,
                        'view_menu' => $view_menu,
                        'tab' => $tab,
                        'project_id'=>$project_id,
                        'response_ezf_id' => $response_ezf_id,
                        'subtask_ezf_id' => $subtask_ezf_id,
                        'maintask_ezf_id' => $maintask_ezf_id,
                        'response_actual_field' => $response_actual_field,
                        'module_id'=>$module_id,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionCalendarRender() {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $defaultView = isset($_GET['defaultView']) ? $_GET['defaultView'] : 'month';
            $forms = isset($_GET['forms']) ? $_GET['forms'] : '';
            $search_cal = isset($_POST['search_cal']) ? $_POST['search_cal'] : '';
            $now_date = isset($_GET['now_date']) ? $_GET['now_date'] : '';
            $eventSources = isset($_GET['eventSources']) ? $_GET['eventSources'] : '';
            $view_menu = isset($_GET['view_menu']) ? $_GET['view_menu'] : '';
            $tab = isset($_GET['tab']) ? $_GET['tab'] : '';
            $project_id = isset($_GET['project_id']) ? $_GET['project_id'] : '';
            $maintask_ezf_id = isset($_GET['maintask_ezf_id']) ? $_GET['maintask_ezf_id'] : '';
            $subtask_ezf_id = isset($_GET['subtask_ezf_id']) ? $_GET['subtask_ezf_id'] : '';
            $response_actual_field = isset($_GET['response_actual_field']) ? $_GET['response_actual_field'] : '';
            $response_ezf_id = isset($_GET['response_ezf_id']) ? $_GET['response_ezf_id'] : '';
            $scale_filter = isset($_GET['scale_filter']) ? $_GET['scale_filter'] : '';
            $task_ezf_id = isset($_GET['task_ezf_id']) ? $_GET['task_ezf_id'] : '';
            $module_id = isset($_GET['module_id']) ? $_GET['module_id'] : '';
            $viewYear= isset($_GET['viewYear']) ? $_GET['viewYear'] : '';
            
            if($forms){
                foreach ($forms as $key => $val){
                    $task_ezf_id = $val['ezf_id'];
                }
            }
            

            return $this->renderAjax('_pms-calendar', [
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'target' => $target,
                        'now_date' => $now_date,
                        'forms' => $forms,
                        'eventSources' => $eventSources,
                        'defaultView' => $defaultView,
                        'view_menu' => $view_menu,
                        'search_cal' => $search_cal,
                        'tab' => $tab,
                        'project_id' => $project_id,
                        'module_id'=>$module_id,
                        'response_ezf_id' => $response_ezf_id,
                        'subtask_ezf_id' => $subtask_ezf_id,
                        'maintask_ezf_id' => $maintask_ezf_id,
                        'task_ezf_id'=>$task_ezf_id,
                        'response_actual_field' => $response_actual_field,
                        'scale_filter' => $scale_filter,
                        'viewYear'=>$viewYear,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionFeed() {
        if (Yii::$app->getRequest()->isAjax) {
            $start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
            $end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');
            $forms = isset($_GET['forms']) ? $_GET['forms'] : '';
            $project_id = isset($_GET['project_id']) ? $_GET['project_id'] : '';
            $tab = isset($_GET['tab']) ? $_GET['tab'] : '';
            $maintask_ezf_id = isset($_GET['maintask_ezf_id']) ? $_GET['maintask_ezf_id'] : '';
            $subtask_ezf_id = isset($_GET['subtask_ezf_id']) ? $_GET['subtask_ezf_id'] : '';
            $response_ezf_id = isset($_GET['response_ezf_id']) ? $_GET['response_ezf_id'] : '';
            $response_actual_field = isset($_GET['response_actual_field']) ? $_GET['response_actual_field'] : '';
            $scale_filter = isset($_GET['scale_filter']) ? $_GET['scale_filter'] : '';
            $user_id = Yii::$app->user->id;

            $forms = EzfFunc::stringDecode2Array($forms);

            $search_cal = isset($_GET['search_cal']) ? $_GET['search_cal'] : '';
            $cal = isset($_GET['cal']) ? $_GET['cal'] : '';
            $cal = EzfFunc::stringDecode2Array($cal);

            $sdate = new \DateTime();
            $sdate->setTimestamp($start);
            $start = $sdate->format('Y-m-d H:i:s');

            $edate = new \DateTime();
            $edate->setTimestamp($end);
            $end = $edate->format('Y-m-d H:i:s');

            Yii::$app->response->format = Response::FORMAT_JSON;
            $events = [];
            $color[] = '#3a87ad'; // default
            $color[] = '#1CE835'; // green
            $color[] = '#F52A1D'; // red
            $color[] = '#E8E820'; // yellow
            $nowDate = date('Y-m-d');
            if (!empty($forms)) {
                foreach ($forms as $key => $value) {
                    $setColor = $color[0];

                    if (isset($value['ezf_id']) && isset($value['subject']) && isset($value['start']) && isset($value['end']) && in_array($key, $cal)) {
                        $repeat = isset($value['repeat']) ? $value['repeat'] : '';

                        $ezform = EzfQuery::getEzformOne($value['ezf_id']);
                        $ezformResponse = EzfQuery::getEzformOne($response_ezf_id);
                        $zdataCalendar = SubjectManagementQuery::GetTableData($ezform, ['target' => $project_id]);
                        if (isset($scale_filter) && $scale_filter != null) {
                            $userRole = CNUser::getUserRoles();
                            $assignRole = null;
                            $assignUser = ' OR INSTR(respons_person), "' . $user_id . '") > 0';
                            if ($userRole) {
                                foreach ($userRole as $key => $val) {
                                    $assignRole .= ' OR INSTR(manage_roles), "' . $val['id'] . '") > 0 ';
                                }
                            }

                            if ($scale_filter == '1') {
                                $zdataCalendar = SubjectManagementQuery::GetDataTaskResponse($ezform, $ezformResponse, 'project_id="' . $project_id . '" AND (ez2.actual_date IS NOT NULL AND ez2.progress >= 100)');
                            } elseif ($scale_filter == '2') {
                                $zdataCalendar = SubjectManagementQuery::GetDataTaskResponse($ezform, $ezformResponse, 'project_id="' . $project_id . '" AND (ez2.actual_date IS NULL AND (ez2.progress < 100 OR ez2.progress IS NULL))');
                            } elseif ($scale_filter == '3') {
                                $zdataCalendar = SubjectManagementQuery::GetTableData($ezform, 'project_id="' . $project_id . '" AND (user_update="' . $user_id . '" ' . $assignUser . $assignRole . ')', null, null, ['column' => 'sort_order']);
                            } else {
                                $zdataCalendar = SubjectManagementQuery::GetTableData($ezform, 'project_id="' . $project_id . '" ', null, null, ['column' => 'sort_order']);
                            }
                        } else {
                            $zdataCalendar = SubjectManagementQuery::GetTableData($ezform, ['project_id' => $project_id], null, null, ['column' => 'sort_order']);
                        }

                        if ($zdataCalendar) {
                            foreach ($zdataCalendar as $keyZdata => $valueZdata) {
                                $allday = false;
                                if (isset($value['allday'])) {
                                    $allday = (isset($valueZdata[$value['allday']]) && $valueZdata[$value['allday']] == 1) ? true : false;
                                }
                                $check_date = explode(' ', $valueZdata[$value['start']]);
                                if (count($check_date) < 2) {
                                    $allday = true;
                                } else {
                                    if ($check_date[1] == '00:00:00') {
                                        $allday = true;
                                    }
                                }

                                $responseData = SubjectManagementQuery::GetTableData($ezformResponse, ['target' => $valueZdata['id']], 'one');
                                //\appxq\sdii\utils\VarDumper::dump($valueZdata['task_name']." ".date('Y-m-d',strtotime($valueZdata[$value['end']]))." ".date($nowDate),0);
                                if (date('Y-m-d',strtotime($valueZdata[$value['start']])) <= date($nowDate) && (isset($responseData[$response_actual_field]) && $responseData[$response_actual_field] != null)) {
                                    $setColor = $color[1];
                                } else if (date('Y-m-d',strtotime($valueZdata[$value['end']])) < date($nowDate) && !isset($responseData[$response_actual_field])) {
                                    $setColor = $color[2];
                                } else if (date('Y-m-d',strtotime($valueZdata[$value['start']])) <= date($nowDate) && $responseData[$response_actual_field] == null) {
                                    $setColor = $color[0];
                                }


                                $event = new \yii2fullcalendar\models\Event();
                                $event->id = 'ezform-' . $value['ezf_id'] . '-' . $valueZdata['id'] . '-' . $valueZdata['user_create'];
                                $event->title = $valueZdata[$value['subject']];
                                $event->start = $valueZdata[$value['start']];
                                $event->end = $valueZdata[$value['end']];
                                $event->color = $setColor;
                                $event->allDay = $allday;
                                $event->editable = isset($value['editable']) && $value['editable'] == 1 ? true : false;
                                $events[] = $event;
                            }
                        }


                        if ($repeat != '') {
                            $zdataCalendarRepeat = EzfQuery::getRepeatEventEzForm($start, $end, $ezform, $value, $search_cal);
                            if ($zdataCalendarRepeat) {
                                foreach ($zdataCalendarRepeat as $keyRZdata => $valueRZdata) {
                                    $allday = false;
                                    if (isset($value['allday'])) {
                                        $allday = (isset($valueRZdata[$value['allday']]) && $valueRZdata[$value['allday']] == 1) ? true : false;
                                    }
                                    $check_date = explode(' ', $valueRZdata[$value['start']]);
                                    if (count($check_date) < 2) {
                                        $allday = true;
                                    } else {
                                        if ($check_date[1] == '00:00:00') {
                                            $allday = true;
                                        }
                                    }

                                    $repeatValue = $valueRZdata[$repeat];
                                    $tStart = new \DateTime($valueRZdata[$value['start']]);
                                    $tEnd = new \DateTime($valueRZdata[$value['end']]);

                                    if ($repeatValue == 'year') {
                                        $newDate = new \DateTime($start);

                                        if ($newDate->format('Y') >= $tStart->format('Y')) {
                                            $event = new \yii2fullcalendar\models\Event();
                                            $event->id = 'ezform-' . $value['ezf_id'] . '-' . $valueRZdata['id'] . '-' . $valueRZdata['user_create'];
                                            $event->title = $valueRZdata[$value['subject']];
                                            $event->start = $newDate->format('Y') . '-' . $tStart->format('m-d H:i:s');
                                            $event->end = $newDate->format('Y') . '-' . $tEnd->format('m-d H:i:s');
                                            $event->color = $value['color'];
                                            $event->allDay = $allday;
                                            $event->editable = isset($value['editable']) && $value['editable'] == 1 ? true : false;
                                            $events[] = $event;
                                        }
                                    } elseif ($repeatValue == 'month') {
                                        $newDate = new \DateTime($start);
                                        $newDate->modify('+15 day');
                                        if ($newDate->format('Ym') >= $tStart->format('Ym')) {
                                            $event = new \yii2fullcalendar\models\Event();
                                            $event->id = 'ezform-' . $value['ezf_id'] . '-' . $valueRZdata['id'] . '-' . $valueRZdata['user_create'];
                                            $event->title = $valueRZdata[$value['subject']];
                                            $event->start = $newDate->format('Y-m') . '-' . $tStart->format('d H:i:s');
                                            $event->end = $newDate->format('Y-m') . '-' . $tEnd->format('d H:i:s');
                                            $event->color = $value['color'];
                                            $event->allDay = $allday;
                                            $event->editable = isset($value['editable']) && $value['editable'] == 1 ? true : false;
                                            $events[] = $event;
                                        }
                                    } else {
                                        $cStart = new \DateTime($start);
                                        $cEnd = new \DateTime($end);

                                        if ($repeatValue == 'day') {
                                            for ($d = $cStart; $d <= $cEnd; $d->modify('+1 day')) {
                                                if ($d >= $tStart) {
                                                    $event = new \yii2fullcalendar\models\Event();
                                                    $event->id = 'ezform-' . $value['ezf_id'] . '-' . $valueRZdata['id'] . '-' . $valueRZdata['user_create'];
                                                    $event->title = $valueRZdata[$value['subject']];
                                                    $event->start = $d->format('Y-m-d') . ' ' . $tStart->format('H:i:s');
                                                    $event->end = $d->format('Y-m-d') . ' ' . $tEnd->format('H:i:s');
                                                    $event->color = $value['color'];
                                                    $event->allDay = $allday;
                                                    $event->editable = isset($value['editable']) && $value['editable'] == 1 ? true : false;
                                                    $events[] = $event;
                                                }
                                            }
                                        } elseif ($repeatValue == 'week') {
                                            $w = $tStart->format('w');
                                            if ($w > 0) {
                                                $cStart->modify("+$w day");
                                            }

                                            for ($d = $cStart; $d <= $cEnd; $d->modify('+7 day')) {
                                                if ($d >= $tStart) {
                                                    $event = new \yii2fullcalendar\models\Event();
                                                    $event->id = 'ezform-' . $value['ezf_id'] . '-' . $valueRZdata['id'] . '-' . $valueRZdata['user_create'];
                                                    $event->title = $valueRZdata[$value['subject']];
                                                    $event->start = $d->format('Y-m-d') . ' ' . $tStart->format('H:i:s');
                                                    $event->end = $d->format('Y-m-d') . ' ' . $tEnd->format('H:i:s');
                                                    $event->color = $value['color'];
                                                    $event->allDay = $allday;
                                                    $event->editable = isset($value['editable']) && $value['editable'] == 1 ? true : false;
                                                    $events[] = $event;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //วันหยุด
            if ($search_cal == '') {
                $stopEvents = EzfQuery::getEventStop($start, $end);
                $stopEventsCustom = EzfQuery::getEventStopCustom($start, $end);

                if ($stopEvents) {
                    if ($stopEventsCustom) {
                        $stopEvents = ArrayHelper::merge($stopEvents, $stopEventsCustom);
                    }

                    foreach ($stopEvents as $key => $value) {
                        $event = new \yii2fullcalendar\models\Event();
                        $event->id = 'holiday-zdata_holiday-' . $value['id'] . '-' . $value['user_create'];
                        $event->title = $value['hname'];
                        $event->start = $value['ddate'] . ' 00:00:00';
                        $event->color = 'rgb(255,255,255, 0)';
                        $event->textColor = '#FF0000';
                        $event->allDay = true;
                        $event->editable = false;
                        $events[] = $event;
                    }
                }
            }


            $result = [
                'status' => 'success',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                'forms' => $forms,
                'events' => $events,
            ];
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionAddbtn() {
        if (Yii::$app->getRequest()->isAjax) {
            $start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
            $end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');
            $allDay = isset($_GET['allDay']) ? $_GET['allDay'] : false;
            $forms = isset($_GET['forms']) ? $_GET['forms'] : '';
            $project_id = isset($_GET['project_id']) ? $_GET['project_id'] : '';
            $module_id = isset($_GET['module_id']) ? $_GET['module_id'] : '';
            $subtask_ezf_id = isset($_GET['subtask_ezf_id']) ? $_GET['subtask_ezf_id'] : '';

            $ezformSub = EzfQuery::getEzformOne($subtask_ezf_id);
            $subData = SubjectManagementQuery::GetTableData($ezformSub, ['target' => $project_id]);

            $sdate = new \DateTime();
            $sdate->setTimestamp($start);
            $start = $sdate->format('Y-m-d H:i:s');

            $edate = new \DateTime();
            $edate->setTimestamp($end);
            $end = $edate->format('Y-m-d H:i:s');


            return $this->renderAjax('_addbtn', [
                        'start_date' => $start,
                        'end_date' => $end,
                        'allDay' => $allDay,
                        'forms' => $forms,
                        'subData' => $subData,
                        'subtask_ezf_id' => $subtask_ezf_id,
                        'project_id' => $project_id,
                        'module_id'=>$module_id,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
