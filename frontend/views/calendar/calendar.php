<?php

use appxq\sdii\widgets\ModalForm;
use yii\helpers\Url;

$this->title = Yii::t('backend', 'สมุดนัดหมาย');
//appxq\sdii\utils\VarDumper::dump(Yii::$app->urlManagerBackend->createUrl(['site/index','id'=>4]));
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
			if(event.id === 'appoint-event'){
                        console.log(event.title);
                            if(event.title !== '0'){
                               var url = '" . Url::to(['ezforms2/ezform-data/ezform',
                            'ezf_id' => '1508128862067166800',
                            'modal' => 'modal-appoint',]) . "';
			    modalAppoint(url); 
                            }                            
                        }		  
		      }
		  "),
        ],
    ]);
    ?>
  </div>
</div>

<?=
ModalForm::widget([
    'id' => 'modal-appoint',
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
]);
?>


<?php
$this->registerJs("	
function modalAppoint(url) {
    $('#modal-appoint .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-appoint').modal('show')
    .find('.modal-content')
    .load(url);
}

$('.fc-prev-button, .fc-next-button, .fc-today-button').click(function(){
    var moment = $('#calendar').fullCalendar('getDate');    
    window.location.href = '" . yii\helpers\Url::to(['/calendar/index', 'date' => '']) . "'+moment.format('YYYY-MM-DD');
    
    return false;
  });
");
?>

