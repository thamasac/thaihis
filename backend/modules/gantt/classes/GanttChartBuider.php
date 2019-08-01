<?php

namespace backend\modules\gantt\classes;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of GanttChartBuider
 *
 */
class GanttChartBuider extends Component {

    //put your code here
    public $project_ezf_id = '';
    public $cate_ezf_id = '';
    public $activity_ezf_id = '';
    public $response_ezf_id = '';
    public $reloadDiv = '';
    public $options = [];
    public $widget_id = "";
    public $project_id = "";
    public $action = "";
    public $schedule_id = "";
    public $check_type = "";
    public $start_date = "";
    public $finish_date = "";
    public $progress = "";
    public $project_name = "";
    public $cate_name = "";
    public $task_name = "";
    public $skinName = "";
    public $module_id = "";
    public $calendar_widget_id = "";
    public $tab = "";
    public $other_ezforms = [];

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
    public function calendarWidget($calendar_widget_id = '') {
        $this->calendar_widget_id = $calendar_widget_id;
        return $this;
    }

    /**
     * @param string $ezf_id ezf id
     * @return $this the query object itself
     */
    public function activity_ezf_id($activity_ezf_id) {
        $this->activity_ezf_id = $activity_ezf_id;
        return $this;
    }

    /**
     * @param string $response_ezf_id  response_ezf_id
     * @return $this the query object itself
     */
    public function response_ezf_id($response_ezf_id) {
        $this->response_ezf_id = $response_ezf_id;
        return $this;
    }

    /**
     * @param string $project_ezf_id project ezf id
     * @return $this the query object itself
     */
    public function project_ezf_id($project_ezf_id) {
        $this->project_ezf_id = $project_ezf_id;
        return $this;
    }

    /**
     * @param string $cate_ezf_id ezf id
     * @return $this the query object itself
     */
    public function cate_ezf_id($cate_ezf_id) {
        $this->cate_ezf_id = $cate_ezf_id;
        return $this;
    }

    /**
     * @param string $check_type ezf id
     * @return $this the query object itself
     */
    public function check_type($check_type) {
        $this->check_type = $check_type;
        return $this;
    }

    /**
     * @param string $start_date ezf id
     * @return $this the query object itself
     */
    public function start_date($start_date) {
        $this->start_date = $start_date;
        return $this;
    }

    /**
     * @param string $project_id ezf id
     * @return $this the query object itself
     */
    public function project_id($project_id) {
        $this->project_id = $project_id;
        return $this;
    }

    /**
     * @param string $finish_date ezf id
     * @return $this the query object itself
     */
    public function finish_date($finish_date) {
        $this->finish_date = $finish_date;
        return $this;
    }

    /**
     * @param string $progress ezf id
     * @return $this the query object itself
     */
    public function progress($progress) {
        $this->progress = $progress;
        return $this;
    }

    /**
     * @param string $project_name ezf id
     * @return $this the query object itself
     */
    public function project_name($project_name) {
        $this->project_name = $project_name;
        return $this;
    }

    /**
     * @param string $cate_name ezf id
     * @return $this the query object itself
     */
    public function cate_name($cate_name) {
        $this->cate_name = $cate_name;
        return $this;
    }

    /**
     * @param string $task_name ezf id
     * @return $this the query object itself
     */
    public function task_name($task_name) {
        $this->task_name = $task_name;
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
     * @param int $skinName columnStatus
     * @return $this the query object itself
     */
    public function skinName($skinName) {
        $this->skinName = $skinName;
        return $this;
    }

    /**
     * @param int $other_ezforms other_ezforms
     * @return $this the query object itself
     */
    public function other_ezforms($other_ezforms) {
        $this->other_ezforms = $other_ezforms;
        return $this;
    }

    /**
     * @inheritdoc
     * @return GanttChartBuider the newly created [[GanttChartBuider]] instance.
     */
    public static function ui() {
        return Yii::createObject(GanttChartBuider::className()); //, [get_called_class()]
    }

    public function buildGanttChart() {
        if ($this->reloadDiv == '') {
            $this->reloadDiv = 'div-' . Yii::$app->uniqueId;
        }

        $view = \Yii::$app->getView();

        $url = Url::to(['/gantt/gantt/' . $this->action,
                    'activity_ezf_id' => $this->activity_ezf_id,
                    'response_ezf_id' => $this->response_ezf_id,
                    'project_ezf_id' => $this->project_ezf_id,
                    'cate_ezf_id' => $this->cate_ezf_id,
                    'check_type' => $this->check_type,
                    'start_date' => $this->start_date,
                    'finish_date' => $this->finish_date,
                    'progress' => $this->progress,
                    'project_name' => $this->project_name,
                    'cate_name' => $this->cate_name,
                    'task_name' => $this->task_name,
                    'reloadDiv' => $this->reloadDiv,
                    'project_id' => $this->project_id,
                    'widget_id' => $this->widget_id,
                    'schedule_widget_id' => $this->schedule_id,
                    'module_id' => $this->module_id,
                    'skin_name' => $this->skinName,
                    'other_ezforms' => $this->other_ezforms,
                    'calendar_widget_id' => $this->calendar_widget_id,
                    'pms_tab' => $this->tab,
        ]);
        $url2 = Url::to(['/gantt/gantt/gantt-main-task',
                    'activity_ezf_id' => $this->activity_ezf_id,
                    'response_ezf_id' => $this->response_ezf_id,
                    'project_ezf_id' => $this->project_ezf_id,
                    'cate_ezf_id' => $this->cate_ezf_id,
                    'check_type' => $this->check_type,
                    'start_date' => $this->start_date,
                    'finish_date' => $this->finish_date,
                    'progress' => $this->progress,
                    'project_name' => $this->project_name,
                    'cate_name' => $this->cate_name,
                    'task_name' => $this->task_name,
                    'reloadDiv' => $this->reloadDiv,
                    'project_id' => $this->project_id,
                    'widget_id' => $this->widget_id,
                    'schedule_widget_id' => $this->schedule_id,
                    'module_id' => $this->module_id,
                    'skin_name' => $this->skinName,
                    'other_ezforms' => $this->other_ezforms,
                    'calendar_widget_id' => $this->calendar_widget_id,
                    'pms_tab' => $this->tab,
        ]);
        $options = [];

        if ($this->check_type == '1') {
            $options['action'] = 'gantt-main-task';
            $options["id"] = $this->reloadDiv;
            $options['data-url'] = $url2;
        }else{
            $options['action'] = $this->action;
            $options["id"] = $this->reloadDiv;
            $options['data-url'] = $url;
        }



        $view->registerJs("
            onRender('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class="sdloader"><i class="sdloader-icon"></i></div>', $options);
    }

    public function buildGanttProject($action) {
        $this->action = $action;
        return $this->buildGanttChart();
    }

    public function buildGanttSchedule() {
        $this->action = "index";
        return $this->buildGanttChart();
    }

}
