<?php

namespace backend\modules\thaihis\classes;

use appxq\sdii\utils\SDUtility;
use appxq\sdii\utils\VarDumper;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of EzfHelper
 *
 * @author appxq
 */
class ThaiHisHelper {

    public static function uiPatientVisit($reloadDiv, $receipt_status, $qname, $date_now) {
        $url = Url::to(['/patient/cashier/queue-view', 'reloadDiv' => $reloadDiv, 'receipt_status' => $receipt_status, 'qname' => $qname, 'date_now' => $date_now]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');                       
        ");

        return $html;
    }

    public static function uiGridOrder($ezf_id, $visit_id, $reloadDiv, $btnDisabled = FALSE, $options = []) {
        $options = EzfFunc::arrayEncode2String($options);
        $url = Url::to(['/thaihis/order/grid-order', 'ezf_id' => $ezf_id, 'visitid' => $visit_id
                    , 'reloadDiv' => $reloadDiv, 'btnDisabled' => $btnDisabled, 'options' => $options]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

    public static function uiOrderList($visit_id, $reloadDiv, $options = []) {
        $options = EzfFunc::arrayEncode2String($options);
        $url = Url::to(['/thaihis/order/order-search', 'visitid' => $visit_id, 'reloadDiv' => $reloadDiv, 'options' => $options]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

    public static function btnAppointment($ezf_id, $visit_id, $target, $options)
    {
        $modal_full_calendar = 'modal-full-calendar-'.SDUtility::getMillisecTime();
        $modal_event_calendar = 'modal-event-calendar-'.SDUtility::getMillisecTime();
        $btn_appointment = 'btn-appointment-'.SDUtility::getMillisecTime();
        $url = Url::to(['/thaihis/appoint-calendar/calendar',
            'ezf_id' => $ezf_id,
            'target' => $target,
            'visit_id' => $visit_id,
            'title' => isset($options['title']) ? $options['title'] : 'Calendar',
            'event_name' => isset($options['event_name']) ? $options['event_name'] : '',
            'start_date' => isset($options['start_date']) ? $options['start_date'] : '',
            'end_date' => isset($options['end_date']) ? $options['end_date'] : '',
            'allDay' => isset($options['allDay']) ? $options['allDay'] : '',
            'modal-full-calendar' => $modal_full_calendar,
            'modal-event-calendar' => $modal_event_calendar
        ]);
        $icon = isset($options['icon']) ? $options['icon'] : 'fa-calendar';
        $color = isset($options['btn_color']) ? $options['btn_color'] : 'btn-default';
        $btn_text = isset($options['btn_text']) ? $options['btn_text'] : 'Appointment';
        $btn_style = isset($options['btn_style']) ? $options['btn_style'] : 'btn-block';
//        VarDumper::dump($icon);
        $dataWidgetOption =  $query = (new \yii\db\Query())
            ->select('options')
            ->from('ezmodule_widget')
            ->where('widget_attribute != 1 AND widget_id = :id',[':id'=>isset($options['widget_event'])?$options['widget_event']:''])->one();
        $dataWidgetOption = isset($dataWidgetOption['options'])?SDUtility::string2Array($dataWidgetOption['options']):[];
        $html = \appxq\sdii\widgets\ModalForm::widget([
            'id' => $modal_full_calendar,
            'size' => 'modal-xl',
            'options' =>[
                'data-url' => $url,
                'class' => 'modal-full-calendar'
            ],
            'tabindexEnable' => false

        ]);
        $html .= \appxq\sdii\widgets\ModalForm::widget([
            'id' => $modal_event_calendar,
            'size' => 'modal-xxl',
            'options' =>[
                'data-options' =>EzfFunc::arrayEncode2String($dataWidgetOption),
                'data-visitid' => $visit_id,
                'class' => 'modal-event-calendar'
            ],
            'tabindexEnable' => false
        ]);
        $html .= Html::tag('div', '<button id="'.$btn_appointment.'" class="btn '.$color.' '.$btn_style.'"><i class="fa '.$icon.'"></i> '.$btn_text.'</button>', ['id' => 'appointment-display', 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
             $('#$modal_event_calendar').on('hidden.bs.modal',function(){
                  $('#calendar-$modal_full_calendar').fullCalendar('refetchEvents');
                   if($('body .modal').hasClass('in')){
                        $('body').addClass('modal-open');
                    } 
//                $('#$modal_full_calendar .modal-content').html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
//                $('#$modal_full_calendar').find('.modal-content').load('$url');
            });
            
            $('#$btn_appointment').click(function(){
                $('#$modal_full_calendar .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                $('#$modal_full_calendar').modal('show');
                $('#$modal_full_calendar').find('.modal-content').load('$url');
            }); 
              
        ");

        return $html;
    }

    public static function btnCloseVisit($visit_tran_id, $visit_type, $reloadDiv, $current_url = '/cpoe', $options = []) {
        $url = Url::to(['/patient/emr/close-visit-btn',
                    'visit_tran_id' => $visit_tran_id,
                    'visit_type' => $visit_type,
                    'reloadDiv' => $reloadDiv,
                    'current_url' => $current_url,
                    'options' => EzfFunc::arrayEncode2String($options)
        ]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiOrderLists($visit_id, $visit_type, $order_status,$options, $reloadDiv) {
        $options = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($options);
        
        $url = Url::to(['/thaihis/order/counter-order-lists', 'visitid' => $visit_id
                    , 'visit_type' => $visit_type, 'reloadDiv' => $reloadDiv, 'order_status' => $order_status,'options'=>$options]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }
    
    public static function uiOrderListCyto($reloadDiv, $options=[]) {
        $options = EzfFunc::arrayEncode2String($options);
        $url = Url::to(['/thaihis/order/order-counter-cyto', 'reloadDiv' => $reloadDiv, 'options' => $options]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

}
?>

