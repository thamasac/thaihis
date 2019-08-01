<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\ezforms2\classes;
use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;
/**
 * Description of ActivityBuilder
 *
 * @author jokeclancool
 */
class CalendarBuilder extends Component {
   //put your code here
    public $ezf_id = '';
    public $modal = 'modal-ezform-main';
    public $reloadDiv = '';
    public $title = '';
    public $targetField = '';
    public $dataid = '';
    
    public $image_field = '';
    public $fields = [];
    public $fields_search = [];
    public $template_items = '';
    public $template_selection = '';
    
    public $options = [];
    
    /**
     * @param array $title show title
     * @return $this the query object itself
     */
    public function title($title) {
        $this->title = $title;
        return $this;
    }
    
    /**
     * @param array $start_date show start_date
     * @return $this the query object itself
     */
    public function start_date($start_date) {
        $this->start_date = $start_date;
        return $this;
    }
    
    
    /**
     * @param string $modal use modal default modal-ezform-main
     * @return $this the query object itself
     */
    public function modal($modal = null) {
        if (isset($modal)) {
            $this->modal = $modal;
        }
        return $this;
    }

    /**
     * @param string $reloadDiv div id for reload html
     * @return $this the query object itself
     */
    public function reloadDiv($reloadDiv = '') {
        $this->reloadDiv = $reloadDiv;
        return $this;
    }


    /**
     * @param string $ezf_id ezf id
     * @return $this the query object itself
     */
    public function ezf_id($ezf_id) {
        $this->ezf_id = $ezf_id;
        return $this;
    }

    /**
     * @param array $options options Html
     * @return $this the query object itself
     */
    public function options($options) {
        $this->options = $options;
        return $this;
    }
    
    /**
     * @param array $end_date show end_date
     * @return $this the query object itself
     */
    public function end_date($end_date) {
        $this->end_date = $end_date;
        return $this;
    }
    
    /**
     * @param array $event_name show event_name
     * @return $this the query object itself
     */
    public function event_name($event_name) {
        $this->event_name = $event_name;
        return $this;
    }
    
    /**
     * @param array $allDay show allDay
     * @return $this the query object itself
     */
    public function allDay($allDay) {
        $this->allDay = $allDay;
        return $this;
    }

    /**
     * @inheritdoc
     * @return CalendarBuilder the newly created [[CalendarBuilder]] instance.
     */
    public static function calendarWidget() {
        return Yii::createObject(CalendarBuilder::className());//, [get_called_class()]
    }

    public function buildCalendar() {
       if(empty($this->reloadDiv)){
           $this->reloadDiv = 'div-'.\appxq\sdii\utils\SDUtility::getMillisecTime();
       }
       
        $url = Url::to(['/ezforms2/calendar/index',
                    'ezf_id' => $this->ezf_id,
                    'modal' => $this->modal,
                    'title' => $this->title,
                    'reloadDiv' => $this->reloadDiv,
                    'event_name' => $this->event_name,
                    'target' => $this->target,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'allDay' => $this->allDay,
        ]);
        
        $options = $this->options;
        $options['id'] = $this->reloadDiv;
        $options['data-url'] = $url;
        
        $view = Yii::$app->getView();
        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class="sdloader "><i class="sdloader-icon"></i></div>', $options);
    }
   
}
