<?php

use appxq\sdii\widgets\ModalForm;
use kartik\datetime\DateTimePicker;
?>

    <div id="show-calendar" data-url="<?=
         yii\helpers\Url::to(['/ezforms2/full-calendar/calendar',
             'ezf_id' => $ezf_id, 'event_name' => $event_name, 'start_date' => $start_date, 'end_date' => $end_date, 'allDay' => $allDay, 'reloadDiv' => 'show-calendar'])
         ?>"></div>

<?php
$noty = \appxq\sdii\helpers\SDNoty::show("'" . \appxq\sdii\helpers\SDHtml::getMsgError() . "Server Error'", '"error"');

$this->registerJs(
        "this.onload = function () {
        
    };handleClientLoad()", yii\web\View::POS_READY, 'my-calendar-handler'
);
$this->registerJs(<<<JS
    var reload = false;
        
    $(function(){
      onloadCalendar();  
    })
    function onloadCalendar () {
        var url = $('#show-calendar').attr('data-url');

        $.get(
                url, //serialize Yii2 form
                {}
        ).done(function (result) {
            $('#show-calendar').html(result);
        }).fail(function () {
            $noty
            console.log('server error');
        });
        return false;
    }

    function reloadCalendar(){
        var url = $('#show-calendar').attr('data-url');

        $.get(
                url, //serialize Yii2 form
                {}
        ).done(function (result) {
            $('#show-calendar').html(result);
        }).fail(function () {
            $noty
            console.log('server error');
        });
        return false;
    }
    
    
JS
);
?>
