
<?php

use appxq\sdii\widgets\ModalForm;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\modules\gantt\classes\GanttQuery;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use kartik\tabs\TabsX;
use backend\modules\ezforms2\classes\EzfHelper;

$customCss = ".red .gantt_cell, .odd.red .gantt_cell, .red .gantt_task_cell, .odd.red .gantt_task_cell { background-color: #FDE0E0; }";
$customCss .= ".green .gantt_cell, .odd.green .gantt_cell, .green .gantt_task_cell, .odd.green .gantt_task_cell { background-color: #BEE4BE; }";
$customCss .= ".yellow .gantt_cell, .odd.yellow .gantt_cell, .yellow .gantt_task_cell, .odd.yellow .gantt_task_cell { background-color: #FBF5A8; } .gray .gantt_cell, .odd.gray .gantt_cell, .gray .gantt_task_cell, .odd.gray .gantt_task_cell { background-color: #E6E6E6; }";
$customCss .= ".high { border: 2px solid #d96c49; color: #d96c49; background: #d96c49; }";
$customCss .= ".high .gantt_task_progress { background: #db2536; }";
$customCss .= ".medium { border: 2px solid #34c461; color: #34c461; background: #34c461; }";
$customCss .= ".medium .gantt_task_progress { background: #23964d; }";
$customCss .= ".low { border: 2px solid #6ba8e3; color: #6ba8e3; background: #6ba8e3; }";
$customCss .= ".low .gantt_task_progress { background: #547dab; } .status_line { background-color: #0ca30a; }";
$customCss .= ".deadline { position: absolute; -moz-box-sizing: border-box; color: white; background: rgb(77, 77, 255); margin-left: -11px; border-radius: 15px; width: 24px; font-size: 20px; text-align: center; z-index: 1; }";
$customCss .= ".overdue-indicator { width: 24px; margin-top: 5px; height: 24px; -moz-box-sizing: border-box; box-sizing: border-box; border-radius: 17px; color: rgb(0, 230, 0); line-height: 25px; text-align: center; font-size: 24px; }";
$customCss .= ".overdue-indicator-danger { width: 24px; margin-top: 5px; height: 24px; -moz-box-sizing: border-box; box-sizing: border-box; border-radius: 17px; color: red; line-height: 25px; text-align: center; font-size: 24px; }";
$customCss .= ".overdue-indicator-warning { width: 24px; margin-top: 5px; height: 24px; -moz-box-sizing: border-box; box-sizing: border-box; border-radius: 17px; color: #FFCC33; line-height: 25px; text-align: center; font-size: 24px; } .custom_progress { display: inline-block; vertical-align: top; text-align: center; height: 100%; }";
$customCss .= ".custom_progress.warning { background-color: #ffd11a; }";
$customCss .= ".custom_progress.success { background-color: #00e600; }";
$customCss .= ".custom_progress.info { background-color: #00bfff; } .custom_progress.danger { background-color: #d9534f; } .custom_progress.primary { background-color: #2e6da4; }";

$href = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
$href .= "/ezmodules/ezmodule/view?id=" . $module_id;

$fields = backend\modules\ezforms2\classes\EzfQuery::getFieldByName($project_ezf_id, 'pms_type');
$pms_type = \appxq\sdii\utils\SDUtility::string2Array($fields['ezf_field_data']);
if (!isset($tab))
    $tab = '1';
$items = [];

$items[] = [
    'label' => '<i class="fa fa-bars" aria-hidden="true"></i>' . " Gantt Chart",
    'headerOptions' => ['style' => 'font-weight:bold', 'data-value' => 1, 'id' => 'tab_pms_gantt', 'class' => 'tab_pms_gantt'],
    'active' => true,
];

$items[] = [
    'label' => '<i class="fa fa-calendar" aria-hidden="true"></i>' . " Calendar",
    'headerOptions' => ['style' => 'font-weight:bold', 'data-value' => 2, 'id' => 'tab_pms_calendar', 'class' => 'tab_pms_calendar'],
    'active' => false,
];

$items[] = [
    'label' => '<i class="fa fa-line-chart" aria-hidden="true"></i>' . " Report",
    'headerOptions' => ['style' => 'font-weight:bold', 'data-value' => 3, 'id' => 'tab_pms_report', 'class' => 'tab_pms_report'],
    'active' => false,
];


echo TabsX::widget([
    'position' => TabsX::POS_ABOVE,
    'align' => TabsX::ALIGN_LEFT,
    'encodeLabels' => false,
    //'enableStickyTabs' => true,
    'items' => $items,
]);

echo ModalForm::widget([
    'id' => 'modal-copy-share',
    'size' => 'modal-lg'
]);
?>

<?php
if ($project_id == null)
    $project_id = $_COOKIE['project_id'];

$unit = "day";
$step = '1';
if ($scale_unit == '0') {
    $unit = "day";
} elseif ($scale_unit == '1') {
    $unit = "week";
} elseif ($scale_unit == '2') {
    $unit = "month";
}

if (isset($scale_step) || $scale_step > 0)
    $step = $scale_step;

$gantt_type = $values['check_type'];

$itemUnit[0]['id'] = '0';
$itemUnit[0]['unit_name'] = Yii::t('gantt', "Day");

$itemUnit[1]['id'] = '1';
$itemUnit[1]['unit_name'] = Yii::t('gantt', "Week");

$itemUnit[2]['id'] = '2';
$itemUnit[2]['unit_name'] = Yii::t('gantt', "Month");

$itemUnit[3]['id'] = '3';
$itemUnit[3]['unit_name'] = Yii::t('gantt', "Custom");

$itemFilter[0]['id'] = '0';
$itemFilter[0]['filter_name'] = Yii::t('gantt', "All Tasks");

$itemFilter[1]['id'] = '1';
$itemFilter[1]['filter_name'] = Yii::t('gantt', "Completed Tasks");

$itemFilter[2]['id'] = '2';
$itemFilter[2]['filter_name'] = Yii::t('gantt', "Uncompleted Tasks");

$itemFilter[3]['id'] = '3';
$itemFilter[3]['filter_name'] = Yii::t('gantt', "Assigned to me");

$itemFilter[4]['id'] = '4';
$itemFilter[4]['filter_name'] = Yii::t('gantt', "Assigned to others");

$itemFilter[4]['id'] = '4';
$itemFilter[4]['filter_name'] = Yii::t('gantt', "Assign to other user");

$itemFilter[5]['id'] = '5';
$itemFilter[5]['filter_name'] = Yii::t('gantt', "Unassigned");

$user_id = Yii::$app->user->id;
$user_role = \cpn\chanpan\classes\CNUser::getUserRoles();
$manageRole = "";
if ($user_role) {
    foreach ($user_role as $key => $val) {
        $manageRole .= ' OR INSTR(RIGHT(LEFT(manage_roles,LENGTH(manage_roles)-1), LENGTH(LEFT(manage_roles,LENGTH(manage_roles)-1))-1), "' . $val['id'] . '") > 0 ';
    }
}

$assignUser = ' OR (project_sharing=3 AND INSTR(RIGHT(LEFT(assign_users,LENGTH(assign_users)-1), LENGTH(LEFT(assign_users,LENGTH(assign_users)-1))-1), "' . $user_id . '") > 0 )';
$manageUser = ' OR INSTR(RIGHT(LEFT(manage_users,LENGTH(manage_users)-1), LENGTH(LEFT(manage_users,LENGTH(manage_users)-1))-1), "' . $user_id . '") > 0 ';
$projectForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($project_ezf_id);
$projectData = \backend\modules\subjects\classes\SubjectManagementQuery::GetTableData($projectForm, 'id="' . $project_id . '"', 'one');
?>

<div  class="row">
    <div class="col-md-12" style="padding-top: 20px;">
        <div class="pull-left" style="font-size: 18px;">
            <a id="back-allmaintask2" data-url="<?=
            Url::to(['/gantt/gantt/gantt-main-task',
                'widget_id' => $widget_id,
                'schedule_id' => $schedule_id,
                'skin_name' => $skin_name,
                'check_type' => $check_type,
                'project_ezf_id' => $project_ezf_id,
                'cate_ezf_id' => $cate_ezf_id,
                'activity_ezf_id' => $activity_ezf_id,
                'response_ezf_id' => $response_ezf_id,
                'start_date' => $start_date,
                'finish_date' => $finish_date,
                'project_name' => $project_name,
                'progress' => $progress,
                'cate_name' => $cate_name,
                'task_name' => $task_name,
                'reloadDiv' => $reloadDiv,
                'module_id' => $module_id,
                'tab' => $tab,
                'pms_type' => $pms_type,
                'other_ezforms' => $other_ezforms,
            ])
            ?>" href="/ezmodules/ezmodule/view?id=<?= $module_id ?>&pms_tab=<?= $tab ?>">All Main Tasks</a> <label><i class="fa fa-chevron-right" aria-hidden="true"></i></label> <label><?= $projectData['project_name'] ?></label>
        </div>
        <div class="dropdown pull-right" style="margin-right: 5px;">
            <button class="btn btn-warning  dropdown-toggle" type="button" data-toggle="dropdown"><?= Yii::t('subject', 'Export PMS') ?>
                <span class="caret"></span></button>
            <ul class="dropdown-menu">
                <li><a href="#" onclick='gantt.exportToMSProject({
                            name: "mypms-export-msproject.xml",
                            header: "<style><?= $customCss ?></style><h1>Project Management System</h1>",
                            footer: "<h4>NCRC Thailand</h4>",
                            skip_circular_links: false,
                            locale: "en",
                            server: "<?= Yii::getAlias('@web') ?>",
                            raw: true
                        })'><i class='fa fa-file'></i> Export to MS Project </a></li>
                <li><a href="#" onclick='gantt.exportToExcel({
                            name: "mypms-export-excel.xlsx",
                            header: "<style><?= $customCss ?></style><h1>Project Management System</h1>",
                            footer: "<h4>NCRC Thailand</h4>",
                            locale: "en",
                            server: "<?= Yii::getAlias('@web') ?>",
                            raw: true
                        })' ><i class='fa fa-file-excel-o '></i> Export to Excel </a></li>
                <li><a href="#" onclick='gantt.exportToPDF({
                            name: "mypms-export-pdf.pdf",
                            header: "<style><?= $customCss ?></style><h1>Project Management System</h1>",
                            footer: "<h4>NCRC Thailand</h4>",
                            locale: "en",
                            server: "<?= Yii::getAlias('@web') ?>",
                            raw: true
                        });'><i class="fa fa-file-pdf-o"></i> Export to PDF</a></li>
                <li><a href="#" onclick='gantt.exportToPNG({
                            name: "mypms-export-pdf.png",
                            header: "<style><?= $customCss ?></style><h1>Project Management System</h1>",
                            footer: "<h4>NCRC Thailand</h4>",
                            locale: "en",
                            server: "<?= Yii::getAlias('@web') ?>",
                            raw: true
                        })'><i class="fa fa-file-image-o"></i> Export to PNG </a></li>
            </ul>
        </div>
    </div>
</div>
<br/>

<input type="hidden" value="<?= $widget_id ?>" name="widget_id" id="data-widget_id">
<input type="hidden" value="<?= $schedule_id ?>" name="schedule_id" id="data-schedule_id">
<div class="gantt-default-index" id="filter-gantt-chart">      
    <section class="content">     
<!--        <button class="btn btn-warning" type="button" onclick='gantt.exportToMSProject({skip_circular_links: false})' style='margin:20px;'><i class='fa fa-file'></i> Export to MS Project </button>
        <button class="btn btn-success" type="button" onclick='gantt.exportToExcel()' style='margin:20px;'><i class='fa fa-file-excel-o '></i> Export to Excel </button>
        <button class="btn btn-primary" type="button" onclick='gantt.exportToPDF()' style='margin:20px;'><i class="fa fa-file-pdf-o"></i> Export to PDF </button>
        <button class="btn btn-info" type="button" onclick='gantt.exportToPNG()'><i class="fa fa-file-image-o"></i> Export to PNG </button>-->

        <div class="row ">
            <div class="col-md-2 pull-left">
                <div>
                    <?= Html::label("Scale Unit", 'scale_unit') ?>
                    <?= Html::dropDownList('scale_unit', isset($scale_unit) ? $scale_unit : '0', ArrayHelper::map($itemUnit, 'id', 'unit_name'), ['class' => 'form-control scale_unit_selector ']) ?>
                </div>
                <div class="" style="display: none;">
                    <?= Html::label(Yii::t('subjects', "Scale Interval"), 'scale_step') ?>
                    <?= Html::input('number', 'scale_step', $step, ['class' => 'form-control scale_step_selector pull-right']) ?>
                </div>

            </div>

            <div class="col-md-3 pull-left">
                <?= Html::label(Yii::t('subjects', "Filter Task"), 'filter_name') ?>
                <?= Html::dropDownList('scale_unit', isset($scale_filter) ? $scale_filter : '0', ArrayHelper::map($itemFilter, 'id', 'filter_name'), ['class' => 'form-control scale_filter_selector pull-left']) ?>

            </div>
            <?php
            $userList = GanttQuery::getAllUser();
                    
            ?>
            <div class="col-md-3 pull-left" id="box_all_users" style="display:none;">
                <?= Html::label(Yii::t('gantt', "Users"), 'user_select') ?>
                <?php
                $attrname_alluser = 'options[group_name]';
                $value_alluser = '';
                echo kartik\select2\Select2::widget([
                    'name' => $attrname_alluser,
                    'value' => $value_alluser,
                    'options' => ['placeholder' => Yii::t('gantt', 'User select...'), 'id' => 'config_all_user'],
                    'data' => ArrayHelper::map($userList, 'user_id', 'name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>

            </div>

            <div class="clearfix"></div>
            <br/>
        </div>


    </section>   
</div>
<div class="clearfix"></div>

<div id="display-gantt"  data-url="<?=
Url::to([
    '/gantt/gantt/gen-gantt',
    'widget_id' => $widget_id,
    'schedule_id' => $schedule_id,
    'skin_name' => $skin_name,
    'project_ezf_id' => $project_ezf_id,
    'cate_ezf_id' => $cate_ezf_id,
    'activity_ezf_id' => $activity_ezf_id,
    'response_ezf_id' => $response_ezf_id,
    'start_date' => $start_date,
    'finish_date' => $finish_date,
    'progress' => $progress,
    'project_name' => $project_name,
    'cate_name' => $cate_name,
    'task_name' => $task_name,
    'reloadDiv' => 'gantt-content',
    'values' => $values,
    'scale_unit' => $scale_unit,
    'scale_step' => $scale_step,
    'scale_filter' => $scale_filter,
    'module_id' => $module_id,
    'project_id' => $project_id,
    'tab' => $tab,
    'other_ezforms' => $other_ezforms,
])
?>">

</div>
<div id="display-calendar-content" data-url="<?=
yii\helpers\Url::to([
    '/gantt/pms-calendar/index2',
    'modal' => "modal-ezform-gantt",
    'reloadDiv' => $reloadDiv,
    'module_id' => $module_id,
    'project_id' => $project_id,
    'now_date' => $now_date,
    'forms' => $calenOption['forms'],
    'eventSources' => $eventSources,
    'defaultView' => $calenOption['defaultView'],
    'view_menu' => $calenOption['view_menu'],
    'tab' => $tab,
    'response_ezf_id' => $calenOption['response_ezf_id'],
    'task_ezf_id' => isset($calenOption['task_ezf_id']) ? $calenOption['task_ezf_id'] : null,
    'subtask_ezf_id' => $calenOption['subtask_ezf_id'],
    'maintask_ezf_id' => $calenOption['maintask_ezf_id'],
    'response_actual_field' => isset($calenOption['response_actual_field']) ? $calenOption['response_actual_field'] : null,
    'other_ezforms' => $other_ezforms,
])
?>">

</div>
<div id="display-report-content" data-url="<?=
     yii\helpers\Url::to([
         '/gantt/pms-report/index',
         'modal' => "modal-ezform-gantt",
         'reloadDiv' => $reloadDiv,
         'module_id' => $module_id,
         'project_id' => $project_id,
         'now_date' => $now_date,
         'forms' => $calenOption['forms'],
         'eventSources' => $eventSources,
         'defaultView' => $calenOption['defaultView'],
         'view_menu' => $calenOption['view_menu'],
         'tab' => $tab,
         'response_ezf_id' => $response_ezf_id,
         'task_ezf_id' => $activity_ezf_id,
         'subtask_ezf_id' => $cate_ezf_id,
         'maintask_ezf_id' => $project_ezf_id,
         'response_actual_field' => isset($calenOption['response_actual_field']) ? $calenOption['response_actual_field'] : null,
         'start_date' => $start_date,
         'finish_date' => $finish_date,
         'progress' => 'progress',
         'actual_field' => 'actual_date',
         'project_name' => $project_name,
         'cate_name' => $cate_name,
         'task_name' => $task_name,
     ])
     ?>">

</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    var checkEvent = 0;
    $(function () {
        var project_id = '<?= $project_id ?>';
        var url = $('#display-gantt').attr('data-url');

        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            data: {},
            cache: true,
            success: function (result) {
                $('#display-gantt').empty();
                $('#display-gantt').html(result);
            }
        });

    });

    $('#tab_pms_calendar').on('click', function () {
        var project_id = '<?= $project_id ?>';
        gantt.clearAll();
        var div = $('#display-calendar-content');
        $('.gantt_grid_top').css('display', 'none');
        $('#filter-gantt-chart').css('display', 'none');
        $('#display-gantt').empty();
        $('#display-report-content').empty();
        var url = $('#display-calendar-content').attr('data-url');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');

        $.get(url, {}, function (result) {
            div.empty();
            div.html(result);
        })
    });

    $('#tab_pms_gantt').on('click', function () {
        var project_id = '<?= $project_id ?>';

        var div = $('#display-gantt');
        $('#display-calendar-content').empty();
        $('#display-report-content').empty();
        $('#filter-gantt-chart').css('display', 'block');
        var url = $('#display-gantt').attr('data-url');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('.gantt_grid_top').css('display', 'block');
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            data: {},
            cache: false,
            success: function (result) {
                gantt.clearAll();
                div.empty();
                div.html(result);
            }
        });
    });

    $('#tab_pms_report').on('click', function () {
        var project_id = '<?= $project_id ?>';

        var div = $('#display-report-content');
        $('#display-calendar-content').empty();
        $('#display-gantt').empty();
        $('#filter-gantt-chart').css('display', 'none');
        var url = $('#display-report-content').attr('data-url');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('.gantt_grid_top').css('display', 'block');
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            data: {},
            cache: false,
            success: function (result) {
                div.empty();
                div.html(result);
            }
        });
    });

    $('#back-allmaintask').click(function () {
        var url = $(this).attr('data-url');
        window.history.replaceState({}, $(this).html(), '<?= $href ?>');
        gantt.clearAll();
        $('#gantt-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            data: {},
            cache: false,
            success: function (result) {
                $('#gantt-content').empty();
                $('#gantt-content').html(result);
            }
        });
    });

    $('.tab_pms_type').on('click', function () {
        var tab = $(this).attr('data-value');
        var project_id = $.cookie("project_id");
        getReloadDiv($('#gantt-content').attr('data-url') + '&project_id=' + project_id + '&tab=' + tab, 'gantt-content');
    });

//    $('#modal-ezform-project').on('hidden.bs.modal', function (e) {
//        var project_id = $.cookie("project_id");
//        var tab = '<?= $tab ?>';
//        getReloadDiv($('#gantt-content').attr('data-url') + '&project_id=' + project_id + '&tab=' + tab, 'gantt-content');
//    });

//    $('#modal-ezform-gantt').on('hidden.bs.modal', function (e) {
//        var project_id = $.cookie("project_id");
//        var tab = '<?= $tab ?>';
//        if (checkEvent == 1)
//            return;

    //getReloadDiv($('#display-gantt').attr('data-url') + '&project_id=' + project_id + '&tab=' + tab, 'display-gantt');
//    });

    $('#modal-ezform-gantt').on('hidden.bs.modal', function (e) {
        gantt.clearAll();
        onInitGantt();
    });

    $('#modal-ezform-config').on('hidden.bs.modal', function (e) {
        gantt.clearAll();
        onInitGantt();
    });


    $('#config_main_task').on('change', function () {
        var project_id = $('#config_main_task').val();
        var tab = '<?= $tab ?>';
        document.cookie = 'project_id=' + project_id + '; path=/';
        document.cookie = 'project_id=' + project_id + '; path=/ezmodules/ezmodule';
        getReloadDiv($('#display-gantt').attr('data-url') + '&project_id=' + project_id + '&tab=' + tab, 'display-gantt');
    });
    $(document).on('click', '.btn-delete-project', function () {
        if (checkEvent == 1)
            return;
        checkEvent = 1;
        var data_id = $(this).attr('data-id');
        var ezf_id = $(this).attr('data-ezf_id');

        yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete this item?') ?>', function () {

            $.post(
                    '/ezforms2/ezform-data/delete?ezf_id=' + ezf_id + '&dataid=' + data_id, {data_id: data_id, ezf_id: ezf_id}
            ).done(function (result) {
                checkEvent = 0;
                if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
                    document.cookie = 'project_id=' + '' + '; path=/';
                    document.cookie = 'project_id=' + '' + '; path=/ezmodules/ezmodule';
                    getReloadDiv($('#gantt-content').attr('data-url') + '&project_id=<?= $project_id ?>', 'gantt-content');
                } else {

                }
            }).fail(function () {
                checkEvent = 0;
            });
        }, function () {
            checkEvent = 0;
        });
    });

    function getReloadDiv(url, div) {
        if (checkEvent == 1)
            return false;
        checkEvent = 1;
        var proID = $('.project-selector').val();
        var scale_unit = $('.scale_unit_selector').val();
        var scale_step = $('.scale_step_selector').val();
        var scale_filter = $('.scale_filter_selector').val();
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            cache: false,
            data: {project_id: proID, scale_unit: scale_unit, scale_step: scale_step, scale_filter: scale_filter},
            success: function (result) {
                gantt.clearAll();
                checkEvent = 0;
                $('#' + div).empty();
                $('#' + div).html(result);
            }
        });
    }

    $('.scale_unit_selector').change(function () {
        var project_id = '<?= $project_id ?>';
        var unit = "day";
        var step = $('.scale_step_selector').val();
        if ($(this).val() == '3') {
            $('.scale_step_selector').parent().show();
        } else {
            if ($(this).val() == '1') {
                unit = "week";
            } else if ($(this).val() == '2') {
                unit = "month";
            }
            $('.scale_step_selector').parent().hide();
        }

        var tab = '<?= $tab ?>';
        gantt.clearAll();
        
        onInitGantt();
        gantt.config.scale_height = 50;
        gantt.config.subscales = [
            {unit: unit, step: 1, date: "%j, %D"}
        ];

        //getReloadDiv($('#display-gantt').attr('data-url') + '&project_id=' + project_id + '&tab=' + tab, 'display-gantt');
    });

    $('.scale_step_selector').change(function () {
        var project_id = '<?= $project_id ?>';
        var tab = '<?= $tab ?>';
        var step = $(this).val();
        gantt.clearAll();

        onInitGantt();
        gantt.config.subscales = [
            {unit: 'day', step: parseInt(step), date: "%j, %D"}
        ];
        //getReloadDiv($('#display-gantt').attr('data-url') + '&project_id=' + project_id + '&tab=' + tab, 'display-gantt');
    });

    $('.scale_filter_selector').change(function () {
        var project_id = '<?= $project_id ?>';
        var tab = '<?= $tab ?>';
        var filter_val = $(this).val();
        if(filter_val == '4'){
            $('#box_all_users').css('display','block');
        }else{
            $('#box_all_users').css('display','none');
            gantt.clearAll();
            onInitGantt();
        }
        
    });

    $('#config_all_user').change(function(){
        var user_val = $(this).val();
        gantt.clearAll();
        onInitGantt();
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>