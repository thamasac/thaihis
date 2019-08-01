<?php

use yii\web\JsExpression;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

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
if (isset($_POST['cal']) && is_array($_POST['cal'])) {
    $cal = $_POST['cal'];
} else {
    $cal = $calNone;
}

$now_date = date('Y-m-d');

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

$clientOptions = [
    'options' => [
        'id' => 'event-calendar-show',
    //'style'=>'max-width:900px;'
    ],
    'clientOptions' => [
        'themeSystem' => 'standard',
        'nowIndicator' => true,
        'height' => 800,
        'header' => [
            'left' => 'prev,next today',
            'center' => 'title',
            'right' => 'month,agendaWeek,agendaDay',
        ],
        'defaultDate' => $now_date,
        'navLinks' => true, // can click day/week names to navigate views
        'selectable' => true,
        'selectHelper' => true,
        'select' => new JsExpression("
		      function(start, end) {
                        console.log(moment.unix(start.unix()).format('YYYY-MM-DD'));
                        var allDay = !Array.isArray(start._i);
                        var modal = 'modal-event-calendar';
                        var ezf_id = '$ezf_id';
                        var reloadDiv = '$reloadDiv';
                        var target = '$target';
                        var initdata = {
                                        app_date: moment.unix(start.unix()).format('YYYY-MM-DD'),
                                        end_date: end.unix(),
                                    };
                        
                        $('#'+modal+' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                        $('#'+modal).modal('show');
                        $.ajax({
                            url: '" . Url::to(['/ezforms2/ezform-data/ezform']) . "',
                            method: 'GET',    
                            dataType: 'HTML',
                            data: {
                                start: start.unix(),
                                end: end.unix(),
                                allDay: allDay,
                                ezf_id:ezf_id,
                                target:target,
                                modal:modal,
                                initdata:btoa(JSON.stringify(initdata)),
                                reloadDiv:reloadDiv
                            },
                            success: function(result, textStatus) {
                                $('#'+modal+' .modal-content').html(result);
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
                            url: '" . Url::to(['/ezforms2/full-calendar/editable']) . "',
                            method: 'GET',    
                            dataType: 'JSON',
                            data: {
                              id: event.id,
                              start: sdate,
                              end: edate.unix(),
                              allDay: event.allDay
                            },
                            success: function(result, textStatus) {
                            console.log(result);
                               if(result.status=='error'){
                                    revertFunc();
                               }else{
                                    " . SDNoty::show('result.message', 'result.status') . "
                               }
                            },
                            error: function(result, textStatus) {
                                revertFunc();
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
                            url: '" . Url::to(['/ezforms2/full-calendar/editable']) . "',
                            method: 'GET',    
                            dataType: 'JSON',
                            data: {
                              id: event.id,
                              start: sdate,
                              end: edate.unix(),
                              allDay: event.allDay,
                            },
                            success: function(result, textStatus) {
                            console.log(result);
                               if(result.status=='error'){
                                    revertFunc();
                               }else{
                                    " . SDNoty::show('result.message', 'result.status') . "
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
        'eventClick' => new JsExpression("
		    function(calEvent, jsEvent, view) {
                           var ezf_id = '" . $input_ezf_id . "';
                           var url = '" . Url::to(['/ezforms2/ezform-data/ezform', 'modal' => 'modal-add-event']) . "';
                           var modal = 'modal-event-calendar';
                           var reloadDiv = '$reloadDiv';
                           var str = calEvent.id;
                           console.log(str);
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
                        $.ajax({
                            url: '" . Url::to(['/ezforms2/full-calendar/feed']) . "', 
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
                              allDay:allDay
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
        <div class="col-md-12">
            <?= \yii2fullcalendar\yii2fullcalendar::widget($clientOptions)
            ?>
        </div>

    </div>
</div>
<?php
$this->registerJs("
    // JS script

    $('#cal-$reloadDiv').on('change', function (e) {
        $('form#jump_menu-<?= $reloadDiv ?>').submit();
    });

    $('#search-$reloadDiv').on('change', function (e) {
        $('form#jump_menu-$reloadDiv').submit();
    });

    $('#year-$reloadDiv').on('change', function (e) {
        $('form#jump_menu-$reloadDiv').submit();
    });

    $('form#jump_menu-$reloadDiv').on('beforeSubmit', function (e) {
        var \$form = $(this);
        var url = $('#$reloadDiv').attr('data-url');

        $.post(
                url, //serialize Yii2 form
                \$form.serialize()
                ).done(function (result) {
            $('#$reloadDiv').html(result);
        }).fail(function () {
" . SDNoty::show("'" . \appxq\sdii\helpers\SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
        });
        return false;
    });

");
?>
