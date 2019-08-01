<?php

use yii\web\JsExpression;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$showCalendar = [];
$showlist = [];
$calNone = [];
$input_ezf_id = $ezf_id;
if (!empty($forms)) {
    foreach ($forms as $key => $value) {
        if (isset($value['ezf_id']) && isset($value['subject']) && isset($value['start']) && isset($value['end'])) {
            $showCalendar[$key] = ['ezf_id' => $value['ezf_id'], 'show' => isset($value['show']) ? $value['show'] : 0, 'label' => $value['label']];
            $showlist[$key] = '<i class="glyphicon glyphicon-certificate" style="color: ' . $value['color'] . ';"></i> ' . $value['label'];
            if (isset($value['show']) && $value['show'] == 1) {
                $calNone[] = $key;
            }
        }
    }
}
if (isset($_GET['cal']) && is_array($_GET['cal'])) {
    $cal = $_GET['cal'];
} else {
    $cal = $calNone;
}

$array_now_date = date_parse($now_date);
$year_cal = isset($_GET['year_cal']) ? $_GET['year_cal'] : $array_now_date['year'];
$month_cal = isset($_GET['month_cal']) ? $_GET['month_cal'] : $array_now_date['month'];

$now_date = $year_cal . '-' . $month_cal . '-' . $array_now_date['day'];

$this->registerCss('
        .fc-loading {
            position: absolute;
            top: 0px;
            right: 170px;
            background-color: red;
            color: white;
            padding: 0 8px;
        }
        .fc-event {
            cursor: pointer;
        }
        
        /* Buttons
------------------------------------------------------------------------*/

.fc-button {
	position: relative;
	display: inline-block;
	padding: 0 .6em;
	overflow: hidden;
	height: 1.9em;
	line-height: 1.9em;
	white-space: nowrap;
	cursor: pointer;
	}
	
.fc-state-default { /* non-theme */
	border: 1px solid;
	}

.fc-state-default.fc-corner-left { /* non-theme */
	border-top-left-radius: 4px;
	border-bottom-left-radius: 4px;
	}

.fc-state-default.fc-corner-right { /* non-theme */
	border-top-right-radius: 4px;
	border-bottom-right-radius: 4px;
	}

.fc-text-arrow {
	margin: 0 .4em;
	font-size: 2em;
	line-height: 23px;
	vertical-align: baseline; /* for IE7 */
	}

.fc-button-prev .fc-text-arrow,
.fc-button-next .fc-text-arrow { /* for &lsaquo; &rsaquo; */
	font-weight: bold;
	}
	
/* icon (for jquery ui) */
	
.fc-button .fc-icon-wrap {
	position: relative;
	float: left;
	top: 50%;
	}
	
.fc-button .ui-icon {
	position: relative;
	float: left;
	margin-top: -50%;
	*margin-top: 0;
	*top: -50%;
	}


.fc-state-default {
	border-color: #ff3b30;
	color: #ff3b30;	
        background-color: #fff;
        background-image: none;
        text-shadow: none;
        box-shadow: none;
}
.fc-button-month.fc-state-default, .fc-button-agendaWeek.fc-state-default, .fc-button-agendaDay.fc-state-default{
    min-width: 67px;
	text-align: center;
	transition: all 0.2s;
	-webkit-transition: all 0.2s;
}
.fullcalendar .fc-day-header a, 
.fullcalendar a.fc-list-heading-main,
.fullcalendar a.fc-list-heading-alt{
    color: inherit;
    text-decoration: none;
}

.fullcalendar .fc-day-header a:hover,
.fullcalendar a.fc-list-heading-main:hover,
.fullcalendar a.fc-list-heading-alt:hover{
    color: inherit;
    text-decoration: underline;
}

.fullcalendar a.fc-day-number{
    color: inherit;
    text-decoration: none;
    border-radius: 50%;
    padding: 5px 8px;
    min-width: 16px;
    text-align: center;
    margin: 2px;
}

.fc-today a.fc-day-number{
    background-color: #ff3b30;
    color: #fff;
}

.fullcalendar a.fc-day-number:hover{
    color: #fff;
    background-color: #b8b8b8;
}

.fc-state-hover,
.fc-state-down,
.fc-state-active,
.fc-state-disabled {
	color: #333333;
	background-color: #FFE3E3;
	}

.fc-state-hover {
	color: #ff3b30;
	text-decoration: none;
	background-position: 0 -15px;
	-webkit-transition: background-position 0.1s linear;
	   -moz-transition: background-position 0.1s linear;
	     -o-transition: background-position 0.1s linear;
	        transition: background-position 0.1s linear;
	}

.fc-state-down,
.fc-state-active {
	background-color: #ff3b30;
	background-image: none;
	outline: 0;
	color: #FFFFFF;
}

.fc-state-disabled {
	cursor: default;
	background-image: none;
	background-color: #FFE3E3;
	filter: alpha(opacity=65);
	box-shadow: none;
	border:1px solid #FFE3E3;
	color: #ff3b30;
	}

    ');
$userid = '';
if (Yii::$app->user->can('doctor')) {
    $userid = Yii::$app->user->id;
}

//$dataDept = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData('zdata_working_unit', Yii::$app->user->identity->profile->department);
//$dept = $dataDept ? $dataDept['unit_code']:'';
$dept = Yii::$app->user->identity->profile->department;
//\appxq\sdii\utils\VarDumper::dump();
$clientOptions = [
    'options' => [
        'id' => 'calendar-' . $modal_full_calendar,
        //'style'=>'max-width:900px;'
    ],
    'clientOptions' => [
        'themeSystem' => 'standard',
        'nowIndicator' => true,
        'height' => 800,
        'header' => [
            'left' => 'prev,next today',
            'center' => 'title',
            'right' => 'month,agendaWeek,agendaDay,listMonth',
        ],
        'defaultDate' => $now_date,
//        'defaultView' => 'month',
        'navLinks' => true, // can click day/week names to navigate views
        'selectable' => true,
        'selectHelper' => true,

        'select' => new JsExpression("
		      function(start, end, jsEvent, view,resource ) {
                        let allDay = !Array.isArray(start._i);
                        let modal = '$modal_event_calendar';
                        let ezf_id = '$ezf_id';
                        let reloadDiv = '$reloadDiv';
                        let target = '$target';
                        let  init_date = moment.unix(start.unix()).format('YYYY-MM-DD');
                        let app_time = '08:00';
                        if(typeof start._i[3] != 'undefined' && typeof start._i[4] != 'undefined'){
                            app_time = start._i[3]+':'+start._i[4];
                        }
                        let app_time_stop = '09:00';
                        if(typeof end._i[3] != 'undefined' && typeof end._i[4] != 'undefined'){
                            app_time_stop = end._i[3]+':'+end._i[4];
                        }
                        let initdata = {
                                        app_date: init_date,
                                        end_date: end.unix(),
                                        app_doctor:'$userid',
                                        app_dept:'$dept',
                                        app_time:app_time,
                                        app_time_stop:app_time_stop
//                                        app_status:'1'
                                    };
                        let url_open_form = '/ezforms2/ezform-data/ezform';
//                        if(target == ''){
//                            url_open_form = '/thaihis/appoint-calendar/select-target';
//                        }
                        $.get('/patient/patient/check-visit',{date:init_date,pt_id:target},function(data){
                            if(data != ''){
                            $('#'+modal+' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                            $('#'+modal).modal('show');
                                 $.ajax({
                                        url: url_open_form,
                                        method: 'GET',    
                                        dataType: 'HTML',
                                        data: {
                                            ezf_id:ezf_id,
                                            dataid:data,
                                            modal:modal,
                                            reloadDiv:reloadDiv,
                                            init_date:init_date
                                        },
                                        success: function(result, textStatus) {
                                            $('#'+modal+' .modal-content').html(result);
                                        }
                                    }); 
                            }else{
                                if(init_date == '".date('Y-m-d')."'){
                                     " . SDNoty::show('"ไม่สามารถเลือกนัดวันนี้ได้"', '"error"') . "
                                }else{
                                    $('#'+modal+' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                                    $('#'+modal).modal('show');
                                     $.ajax({
                                        url: url_open_form,
                                        method: 'GET',    
                                        dataType: 'HTML',
                                        data: {
                                            start: start.unix(),
                                            end: end.unix(),
                                            allDay: allDay,
                                            ezf_id:ezf_id,
                                            target:target,
                                            modal:modal,
                                            init_date:init_date,
                                            initdata:btoa(JSON.stringify(initdata)),
                                            reloadDiv:reloadDiv
                                        },
                                        success: function(result, textStatus) {
                                            $('#'+modal+' .modal-content').html(result);
                                        }
                                    }); 
                                }
                            }
                        });
                       
                      }
		  "),
        'dayClick' => new JsExpression("
                    function(date, jsEvent, view) {
                         //console.log(jsEvent);
                    }
		  "),
        //'ajaxEvents' => Url::to(['/timetrack/default/jsoncalendar']),
//'editable' => true,
//              'eventDragStart' => new JsExpression("
//                    function( event, jsEvent, ui, view ) {
//                         console.log(event);
//                    }
//                "),
        'eventDrop' => new JsExpression("
                    function(event, delta, revertFunc) {
                        var sdate = event.start.unix();
                        var end = event.end;
                        var edate;
                        if(!end){
                            edate = event.start;
                            if(event.allDay){
                                edate = edate.add(1, 'days');
                            } else {
                                edate = edate.add(2, 'hours');//minutes
                            }
                        } else {
                            edate = event.end;
                        }
                        
                        $.ajax({
                            url: '" . Url::to(['/thaihis/appoint-calendar/editable']) . "',
                            method: 'GET',    
                            dataType: 'JSON',
                            data: {
                              id: event.id,
                              start: sdate,
                              end: edate.unix(),
                              allDay: event.allDay,
                              field_dstart:'$start_date',
                              field_estart:'$end_date'
                            },
                            success: function(result, textStatus) {
                               if(result.status=='error'){
                                    revertFunc();
                                    " . SDNoty::show('result.message', 'result.status') . "
                               }else{
                               " . SDNoty::show('result.message', 'result.status') . "
                                   if(result.event == 'feed'){
                                        $('#calendar-$modal_full_calendar').fullCalendar('refetchEvents');
                                   }
                               }
                            },
                            error: function(result, textStatus) {
                                revertFunc();
                                " . SDNoty::show('result.message', 'result.status') . "
                            }
                        }); 
                      }
		  "),
        'eventResize' => new JsExpression("
                    function(event, delta, revertFunc) {
                        var sdate = event.start.unix();
                        var end = event.end;
                        var edate;
                        if(!end){
                            edate = event.start;
                            if(event.allDay){
                                edate = edate.add(1, 'days');
                            } else {
                                edate = edate.add(2, 'hours');//minutes
                            }
                        } else {
                            edate = event.end;
                        }
                        
                        $.ajax({
                            url: '" . Url::to(['/thaihis/appoint-calendar/editable']) . "',
                            method: 'GET',    
                            dataType: 'JSON',
                            data: {
                              id: event.id,
                              start: sdate,
                              end: edate.unix(),
                              allDay: event.allDay,
                              field_dstart:'$start_date',
                              field_estart:'$end_date'
                            },
                            success: function(result, textStatus) {
                               if(result.status=='error'){
                                    revertFunc();
                               }else{
                               " . SDNoty::show('result.message', 'result.status') . "
                                   if(result.event == 'feed'){
                                        $('#calendar-$modal_full_calendar').fullCalendar('refetchEvents');
                                   }
                               }
                            },
                            error: function(result, textStatus) {
                                revertFunc();
                            }
                        }); 
                    }
		  "),
        'eventLimit' => true, // allow "more" link when too many events
        'eventOrder' => 'id',
        'eventRender' => new JsExpression("
                      function(event, element) {
                        element.find('.fc-content').css('text-overflow', 'initial');
                        element.find('.fc-content').css('white-space','pre-line');
                        element.find('.fc-time').remove();
                        element.find('.fc-title').html('<i class=\"glyphicon glyphicon-calendar\"></i> '+event.title);
                      }        
                "),
        'eventClick' => new JsExpression("
		    function(calEvent, jsEvent, view) {
		    
                           var ezf_id = '" . $input_ezf_id . "';
                           var url = '" . Url::to(['/ezforms2/ezform-data/ezform', 'modal' => $modal_event_calendar]) . "';
                           var modal = '$modal_event_calendar';
                           var reloadDiv = '$reloadDiv';
                           var str = calEvent.id;
//                           console.log(str);
                        var idenArry = str.split('-');
                        if( idenArry[0]=='ezform'){
                            $('#'+modal+' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                            $('#'+modal).modal('show');
                            $.ajax({
                                url: url,
                                method: 'GET',    
                                dataType: 'HTML',
                                data: {
                                  ezf_id: idenArry[1],
                                  dataid: idenArry[2],
                                  reloadDiv:reloadDiv,
                                  modal:modal
                                },
                                success: function(result, textStatus) {
                                    $('#'+modal+' .modal-content').html(result);
                                }
                            }); 

                        }
                        return false;
		      }
		  "),
        'events' => new JsExpression("
                      function(start, end, timezone, callback) {
                        var ezf_id = '$ezf_id';
                        var target = '$target';
                        var event_name = '$event_name';
                        var start_date = '$start_date';
                        var end_date = '$end_date';
                        var allDay = '$allDay';
                        let select_all = '$select_all';
                        let init_date = '$now_date';
                        let app_time = '08:00';
                        if(typeof start._i[3] != 'undefined' && typeof start._i[4] != 'undefined'){
                            app_time = start._i[3]+':'+start._i[4];
                        }
                        let app_time_stop = '09:00';
                        if(typeof end._i[3] != 'undefined' && typeof end._i[4] != 'undefined'){
                            app_time_stop = end._i[3]+':'+end._i[4];
                        }
                        let initdata = {
                                        app_date: init_date,
                                        end_date: end.unix(),
                                        app_doctor:'$userid',
                                        app_dept:'$dept',
                                        app_time:app_time,
                                        app_time_stop:app_time_stop
//                                        app_status:'1'
                                    };
                        let init_data = btoa(JSON.stringify(initdata));
                         $('#btn-appoint-$reloadDiv').attr('data-url','" . Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezf_id, 'target' => $target, 'modal' => $modal_event_calendar]) . "'+'&initdata='+init_data);
                        $.ajax({
                            url: '" . Url::to(['/thaihis/appoint-calendar/feed']) . "', 
                            method: 'GET',    
                            dataType: 'JSON',
                            data: {
                              start: start.unix(),
                              end: end.unix(),
                              ezf_id:ezf_id,
                              target:target,
                              event_name:event_name,
                              start_date:start_date,
                              end_date:end_date,
                              allDay:allDay,
                              select_all:select_all
                            },
                            success: function(result, textStatus) {
                              callback(result);
                            }
                        });  
                      }
                "),
    ],
];
?>

<div class="modal-header">
    <h4 class="pull-left"><?= $title ?></h4>
    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-10">
            <?= appxq\sdii\widgets\SDfullcalendar::widget($clientOptions); ?>
        </div>
        <div class="col-md-2 sdbox-col text-right">
            <?php if ($target) { ?>
<!--                <div style="margin-bottom: 15px;">-->
<!--                    --><?php //echo \backend\modules\ezforms2\classes\BtnBuilder::btn()
//                        ->ezf_id($ezf_id)
//                        ->target($target)
//                        ->modal($modal_event_calendar)
//                        ->options(['class' => 'btn btn-success', 'id' => 'btn-appoint-' . $reloadDiv])
////                    ->initdata([
//////                        'app_date' => date('Y-m-d'),
////                        'app_doctor' => $userid,
////                        'app_dept' => $dept
////                    ])
//                        ->buildBtnAdd() ?>
<!--                </div>-->
            <?php } ?>
            <div style="margin-bottom: 15px;">
                <?= \yii\helpers\Html::dropDownList('month_cal', isset($month_cal) && $month_cal != '' ? $month_cal : date('m'), [
                    '01' => 'มกราคม',
                    '02' => 'กุมภาพันธ์',
                    '03' => 'มีนาคม',
                    '04' => 'เมษายน',
                    '05' => 'พฤษภาคม',
                    '06' => 'มิถุนายน',
                    '07' => 'กรกฎาคม',
                    '08' => 'สิงหาคม',
                    '09' => 'กันยายน',
                    '10' => 'ตุลาคม',
                    '11' => 'พฤศจิกายน',
                    '12' => 'ธันวาคม'
                ], ['class' => 'form-control', 'id' => "month-$reloadDiv", 'placeholder' => Yii::t('app', 'Moonth')]) ?>
            </div>
            <div style="margin-bottom: 15px;">
                <?= \yii\helpers\Html::textInput('year_cal', isset($year_cal) && $year_cal != '' ? $year_cal : date('Y'), ['class' => 'form-control', 'id' => "year-$reloadDiv", 'placeholder' => Yii::t('app', 'Year'), 'type' => 'number']) ?>
            </div>
            <div style="margin-bottom: 15px;">
                <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('select-all', $select_all ? true : false, ['label' => 'ดูรายการนัดทั้งหมด', 'id' => 'select-all-' . $reloadDiv]) ?>
            </div>

        </div>
        <div class="clearfix"></div>
    </div>
</div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    // JS script

    $('#year-<?=$reloadDiv?>').change(function (e) {
        var url = $('#<?=$modal_full_calendar?>').attr('data-url');
        let year = $(this).val();
        let month = $('#month-<?=$reloadDiv?>').val();
        let select_all = 0;
        if ($('#select-all-<?=$reloadDiv?>').is(':checked')) {
            select_all = 1;
        }
        $.get(
            url, //serialize Yii2 form
            {'year_cal': year, select_all: select_all, month_cal: month}
        ).done(function (result) {
            $('#<?=$modal_full_calendar?> .modal-content').html(result);
        }).fail(function () {
            <?= \appxq\sdii\helpers\SDNoty::show("'" . \appxq\sdii\helpers\SDHtml::getMsgError() . "Server Error'", '"error"')?>
            console.log('server error');
        });
        return false;
    });

    $('#select-all-<?=$reloadDiv?>').change(function (e) {
        var url = $('#<?=$modal_full_calendar?>').attr('data-url');
        let year = $('#year-<?=$reloadDiv?>').val();
        let month = $('#month-<?=$reloadDiv?>').val();
        let select_all = 0;
        if ($(this).is(':checked')) {
            select_all = 1;
        }
        $.get(
            url, //serialize Yii2 form
            {'year_cal': year, select_all: select_all, month_cal: month}
        ).done(function (result) {
            $('#<?=$modal_full_calendar?> .modal-content').html(result);
        }).fail(function () {
            <?= \appxq\sdii\helpers\SDNoty::show("'" . \appxq\sdii\helpers\SDHtml::getMsgError() . "Server Error'", '"error"')?>
            console.log('server error');
        });
        return false;
    });
    $('#month-<?=$reloadDiv?>').change(function (e) {
        var url = $('#<?=$modal_full_calendar?>').attr('data-url');
        let year = $('#year-<?=$reloadDiv?>').val();
        let month = $(this).val();
        let select_all = 0;
        if ($('#select-all-<?=$reloadDiv?>').is(':checked')) {
            select_all = 1;
        }
        $.get(
            url, //serialize Yii2 form
            {'year_cal': year, select_all: select_all, month_cal: month}
        ).done(function (result) {
            $('#<?=$modal_full_calendar?> .modal-content').html(result);
        }).fail(function () {
            <?= \appxq\sdii\helpers\SDNoty::show("'" . \appxq\sdii\helpers\SDHtml::getMsgError() . "Server Error'", '"error"')?>
            console.log('server error');
        });
        return false;
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>
