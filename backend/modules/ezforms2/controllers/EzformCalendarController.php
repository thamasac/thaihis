<?php

namespace backend\modules\ezforms2\controllers;

use appxq\sdii\utils\VarDumper;
use Yii;
use backend\modules\ezforms2\models\EzformSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;

/**
 * EzformController implements the CRUD actions for Ezform model.
 */
class EzformCalendarController extends Controller {

    public function behaviors() {
        return [
            /* 	    'access' => [
              'class' => AccessControl::className(),
              'rules' => [
              [
              'allow' => true,
              'actions' => ['index', 'view'],
              'roles' => ['?', '@'],
              ],
              [
              'allow' => true,
              'actions' => ['view', 'create', 'update', 'delete', 'deletes'],
              'roles' => ['@'],
              ],
              ],
              ], */
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action) {
        if (parent::beforeAction($action)) {
            if (in_array($action->id, array('create', 'update'))) {
                
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Lists all Ezform models.
     * @return mixed
     */
    public function actionIndex() {
        
        return $this->render('index', [
                    
        ]);
    }

    /**
     * Displays a single Ezform model.
     * @param string $id
     * @return mixed
     */
    public function actionFeed() {
        if (Yii::$app->getRequest()->isAjax) {
            $start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
            $end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');
            $forms = isset($_GET['forms'])?$_GET['forms']:'';
            $forms = EzfFunc::stringDecode2Array($forms);
            
            $search_cal = isset($_GET['search_cal'])?$_GET['search_cal']:'';
            $cal = isset($_GET['cal'])?$_GET['cal']:'';
            $cal = EzfFunc::stringDecode2Array($cal);
            
            $sdate = new \DateTime();
            $sdate->setTimestamp($start);
            $start = $sdate->format('Y-m-d H:i:s');
            
            $edate = new \DateTime();
            $edate->setTimestamp($end);
            $end = $edate->format('Y-m-d H:i:s');
            
            Yii::$app->response->format = Response::FORMAT_JSON;
            $events = [];
            
            if(!empty($forms)){
                foreach ($forms as $key => $value) {
                    if(isset($value['ezf_id']) && isset($value['subject']) && isset($value['start']) && isset($value['end']) && in_array($key, $cal)){
                        $repeat = isset($value['repeat'])?$value['repeat']:'';
                        
                        $ezform = EzfQuery::getEzformOne($value['ezf_id']);
                        $zdataCalendar = EzfQuery::getEventEzForm($start, $end, $ezform, $value, $search_cal);
                        if($zdataCalendar){
                            foreach ($zdataCalendar as $keyZdata => $valueZdata) {
                                $allday = false;
                                if(isset($value['allday'])){
                                    $allday = (isset($valueZdata[$value['allday']]) && $valueZdata[$value['allday']]==1)?true:false;
                                }
                                $check_date = explode(' ', $valueZdata[$value['start']]);
                                if(count($check_date)<2){
                                    $allday = true;
                                } else {
                                    if($check_date[1]=='00:00:00'){
                                        $allday = true;
                                    }
                                }
                                
                                $event = new \yii2fullcalendar\models\Event();
                                $event->id = 'ezform-'.$value['ezf_id'].'-'.$valueZdata['id'].'-'.$valueZdata['user_create'];
                                $event->title = $valueZdata[$value['subject']];
                                $event->start = $valueZdata[$value['start']];
                                $event->end = $valueZdata[$value['end']];
                                $event->color = $value['color'];
                                $event->allDay = $allday;
                                $event->editable = isset($value['editable']) && $value['editable']==1?true:false;
                                $events[] = $event;
                            }
                        }
                        
                        
                        if($repeat!=''){
                            $zdataCalendarRepeat = EzfQuery::getRepeatEventEzForm($start, $end, $ezform, $value, $search_cal);
                            if($zdataCalendarRepeat){
                                foreach ($zdataCalendarRepeat as $keyRZdata => $valueRZdata) {
                                    $allday = false;
                                    if(isset($value['allday'])){
                                        $allday = (isset($valueRZdata[$value['allday']]) && $valueRZdata[$value['allday']]==1)?true:false;
                                    }
                                    $check_date = explode(' ', $valueRZdata[$value['start']]);
                                    if(count($check_date)<2){
                                        $allday = true;
                                    }else {
                                        if($check_date[1]=='00:00:00'){
                                            $allday = true;
                                        }
                                    }
                                    
                                    $repeatValue = $valueRZdata[$repeat];
                                    $tStart = new \DateTime($valueRZdata[$value['start']]);
                                    $tEnd = new \DateTime($valueRZdata[$value['end']]);
                                        
                                    if($repeatValue == 'year'){
                                        $newDate = new \DateTime($start);
                                        
                                        if($newDate->format('Y')>=$tStart->format('Y')){
                                            $event = new \yii2fullcalendar\models\Event();
                                            $event->id = 'ezform-'.$value['ezf_id'].'-'.$valueRZdata['id'].'-'.$valueRZdata['user_create'];
                                            $event->title = $valueRZdata[$value['subject']];
                                            $event->start = $newDate->format('Y').'-'.$tStart->format('m-d H:i:s');
                                            $event->end = $newDate->format('Y').'-'.$tEnd->format('m-d H:i:s');
                                            $event->color = $value['color'];
                                            $event->allDay = $allday;
                                            $event->editable = isset($value['editable']) && $value['editable']==1?true:false;
                                            $events[] = $event;
                                        }
                                    } elseif ($repeatValue == 'month') {
                                        $newDate = new \DateTime($start);
                                        $newDate->modify('+15 day');
                                        if($newDate->format('Ym')>=$tStart->format('Ym')){
                                           $event = new \yii2fullcalendar\models\Event();
                                            $event->id = 'ezform-'.$value['ezf_id'].'-'.$valueRZdata['id'].'-'.$valueRZdata['user_create'];
                                            $event->title = $valueRZdata[$value['subject']];
                                            $event->start = $newDate->format('Y-m').'-'.$tStart->format('d H:i:s');
                                            $event->end = $newDate->format('Y-m').'-'.$tEnd->format('d H:i:s');
                                            $event->color = $value['color'];
                                            $event->allDay = $allday;
                                            $event->editable = isset($value['editable']) && $value['editable']==1?true:false;
                                            $events[] = $event;
                                        }
                                    } else {
                                        $cStart = new \DateTime($start);
                                        $cEnd = new \DateTime($end);
                                       
                                        if($repeatValue == 'day'){
                                            for($d = $cStart; $d <= $cEnd; $d->modify('+1 day')){
                                                if($d>=$tStart){
                                                    $event = new \yii2fullcalendar\models\Event();
                                                    $event->id = 'ezform-'.$value['ezf_id'].'-'.$valueRZdata['id'].'-'.$valueRZdata['user_create'];
                                                    $event->title = $valueRZdata[$value['subject']];
                                                    $event->start = $d->format('Y-m-d').' '.$tStart->format('H:i:s');
                                                    $event->end = $d->format('Y-m-d').' '.$tEnd->format('H:i:s');
                                                    $event->color = $value['color'];
                                                    $event->allDay = $allday;
                                                    $event->editable = isset($value['editable']) && $value['editable']==1?true:false;
                                                    $events[] = $event;
                                                }
                                            }
                                        } elseif($repeatValue == 'week'){
                                            $w = $tStart->format('w');
                                            if($w>0){
                                                $cStart->modify("+$w day");
                                            }
                                            
                                            for($d = $cStart; $d <= $cEnd; $d->modify('+7 day')){
                                                if($d>=$tStart){
                                                    $event = new \yii2fullcalendar\models\Event();
                                                    $event->id = 'ezform-'.$value['ezf_id'].'-'.$valueRZdata['id'].'-'.$valueRZdata['user_create'];
                                                    $event->title = $valueRZdata[$value['subject']];
                                                    $event->start = $d->format('Y-m-d').' '.$tStart->format('H:i:s');
                                                    $event->end = $d->format('Y-m-d').' '.$tEnd->format('H:i:s');
                                                    $event->color = $value['color'];
                                                    $event->allDay = $allday;
                                                    $event->editable = isset($value['editable']) && $value['editable']==1?true:false;
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
            if($search_cal==''){
                $stopEvents = EzfQuery::getEventStop($start, $end);
                $stopEventsCustom = EzfQuery::getEventStopCustom($start, $end);

                if($stopEvents){
                    if($stopEventsCustom){
                        $stopEvents = ArrayHelper::merge($stopEvents, $stopEventsCustom);
                    }

                    foreach ($stopEvents as $key => $value) {
                        $event = new \yii2fullcalendar\models\Event();
                        $event->id = 'holiday-zdata_holiday-'.$value['id'].'-'.$value['user_create'];
                        $event->title = $value['hname'];
                        $event->start = $value['ddate'].' 00:00:00';
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
            $forms = isset($_GET['forms'])?$_GET['forms']:'';
            $forms = EzfFunc::stringDecode2Array($forms);
            
            $sdate = new \DateTime();
            $sdate->setTimestamp($start);
            $start = $sdate->format('Y-m-d H:i:s');
            
            $edate = new \DateTime();
            $edate->setTimestamp($end);
            $end = $edate->format('Y-m-d H:i:s');
            
            
            return $this->renderAjax('_addbtn', [
                        'start' => $start,
                        'end' => $end,
                        'allDay' => $allDay,
                        'forms' => $forms,
            ]);
                   
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionEditable() {
        if (Yii::$app->getRequest()->isAjax) {
            $start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
            $end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            $allDay = isset($_GET['allDay']) ? $_GET['allDay'] : 'false';
            $forms = isset($_GET['forms'])?$_GET['forms']:'';
            $forms = EzfFunc::stringDecode2Array($forms);
            
            $allDay = $allDay=='true'?1:0;
                    
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
            if(!empty($forms) && isset($idenArry[0]) && $idenArry[0]=='ezform'){
                foreach ($forms as $key => $value) {
                    if(isset($value['ezf_id']) && isset($value['subject']) && isset($value['start']) && isset($value['end']) && $value['ezf_id']==$idenArry[1]){
                        $modelEzf = EzfQuery::getEzformOne($value['ezf_id']);
                        $version = $modelEzf->ezf_version;
                        $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $idenArry[2]);

                        if ($modelZdata) {
                            if($modelZdata->rstat!=0 && !empty($modelZdata->ezf_version)){
                                $version = (in_array($modelZdata->rstat, [0,1]))?$version:$modelZdata->ezf_version;
                            }
                            if(!empty($modelZdata->ezf_version)){
                                $modelEzf->ezf_version = (in_array($modelZdata->rstat, [0,1]))?$version:$modelZdata->ezf_version;
                            }
                        }
                        
                        $modelFieldsAll = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);
                        Yii::$app->session['show_varname'] = 0;
                        Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
                        
                        $sField = EzfQuery::getFieldByNameVersion($modelEzf->ezf_id, $value['start'], $version);
                        if($sField){
                            if($sField['ezf_field_type']==64 && $allDay=='false'){
                                $result = [
                                    'status' => 'error',
                                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                                ];
                                return $result;
                            }
                        } else {
                             $result = [
                                'status' => 'error',
                                'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                            ];
                            return $result;
                        }
                        
                        $modelZ = EzfFunc::setDynamicModel($modelFieldsAll, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
                        $modelZ = EzfUiFunc::loadData($modelZ, $modelEzf->ezf_table, $idenArry[2]);
                        if($modelZ){
                            if(isset($value['allday']) && !empty($value['allday'])){
                                $modelZ->{$value['allday']} = $allDay;
                            }
                            
                            $modelZ->{$value['end']} = ($allDay)?$endAllDay:$end;
                            $modelZ->{$value['start']} = ($allDay)?$startAllDay:$start;
                            $modelZ->user_update = \Yii::$app->user->id;
                            $modelZ->update_date = new \yii\db\Expression('NOW()');
                            
                            $data = EzfUiFunc::saveData($modelZ, $modelEzf->ezf_table, $modelEzf->ezf_id, $modelZ->id);
                            
                            $result = [
                                'status' => 'success',
                                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                                'data' => $data,
                            ];
                            return $result;
                        } else {
                            $result = [
                                'status' => 'error',
                                'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                                'data' => $data,
                            ];
                        }
                        break;
                    }
                }
            }
            
            $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                ];
                return $result;
                   
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
}
