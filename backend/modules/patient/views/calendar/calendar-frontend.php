<?php

use appxq\sdii\widgets\ModalForm;
use yii\helpers\Url;

$this->title = Yii::t('backend', 'สมุดนัดหมาย');

\backend\modules\ezforms2\classes\EzfStarterWidget::begin();
?>
<div class="row" style="margin: 10px">
  <div class="col-md-12">
    <?php
    echo \yii2fullcalendar\yii2fullcalendar::widget([
        'options' => [
            'id' => 'calendar',
        ],
        'events' => $events,
        'clientOptions' => [
            'lang' => 'th',
            'header' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'month',
            ],
            'defaultView' => 'month',
            'defaultDate' => $dateNow,
            'eventOrder' => 'id',
            'height' => new \yii\web\JsExpression("
                function(date, jsEvent, view) {
                 var height = $(window).height()*0.83
                      
                      return height;
                }		     
		  "),
            //'navLinks' => true,
            'eventAfterRender' => new \yii\web\JsExpression("
                function (event, element, view) {
                    if(event.id === 'stop-event'){
                        let height = $('.fc-day.fc-widget-content').height() - $('.fc-day-top.fc-sun.fc-past').height()
                            height = height - 7;
                            element.css('height', height);
                            element.css('text-align','center');
                            element.css('font-size','16px');
                        }else{
                        let width = ($('.fc-event-container').width() - 40)/2;
                            element.css('width', '40px');
                            element.css('text-align','center');
                            element.css('font-size','20px');
                            element.css('padding','6px 0');
                            element.css('line-height','1.4');
                            element.css('border-radius', '20px');
                            element.css('margin-left', width);
                        }
                    }                
                "),
            'eventClick' => new \yii\web\JsExpression("
		      function(event, element, view) {
			if(event.id !== 'stop-event'){
                            if(event.title !== '0'){
                            var url = '" . Url::to(['/ezforms2/ezform-data/ezform',
                        'ezf_id' => '1511490170071641200', 'modal' => 'modal-ezform-main', 'reloadDiv' => 'modal-appoint',
                        'initdata' => $initdata, 'target' => '']) . "'+event.id;
			    modalEzformMain(url,'modal-ezform-main'); 
                            }                            
                        }		  
		      }
		  "),
        ],
    ]);

    \backend\modules\ezforms2\classes\EzfStarterWidget::end();
    ?>
  </div>
</div>

<?=
ModalForm::widget([
    'id' => 'modal-appoint',
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
    'options' => [
        'data-url' => Url::to(['/patient/calendar/front-end-success', 'dataid' => '']),
    ]
]);
?>

<?php
$this->registerJs("	
$('.fc-prev-button, .fc-next-button, .fc-today-button').click(function(){
    var moment = $('#calendar').fullCalendar('getDate');    
    window.location.href = '" . Url::to(['/patient/calendar/front-end', 'date' => '']) . "'+moment.format('YYYY-MM-DD');
    
    return false;
  });
  
$('#modal-ezform-main').on('hidden.bs.modal', function (e) {
var dataid = $('#modal-appoint').attr('data-dataid');
    if(dataid){
    $('#modal-appoint').modal('show')
        /*var checkup_url = '/patient/restful/ptprofile-byid?pt_id=1503410811001754400';
        modalAppoint(checkup_url);*/
    }
});

function modalAppoint(url) {        
    $('#modal-appoint .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-appoint').modal('show')
    .find('.modal-content')
    .load(url);
}
");
?>

