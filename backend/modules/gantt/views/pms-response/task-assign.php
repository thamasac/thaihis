<?php

use appxq\sdii\widgets\ModalForm;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use backend\modules\gantt\classes\GanttQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div id="gantt_here" style='width:100%; height:600px;'></div>
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
if (!isset($unit))
    $unit = 'day';
if (!isset($step))
    $step = '1';
?>
<script>
    $(function () {
        setTimeout(function () {
//            $(".gantt_task").on({
//                mousemove: function (e) {
//                    switch (e.which)
//                    {
//                        case 3:
//                            var mx2 = e.pageX - this.offsetLeft;
//                            if (mx)
//                                this.scrollLeft = this.sx + mx - mx2;
//                            break;
//
//                    }
//
//                },
//                mousedown: function (e) {
//                    switch (e.which)
//                    {
//                        case 3:
//                            this.sx = this.scrollLeft;
//                            mx = e.pageX - this.offsetLeft;
//                            break;
//                    }
//
//                }
//            });
//            $(document).on("mouseup", function () {
//                mx = 0;
//            });
        }, 1000);
        var mx = 0;
//        $('#gantt_here').on("contextmenu", function (e) {
//            return false;
//        });

    });

    var checkEvent = 0;
    function showDate(date) {
        console.log('scroll');
        var date_x = gantt.posFromDate(date);
        var scroll_to = Math.max(date_x - gantt.config.task_scroll_offset, 0);
        gantt.scrollTo(scroll_to);
    }

    $(function () {
        var project_id = $.cookie('project_id');

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
                name: "end_date", label: "Due Date", resize: true, width: 90, align: "center",
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
        //gantt.config.date_scale = "%j, %D";
        gantt.config.scale_height = 50;

        gantt.config.subscales = [
            {unit: "<?= $unit ?>", step: <?= $step ?>, date: "%j, %D"}
        ];

        gantt.readonly = true;

        gantt.config.row_height = 28;
        gantt.config.order_branch = true;
        gantt.config.order_branch_free = true;
        gantt.config.grid_resize = true;
        gantt.config.auto_scheduling_strict = true;
        gantt.config.xml_date = '%Y-%m-%d %H:%i:%s';
        gantt.getGridColumn('duration').hide = true;
        gantt.getGridColumn('progress').hide = true;

        gantt.init('gantt_here');
        gantt.attachEvent("onTaskLoading", function (task) {

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
                el.setAttribute('title', "Finish Task at " + gantt.templates.task_date(task.actual_date));
                return el;
            }
        });

        gantt.load('/gantt/pms-response/connector-task-assign?project_ezf_id=<?= $project_ezf_id ?>&category_ezf_id=<?= $category_ezf_id ?>&activity_ezf_id=<?= $ezf_id ?>&response_ezf_id=<?= $response_ezf_id ?>&taskid=<?= $taskid ?>');

        var dp = new gantt.dataProcessor('/gantt/pms-response/connector-task-assign?project_ezf_id=<?= $project_ezf_id ?>&category_ezf_id=<?= $category_ezf_id ?>&activity_ezf_id=<?= $ezf_id ?>&response_ezf_id=<?= $response_ezf_id ?>&taskid=<?= $taskid ?>');
        dp.init(gantt);

        setTimeout(function () {
            showDate(new Date());
        }, 8000);


    });
    function renderLabel(progress, sum, txt, css) {

        var relWidth = progress / sum * 100;

        var cssClass = "custom_progress ";
        cssClass += css;
        return "<div class='" + cssClass + "' style='width:" + relWidth + "%'>" + txt + "</div>";

    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>