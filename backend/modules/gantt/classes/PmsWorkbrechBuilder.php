<?php

namespace backend\modules\gantt\classes;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of PmsWorkbrechBuilder
 *
 */
class PmsWorkbrechBuilder extends Component {

    public $reloadDiv = '';
    public $options = [];
    public $widget_id = "";
    public $module_id = "";
    public $pms_widget_id = "";
    public $tab = "";
    
    /**
     * @param string $module_id target id
     * @return $this the query object itself
     */
    public function moduleId($module_id = '') {
        $this->module_id = $module_id;
        return $this;
    }
    
    /**
     * @param string $tab target tab
     * @return $this the query object itself
     */
    public function tab($tab = '') {
        $this->tab = $tab;
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
     * @param string $calendar_widget_id calendar_widget_id id
     * @return $this the query object itself
     */
    public function pmsWidget($pms_widget_id = '') {
        $this->pms_widget_id = $pms_widget_id;
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
    public function widgetId($widget_id) {
        $this->widget_id = $widget_id;
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
    
    /**
     * @inheritdoc
     * @return PmsWorkbrechBuilder the newly created [[PmsWorkbrechBuilder]] instance.
     */
    public static function ui() {
        return Yii::createObject(PmsWorkbrechBuilder::className()); //, [get_called_class()]
    }

    public function buildPmsWorkbrench() {
        if ($this->reloadDiv == '') {
            $this->reloadDiv = 'div-' . Yii::$app->uniqueId;
        }

        $view = \Yii::$app->getView();

        $url = Url::to(['/gantt/pms-workbrench/index',
                    'reloadDiv' => $this->reloadDiv,
                    'widget_id' => $this->widget_id,
                    'module_id' => $this->module_id,
                    'pms_widget_id' => $this->pms_widget_id,
        ]);
       
        $options = [];

        if (is_array($options)) {
            $options['action'] ='index';
            $options["id"] = $this->reloadDiv;
            $options['data-url'] = $url;
        }

        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class="sdloader"><i class="sdloader-icon"></i></div>', $options);
    }

}
