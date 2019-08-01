<?php

namespace backend\modules\subjects\classes;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of FinancialBuilder
 *
 */
class FinancialBuilder extends Component {

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
    public $status = "";
    public $maintab = "";
    public $subtab = "";

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
     * @param string $status status
     * @return $this the query object itself
     */
    public function status($status) {
        $this->status = $status;
        return $this;
    }

    /**
     * @param string $maintab maintab
     * @return $this the query object itself
     */
    public function maintab($maintab) {
        $this->maintab = $maintab;
        return $this;
    }

    /**
     * @param string $status subtab
     * @return $this the query object itself
     */
    public function subtab($subtab) {
        $this->subtab = $subtab;
        return $this;
    }

    /**
     * @inheritdoc
     * @return FinancialBuilder the newly created [[FinancialBuilder]] instance.
     */
    public static function ui() {
        return Yii::createObject(FinancialBuilder::className()); //, [get_called_class()]
    }

    public function buildFinancial() {
        if ($this->reloadDiv == '') {
            $this->reloadDiv = 'div-' . Yii::$app->uniqueId;
        }

        $view = \Yii::$app->getView();
        $url = Url::to(['/subjects/subject-management/main-financial',
                    'reloadDiv' => $this->reloadDiv,
                    'schedule_id' => $this->schedule_id,
                    'widget_id' => $this->widget_id,
                    'module_id' => $this->module_id,
                    'status' => $this->status,
                    'maintab' => $this->maintab,
                    'subtab' => $this->subtab,
                    'options' => $this->options,
                    'user_create' => $this->user_create,
                    'user_update' => $this->user_update,
        ]);
        $options = [];

        if (is_array($options)) {
            $options['action'] = 'financial';
            $options["id"] = $this->reloadDiv;
            $options['data-url'] = $url;
        }

        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class="sdloader "><i class="sdloader-icon"></i></div>', $options);
    }

}
