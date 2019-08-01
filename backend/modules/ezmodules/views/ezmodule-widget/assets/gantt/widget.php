<?php

// start widget builder
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use appxq\sdii\widgets\ModalForm;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfQuery;

$this->registerJsFile(
        '@web/js-gantt/dhtmlxgantt.js?48', ['depends' => [\yii\web\JqueryAsset::className()]]
);

$this->registerJsFile(
        '@web/js-gantt/api.js?3', ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
        '@web/js-gantt/dhtmlxgantt_marker.js?8', ['depends' => [\yii\web\JqueryAsset::className()]]
);

$this->registerJsFile(
        '@web/js-gantt/grid_struct.js?1', ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
        '@web/js-gantt/fit_task_text.js?2', ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
        '@web/js-gantt/wbs_calc.js?2', ['depends' => [\yii\web\JqueryAsset::className()]]
);

//$this->registerJsFile(
//        '@web/js-gantt/dhtmlxgantt_critical_path.js?8', ['depends' => [\yii\web\JqueryAsset::className()]]
//);
//
$this->registerJsFile(
        '@web/js-gantt/dhtmlxgantt_smart_rendering.js?8', ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
        '@web/js-gantt/dhtmlxgantt_tooltip.js?8', ['depends' => [\yii\web\JqueryAsset::className()]]
);
//$this->registerJsFile(
//        '@web/js-gantt/dhtmlxgantt_undo.js?8', ['depends' => [\yii\web\JqueryAsset::className()]]
//);
$this->registerJsFile(
        '@web/js-gantt/dhtmlxgantt_fullscreen.js?8', ['depends' => [\yii\web\JqueryAsset::className()]]
);
//$this->registerJsFile(
//        '@web/js-gantt/nav.js?8', ['depends' => [\yii\web\JqueryAsset::className()]]
//);
$this->registerJsFile(
        'https://cdn.ravenjs.com/3.10.0/raven.min.js?3', ['depends' => [\yii\web\JqueryAsset::className()]]
);

$this->registerCssFile("@web/js-gantt/gantt-custom-style.css");
$this->registerCssFile("@web/css/bootstrap-datetimepicker.css");

$this->registerJsFile('@web/js/moment.js');
$this->registerJsFile('@web/js/bootstrap-datetimepicker.js');
$this->registerJsFile('@web/js-vis/vis.js');
$this->registerCssFile("@web/js-vis/vis-timeline-graph2d.min.css");
$this->registerJsFile("@web/js-vis/html2canvas.js");
$this->registerCss('
    #timelineMS{
    margin: 2em 0 2em 0;
    border-radius: 3px;
    box-shadow: 0 2px 4px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12)!important;
}
    .vis-item.success  { background-color: #34fe1f;border-color:#00ff00; }
    .vis-item.warning  { background-color: #ff7a2c;border-color: #ff7702; }
    .vis-item.danger { background-color: #ff3232;border-color: #ff0000; }
    
     .vis-item.vis-selected {  background-color: #fff785;border-color:#ffc200; box-shadow: 0 0 15px #ffd700; }


');


/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
  'options' => $options,
  'widget_config' => $widget_config,
  'model' => $model,
  'modelOrigin'=>$modelOrigin,
  'menu' => $menu,
  'module' => $module,
  'addon' => $addon,
  'filter' => $filter,
  'reloadDiv' => $reloadDiv,
  'dataFilter' => $dataFilter,
  'modelFilter' => $modelFilter,
  'target' => $target,
 */

$schedule_widget_id = isset($options['widget_id']) ? $options['widget_id'] : '';
$widget_id = $widget_config->widget_id;
$skin_name = isset($options['skin_name']) ? $options['skin_name'] : '';
$module_id = isset($_GET['id']) ? $_GET['id'] : '';
$pms_tab = isset($_GET['pms_tab']) ? $_GET['pms_tab'] : '1';
$project_id = isset($_GET['pmsid']) ? $_GET['pmsid'] : '';


if ($skin_name == "default") {
    $this->registerCssFile("@web/js-gantt/dhtmlxgantt.css");
} else {
    $this->registerCssFile("@web/js-gantt/dhtmlxgantt_" . $skin_name . ".css");
}
?>
<?=

ModalForm::widget([
    'id' => 'modal-ezform-project',
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
]);
?>

<?=

ModalForm::widget([
    'id' => 'modal-ezform-gantt',
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
]);
?>
<?=

ModalForm::widget([
    'id' => 'modal-pms-calendar',
    'size' => 'modal-lg',
    'tabindexEnable' => false,
]);
?>

<?=

ModalForm::widget([
    'id' => 'modal-ezform-task',
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
]);
?>
<?=
ModalForm::widget([
    'id' => 'modal-ezform-config',
    'size' => 'modal-lg',
    'tabindexEnable'=>false,
]);
?>
<?php

$user_id = Yii::$app->user->id;
if (isset($options['check_type'])) {
    if ($options['check_type'] == '1') {
        //$projectForm = EzfQuery::getEzformOne($options['project_ezf_id']);
        //$projectList = \backend\modules\subjects\classes\SubjectManagementQuery::GetTableData($projectForm, 'project_sharing=2 OR (project_sharing=3 AND "' . $user_id . '" IN (assign_users)) OR user_create="' . $user_id . '"');
        $render = backend\modules\gantt\classes\GanttChartBuider::ui()
                ->widgetId($widget_id)
                ->check_type($options['check_type'])
                ->project_ezf_id($options['project_ezf_id'])
                ->project_id($project_id)
                ->cate_ezf_id($options['cate_ezf_id'])
                ->activity_ezf_id($options['activity_ezf_id'])
                ->response_ezf_id($options['response_ezf_id'])
                ->start_date(isset($options['start_date']) ? $options['start_date'] : null)
                ->progress(isset($options['progress']) ? $options['progress'] : null)
                ->finish_date(isset($options['finish_date']) ? $options['finish_date'] : null)
                ->project_name(isset($options['project_name']) ? $options['project_name'] : null)
                ->cate_name(isset($options['cate_name']) ? $options['cate_name'] : null)
                ->task_name(isset($options['task_name']) ? $options['task_name'] : null)
                ->scheduleId(isset($options['schedule_widget_id']) ? $options['schedule_widget_id'] : null)
                ->skinName($skin_name)
                ->other_ezforms(isset($options['other_ezforms']) ? $options['other_ezforms'] : null)
                ->calendarWidget(isset($options['calendar_widget_id']) ? $options['calendar_widget_id'] : null)
                ->reloadDiv("gantt-content")
                ->tab($pms_tab)
                ->moduleId($module_id);

        $params = [
            '/gantt/gantt/gantt-project',
            'widget_id' => $widget_id,
            'check_type' => isset($options['check_type']) ? $options['check_type'] : null,
            'schedule_id' => isset($options['schedule_widget_id']) ? $options['schedule_widget_id'] : null,
            'skin_name' => $skin_name,
            'project_ezf_id' => $options['project_ezf_id'],
            'cate_ezf_id' => $options['cate_ezf_id'],
            'activity_ezf_id' => $options['activity_ezf_id'],
            'response_ezf_id' => $options['response_ezf_id'],
            'project_name' => isset($options['project_name']) ? $options['project_name'] : null,
            'start_date' => isset($options['start_date']) ? $options['start_date'] : null,
            'finish_date' => isset($options['finish_date']) ? $options['finish_date'] : null,
            'progress' => isset($options['progress']) ? $options['progress'] : null,
            'cate_name' => isset($options['cate_name']) ? $options['cate_name'] : null,
            'task_name' => isset($options['task_name']) ? $options['task_name'] : null,
            'reloadDiv' => "gantt-content",
            'module_id' => $module_id,
            'pms_tab' => isset($pms_tab) ? $pms_tab : '1',
            'calendar_widget_id' => isset($options['calendar_widget_id']) ? $options['calendar_widget_id'] : null,
            'other_ezforms' => isset($options['other_ezforms']) ? $options['other_ezforms'] : null,
        ];
        $calendar_widget_id = isset($options['calendar_widget_id']) ? $options['calendar_widget_id'] : null;
        if ($project_id != '') {
            $url_onload = yii\helpers\Url::to(
                            $params
            );

            $now_date = isset($_GET['now_date']) ? $_GET['now_date'] : date('Y-m-d');
            $calenOption = null;
            if ($calendar_widget_id) {
                $calenWidget = SubjectManagementQuery::getWidgetById($calendar_widget_id);
                $calenOption = $calenWidget['options'];
                $calenOption = \appxq\sdii\utils\SDUtility::string2Array($calenOption);
            }

            $userid = \Yii::$app->user->id;
            if ($project_id == '') {
                $project_id = $_SESSION['project_id'];
            }
            if ($project_id != null)
                $_SESSION['project_id'] = $project_id;

            $values = [];
            $values['widget_id'] = $widget_id;
            $values['schedule_id'] = isset($options['schedule_widget_id']) ? $options['schedule_widget_id'] : null;
            $values['check_type'] = isset($options['check_type']) ? $options['check_type'] : null;
            $values['project_ezf_id'] = $options['project_ezf_id'];
            $values['cate_ezf_id'] = $options['cate_ezf_id'];
            $values['activity_ezf_id'] = $options['activity_ezf_id'];
            $values['response_ezf_id'] = $options['response_ezf_id'];
            $values['start_date'] = isset($options['start_date']) ? $options['start_date'] : null;
            $values['finish_date'] = isset($options['finish_date']) ? $options['finish_date'] : null;
            $values['skin_name'] = $skin_name;
            $values['project_id'] = $project_id;
            $values['reloadDiv'] = "gantt-content";

            echo "<div id='gantt-content' data-url='" . $url_onload . "'>";
            echo $this->render('../../../../../gantt/views/gantt/gantt-project', [
                'widget_id' => $widget_id,
                'check_type' => isset($options['check_type']) ? $options['check_type'] : null,
                'schedule_id' => isset($options['schedule_widget_id']) ? $options['schedule_widget_id'] : null,
                'skin_name' => $skin_name,
                'project_ezf_id' => $options['project_ezf_id'],
                'cate_ezf_id' => $options['cate_ezf_id'],
                'activity_ezf_id' => $options['activity_ezf_id'],
                'response_ezf_id' => $options['response_ezf_id'],
                'project_name' => isset($options['project_name']) ? $options['project_name'] : null,
                'start_date' => isset($options['start_date']) ? $options['start_date'] : null,
                'finish_date' => isset($options['finish_date']) ? $options['finish_date'] : null,
                'progress' => isset($options['progress']) ? $options['progress'] : null,
                'cate_name' => isset($options['cate_name']) ? $options['cate_name'] : null,
                'task_name' => isset($options['task_name']) ? $options['task_name'] : null,
                'reloadDiv' => "gantt-content",
                'module_id' => $module_id,
                'project_id' => $project_id,
                'pms_tab' => isset($pms_tab) ? $pms_tab : '1',
                'calendar_widget_id' => isset($options['calendar_widget_id']) ? $options['calendar_widget_id'] : null,
                'other_ezforms' => isset($options['other_ezforms']) ? $options['other_ezforms'] : null,
                'scale_unit' => isset($scale_unit) ? $scale_unit : 'day',
                'scale_step' => isset($scale_step) ? $scale_step : '1',
                'scale_filter' => isset($scale_filter) ? $scale_filter : null,
                'now_date' => date('Y-m-d'),
                'values' => $values,
                'calenOption' => $calenOption,
                'eventSources' => [],
            ]);
            echo "</div>";
        } else {

            $params[0] = '/gantt/gantt/gantt-main-task';
            $url_onload = yii\helpers\Url::to(
                            $params
            );

            $projectForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($options['project_ezf_id']);
            $user_role = \cpn\chanpan\classes\CNUser::getUserRoles();
            $manageRole = "";
            if ($user_role) {
                foreach ($user_role as $key => $val) {
                    $manageRole .= ' OR INSTR(manage_roles, "' . $val['id'] . '") > 0 ';
                }
            }
            $assignUser = ' OR (project_sharing=3 AND INSTR(assign_users, "' . $user_id . '") > 0 )';
            $manageUser = ' OR INSTR(manage_users, "' . $user_id . '") > 0 ';

            $pmsData = SubjectManagementQuery::GetTableData($projectForm, "pms_type={$pms_tab} AND (user_create='{$user_id}' OR project_sharing='2' {$assignUser} {$manageUser} )",'all',null,['column'=>'update_date','order'=>'desc']);
            
            $progress = null;
            $task_amt = null;
            $milestone_amt = null;
            $fms_amt = null;
            $shopping_amt = null;
        $approve_amt = null;
        $complete_amt = null;

            if ($pmsData) {
                foreach ($pmsData as $key => $val) {
                    $taskProgress = 0;
                    $taskAmt = 0;
                    $taskData = SubjectManagementQuery::GetTableDataNotEzform("pms_task_target", ' rstat NOT IN(0,3) AND target='.$val['id']);
                    foreach ($taskData as $key2 => $val2) {
                        if ($val2['priority'] == '2')
                            continue;
                        //$resData = SubjectManagementQuery::GetTableData($responseForm, ['target' => $val2['id']], 'one');
                        if($val2['progress'] && $val2['progress'] > 0)
                            $taskProgress += $val2['progress'];
                        
                        $taskAmt += 1;
                        if (!isset($task_amt[$val['id']]))
                            $task_amt[$val['id']] = 0;
                        if (!isset($milestone_amt[$val['id']]))
                            $milestone_amt[$val['id']] = 0;
                        if (!isset($fms_amt[$val['id']]))
                            $fms_amt[$val['id']] = 0;
                        if (!isset($shopping_amt [$val ['id']]))
                        $shopping_amt [$val ['id']] = 0;
                    if (!isset($complete_amt [$val ['id']]))
                        $complete_amt [$val ['id']] = 0;
                    if (!isset($approve_amt [$val ['id']]))
                        $approve_amt [$val ['id']] = 0;

                        $task_amt[$val['id']] += 1;
                        
                        if (isset($val2['task_type']) && $val2['task_type'] == 'milestone') {
                            $milestone_amt[$val['id']] += 1;
                        } else if (isset($val2['task_financial']) && $val2['task_financial'] == '1') {
                            $fms_amt[$val['id']] += 1;
                        }
                        
                        if ($val2 ['assign_user'] == '' && $val2 ['request_type'] == '1' && ($val2 ['credit_points'] == '' && $val2 ['reward_points'] == '')) {
                            $shopping_amt[$val ['id']] += 1;
                        } else if ($val2 ['approved'] == '' && ($val2 ['credit_points'] != '' || $val2 ['reward_points'] != '')) {
                            $approve_amt[$val ['id']] += 1;
                        } else if ($val2 ['assign_user'] == '' && $val2 ['approved'] == '1' && $val2 ['request_type'] == '1') {
                            $shopping_amt[$val ['id']] += 1;
                        }


                        if (isset($val2 ['task_status']) && $val2 ['task_status'] == '3') {
                            $complete_amt[$val ['id']] += 1;
                        }
                    }
                    if ($taskProgress > 0 )
                        $pmsData[$key]['progress'] = $taskProgress / $taskAmt;
                    else
                        $pmsData[$key]['progress'] = 0;
                }
            }
            
            $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels' => $pmsData,
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);

            $url_curr = \cpn\chanpan\classes\CNServerConfig::getDomainName();
            //$url_curr = "nccl2.work.ncrc.in.th";
            $url_curr_arr = explode('.', $url_curr);
            $url_curr_pre = isset($url_curr_arr[0]) ? $url_curr_arr[0] : '';
            $projectData = \backend\modules\subjects\classes\ReportQuery::getProjectData2($url_curr_pre);
            $pms_updateid = '';
            if (isset($projectData['id'])) {
                $checkUpdate = backend\modules\gantt\classes\GanttQuery::checkPmsUpdateLog($projectData['id']);
                if (!$checkUpdate) {
                    $pms_updateid = $projectData['id'];
                }
            }

            echo "<div id='gantt-content' data-url='" . $url_onload . "'>";

            echo $this->render('../../../../../gantt/views/gantt/gantt-maintask', [
                'widget_id' => $widget_id,
                'check_type' => isset($options['check_type']) ? $options['check_type'] : null,
                'schedule_id' => isset($options['schedule_widget_id']) ? $options['schedule_widget_id'] : null,
                'skin_name' => $skin_name,
                'project_ezf_id' => $options['project_ezf_id'],
                'cate_ezf_id' => $options['cate_ezf_id'],
                'activity_ezf_id' => $options['activity_ezf_id'],
                'response_ezf_id' => $options['response_ezf_id'],
                'project_name' => isset($options['project_name']) ? $options['project_name'] : null,
                'start_date' => isset($options['start_date']) ? $options['start_date'] : null,
                'finish_date' => isset($options['finish_date']) ? $options['finish_date'] : null,
                'progress' => isset($options['progress']) ? $options['progress'] : null,
                'cate_name' => isset($options['cate_name']) ? $options['cate_name'] : null,
                'task_name' => isset($options['task_name']) ? $options['task_name'] : null,
                'reloadDiv' => "gantt-content",
                'module_id' => $module_id,
                'pms_tab' => isset($pms_tab) ? $pms_tab : '1',
                'calendar_widget_id' => isset($options['calendar_widget_id']) ? $options['calendar_widget_id'] : null,
                'other_ezforms' => isset($options['other_ezforms']) ? $options['other_ezforms'] : null,
                'dataProvider' => $dataProvider,
                'task_amt' => $task_amt,
                'milestone_amt' => $milestone_amt,
                'fms_amt' => $fms_amt,
                'shopping_amt' => $shopping_amt,
                'complete_amt' => $complete_amt,
                'approve_amt' => $approve_amt,
                'pms_updateid' => $pms_updateid,
            ]);
            echo "</div>";
            //echo $render->buildGanttProject('gantt-main-task');
        }
    } else {
        echo backend\modules\gantt\classes\GanttChartBuider::ui()
                ->widgetId($widget_id)
                ->check_type($options['check_type'])
                ->project_ezf_id($options['project_ezf_id'])
                ->cate_ezf_id($options['cate_ezf_id'])
                ->scheduleId($schedule_widget_id)
                ->skinName($skin_name)
                ->reloadDiv("gantt-content")
                ->moduleId($module_id)
                ->buildGanttSchedule();
    }
} else {
    echo backend\modules\gantt\classes\GanttChartBuider::ui()
            ->widgetId($widget_id)
            ->check_type(isset($options['check_type']) ? $options['check_type'] : '')
            ->project_ezf_id(isset($options['project_ezf_id']) ? $options['project_ezf_id'] : '')
            ->cate_ezf_id(isset($options['cate_ezf_id']) ? $options['cate_ezf_id'] : '')
            ->scheduleId($schedule_widget_id)
            ->skinName($skin_name)
            ->reloadDiv("gantt-content")
            ->moduleId($module_id)
            ->buildGanttSchedule();
}
?>

<?php

\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    function onRender(url, div) {
        var content = $('#' + div);
        content.html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
        $.get(url, function (data) {
            content.html(data);
        })
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>