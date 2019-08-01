<?php

namespace backend\modules\subjects\classes;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of ScheduleBuilder
 *
 */
class ScheduleBuilder extends Component {

    //put your code her
    public $widget_id = '';
    public $reloadDiv = '';
    public $options = [];
    public $main_ezf_id = "";
    public $action = "";
    public $randomize_id = "";
    public $user_create = "";
    public $user_update = "";
    public $group_name = "";
    public $module_id = "";
    
    /**
     * @param string $module_id target id
     * @return $this the query object itself
     */
    public function moduleId($module_id = '') {
        $this->module_id = $module_id;
        return $this;
    }

    /**
     * @param string $reloadDiv target id
     * @return $this the query object itself
     */
    public function reloadDiv($reloadDiv = '') {
        $this->reloadDiv = $reloadDiv;
        return $this;
    }

    /**
     * @param string $main_ezf_id ezf id
     * @return $this the query object itself
     */
    public function main_ezf_id($main_ezf_id) {
        $this->main_ezf_id = $main_ezf_id;
        return $this;
    }

  
    /**
     * @param string $cate_ezf_id ezf id
     * @return $this the query object itself
     */
    public function action($action) {
        $this->action = $action;
        return $this;
    }

    /**
     * @param int $node_id pageSize
     * @return $this the query object itself
     */
    public function widget_id($widget_id) {
        $this->widget_id = $widget_id;
        return $this;
    }
    
    /**
     * @param int $node_id pageSize
     * @return $this the query object itself
     */
    public function randomize_id($randomize_id) {
        $this->randomize_id = $randomize_id;
        return $this;
    }
    
    /**
     * @param int $options pageSize
     * @return $this the query object itself
     */
    public function options($options) {
        $this->options = $options;
        return $this;
    }
    
    /**
     * @param string $user_create pageSize
     * @return $this the query object itself
     */
    public function user_create($user_create) {
        $this->user_create = $user_create;
        return $this;
    }
    
    /**
     * @param string $user_update pageSize
     * @return $this the query object itself
     */
    public function user_update($user_update) {
        $this->user_update = $user_update;
        return $this;
    }


    /**
     * @inheritdoc
     * @return ScheduleBuilder the newly created [[ScheduleBuilder]] instance.
     */
    public static function ui() {
        return Yii::createObject(ScheduleBuilder::className()); //, [get_called_class()]
    }

    public function buildSchedule() {
        if ($this->reloadDiv == '') {
            $this->reloadDiv = 'div-' . Yii::$app->uniqueId;
        }
        
        if($this->action == ''){
            $this->action = '/subjects/subject-management/main-schedule';
        }

        $view = \Yii::$app->getView();

        $url = Url::to([$this->action,
                    'reloadDiv' => $this->reloadDiv,
                    'widget_id' => $this->widget_id,
                    'main_ezf_id' => $this->main_ezf_id,
                    'randomize_id' => $this->randomize_id,
                    'options'=> $this->options,
                    'user_create'=>$this->user_create,
                    'user_update'=>$this->user_update,
                    'module_id'=>$this->module_id,
        ]);
        $options = [];

        if (is_array($options)) {
            $options['action'] = 'schedule';
            $options["id"] = $this->reloadDiv;
            $options['data-url'] = $url;
        }

        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class="sdloader"><i class="sdloader-icon"></i></div>', $options);
    }
    

}
