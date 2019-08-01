<?php

use yii\helpers\Url;

$unit = "week";
$step = '2';
if ($scale_unit == '0') {
    $unit = "day";
} elseif ($scale_unit == '1') {
    $unit = "week";
} elseif ($scale_unit == '2') {
    $unit = "month";
}

if (isset($scale_step) || $scale_step > 0)
    $step = $scale_step;
?>

<div class="row">
    <div id="gantt_here" style='width:100%; height:700px;'></div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    var checkEvenLoad = null;

    $(function () {
        var mx = 0;
        setTimeout(function () {

//            $(".gantt_task").on({
//                mousemove: function (e) {
//                    switch (e.which)
//                    {
//                        case 3:
//                            var mx2 = e.pageX - this.offsetLeft;
//                            if (mx)
//                                this.scrollLeft = this.sx + mx - mx2;
//
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
        }, 3000);
        $('#gantt_here').on("contextmenu", function (e) {
            return false;
        });

        gantt.config.columns = [
            {
                name: "overdue", label: "", width: 38, template: function (obj) {
                    var earliest_date = gantt.date.parseDate(obj.start_date, "xml_date");
                    var latest_date = gantt.date.parseDate(obj.latest_date, "xml_date");
                    var date = new Date()
                    var nowDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());

                    if (obj.actual_date) {
                        var actual_date = gantt.date.parseDate(obj.actual_date, "xml_date");
                        //if (actual_date && obj.latest_date > actual_date) {
                        return '<div class="overdue-indicator-success"><i class="fa fa-check-circle"></i></div>';
                        //}
                    } else if (nowDate >= earliest_date && nowDate <= latest_date) {
                        return '<div class="overdue-indicator-warning" ><i class="fa fa-warning"></i></div>';
                    } else if (obj.actual_date == '' && nowDate >= latest_date) {
                        return '<div class="overdue-indicator-danger" ><i class="fa fa-exclamation-circle"></i></div>';
                    }
                    return '<div></div>';
                }
            },
            {name: "text", label: "Task name", tree: true, width: 150},
            {
                name: "start_date", label: "Earliest Date", align: "center", width: 100,
                template: function (item) {
                    if (!item.start_date)
                        return "Not Set!";
                    return item.start_date;
                }
            },

            {
                name: "plan_date", label: "Plan Date", align: "center", width: 100,
                template: function (item) {
                    if (!item.plan_date)
                        return "Not Set!";
                    return item.plan_date;
                }
            },
            {
                name: "latest_date", label: "Latest Date", align: "center", width: 100,
                template: function (item) {
                    if (!item.latest_date)
                        return "Not Set!";
                    return item.latest_date;
                }
            },
            {
                name: "actual_date", label: "Actual Date", align: "center", width: 100,
                template: function (item) {
                    if (!item.actual_date)
                        return '<button class="btn btn-success btn-xs" onclick=\'addNewtask("' + item.ezf_id + '","' + item.target + '","' + item.visit_id + '")\'><i class="fa fa-plus"></i></button>';
                    return item.actual_date;
                }
            },
        ];

        var date_to_str = gantt.date.date_to_str(gantt.config.task_date);
        var now = new Date();
        var toDate = now.getFullYear() + '-' + (now.getMonth() + 1) + '-' + now.getDate();
        gantt.addMarker({
            start_date: now,
            css: "today",
            text: "Today",
            title: "Today: " + date_to_str(now)
        });

        gantt.templates.task_text = function (start, end, task) {
            var summ = task.progress1 + task.progress2 + task.progress3;
            if(task.priority == '3'){
                return renderLabel(task.progress1, summ, "Earliest Date", "nearly_done") + renderLabel(task.progress2, summ, "Plan", "in_progress") + renderLabel(task.progress3, summ, "Latest Date", "idle");
            }else{
                return '';
            }

        };
//    gantt.config.scale_unit = "month";
//    gantt.config.date_scale = "%F, %Y";
//
        gantt.config.scale_height = 50;
        gantt.config.scale_unit = "month";
        gantt.config.date_scale = "%F";

        gantt.config.subscales = [
            {unit: "<?= $unit ?>", step: <?= $step ?>, date: "%j, %D"}
        ];

        gantt.config.xml_date = '%Y-%m-%d %H:%i:%s';
        gantt.config.readonly = true;
        gantt.init('gantt_here');

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

        gantt.addTaskLayer(function draw_deadline(task) {
            if (task.actual_date) {
                var el = document.createElement('div');
                el.className = 'deadline';
                var sizes = gantt.getTaskPosition(task, task.actual_date);

                el.style.left = sizes.left + 'px';
                el.style.top = sizes.top + 'px';
                el.innerHTML = "<i class='fa fa-thumb-tack'></i>";
                el.setAttribute('title', "Open Activity at " + gantt.templates.task_date(task.actual_date));
                return el;
            }
        });

        onInitGantt();
    });
    
    function onInitGantt(){
        var scale_unit = 'day';
        var unit_val = $('.scale_unit_selector').val();
        if(unit_val=='1'){
            scale_unit = "week";
        }else if(unit_val =='2'){
            scale_unit = "month";
        }
        
        var scale_step = $('.scale_step_selector').val();
        var url = '/gantt/gantt/connector-subject?widget_id=<?= $widget_id ?>&schedule_id=<?= $schedule_id ?>&data_id=<?= $data_id ?>&data_ptid=<?= $data_ptid ?>&group_id=<?= $group_id ?>';
        url += "&scale_unit="+scale_unit+"&scale_step="+scale_step;
        gantt.load(url);

        var dp = new gantt.dataProcessor(url);
        dp.init(gantt);
    }
    
    
    $('.scale_unit_selector').change(function () {
        //getReloadDiv($('#gantt-individual').attr('data-url'), 'gantt-individual');
        var scale_unit = 'day';
        var unit_val = $('.scale_unit_selector').val();
        if(unit_val=='1'){
            scale_unit = "week";
        }else if(unit_val =='2'){
            scale_unit = "month";
        }
        
        var scale_step = $('.scale_step_selector').val();
        gantt.clearAll();
        gantt.config.subscales = [
            {unit: scale_unit, step: scale_step, date: "%j, %D"}
        ];

        onInitGantt();
    });

    $('.scale_step_selector').change(function () {
        //getReloadDiv($('#gantt-individual').attr('data-url'), 'gantt-individual');
        var scale_unit = 'day';
        var unit_val = $('.scale_unit_selector').val();
        if(unit_val=='1'){
            scale_unit = "week";
        }else if(unit_val =='2'){
            scale_unit = "month";
        }
        
        var scale_step = $('.scale_step_selector').val();
        gantt.config.subscales = [
            {unit: scale_unit, step: scale_step, date: "%j, %D"}
        ];

        gantt.clearAll();
        onInitGantt();
    });
    
    function renderLabel(progress, sum, txt, css) {

        var relWidth = progress / sum * 100;

        var cssClass = "custom_progress ";
        cssClass += css;
        return "<div class='" + cssClass + "' style='width:" + relWidth + "%'>" + txt + "</div>";

    }
    function addNewtask(ezf_id, target, visit_id) {
        var url = '<?=
Url::to(['/ezforms2/ezform-data/ezform',
    'modal' => 'modal-ezform-gantt',
    'require' => 'new_task',
]);
?>';
        var initdata = {visit_name: visit_id,group_id_init: '<?=$group_id?>', schedule_id_init: '<?=$schedule_id?>'};
        var data = {ezf_id: ezf_id, target: target, initdata: btoa(JSON.stringify(initdata))};
        $('#modal-ezform-gantt').modal();
        $.ajax({
            url: url,
            method: 'get',
            data: data,
            cache: true,
            success: function (result) {
                $('#modal-ezform-gantt').find('.modal-content').html(result);
            }
        });
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
