
<?php

use appxq\sdii\widgets\ModalForm;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use backend\modules\gantt\classes\GanttQuery;

$this->registerJsFile(
        '@web/js-gantt/dhtmlxgantt.js?48', ['depends' => [\yii\web\JqueryAsset::className()]]
);

$this->registerJsFile(
        '@web/js-gantt/api.js?3', ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
        '@web/js-gantt/dhtmlxgantt_marker.js?8', ['depends' => [\yii\web\JqueryAsset::className()]]
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
?>

<?php
$user_id = Yii::$app->user->id;
$project_id = "1531362434013388200";
$unit = "day";
$step = '1';
if ($scale_unit == '0' || $scale_unit == '3') {
    $unit = "day";
} elseif ($scale_unit == '1') {
    $unit = "week";
} elseif ($scale_unit == '2') {
    $unit = "month";
}

if (isset($scale_step))
    $step = $scale_step;

$gantt_type = $values['check_type'];


$projectForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($project_ezf_id);
$projectList = \backend\modules\subjects\classes\SubjectManagementQuery::GetTableData($projectForm);

$activityForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_ezf_id);
?>

<input type="hidden" value="<?= $widget_id ?>" name="widget_id" id="data-widget_id">
<input type="hidden" value="<?= $schedule_id ?>" name="schedule_id" id="data-schedule_id">
<div class="gantt-default-index">      
    <section class="content">     
<!--        <input class="btn btn-warning" value="Export to MS Project" type="button" onclick='gantt.exportToMSProject({skip_circular_links: false})'
               style='margin:20px;'>
        <input class="btn btn-success" value="Export to Excel" type="button" onclick='gantt.exportToExcel()' style='margin:20px;'>
        <input class="btn btn-primary" value="Export to PDF " type="button" onclick='gantt.exportToPDF()' style='margin:20px;'>
        <input class="btn btn-info" value="Export to PNG " type="button" onclick='gantt.exportToPNG()'>-->
    </section>   
</div>


<!--<script src="/js-gantt/htmlgantt.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="/js-gantt/dhtmlxgantt.css" type="text/css" media="screen" title="no title" charset="utf-8">-->

<?php
$this->registerCssFile("@web/js-gantt/dhtmlxgantt.css");
?>

<div class="header">

</div>
<div id="text_warning_header" style="text-align: center;font-size: 20px;">
    Please wait to be exporting ...
</div>
<a href="#" class="btn btn-default btn_export_pdf" onclick='gantt.exportToPDF({
            name: "mypms-export-pdf.pdf",
            header: "<style><?= $customCss ?></style><h1>Project Management System</h1>",
            footer: "<h4>NCRC Thailand</h4>",
            locale: "en",
            server: "<?= Yii::getAlias('@web') ?>",
            raw: true
        });'><i class="fa fa-file-pdf-o"></i> Export to PDF</a><br/>
<div id="gantt_here" style='width:100%; height:850px;'></div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";

$btnCateAdd = "";
$permission = 0;
$ownProject = false;
$myTask = [];

//}
?>
<script>
    $(function(){
        setTimeout(function(){
            $('.btn_export_pdf').click();
            $('#text_warning_header').empty();
        },3500);
        
    });
    
    var checkEvent = 0;
    function showDate(date) {
        console.log('scroll');
        var date_x = gantt.posFromDate(date);
        var scroll_to = Math.max(date_x - gantt.config.task_scroll_offset, 0);
        gantt.scrollTo(scroll_to);
    }

    $(function () {
        var project_id = "1531362434013388200";

        gantt.config.grid_width = 300;
        gantt.config.add_column = false;
        gantt.templates.grid_row_class = function (start_date, end_date, item) {
            if (item.status == 3)
                return "red";
            if (item.status == 2)
                return "yellow";
            if (item.status == 0)
                return "gray";
            if (item.status >= 4)
                return "green";
        };

        var date_to_str = gantt.date.date_to_str(gantt.config.task_date);
        var now = new Date();
        var toDate = now.getFullYear() + '-' + (now.getMonth() + 1) + '-' + now.getDate();
        gantt.addMarker({
            start_date: now,
            css: "today",
            text: "Today",
            title: "Today: " + date_to_str(now)
        });

        gantt.config.columns = [

            {name: "text", label: "Task name", tree: true, resize: true, width: 220},
            {
                name: "start_date", label: "Start Date", resize: true, width: 90, align: "center",
            },
            {
                name: "finish_date", label: "Due Date", resize: true, width: 90, align: "center",
            },
            {name: "duration", label: "Duration", align: "center", resize: true, width: 40},
            {
                name: "progress", label: "Progress", align: "center", resize: true, width: 80, template: function (dat) {
                    return Math.round(dat.progress * 100) + " %";
                }
            },
            {
                name: "priority", label: "Type", align: "center", resize: true, width: 80, template: function (obj) {
                    if (obj.priority == 1) {
                        return "Main Task"
                    }
                    if (obj.priority == 2) {
                        return "Sub-Task"
                    }
                    if (obj.type_task == 'milestone') {
                        return "Milestone";
                    }
                    return "Task Item"
                }
            },
            {
                name: "financial", label: "Sent To", align: "center", width: 60, resize: true, template: function (obj) {
                    if (obj.type_task == 'task' || obj.type_task == 'milestone') {
                        var html = "";
                        if (obj.sent_module_1 == '1') {
                            html += ' <a href="javascript:void(0)"><label style="color:blue;font-size:18px;cursor: pointer;" data-toggle="tooltip" title="' + obj.sent_module_txt1 + '"><i class="fa fa-location-arrow"></i></label></a>';
                        }
                        if (obj.sent_module_2 == '1') {
                            html += ' <a href="javascript:void(0)" id="sent_to_milestone" data-dataid="' + obj.id + '"><label style="color:orange;font-size:18px;cursor: pointer;" data-toggle="tooltip" title="' + obj.sent_module_txt2 + '"><i class="fa fa-location-arrow"></i></label></a>';
                        }
                        if (obj.sent_module_3 == '1') {
                            html += ' <a href="javascript:void(0)"><label style="color:green;font-size:18px;cursor: pointer;" data-toggle="tooltip" title="' + obj.sent_module_txt3 + '"><i class="fa fa-location-arrow"></i></label></a>';
                        }
                        if (obj.sent_module_4 == '1') {
                            html += ' <a href="javascript:void(0)"><label style="color:skyblue;font-size:18px;cursor: pointer;" data-toggle="tooltip" title="' + obj.sent_module_txt4 + '"><i class="fa fa-location-arrow"></i></label></a>';
                        }
                        return html;
                    } else {
                        return '';
                    }
                }
            },
        ];

        gantt.templates.rightside_text = function (start, end, task) {
            if (task.deadline) {
                if (end.valueOf() > task.deadline.valueOf()) {
                    var overdue = Math.ceil(Math.abs((end.getTime() - task.deadline.getTime()) / (24 * 60 * 60 * 1000)));
                    var text = "<b>Overdue: " + overdue + " days</b>";
                    return text;
                }
            }
        };

        gantt.config.scale_unit = "month";

        gantt.config.date_scale = "%F";
        gantt.config.scale_height = 50;

        gantt.config.subscales = [
            {unit: "<?= $unit ?>", step: <?= $step ?>, date: "%j, %D"}
        ];

        gantt.config.row_height = 28;
        gantt.config.order_branch = true;
        gantt.config.order_branch_free = true;
        gantt.config.grid_resize = true;
        gantt.config.auto_scheduling_strict = true;
        gantt.config.xml_date = '%Y-%m-%d %H:%i:%s';
        gantt.getGridColumn('duration').hide = true;
        gantt.getGridColumn('progress').hide = true;
        gantt.config.auto_scheduling = true;
        gantt.config.auto_scheduling_strict = true;
        gantt.init('gantt_here');
        gantt.config.readonly = true;
        gantt.config.branch_loading = true;
        gantt.attachEvent("onTaskLoading", function (task) {
//            if (task.id === checkEvenLoad){
//                console.log('return');
//                return;
//            }
            if (task.actual_date)
                task.actual_date = gantt.date.parseDate(task.actual_date, "xml_date");

            checkEvenLoad = task.id;
            return true;
        });
        gantt.templates.progress_text = function (start, end, task) {
            return "<span style='text-align:left;color:#ffffff;margin-left:10px;' class='pull-left'>" + Math.round(task.progress * 100) + " % </span>";
        };

        gantt.templates.task_class = function (start, end, task) {
            if (task.deadline && end.valueOf() > task.deadline.valueOf()) {
                return 'overdue';
            } else {
                switch (task.priority) {
                    case "1":
                        return "high";
                        break;
                    case "2":
                        return "medium";
                        break;
                    case "3":
                        return "low";
                        break;
                }
            }
        };
        gantt.templates.task_text = function (start, end, task) {
            if (task.segment > 1 && task.type_task == 'task') {
                var summ = '100';
                var html = "";
                var template = ["warning", "success", "info", 'danger', 'primary'];
                var value = (100 / task.segment);
                var color_count = 0;
                for (var i = 1; i <= task.segment; i++) {
                    html += renderLabel(value, summ, "Segment " + i, template[color_count]);
                    if (color_count < (template.length - 1)) {
                        color_count++;
                    } else {
                        color_count = 0
                    }
                }
                //return renderLabel(task.progress1, summ, "Segment 1", "nearly_done") + renderLabel(task.progress2, summ, "Segment 2", ) + renderLabel(task.progress3, summ, "Segment 3", );

                return html;
            }
            return task.text;
        };

        gantt.addTaskLayer(function draw_deadline(task) {
            if (task.actual_date) {
                var el = document.createElement('div');
                el.className = 'deadline';
                var sizes = gantt.getTaskPosition(task, task.actual_date);

                el.style.left = sizes.left + 'px';
                el.style.top = sizes.top + 'px';
                el.innerHTML = "<i class='fa fa-thumb-tack'></i>";
                el.setAttribute('title', "Task Finished at " + gantt.templates.task_date(task.actual_date));
                return el;
            }
        });

        gantt.load('/gantt/gantt/connector-project?widget_id=<?= $widget_id ?>&schedule_id=<?= $schedule_id ?>&project_ezf_id=<?= $project_ezf_id ?>&cate_ezf_id=<?= $cate_ezf_id ?>&activity_ezf_id=<?= $activity_ezf_id ?>&response_ezf_id=<?= $response_ezf_id ?>&start_date=<?= $start_date ?>&finish_date=<?= $finish_date ?>&progress=<?= $progress ?>&project_name=<?= $project_name ?>&cate_name=<?= $cate_name ?>&task_name=<?= $task_name ?>&project_id=' + project_id + '&scale_filter=<?= $scale_filter ?>');

        var dp = new gantt.dataProcessor('/gantt/gantt/connector-project?widget_id=<?= $widget_id ?>&schedule_id=<?= $schedule_id ?>&project_ezf_id=<?= $project_ezf_id ?>&cate_ezf_id=<?= $cate_ezf_id ?>&activity_ezf_id=<?= $activity_ezf_id ?>&response_ezf_id=<?= $response_ezf_id ?>&start_date=<?= $start_date ?>&finish_date=<?= $finish_date ?>&progress=<?= $progress ?>&project_name=<?= $project_name ?>&cate_name=<?= $cate_name ?>&task_name=<?= $task_name ?>&project_id=' + project_id + '&scale_filter=<?= $scale_filter ?>');
        dp.init(gantt);


    });
    function renderLabel(progress, sum, txt, css) {

        var relWidth = progress / sum * 100;

        var cssClass = "custom_progress ";
        cssClass += css;
        return "<div class='" + cssClass + "' style='width:" + relWidth + "%'>" + txt + "</div>";

    }

    $(document).on('click', '.btn-delete-task', function () {
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
                    getReloadDiv($('#display-gantt').attr('data-url') + '&project_id=<?= $project_id ?>', 'display-gantt');
                } else {

                }
            }).fail(function () {
                checkEvent = 0;
            });
        }, function () {
            checkEvent = 0;
        });
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

    $(document).on('click', '.btn-edit-task', function () {
        if (checkEvent == 1)
            return;
        checkEvent = 1;
        var data_id = $(this).attr('data-id');
        var ezf_id = $(this).attr('data-ezf_id');
        var type = $(this).attr('data-type');
        var dat = {id: data_id, text: 'edit', type_task: type};
        onModalAddGanttDialog(null, dat);
    });

    $(document).on('click', '.btn-edit-project', function () {
        if (checkEvent == 1)
            return;
        checkEvent = 1;
        var data_id = $(this).attr('data-id');
        var ezf_id = $(this).attr('data-ezf_id');
        var url = '<?=
Url::to(['/ezforms2/ezform-data/ezform',
    'modal' => 'modal-ezform-gantt',
]);
?>';
        $('#modal-ezform-gantt').modal();
        $.ajax({
            url: url,
            method: 'get',
            data: {ezf_id: ezf_id, dataid: data_id},
            cache: true,
            success: function (data) {
                checkEvent = 0;
                $('#modal-ezform-gantt').find('.modal-content').empty();
                $('#modal-ezform-gantt').find('.modal-content').html(data);
            }
        });
    });

    $('#check_col_taskname').change(function () {
        var state_show = gantt.getGridColumn('text').hide;
        gantt.getGridColumn('text').hide = !state_show;
        gantt.render();
    });
    $('#check_col_startdate').change(function () {
        var state_show = gantt.getGridColumn('start_date').hide;
        gantt.getGridColumn('start_date').hide = !state_show;
        gantt.render();
    });
    $('#check_col_finishdate').change(function () {
        var state_show = gantt.getGridColumn('finish_date').hide;
        gantt.getGridColumn('finish_date').hide = !state_show;
        gantt.render();
    });
    $('#check_col_priority').change(function () {
        var state_show = gantt.getGridColumn('priority').hide;
        gantt.getGridColumn('priority').hide = !state_show;
        gantt.render();
    });
    $('#check_col_day').change(function () {
        var state_show = gantt.getGridColumn('duration').hide;
        gantt.getGridColumn('duration').hide = !state_show;
        gantt.render();
    });
    $('#check_col_progress').change(function () {
        var state_show = gantt.getGridColumn('progress').hide;
        gantt.getGridColumn('progress').hide = !state_show;
        gantt.render();
    });

    function onUpdateLink(data) {
        var widget_id = $('#data-widget_id').val();
        var schedule_id = $('#data-schedule_id').val();
        var url = '/gantt/gantt/update-link';
        $.post(url, {dataList: JSON.stringify(data), widget_id: widget_id, schedule_id: schedule_id}, function (result) {
            if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
            }
        });
    }

    function onUpdateGantt(t, dat) {
        if (checkEvent == 1)
            return;
        checkEvent = 1;
        var widget_id = $('#data-widget_id').val();
        var schedule_id = $('#data-schedule_id').val();
        var gantt_type = '<?= $gantt_type ?>';
        var activity_ezf_id = '<?= $activity_ezf_id ?>';
        var url = '/gantt/gantt/update-gantt';
        if (dat.type_task == 'task' || dat.type_task == 'milestone') {
            $.post(url, {options: JSON.stringify(dat), gantt_type: gantt_type, activity_ezf_id: activity_ezf_id}, function (result) {
                checkEvent = 0;
            });
        } else {
            checkEvent = 0;
        }
    }

    function onModalAddGanttDialog(e, dat) {
        var proId = $.cookie("project_id");
        var require = dat.text.replace(' ', '_');
        var widget_id = $('#data-widget_id').val();
        var schedule_id = $('#data-schedule_id').val();
        var data_id = "";
        var taskid = "";
        var data = {};

        var permiss = '<?= $permission ?>';
        var ownProject = '<?= $ownProject ?>';
        var ownTask = JSON.parse('<?= $myTask ?>');

        var initdata = {};
        if (dat.text == "New task") {
            initdata = {project_id: proId};
            data = {initdata: JSON.stringify(initdata), modal: 'modal-ezform-gantt'};
        } else if (dat.text == "edit") {
            data_id = dat.response_id;
            taskid = dat.id;
            data = {dataid: taskid, modal: 'modal-ezform-gantt'};
        } else {
            data_id = dat.response_id;
            taskid = dat.id;
            data = {dataid: data_id, taskid: taskid, modal: 'modal-ezform-gantt'};
        }

        var url = '';
        if (dat.text == 'edit') {
            url = '<?=
Url::to(['/ezforms2/ezform-data/ezform',
    'ezf_id' => $activity_ezf_id,
]);
?>';
        } else {
            url = '<?=
Url::to(['/gantt/pms-response/index',
    'page_from' => 'pms',
    'ezf_id' => $activity_ezf_id,
    'response_ezf_id' => $response_ezf_id,
    'action' => 'view',
]);
?>';
        }
        if (dat.ezf_id !== '' && dat.type_task == 'task' && (ownProject == 1 || $.inArray(dat.id, ownTask) !== -1)) {
            $('#modal-ezform-gantt').modal();
            $.ajax({
                url: url,
                method: 'get',
                data: data,
                cache: true,
                success: function (data) {
                    checkEvent = 0;
                    $('#modal-ezform-gantt').find('.modal-content').empty();
                    $('#modal-ezform-gantt').find('.modal-content').html(data);

                }
            });
        } else {
            checkEvent = 0;
        }

    }
    $(document).on('click', '.btn-delete-nodegantt', function () {
        var dataid = $('form#ezform-<?= $activity_ezf_id ?>').attr('data-dataid');
        onDeleteTask(dataid, '<?= $activity_ezf_id ?>');
    });

    function addNewtask(ezf_id, id, parent) {
        var proId = $.cookie("project_id");
        var url = '<?=
Url::to(['/ezforms2/ezform-data/ezform',
    'modal' => 'modal-ezform-gantt',
    'require' => 'new_task',
]);
?>';

        var taskId = gantt.createTask({
            id: 10,
            text: "New Task",
            start_date: "2018-07-22",
            type: 'task',
            ezf_id: ezf_id,
            duration: 10
        }, id, 1);

        var initdata = {'category_id': '' + id};
        var data = {ezf_id: '<?= $activity_ezf_id ?>', initdata: btoa(JSON.stringify(initdata)), target: parent};
        $('#modal-ezform-gantt').modal();
        $.ajax({
            url: url,
            method: 'get',
            data: data,
            cache: true,
            success: function (data) {
                $('#modal-ezform-gantt').find('.modal-content').empty();
                $('#modal-ezform-gantt').find('.modal-content').html(data);
            }
        });
    }

    function addNewCategory(ezf_id, id) {
        var url = '<?=
Url::to(['/ezforms2/ezform-data/ezform',
    'modal' => 'modal-ezform-gantt',
    'require' => 'new_task',
]);
?>';
        console.log(ezf_id);
        var data = {ezf_id: ezf_id, target: id};
        $('#modal-ezform-gantt').modal();
        $.ajax({
            url: url,
            method: 'get',
            data: data,
            cache: true,
            success: function (data) {
                $('#modal-ezform-gantt').find('.modal-content').empty();
                $('#modal-ezform-gantt').find('.modal-content').html(data);
            }
        });
    }

    function onDragEndItem(dat) {
        var drop_target = dat.$drop_target;
        var new_order = dat.$index;
        var url = '<?=
Url::to(['/gantt/gantt/dragitem-update',
    'modal' => 'modal-ezform-gantt',
    'reloadDiv' => $reloadDiv,
]);
?>';
        var ezf_id = dat.ezf_id;

        var data = {ezf_id: ezf_id, target: dat.id, type: dat.type, parent: dat.parent, drop_target: drop_target, new_order: new_order};
        $.ajax({
            url: url,
            method: 'get',
            data: data,
            cache: true,
            success: function (result) {
                console.log('success');
            }
        });
    }

    function onDeleteTask(dataid, ezf_id) {
        if (checkEvent == 1)
            return;
        checkEvent = 1;
        var url = '/ezforms2/ezform-data/delete?ezf_id=' + ezf_id + '&dataid=' + dataid + '&reloadDiv=<?= $reloadDiv ?>';
        $.ajax({
            url: url,
            method: 'POST',
            cache: true,
            data: {ezf_id: ezf_id, dataid: dataid},
            success: function (result) {
                checkEvent = 0;
                if (result.status == "success") {
                    $(document).find('#modal-ezform-gantt').modal('hide');
                    getReloadDiv($('#<?= $reloadDiv ?>').attr('data-url') + '&project_id=<?= $project_id ?>', '<?= $reloadDiv ?>');
                }
            }
        });
    }

    function onBeforeDeleteLink(dat, txt) {
        if (checkEvent == 1)
            return;
        checkEvent = 1;
        yii.confirm(txt, function () {
            checkEvent = 0;
            $('.gantt_task_link').each(function (i, e) {
                if ($(e).attr('link_id') == dat.id) {
                    $(e).remove();
                }
            });

            onDeleteLink(dat);
        }, function () {
            checkEvent = 0;
        });

    }

    function onDeleteLink(dat) {
        if (checkEvent == 1)
            return;
        checkEvent = 1;
        var widget_id = $('#data-widget_id').val();
        var schedule_id = $('#data-schedule_id').val();
        var url = '/gantt/gantt/delete-link';
        $.ajax({
            url: url,
            method: "post",
            type: 'JSON',
            cache: true,
            data: {dataList: JSON.stringify(dat), widget_id: widget_id, schedule_id: schedule_id},
            success: function (result) {
                checkEvent = 0;
                if (result.status == "success") {
<?= SDNoty::show('result.message', 'result.status') ?>;
                    $(document).find('.gantt-alert').remove();
                }
            }
        });
    }

    $(document).on('click', '.gantt_cancel_button', function () {
        $(document).find('.gantt-alert').remove();
    });

    $(document).on('click', '#sent_to_milestone', function () {
        var dataid = $(this).attr('data-dataid');
        var url = "/gantt/timeline-milestone/index";
        $('#modal-ezform-project').modal();
        $('#modal-ezform-project').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {dataid: dataid}, function (result) {
            $('#modal-ezform-project').find('.modal-content').empty();
            $('#modal-ezform-project').find('.modal-content').html(result);
        });
    });


    function onCopyTask(id, ezf_id, task_type) {
        var widget_id = $('#data-widget_id').val();
        var schedule_id = $('#data-schedule_id').val();
        var activity_ezf = '<?= $activity_ezf_id ?>';
        var cate_ezf = '<?= $cate_ezf_id ?>';
        var project_ezf = '<?= $project_ezf_id ?>';
        var url = '/gantt/gantt/copy-task';
        $.ajax({
            url: url,
            method: "get",
            type: 'JSON',
            cache: true,
            data: {dataid: id, activity_ezf: activity_ezf, cate_ezf: cate_ezf, project_ezf: project_ezf, task_type: task_type, schedule_id: schedule_id},
            success: function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>
                getReloadDiv($('#<?= $reloadDiv ?>').attr('data-url') + '&project_id=<?= $project_id ?>', '<?= $reloadDiv ?>');
            }
        });
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>