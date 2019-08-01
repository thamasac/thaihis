<?php

namespace backend\modules\subjects\classes;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of ProcedureBuilder
 *
 */
class ProcedureBuilder extends Component {

    //put your code here
    public $project_ezf_id = '';
    public $cate_ezf_id = '';
    public $activity_ezf_id = '';
    public $reloadDiv = '';
    public $options = [];
    public $widget_id = "";
    public $action = "";
    public $schedule_id = "";
    public $user_create = "";
    public $user_update = "";
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
     * @param string $ezf_id ezf id
     * @return $this the query object itself
     */
    public function widget_id($widget_id) {
        $this->widget_id = $widget_id;
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

    public function options($options) {
        $this->options = $options;
        return $this;
    }

    /**
     * @param int $column_status columnStatus
     * @return $this the query object itself
     */
    public function scheduleId($schedule_id) {
        $this->schedule_id = $schedule_id;
        return $this;
    }

    public function widgetConfig($widget_config) {
        $this->widget_config = $widget_config;
        return $this;
    }

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
     * @return ProcedureBuilder the newly created [[ProcedureBuilder]] instance.
     */
    public static function ui() {
        return Yii::createObject(ProcedureBuilder::className()); //, [get_called_class()]
    }

    public function buildProcedure() {
        if ($this->reloadDiv == '') {
            $this->reloadDiv = 'div-' . Yii::$app->uniqueId;
        }

        $view = \Yii::$app->getView();
        $url = Url::to(['/subjects/subject-management/main-procedure',
                    'reloadDiv' => $this->reloadDiv,
                    'schedule_id' => $this->schedule_id,
                    'widget_id' => $this->widget_id,
                    'options' => $this->options,
                    'user_create' => $this->user_create,
                    'user_update' => $this->user_update,
                    'module_id'=>$this->module_id,
        ]);
        $options = [];

        if (is_array($options)) {
            $options['action'] = 'main-procedure';
            $options["id"] = $this->reloadDiv;
            $options['data-url'] = $url;
        }

        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class="sdloader"><i class="sdloader-icon"></i></div>', $options);
    }

}
