
<?php

use appxq\sdii\widgets\ModalForm;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use backend\modules\gantt\classes\GanttQuery;
use appxq\sdii\utils\SDUtility;
?>
<?php
$user_id = Yii::$app->user->id;
if ($project_id == null)
    $project_id = isset($_COOKIE['project_id']) ? $_COOKIE['project_id'] : null;

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
$options_pms = GanttQuery::getOptionsPMS($project_id);

$user_role = GanttQuery::getRolesByUserId($user_id);
$isdirector = GanttQuery::findArraybyFieldName($user_role, 'Director', 'role_name') ? '1' : '0';

$projectForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($project_ezf_id);
$projecData= \backend\modules\subjects\classes\SubjectManagementQuery::GetTableData($projectForm, ['id'=>$project_id],'one');
$cateForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($cate_ezf_id);
$activityForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($activity_ezf_id);
$main_shared = isset($projecData['project_sharing'])?$projecData['project_sharing']:'1';

$request_form = backend\modules\ezforms2\classes\EzfQuery::getEzformOne(\backend\modules\gantt\Module::$formsId['request_task_form']);
$reject_form = backend\modules\ezforms2\classes\EzfQuery::getEzformOne(\backend\modules\gantt\Module::$formsId['reject_request_form']);
?>
<style>
    .list-group > a{
        background-color:#E6F2FA;
    }
    .list-group > a:hover{
        background-color:#C0DDF1;
    }
</style>

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


<div class="header">
    New Task Items waiting for you to accept: <label><code id="not_accept_amt">0</code></label>
    <i class='fa fa-user-o' title='My all tasks' style='color:blue;font-size:16px;'></i> <label id="mytask_amt">0</label> /
    <i class='fa fa-clock-o' title='Waiting approve by director' style='color:orange;font-size:16px;'></i> <label id="wait_approve_amt">0</label> /
    <i class='fa fa-shopping-cart' title='Ready for shopping' style='color:skyblue;font-size:16px;'></i> <label id="shopping_amt">0</label> 

</div>
<div class="gantt_grid_top"  role="row" style="height: 35px; line-height: 34px; width: inherit;padding-left:10px;">
    <button class="btn btn-success btn-xs" onclick='addNewCategory("<?= $cate_ezf_id ?>", "<?= $project_id ?>")'><i class="fa fa-plus" style="color:white;"></i> Add new sub-task</button>
    <button class="btn btn-info btn-xs" onclick='addNewtask("<?= $cate_ezf_id ?>", "<?= $project_id ?>", "<?= $project_id ?>")'><i class="fa fa-plus" style="color:white;"></i> Add new task item</button>
    <button class="btn btn-warning btn-xs " onclick='addNewMilestone("<?= $cate_ezf_id ?>", "<?= $project_id ?>", "<?= $project_id ?>")'><i class="fa fa-plus" style="color:white;"></i> Add new milestone</button>
    <!--    <div class="gantt_grid_head_cell gantt_grid_head_text  " style="width:156px;" role="columnheader" aria-label="Task Name" column_id="text">
    <?= Html::checkbox('task_col_check', in_array('text', $options_pms['column_show']) ? false : true, ['id' => 'check_col_taskname', 'class' => 'checkbox_grid_head']) ?><?= Html::label(Yii::t('subject', 'Task Name'), 'task_col_check') ?> 
        </div>
        <div class="gantt_grid_head_cell gantt_grid_head_start_date  " style="width:90px;" role="columnheader" aria-label="Start Time" column_id="start_date">
    <?= Html::checkbox('startdate_col_check', in_array('start_date', $options_pms['column_show']) ? false : true, ['id' => 'check_col_startdate', 'class' => 'checkbox_grid_head']) ?><?= Html::label(Yii::t('subject', 'Start Date'), 'startdate_col_check') ?>
        </div>
        <div class="gantt_grid_head_cell gantt_grid_head_duedate  " style="width:90px;" role="columnheader" aria-label="Duration" column_id="duedate">
    <?= Html::checkbox('duedate_col_check', in_array('duedate', $options_pms['column_show']) ? false : true, ['id' => 'check_col_finishdate', 'class' => 'checkbox_grid_head']) ?><?= Html::label(Yii::t('subject', 'Due Date'), 'duedate_col_check') ?>
        </div>-->
    <div class="gantt_grid_head_cell gantt_grid_head_credit_points  " style="width:120px;" role="columnheader" aria-label="Credit Points" column_id="credit_points">
        <?= Html::checkbox('credit_points_col_check', in_array('credit_points', $options_pms['column_show']) ? false : true, ['id' => 'check_col_credit_points', 'class' => 'checkbox_grid_head']) ?><?= Html::label(Yii::t('gantt', 'Credit Points'), 'credit_points_col_check') ?>
    </div>
    <div class="gantt_grid_head_cell gantt_grid_head_reward_points  " style="width:120px;" role="columnheader" aria-label="Reward Points" column_id="reward_points">
        <?= Html::checkbox('reward_points_col_check', in_array('reward_points', $options_pms['column_show']) ? false : true, ['id' => 'check_col_reward_points', 'class' => 'checkbox_grid_head']) ?><?= Html::label(Yii::t('gantt', 'Reward Points'), 'reward_points_col_check') ?>
    </div>
    <div class="gantt_grid_head_cell gantt_grid_head_priority  " style="width:90px;" role="columnheader" aria-label="Duration" column_id="priority">
        <?= Html::checkbox('priority_col_check', in_array('priority', $options_pms['column_show']) ? false : true, ['id' => 'check_col_priority', 'class' => 'checkbox_grid_head']) ?><?= Html::label(Yii::t('subject', 'Type'), 'priority_col_check') ?>
    </div>
    <div class="gantt_grid_head_cell gantt_grid_head_day  " style="width:90px;" role="columnheader" aria-label="Day" column_id="duration">
        <?= Html::checkbox('day_col_check', in_array('duration', $options_pms['column_show']) ? false : true, ['id' => 'check_col_day', 'class' => 'checkbox_grid_head']) ?><?= Html::label(Yii::t('subject', 'Duration'), 'day_col_check') ?>
    </div>
    <div class="gantt_grid_head_cell gantt_grid_head_progress  " style="width:90px;" role="columnheader" aria-label="Progress" column_id="progress">
        <?= Html::checkbox('progress_col_check', in_array('progress', $options_pms['column_show']) ? false : true, ['id' => 'check_col_progress', 'class' => 'checkbox_grid_head']) ?><?= Html::label(Yii::t('subject', 'Progress'), 'progress_col_check') ?>
    </div>
    <div class="gantt_grid_head_cell gantt_grid_head_financial  " style="width:90px;" role="columnheader" aria-label="Sent to" column_id="financial">
        <?= Html::checkbox('financial_col_check', in_array('financial', $options_pms['column_show']) ? false : true, ['id' => 'check_col_financial', 'class' => 'checkbox_grid_head']) ?><?= Html::label(Yii::t('subject', 'Sent to'), 'financial_col_check') ?>
    </div>
    <div class="gantt_grid_head_cell gantt_grid_head_task_status  " style="width:90px;" role="columnheader" aria-label="Duration" column_id="task_status">
        <?= Html::checkbox('task_status_col_check', in_array('task_status', $options_pms['column_show']) ? false : true, ['id' => 'check_col_task_status', 'class' => 'checkbox_grid_head']) ?><?= Html::label(Yii::t('subject', 'Status'), 'task_status_col_check') ?>
    </div>
    <div class="gantt_grid_head_cell gantt_grid_head_assign  " style="width:90px;" role="columnheader" aria-label="Duration" column_id="assign_to">
        <?= Html::checkbox('assign_col_check', in_array('assign_to', $options_pms['column_show']) ? false : true, ['id' => 'check_col_assign', 'class' => 'checkbox_grid_head']) ?><?= Html::label(Yii::t('subject', 'Assigned'), 'assign_col_check') ?>
    </div>
    <div class="gantt_grid_head_cell gantt_grid_head_ezf_id  " style="width:90px;" role="columnheader" aria-label="Duration" column_id="ezf_id">
        <?= Html::checkbox('ezf_id_col_check', in_array('ezf_id', $options_pms['column_show']) ? false : true, ['id' => 'check_col_ezf_id', 'class' => 'checkbox_grid_head']) ?><?= Html::label(Yii::t('subject', 'Actions'), 'ezf_id_col_check') ?>
    </div>

    <!--<?= Html::button('<i class="fa fa-expand"></i> ' . Yii::t('subject', 'Full Screen'), ['class' => 'btn btn-default pull-right']) ?>-->
</div>
<div id="request_task_send" data-url="<?= Url::to(['/gantt/gantt/request-task-send', 'task_ezf_id' => $activity_ezf_id]) ?>"></div>
<div id="gantt_here" style='width:100%; height:800px;'></div>

<div id="contextAction" style="display: none;position: absolute; left: 100px; top: 100px; width: 200px; height: auto;">
    <div class="list-group"  id="list_for_maintask" style="display: none;">
        <a href="javascript:void(0)" class="list-group-item list-group-item-action" onclick='addNewCategory("<?= $cate_ezf_id ?>", "", this)'><i class="fa fa-plus" style="color:green;"></i> Add new sub-task</a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action" onclick='addNewtask("<?= $activity_ezf_id ?>", "", "<?= $project_id ?>", this)'><i class="fa fa-plus" style="color:green;"></i> Add new task</a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action" onclick='addNewMilestone("<?= $cate_ezf_id ?>", "", "<?= $project_id ?>", this)'><i class="fa fa-plus" style="color:green;"></i> Add new milestone</a>
    </div>
    <div class="list-group"  id="list_for_subtask" style="display: none;">
        <a href="javascript:void(0)" class="list-group-item list-group-item-action" onclick='addNewCategory("<?= $cate_ezf_id ?>", "", this)'><i class="fa fa-plus" style="color:green;"></i> Add new sub-task</a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action" onclick='addNewtask("<?= $activity_ezf_id ?>", "", "<?= $project_id ?>", this)'><i class="fa fa-plus" style="color:green;"></i> Add new task</a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action" onclick='addNewMilestone("<?= $cate_ezf_id ?>", "", "<?= $project_id ?>", this)'><i class="fa fa-plus" style="color:green;"></i> Add new milestone</a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-edit-project"><i class="fa fa-pencil" style="color:blue;"></i> Edit</a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-delete-task"><i class="fa fa-trash" style="color:red;"></i> Delete</a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action" onclick='onCopyTask(this)'><i class="fa fa-copy" style="color:skyblue;"></i> Copy task </a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action" onclick='copyAndShareTask(this)'><i class="fa fa-share-square" aria-hidden="true" style="color:blue;"></i> Sharing task </a>
    </div>
    <div class="list-group" id="list_for_taskitem" style="display: none;">
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-accept-task"><i class="fa fa-thumbs-o-up" aria-hidden="true" style="color:green;"></i> Accept task</a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-decline-task"><i class="fa fa-hand-paper-o" aria-hidden="true" style="color:green;"></i> Decline task</a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-response-task"><i class="fa fa-gavel" aria-hidden="true" style="color:green;"></i> Response task</a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-terminate-task"><i class="fa fa-minus-circle" aria-hidden="true" style="color:red;"></i> Leave </a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-pending-task"><i class="fa fa-hourglass-start" aria-hidden="true" style="color:orange;"></i> Pending </a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-refer-task"><i class="fa fa-handshake-o" aria-hidden="true" style="color:blue;"></i> Refer </a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-approve-task"><i class="fa fa-gavel" aria-hidden="true" style="color:green;"></i> Approve task</a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-edit-task"><i class="fa fa-pencil" style="color:blue;"></i> Edit</a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-delete-task"><i class="fa fa-trash" style="color:red;"></i> Delete</a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-copy-task" onclick='onCopyTask(this)'><i class="fa fa-copy" style="color:skyblue;"></i> Copy task </a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-share-task" onclick='copyAndShareTask(this)'><i class="fa fa-share-square" aria-hidden="true" style="color:blue;"></i> Sharing task </a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-request-task-list" onclick='requestOfTask(this)'><i class="fa fa-list" aria-hidden="true" style="color:skyblue;"></i> Requests of task </a>
    </div>
    <div class="list-group" id="list_for_taskrequest" style="display: none;">
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-approve-task"><i class="fa fa-gavel" aria-hidden="true" style="color:green;"></i> Approve task</a>
        <a href="javascript:void(0)" class="list-group-item list-group-item-action btn-edit-task"><i class="fa fa-pencil" style="color:blue;"></i> Edit</a>
        <a href="javascript:void(0)" data-project_id="<?= $project_id ?>" class="list-group-item list-group-item-action btn-request-task"><i class="fa fa-paper-plane" aria-hidden="true" style="color:green;"></i> Request task </a>

    </div>
</div>
<?php
$addSubBtn = '<a href="javascript:void(0)" style="font-size:14px;" class="btn btn-success btn-xs" onclick=\'addNewCategory("' . $cate_ezf_id . '","' . $project_id . '")\' title="Add new Sub-Task"><i class="fa fa-plus"></i> </a>';
$addTaskBtn = '<a href="javascript:void(0)" style="font-size:14px;" class="btn btn-info btn-xs" onclick=\'addNewtask("' . $activity_ezf_id . '","' . $project_id . '","' . $project_id . '")\' title="Add new Task Item"><i class="fa fa-plus"></i> </a>';
$addMilestoneBtn = '<a href="javascript:void(0)" style="font-size:14px;" class="btn btn-warning btn-xs" onclick=\'addNewMilestone("' . $activity_ezf_id . '","' . $project_id . '","' . $project_id . '","milestone")\' title="Add new Milestone"><i class="fa fa-plus"></i> </a>';
?>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";

$btnCateAdd = "";
$permission = 1;
$ezmManage = 0;
$ownProject = false;
$myTask = [];
$mySubtask = [];

if (EzfAuthFuncManage::auth()->accessManage($module_id, 1))
    $ezmManage = 1;

$ownProject = GanttQuery::checkOwnData($projectForm, $project_id);
if (!$ownProject) {
    $dataCreate = cpn\chanpan\classes\utils\CNProject::getMyProject();
    if ($dataCreate['data_create']['user_create'] == $user_id) {
        $ownProject = true;
    }
}
if ($ownProject) {
    $btnCateAdd = backend\modules\ezforms2\classes\EzfHelper::btn($project_ezf_id)->options(['class' => 'btn btn-success btn-xs', "data-toggle" => "tooltip", 'title' => 'Add new main task'])->label('<i class="fa fa-plus"> </i>')->initdata(['pms_type' => $tab])->modal('modal-ezform-gantt')->buildBtnAdd();
}
$myTask = GanttQuery::getMytask($activityForm, $project_id);
$mySubtask = GanttQuery::getMySubtask($cateForm, $project_id);
$user_id = Yii::$app->user->id;
?>
<script>
    $(function () {
        $('#gantt_here').css('height', ($(window).height() - 200));
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
        var project_id = '<?= $project_id ?>';

        gantt.config.grid_width = 300;
        gantt.config.add_column = false;
        gantt.templates.grid_row_class = function (start_date, end_date, item) {
            if (item.priority == '3') {
                var user_id = '<?= $user_id ?>';
                var ownTask = JSON.parse('<?= $myTask ?>');
                if (item.myassign_accept == '1') {
                    return "blue_sky";
                } else if (item.myassign == '1') {
                    return "yellow_thin";
                } else if (item.new_task == 1) {
                    return "new_task_added";
                }
                return "";
            } else if (item.new_task == 1) {
                return "new_task_added";
            }

        };
        var dateEditor = {type: "date", map_to: "start_date", min: new Date(2018, 0, 1), max: new Date(2019, 0, 1)};
        gantt.config.columns = [
//             {
//                 name: "share_from", label: "", resize: false, width: 70, template: function (obj) {
//                     if (obj.sub_type && obj.sub_type === '2' && obj.priority == '3') {
//                         var html = '';
//                         if (obj.status == '3') {
//                             html = '<label style="color:#FF2A00;font-size:16px;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></label>';
//                         } else if (obj.status == '2') {
//                             html = '<label style="color:#EECD02;font-size:16px;"><i class="fa fa-info-circle" aria-hidden="true"></i></label>';
//                         } else if (obj.status == '4') {
//                             html = '<label style="color:#5CEC05;font-size:16px;"><i class="fa fa-check-circle" aria-hidden="true"></i></label>';
//                         }

//                         return html;
//                     } else {
//                         if (obj.share_from) {
//                             return '<label style="color:blue;"><i class="fa fa-share" title="Shared from ' + obj.share_from + '" aria-hidden="true"></i></label>';
//                         } else {
//                             html = '';
//                             if (obj.status == '3') {
//                                 html = '<label style="color:#FF2A00;font-size:16px;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></label>';
//                             } else if (obj.status == '2') {
//                                 html = '<label style="color:#EECD02;font-size:16px;"><i class="fa fa-info-circle" aria-hidden="true"></i></label>';
//                             } else if (obj.status == '4') {
//                                 html = '<label style="color:#5CEC05;font-size:16px;"><i class="fa fa-check-circle" aria-hidden="true"></i></label>';
//                             }

//                             return html;
//                         }
//                     }
//                     return '';
//                 }
//             },
            {
                name: "text", label: "Task Name", tree: true, resize: true, width: 250, template: function (dat) {
                    var html = '';
                    if (dat.sub_type && dat.sub_type === '2' && dat.priority == '3') {
                        var html = '';
                        if (dat.status == '3') {
                            html = '<label style="color:#FF2A00;font-size:16px;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></label>';
                        } else if (dat.status == '2') {
                            html = '<label style="color:#EECD02;font-size:16px;"><i class="fa fa-info-circle" aria-hidden="true"></i></label>';
                        } else if (dat.status == '4') {
                            html = '<label style="color:#5CEC05;font-size:16px;"><i class="fa fa-check-circle" aria-hidden="true"></i></label>';
                        }

                        //return html;
                    } else {
                        if (dat.share_from) {
                            return '<label style="color:blue;"><i class="fa fa-share" title="Shared from ' + dat.share_from + '" aria-hidden="true"></i></label>';
                        } else {
                            html = '';
                            if (dat.status == '3') {
                                html = '<label style="color:#FF2A00;font-size:16px;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></label>';
                            } else if (dat.status == '2') {
                                html = '<label style="color:#EECD02;font-size:16px;"><i class="fa fa-info-circle" aria-hidden="true"></i></label>';
                            } else if (dat.status == '4') {
                                html = '<label style="color:#5CEC05;font-size:16px;"><i class="fa fa-check-circle" aria-hidden="true"></i></label>';
                            }

                            //return html;
                        }
                    }

                    if (dat.priority == '1') {
                        return " <div class='task_name_column' data-user_create='" + dat.user_create + "' data-dataid='" + dat.dataid + "' data-taskid='" + dat.id + "' data-data_task_type='" + dat.type + "' style='font-weight: bold;' data-data_type='" + dat.priority + "'>" + html + " <span class='gantt_grid_text'>" + dat.text + " </span> </div>";
                    } else if (dat.priority == '2') {
                        return " <div class='task_name_column' data-user_create='" + dat.user_create + "' data-dataid='" + dat.dataid + "' data-taskid='" + dat.id + "' data-data_task_type='" + dat.type + "' style='font-weight: bold;' data-data_type='" + dat.priority + "'>" + html + "<span class='gantt_grid_wbs'>" + ganttModules.wbs.getTaskPath(dat.id) + "</span> <span class='gantt_grid_text'>" + dat.text + " </span> </div>";
                    } else if (dat.sub_type == '2') {
                        return  " <div class='' data-user_create='" + dat.user_create + "' data-dataid='" + dat.dataid + "' data-taskid='" + dat.id + "' data-data_task_type='" + dat.type + "' data-data_type='" + dat.priority + "' style='color:#0235AF;'> " + html + "<span class='gantt_grid_wbs'>" + ganttModules.wbs.getTaskPath(dat.id) + "</span> <span class='gantt_grid_text'>" + dat.text + " </span> </div>";
                    } else {
                        return " <div class='task_name_column' data-user_create='" + dat.user_create + "' data-dataid='" + dat.dataid + "' data-data_task_type='" + dat.type + "' data-taskid='" + dat.id + "' data-data_type='" + dat.priority + "' style='color:#0235AF;'> " + html + "<span class='gantt_grid_wbs'>" + ganttModules.wbs.getTaskPath(dat.id) + "</span> <span class='gantt_grid_text'>" + dat.text + " </span> </div>";
                    }
                }
            },
            {
                name: "start_date", label: "Start Date", resize: true, width: 120, align: "center", template: function (dat) {
                    if (dat.priority == '3')
                        return "<div class='task_start_date_column' data-user_create='" + dat.user_create + "' data-dataid='" + dat.dataid + "' data-taskid='" + dat.id + "' data-value='" + dat.start_date + "' data-data_task_type='" + dat.type + "' data-data_type='" + dat.priority + "'>" + dat.start_date.toLocaleDateString() + "</div>";
                    else
                        return "<span style='font-weight: bold;'>" + dat.start_date.toLocaleDateString() + "</span>";

                }
            },
            {
                name: "finish_date", label: "Due Date", resize: true, width: 120, align: "center", template: function (dat) {
                    if (dat.priority == '3')
                        return "<div class='task_end_date_column' data-user_create='" + dat.user_create + "' data-dataid='" + dat.dataid + "' data-taskid='" + dat.id + "' data-value='" + dat.end_date + "' data-data_task_type='" + dat.type + "' data-data_type='" + dat.priority + "'>" + dat.end_date.toLocaleDateString() + "</div>";
                    else
                        return "<span style='font-weight: bold;'>" + dat.end_date.toLocaleDateString() + "</span>";
                }
            },
            {
                name: "credit_points", label: "Credit points", align: "center", resize: true, width: 80
            },
            {
                name: "reward_points", label: "Reward points", align: "center", resize: true, width: 80
            },
            {name: "duration", label: "Duration", align: "center", resize: true, width: 40},
            {
                name: "progress", label: "Progress", align: "center", resize: true, width: 80, template: function (dat) {
                    if (dat.priority == '3')
                        return Math.round(dat.progress * 100) + " %";
                    else
                        return "<span style='font-weight: bold;'>" + Math.round(dat.progress * 100) + " %" + "</span>";
                }
            },
            {
                name: "priority", label: "Type", align: "center", resize: true, width: 80, template: function (obj) {
                    if (obj.priority == 1) {
                        return "<span style='font-weight: bold;'>Main Task</span>";
                    }
                    if (obj.priority == 2) {
                        return "<span style='font-weight: bold;'> Sub-task </span>";
                    }
                    if (obj.type_task == 'milestone') {
                        return "Milestone";
                    }
                    return obj.task_item_type;
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
            {
                name: "task_status", label: "Status", align: "center", width: 80, resize: true, template: function (obj) {
                    if (obj.task_status == '1') {
                        return "<label class='label label-info'>On-going</label>";
                    } else if (obj.task_status == '2') {
                        return "<label class='label label-warning' title='Waiting for review'>RE</label>";
                    } else if (obj.task_status == '3') {
                        return "<label class='label label-success'>Completed</label>";
                    } else if (obj.task_status == '4') {
                        return "<label class='label label-default'>Abandoned</label>";
                    } else if (obj.task_status == '5') {
                        return "<label class='label label-warning'>Pending</label>";
                    } else if (obj.task_status == '6') {
                        return "<label class='label label-primary' title='Waiting for approve'>APP</label>";
                    }

                    return '';

                }
            },
            {
                name: "assign_to", label: "Assigned", align: "center", resize: false, width: 80, template: function (obj) {
                    if (obj.assign_avatar && obj.assign_avatar != '') {
                        var pos = -30;
                        var pos_inc = 0;
                        var avatar_img = "<div style='position:relative;'>";
                        if (obj.assign_role_full && obj.assign_role_full != '') {
                            avatar_img += "<label class='img-circle' style='color:#1184D3;position:absolute;left:" + (-35) + "px;' title='" + obj.assign_role_full + "' width='30px' >" + obj.assign_role_short + "</label>";
                            pos_inc = 10;
                        }
                        $.each(obj.assign_avatar, function (i, e) {
                            if (obj.assign_avatar.length == 1)
                                pos = -20;
                            if (obj.assign_avatar.length == 2)
                                pos = -25;
                            avatar_img += "<img class='img-circle' style='position:absolute;left:" + (pos + (i * 10) + pos_inc) + "px;' alt='Cinque Terre' title='" + obj.assign_user + "' width='30px' src='" + e + "'>";
                        });

                        if (obj.approved == '0' && (obj.credit_points != '' || obj.reward_points != '')) {
                            avatar_img += " <i class='fa fa-clock-o' title='Waiting approve by director' style='color:orange;font-size:16px;margin-left:10px;'></i>";
                        }
                        return avatar_img + "</div>";
                    } else if (obj.assign_role_full && obj.assign_role_full != '') {
                        var pos = -30;
                        var avatar_role = "<div style='position:relative;'>";
                        avatar_role += "<label class='img-circle' style='color:#1184D3;' title='" + obj.assign_role_full + "' width='30px' >" + obj.assign_role_short + "</label>";

                        return avatar_role + "</div>";
                    } else if (obj.myrequest == '1') {
                        return "<label class='label label-warning'>Wait</label>";
                    } else if ((obj.request_type == '1' || obj.request_type == '2') && obj.assign_user_state == '0') {
                        if ((obj.credit_points == '' && obj.reward_points == '')) {
                            if (obj.request_new_amt > 0) {
                                return "<i class='fa fa-shopping-cart' title='Ready for shopping' style='color:skyblue;font-size:16px;'></i> <label class='label label-danger'>" + obj.request_new_amt + "</label>";
                            }
                            return "<i class='fa fa-shopping-cart' title='Ready for shopping' style='color:skyblue;font-size:16px;'></i>";

                        } else {
                            if (obj.approved == '0') {
                                return "<i class='fa fa-clock-o' title='Waiting approve by director' style='color:orange;font-size:16px;'></i>";
                            } else {
                                if (obj.request_new_amt > 0) {
                                    return "<i class='fa fa-shopping-cart' title='Ready for shopping' style='color:skyblue;font-size:16px;'></i> <label class='label label-danger'>" + obj.request_new_amt + "</label>";
                                }
                                return "<i class='fa fa-shopping-cart' title='Ready for shopping' style='color:skyblue;font-size:16px;'></i>";
                            }
                        }
                    }
                    return "";
                }
            },
            {
                name: "ezf_id", label: `<?= $addSubBtn . ' ' . $addTaskBtn . ' ' . $addMilestoneBtn ?>`, resize: false, width: 110, align: "center", template: function (obj) {
                    var permiss = '<?= $permission ?>';
                    var ownProject = '<?= $ownProject ?>';
                    var ownTask = JSON.parse('<?= $myTask ?>');
                    var ownSubTask = JSON.parse('<?= $mySubtask ?>');
                    var userId = '<?= $user_id ?>';
                    var ezmModule = '<?= $ezmManage ?>';

                    if (obj.sub_type && obj.sub_type === '2') {
                        if (obj.priority == '5' && ezmModule == '1' && ownProject == 1) {
                            var html = '<a href="javascript:void(0)" style="font-size:16px;color:green;" title="Add new task"  class="" onclick=\'addDataEzform(' + JSON.stringify(obj) + ')\'><i class="fa fa-plus"></i></a>';
                            if (obj.actual_date && obj.actual_date !== '') {
                                html = '<a href="javascript:void(0)" style="font-size:16px;color:blue;" title="Add new task"  class="" onclick=\'addDataEzform(' + JSON.stringify(obj) + ')\'><i class="fa fa-pencil"></i></a>';
                            }
                        } else if (obj.priority == '2') {
                            html = ' <a href="javascript:void(0)" style="font-size:16px;color:#3C6EE3;" title="Edit this sub-task" class="btn-edit-project" data-ezf_id="' + obj.ezf_id + '" data-taskid="' + obj.id + '" data-task_dataid="' + obj.dataid + '" data-type="category" ><i class="fa fa-pencil"></i></a>';
                            html += ' <a href="javascript:void(0)" style="font-size:16px;color:#F04141;" title="Delete this sub-task" class="btn-delete-task" data-priority="' + obj.priority + '" data-ezf_id="' + obj.ezf_id + '" data-taskid="' + obj.id + '" data-task_dataid="' + obj.dataid + '" data-parentid="' + obj.parent + '" ><i class="fa fa-trash"></i></a>';
                            html += ' <a href="javascript:void(0)" style="font-size:16px;cursor: copy;" title="Copy and Share" onclick=\'copyAndShareTask("' + obj.id + '","' + obj.ezf_id + '","sub-task")\' class="btn-copy-task" data-ezf_id="' + obj.ezf_id + '" data-id="' + obj.id + '" data-type="sub-task"><i class="fa fa-copy"></i></a>';
                        } else {
                            html = '';
                        }

                        
                        return html;
                    } else {
                        if (obj.priority == '1') {
                            if (permiss == '1' && ownProject == 1) {
//                                var html = '<a href="javascript:void(0)" style="font-size:16px;color:green;"  class="" onclick=\'addNewCategory("' + obj.category + '","' + obj.id + '")\'><i class="fa fa-plus"></i></a>';
//                                html += ' <a href="javascript:void(0)" style="font-size:16px;color:#3C6EE3;"  class="btn-edit-project" data-ezf_id="' + obj.ezf_id + '" data-id="' + obj.id + '" data-type="project"><i class="fa fa-pencil"></i></a>';
//                                html += ' <a href="javascript:void(0)" style="font-size:16px;color:#F04141;" class="btn-delete-project" data-ezf_id="' + obj.ezf_id + '" data-id="' + obj.id + '" ><i class="fa fa-trash"></i></a>';
                                //html += ' <a href="javascript:void(0)" style="font-size:16px;" class="btn-copy-task" onclick=\'copyAndShareTask("' + obj.id + '","' + obj.ezf_id + '","main-task")\' data-ezf_id="' + obj.ezf_id + '" data-id="' + obj.id + '" ><i class="fa fa-copy"></i></a>';

                                return '';
                            }
                        } else
                        if (obj.priority == '2' && (obj.id && obj.id !== "null")) {
                            if (permiss == '1' && ownProject == '1' || $.inArray(obj.id, ownSubTask) !== -1 || userId == obj.user_create) {
                                var html = '<a href="javascript:void(0)" style="font-size:16px;color:green;" title="Add new task"  class="" onclick=\'addNewtask("' + obj.ezf_id + '","' + obj.category + '","' + obj.target + '")\'><i class="fa fa-plus"></i></a>';
                                html += ' <a href="javascript:void(0)" style="font-size:16px;color:#3C6EE3;" title="Edit this sub-task" class="btn-edit-project" data-ezf_id="' + obj.ezf_id + '" data-taskid="' + obj.id + '" data-task_dataid="' + obj.dataid + '" data-type="category" ><i class="fa fa-pencil"></i></a>';
                                html += ' <a href="javascript:void(0)" style="font-size:16px;color:#F04141;" title="Delete this sub-task" class="btn-delete-task" data-priority="' + obj.priority + '" data-ezf_id="' + obj.ezf_id + '" data-taskid="' + obj.id + '" data-task_dataid="' + obj.dataid + '" data-parentid="' + obj.parent + '" ><i class="fa fa-trash"></i></a>';
                                html += ' <a href="javascript:void(0)" style="font-size:16px;cursor: copy;" title="Copy and Share" onclick="copyAndShareTask(this)" class="btn-copy-task" data-ezf_id="' + obj.ezf_id + '" data-taskid="' + obj.id + '" data-task_dataid="' + obj.dataid + '" data-task_type="sub-task"><i class="fa fa-copy"></i></a>';

                                return html;
                            } else {
                                var html = '';
                                //html = '<a href="javascript:void(0)" style="font-size:16px;color:green;" title="Add new task"  class="" onclick=\'addNewtask("' + obj.ezf_id + '","' + obj.category + '","' + obj.target + '")\'><i class="fa fa-plus"></i></a>';
                                //html += ' <a href="javascript:void(0)" style="font-size:16px;cursor: copy;" title="Copy and Share" class="btn-copy-task" data-ezf_id="' + obj.ezf_id + '" data-id="' + obj.id + '" data-type="sub-task" onclick=\'copyAndShareTask("' + obj.id + '","' + obj.ezf_id + '","sub-task")\'><i class="fa fa-copy"></i></a>';

                                return html;
                            }
                        } else if (obj.priority == '3' && (obj.id && obj.id !== "null")) {
                            if (ownProject == '1' || $.inArray(obj.id, ownTask) !== -1 || userId == obj.user_create || obj.is_co_owner == '1') {
                                var html = ' <a href="javascript:void(0)" style="font-size:16px;color:#3C6EE3;" title="Edit this task" class="btn-edit-task" data-ezf_id="' + obj.ezf_id + '" data-taskid="' + obj.id + '" data-task_dataid="' + obj.dataid + '" data-type="task"><i class="fa fa-pencil"></i></a>';
                                html += ' <a href="javascript:void(0)" style="font-size:16px;color:#F04141;" title="Delete this task" class="btn-delete-task" data-priority="' + obj.priority + '" data-ezf_id="' + obj.ezf_id + '" data-taskid="' + obj.id + '" data-task_dataid="' + obj.dataid + '" data-parentid="' + obj.parent + '" ><i class="fa fa-trash"></i></a>';
                                html += ' <a href="javascript:void(0)" style="font-size:16px;cursor: copy;" class="btn-copy-task" data-ezf_id="' + obj.ezf_id + '" data-taskid="' + obj.id + '" data-task_dataid="' + obj.dataid + '" data-task_type="task" onclick="copyAndShareTask(this)"><i class="fa fa-copy"></i></a>';
                                return html;
                            }
                        }
                    }

                    return "";
                }
            },
        ];

//        gantt.templates.tooltip_text = function (start, end, task) {
//            return "<b>Task:</b> " + task.text + "<br/> <b>Start date:</b> " + getDateInputFormat(task.start_date) + "<br/> <b>End date:</b> " + getDateInputFormat(task.end_date) + "<br/> <b>Assign to:</b> " + task.assign_user;
//        };

        gantt.templates.rightside_text = function (start, end, task) {
            if (task.deadline) {
                if (end.valueOf() > task.deadline.valueOf()) {
                    var overdue = Math.ceil(Math.abs((end.getTime() - task.deadline.getTime()) / (24 * 60 * 60 * 1000)));
                    var text = "<b>Overdue: " + overdue + " days</b>";
                    return text;
                }
            }
        };

//    gantt.config.scale_unit = "month";
//    gantt.config.date_scale = "%F, %Y";
//        gantt.attachEvent("onTemplatesReady", function () {
//            var toggle = document.getElementById("gantt-fullscreen");
//            toggle.onclick = function () {
//                if (!gantt.getState().fullscreen) {
//                    gantt.expand();
//                } else {
//                    gantt.collapse();
//                }
//            };
//        });

        gantt.config.scale_unit = "month";

        gantt.config.date_scale = "%F";
        //gantt.config.date_scale = "%j, %D";
        gantt.config.scale_height = 50;

        gantt.config.subscales = [
            {unit: "<?= $unit ?>", step: <?= $step ?>, date: "%j, %D"}
        ];

        gantt.config.row_height = 30;
        gantt.config.order_branch = true;
        gantt.config.order_branch_free = true;

        var ops = <?= json_encode($options_pms['column_show']) ?>;
        gantt.config.sort = true;
        gantt.config.columns[1].sort = true;
        gantt.config.columns[5].sort = false;
        gantt.config.columns[6].sort = false;
        gantt.config.columns[7].sort = false;
        gantt.config.columns[9].sort = false;
        gantt.config.grid_resize = true;
        gantt.config.xml_date = '%Y-%m-%d %H:%i:%s';

        $.each(ops, function (i, e) {
            gantt.getGridColumn(e).hide = true;
        });


        gantt.config.auto_scheduling = true;
        gantt.config.auto_scheduling_strict = true;
        gantt.init('gantt_here');
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

        gantt.config.font_width_ratio = 7;
        gantt.templates.leftside_text = function leftSideTextTemplate(start, end, task) {
            if (getTaskFitValue(task) === "left") {
                return task.text;
            }
            return "";
        };
        gantt.templates.rightside_text = function rightSideTextTemplate(start, end, task) {
            if (getTaskFitValue(task) === "right") {
                return task.text;
            }
            return "";
        };

        gantt.templates.task_text = function (start, end, task) {
            if (getTaskFitValue(task) === "center") {
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
            }
            return "";
        };

        gantt.attachEvent("onContextMenu", function (taskId, linkId, event) {
            var permiss = '<?= $permission ?>';
            var ownProject = '<?= $ownProject ?>';
            var ownTask = JSON.parse('<?= $myTask ?>');
            var ownSubTask = JSON.parse('<?= $mySubtask ?>');
            var userId = '<?= $user_id ?>';
            var ezmModule = '<?= $ezmManage ?>';

            var task = gantt.getTask(taskId);
            var x = (event.pageX - $('#gantt_here').offset().left) + 20;
            var y = (event.pageY - ($('#gantt_here').offset().top / 2)) - 30;
            var screenX = event.screenX;
            var screenY = event.screenY;
            var screenMx = $(window).height();

            if (screenY > (screenMx - $('#contextAction').height() / 2)) {
                if (task.priority == '2') {
                    if (permiss == '1' && ownProject == '1' || $.inArray(task.id, ownSubTask) !== -1 || userId == task.user_create) {
                        y = event.pageY - ($('#gantt_here').offset().top);
                    } else {
                        y = event.pageY - ($('#gantt_here').offset().top) + $('#contextAction').height();
                    }
                } else if (task.priority == '3') {
                    if (ownProject == '1' || userId == task.user_create || task.isco_owner == '1') {
                        y = event.pageY - ($('#gantt_here').offset().top - 30);
                    } else if ($.inArray(task.id, ownTask) !== -1 || task.myassign_accept == '1') {
                        y = event.pageY - ($('#gantt_here').offset().top - 130);
                    } else if (task.request_type == '1' || task.request_type == '2') {
                        y = event.pageY - ($('#gantt_here').offset().top - 230);
                    }
                }
            }

            if (taskId) {

                //console.log(task);
                if (task.priority == '1') {
                    var anchor = $('#contextAction').find('#list_for_maintask').find('a');
                    $.each(anchor, function (i, e) {
                        $(e).attr('data-taskid', taskId);
                        $(e).attr('data-task_dataid', task.dataid);
                        $(e).attr('data-ezf_id', task.ezf_id);
                        $(e).attr('data-task_type', task.type_task);
                        $(e).attr('data-priority', task.priority);
                    });
                    $('#contextAction').css('display', 'block');
                    $('#contextAction').find('#list_for_maintask').css('display', 'block');
                    $('#contextAction').find('#list_for_subtask').css('display', 'none');
                    $('#contextAction').find('#list_for_taskitem').css('display', 'none');
                    $('#contextAction').find('#list_for_taskrequest').css('display', 'none');
                } else if (task.priority == '2') {
                    var anchor = $('#contextAction').find('#list_for_subtask').find('a');
                    $.each(anchor, function (i, e) {
                        $(e).attr('data-taskid', taskId);
                        $(e).attr('data-task_dataid', task.dataid);
                        $(e).attr('data-ezf_id', task.ezf_id);
                        $(e).attr('data-task_type', task.type_task);
                        $(e).attr('data-priority', task.priority);
                    });
                    if (permiss == '1' && ownProject == '1' || $.inArray(task.id, ownSubTask) !== -1 || userId == task.user_create) {
                        $('#contextAction').css('display', 'block');

                        $('#contextAction').find('#list_for_subtask').css('display', 'block');
                        $('#contextAction').find('#list_for_taskitem').css('display', 'none');
                        $('#contextAction').find('#list_for_taskrequest').css('display', 'none');
                        $('#contextAction').find('#list_for_maintask').css('display', 'none');
                    }
                } else if (task.priority == '3') {
                    var isdirector = '<?= $isdirector ?>';
                    if ($.inArray(task.id, ownTask) !== -1 || task.myassign_accept == '1' || userId == task.user_create || ownProject == '1' || task.isco_owner == '1' || task.isreviewer == '1' || task.isapprover == '1' || isdirector == '1') {
                        if (!task.request_new_amt)
                            task.request_new_amt = 0;
                        $('#contextAction').css('display', 'block');
                        $('#list_for_taskitem').parent().find('.btn-request-task-list').find('label').remove();
                        $('#list_for_taskitem').parent().find('.btn-request-task-list').append(" <label class='label label-danger'> " + task.request_new_amt + "</label>");
                        var anchor = $('#contextAction').find('#list_for_taskitem').find('a');
                        $.each(anchor, function (i, e) {
                            if (userId == task.user_create || ownProject == '1' || task.isco_owner == '1') {
                                $(e).parent().find('.btn-request-task-list').css('display', 'block');

                                $(e).parent().find('.btn-edit-task').css('display', 'block');
                                $(e).parent().find('.btn-delete-task').css('display', 'block');
                                $(e).parent().find('.btn-copy-task').css('display', 'block');
                                $(e).parent().find('.btn-share-task').css('display', 'block');
                                $(e).parent().find('.btn-accept-task').css('display', 'none');
                                $(e).parent().find('.btn-decline-task').css('display', 'none');
                                $(e).parent().find('.btn-terminate-task').css('display', 'none');
                                $(e).parent().find('.btn-pending-task').css('display', 'none');
                                $(e).parent().find('.btn-refer-task').css('display', 'none');
                                $(e).parent().find('.btn-approve-task').css('display', 'none');

                                if (task.myassign == '1' && task.myassign_accept == '0') {
                                    $(e).parent().find('.btn-accept-task').css('display', 'block');
                                    $(e).parent().find('.btn-decline-task').css('display', 'block');
                                }
                            } else {
                                $(e).parent().find('.btn-request-task-list').css('display', 'none');
                                $(e).parent().find('.btn-edit-task').css('display', 'none');
                                $(e).parent().find('.btn-delete-task').css('display', 'none');
                                $(e).parent().find('.btn-copy-task').css('display', 'none');
                                $(e).parent().find('.btn-share-task').css('display', 'none');
                                $(e).parent().find('.btn-response-task').css('display', 'none');
                                $(e).parent().find('.btn-terminate-task').css('display', 'none');
                                $(e).parent().find('.btn-pending-task').css('display', 'none');
                                $(e).parent().find('.btn-refer-task').css('display', 'none');
                                $(e).parent().find('.btn-accept-task').css('display', 'none');
                                $(e).parent().find('.btn-decline-task').css('display', 'none');
                                $(e).parent().find('.btn-approve-task').css('display', 'none');
                                
                                if (task.myassign == '1' && task.myassign_accept == '0') {
                                    $(e).parent().find('.btn-accept-task').css('display', 'block');
                                    $(e).parent().find('.btn-decline-task').css('display', 'block');
                                } else if (task.myassign_accept == '1') {
                                    $(e).parent().find('.btn-response-task').css('display', 'block');
                                    $(e).parent().find('.btn-terminate-task').css('display', 'block');
                                    $(e).parent().find('.btn-pending-task').css('display', 'block');
                                    $(e).parent().find('.btn-refer-task').css('display', 'block');
                                } else if (task.isreviewer == '1' || task.isapprover == '1' || isdirector == '1') {
                                    $(e).parent().find('.btn-response-task').css('display', 'block');
                                }
                                
                                if ((isdirector == '1') && (task.credit_points != '' || task.reward_points != '')) {
                                    
                                    if(task.approved == '0')
                                        $(e).parent().find('.btn-approve-task').css('display', 'block');
                                
                                    $(e).parent().find('.btn-edit-task').css('display', 'block');
                                } 
                            }
                            $(e).attr('data-taskid', taskId);
                            $(e).attr('data-task_dataid', task.dataid);
                            $(e).attr('data-ezf_id', task.ezf_id);
                            $(e).attr('data-type', task.type);
                            $(e).attr('data-task_type', task.type_task);
                            $(e).attr('data-isviwer', task.isviwer);
                            $(e).attr('data-isapprover', task.isviwer);
                            $(e).attr('data-priority', task.priority);
                        });
                        $('#contextAction').find('#list_for_subtask').css('display', 'none');
                        $('#contextAction').find('#list_for_taskrequest').css('display', 'none');
                        $('#contextAction').find('#list_for_taskitem').css('display', 'block');
                        $('#contextAction').find('#list_for_maintask').css('display', 'none');
                    } else if (task.request_type == '1' || task.request_type == '2') { /// Task is not yet to assign

                        if (((task.assign_user == "" || task.assign_user == null) && (task.credit_points == '' && task.reward_points == ''))) {
                            $('#contextAction').css('display', 'block');
                            var anchor = $('#contextAction').find('#list_for_taskrequest').find('a');
                            $.each(anchor, function (i, e) {
                                $('#contextAction').find('#list_for_taskrequest').css('display', 'block');
                                $(e).attr('data-myrequestid', task.myrequestid);
                                $(e).attr('data-taskid', taskId);
                                $(e).attr('data-task_dataid', task.dataid);
                                $(e).attr('data-request_type', task.request_type);
                                $(e).attr('data-isviwer', task.isviwer);
                                $(e).attr('data-isapprover', task.isviwer);
                                $(e).attr('data-priority', task.priority);

                                $(e).parent().find('.btn-approve-task').css('display', 'none');
                                $(e).parent().find('.btn-edit-task').css('display', 'none');

                            });
                            $('#contextAction').find('#list_for_taskrequest').css('display', 'block');
                            $('#contextAction').find('#list_for_subtask').css('display', 'none');
                            $('#contextAction').find('#list_for_taskitem').css('display', 'none');
                            $('#contextAction').find('#list_for_maintask').css('display', 'none');


                        } else if (((task.assign_user == "" || task.assign_user == null) && (task.credit_points != '' || task.reward_points != ''))) {
                            $('#contextAction').css('display', 'block');
                            var anchor = $('#contextAction').find('#list_for_taskrequest').find('a');
                            $.each(anchor, function (i, e) {
                                $('#contextAction').find('#list_for_taskrequest').css('display', 'block');
                                $(e).attr('data-myrequestid', task.myrequestid);
                                $(e).attr('data-taskid', taskId);
                                $(e).attr('data-task_dataid', task.dataid);
                                $(e).attr('data-request_type', task.request_type);
                                $(e).attr('data-priority', task.priority);
                                $(e).parent().find('.btn-approve-task').css('display', 'none');
                                $(e).parent().find('.btn-edit-task').css('display', 'none');
                                $(e).parent().find('.btn-request-task').css('display', 'none');

                                if ((isdirector == '1') && task.approved == '0') {
                                    $(e).parent().find('.btn-approve-task').css('display', 'block');
                                    $(e).parent().find('.btn-edit-task').css('display', 'block');
                                } else if (task.approved == '1') {
                                    $(e).parent().find('.btn-request-task').css('display', 'block');
                                }
                            });
                            $('#contextAction').find('#list_for_taskrequest').css('display', 'block');
                            $('#contextAction').find('#list_for_subtask').css('display', 'none');
                            $('#contextAction').find('#list_for_taskitem').css('display', 'none');
                            $('#contextAction').find('#list_for_maintask').css('display', 'none');


                        }
                    }
                } else {
                    $('#contextAction').css('display', 'none');
                }

                $(window).scroll(function () {
                    $('#contextAction').css('display', 'none');
                });

                $(window).mousedown(function (e) {
                    if (!$(e.target).hasClass('list-group-item')) {
                        $('#contextAction').css('display', 'none');
                    }
                });
                $(window).mouseup(function (e) {
                    if ($(e.target).hasClass('list-group-item')) {
                        $('#contextAction').css('display', 'none');
                    }
                });

                $('#contextAction').css('top', y);
                $('#contextAction').css('left', x);

                return false;
            }
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
                el.setAttribute('title', "Task Finished at " + gantt.templates.task_date(task.actual_date));
                return el;
            }
        });

        onInitGantt();

        setTimeout(function () {
            showDate(new Date());
        }, 2000);


    });
    function getTaskFitValue(task) {
        var taskStartPos = gantt.posFromDate(task.start_date),
                taskEndPos = gantt.posFromDate(task.end_date);

        var width = taskEndPos - taskStartPos;
        var textWidth = (task.text || "").length * gantt.config.font_width_ratio;

        if (width < textWidth) {
            var ganttLastDate = gantt.getState().max_date;
            var ganttEndPos = gantt.posFromDate(ganttLastDate);
            if (ganttEndPos - taskEndPos < textWidth) {
                return "left"
            } else {
                return "right"
            }
        } else {
            return "center";
        }
    }
    function onInitGantt() {
        var date_to_str = gantt.date.date_to_str(gantt.config.task_date);
        var now = new Date();
        var toDate = now.getFullYear() + '-' + (now.getMonth() + 1) + '-' + now.getDate();
        var scale_filter = $('.scale_filter_selector').val();
        var user_filter = $('#config_all_user').val();
        gantt.addMarker({
            start_date: now,
            css: "today",
            text: "Today",
            title: "Today: " + date_to_str(now)
        });

        var project_id = '<?= $project_id ?>';
        var not_accept = $('#not_accept_amt');
        var mytask_amt = $('#mytask_amt');
        var wait_approve = $('#wait_approve_amt');
        var shopping_amt = $('#shopping_amt');

        $.get('/gantt/gantt/get-header-amount', {project_id: project_id}, function (result) {
            not_accept.html(result.not_accept_amt);
            mytask_amt.html(result.mytask_amt);
            wait_approve.html(result.wait_approve_amt);
            shopping_amt.html(result.shopping_amt);
        });


        gantt.load('/gantt/gantt/connector-pms-target?widget_id=<?= $widget_id ?>&schedule_widget_id=<?= $schedule_id ?>&project_ezf_id=<?= $project_ezf_id ?>&activity_ezf_id=<?= $activity_ezf_id ?>&response_ezf_id=<?= $response_ezf_id ?>&start_date=<?= $start_date ?>&finish_date=<?= $finish_date ?>&progress=<?= $progress ?>&project_name=<?= $project_name ?>&cate_name=<?= $cate_name ?>&task_name=<?= $task_name ?>&project_id=' + project_id + '&scale_filter=' + scale_filter + "&user_filter=" + user_filter);
        var dp = new gantt.dataProcessor('/gantt/gantt/connector-pms-target?widget_id=<?= $widget_id ?>&schedule_widget_id=<?= $schedule_id ?>&project_ezf_id=<?= $project_ezf_id ?>&activity_ezf_id=<?= $activity_ezf_id ?>&response_ezf_id=<?= $response_ezf_id ?>&start_date=<?= $start_date ?>&finish_date=<?= $finish_date ?>&progress=<?= $progress ?>&project_name=<?= $project_name ?>&cate_name=<?= $cate_name ?>&task_name=<?= $task_name ?>&project_id=' + project_id + '&scale_filter=' + scale_filter + "&user_filter=" + user_filter);

//        gantt.load('/gantt/gantt/connector-project?widget_id=<?= $widget_id ?>&schedule_id=<?= $schedule_id ?>&project_ezf_id=<?= $project_ezf_id ?>&cate_ezf_id=<?= $cate_ezf_id ?>&activity_ezf_id=<?= $activity_ezf_id ?>&response_ezf_id=<?= $response_ezf_id ?>&start_date=<?= $start_date ?>&finish_date=<?= $finish_date ?>&progress=<?= $progress ?>&project_name=<?= $project_name ?>&cate_name=<?= $cate_name ?>&task_name=<?= $task_name ?>&project_id=' + project_id + '&scale_filter=<?= $scale_filter ?>');
//        var dp = new gantt.dataProcessor('/gantt/gantt/connector-project?widget_id=<?= $widget_id ?>&schedule_id=<?= $schedule_id ?>&project_ezf_id=<?= $project_ezf_id ?>&cate_ezf_id=<?= $cate_ezf_id ?>&activity_ezf_id=<?= $activity_ezf_id ?>&response_ezf_id=<?= $response_ezf_id ?>&start_date=<?= $start_date ?>&finish_date=<?= $finish_date ?>&progress=<?= $progress ?>&project_name=<?= $project_name ?>&cate_name=<?= $cate_name ?>&task_name=<?= $task_name ?>&project_id=' + project_id + '&scale_filter=<?= $scale_filter ?>');
        dp.init(gantt);
    }

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
        var data_id = $(this).attr('data-task_dataid');
        var taskid = $(this).attr('data-taskid');
        var priority = $(this).attr('data-priority');
        var parentid = $(this).attr('data-parentid');
        var ezf_id = $(this).attr('data-ezf_id');

        yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete this item?') ?>', function () {

            $.post(
                    '/ezforms2/ezform-data/delete?ezf_id=' + ezf_id + '&dataid=' + data_id, {data_id: data_id, ezf_id: ezf_id, task_type: priority}
            ).done(function (result) {
                checkEvent = 0;
                $.post('/gantt/gantt/delete-pms-task', {dataid: data_id, ezf_id: ezf_id, parentid: parentid, task_type: priority}, function (result2) {
                    console.log("Delete target " + result2.status);
                });
                var tasks = gantt.getChildren(taskid);

                $.each(tasks, function (i, e) {
                    var dat = gantt.getTask(e);
                    $.post(
                            '/ezforms2/ezform-data/delete?ezf_id=' + dat.ezf_id + '&dataid=' + dat.dataid, {data_id: dat.dataid, ezf_id: dat.ezf_id}
                    ).done(function (result) {

                    });
                });

                gantt.deleteTask(taskid);
                gantt.refreshData();
                if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
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
        var data_id = $(this).attr('data-task_dataid');
        var taskid = $(this).attr('data-taskid');
        var ezf_id = $(this).attr('data-ezf_id');
        var type = $(this).attr('data-type');
        var dat = {id: taskid, dataid: data_id, ezf_id: ezf_id, text: 'edit', type_task: type};
        onModalAddGanttDialog(null, dat);
    });

    $(document).on('click', '.btn-response-task', function () {
        if (checkEvent == 1)
            return;
        checkEvent = 1;
        var data_id = $(this).attr('data-task_dataid');
        var taskid = $(this).attr('data-taskid');
        var ezf_id = $(this).attr('data-ezf_id');
        var type = $(this).attr('data-type');
        var task = gantt.getTask(taskid);
        onModalAddGanttDialog(null, task);
    });

    $(document).on('click', '.btn-edit-project', function () {
        if (checkEvent == 1)
            return;
        checkEvent = 1;
        var data_id = $(this).attr('data-task_dataid');
        var taskid = $(this).attr('data-taskid');
        var ezf_id = $(this).attr('data-ezf_id');
        var url = '<?=
Url::to(['/ezforms2/ezform-data/ezform',
    'modal' => 'modal-ezform-gantt',
]);
?>';
        $('#modal-ezform-gantt').modal();
        $('#modal-ezform-gantt').find('.modal-content').html('<div class="modal-body"><div class="sdloader"><i class="sdloader-icon"></i></div></div>');
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
    $('#check_col_financial').change(function () {
        var state_show = gantt.getGridColumn('financial').hide;
        gantt.getGridColumn('financial').hide = !state_show;
        gantt.render();
    });
    $('#check_col_task_status').change(function () {
        var state_show = gantt.getGridColumn('task_status').hide;
        gantt.getGridColumn('task_status').hide = !state_show;
        gantt.render();
    });
    $('#check_col_assign').change(function () {
        var state_show = gantt.getGridColumn('assign_to').hide;
        gantt.getGridColumn('assign_to').hide = !state_show;
        gantt.render();
    });
    $('#check_col_ezf_id').change(function () {
        var state_show = gantt.getGridColumn('ezf_id').hide;
        gantt.getGridColumn('ezf_id').hide = !state_show;
        gantt.render();
    });
    $('#check_col_credit_points').change(function () {
        var state_show = gantt.getGridColumn('credit_points').hide;
        gantt.getGridColumn('credit_points').hide = !state_show;
        gantt.render();
    });
    $('#check_col_reward_points').change(function () {
        var state_show = gantt.getGridColumn('reward_points').hide;
        gantt.getGridColumn('reward_points').hide = !state_show;
        gantt.render();
    });

    function onUpdateLink(data) {
        var widget_id = $('#data-widget_id').val();
        var schedule_id = $('#data-schedule_id').val();
        var project_id = '<?= $project_id ?>';

        var url = '/gantt/gantt/update-link';
        $.post(url, {dataList: JSON.stringify(data), widget_id: widget_id, schedule_id: schedule_id, project_id: project_id}, function (result) {
            if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
            }
        });
    }

    function onUpdateGantt(t, dat) {
        if (checkEvent == 1)
            return;
        checkEvent = 1;
        var permiss = '<?= $permission ?>';
        var ownProject = '<?= $ownProject ?>';
        var ownTask = JSON.parse('<?= $myTask ?>');
        var ownSubTask = JSON.parse('<?= $mySubtask ?>');
        var userId = '<?= $user_id ?>';
        var ezmModule = '<?= $ezmManage ?>';

        var taskDat = gantt.getTask(dat.id);
        if (ownProject == '1' || $.inArray(dat.id, ownTask) !== -1 || userId == dat.user_create) {
            var widget_id = $('#data-widget_id').val();
            var schedule_id = $('#data-schedule_id').val();
            var gantt_type = '<?= $gantt_type ?>';
            var activity_ezf_id = '<?= $activity_ezf_id ?>';
            var response_ezf_id = '<?= $response_ezf_id ?>';
            var url = '/gantt/gantt/update-gantt';
            if (dat.type_task == 'task' || dat.type_task == 'milestone') {
                $.post(url, {options: JSON.stringify(dat), gantt_type: gantt_type, activity_ezf_id: activity_ezf_id, response_ezf_id: response_ezf_id}, function (result) {
                    checkEvent = 0;
                });
            } else {
                checkEvent = 0;
            }
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
        var task_dataid = "";
        var data = {};
        var userId = '<?= $user_id ?>';
        var isdirector = '<?= $isdirector ?>';
        var main_shared = '<?=$main_shared?>';
        var obj = gantt.getTask(dat.id);

        var permiss = '<?= $permission ?>';
        var ownProject = '<?= $ownProject ?>';
        var ownTask = JSON.parse('<?= $myTask ?>');
        var ezform_work;
        var ezform_work_ops;
        var credit_points = obj.credit_points;
        var reward_points = obj.reward_points;
        var isreviewer = obj.isreviewer;
        var isapprover = obj.isapprover;
        if (isapprover != '1') {
            isapprover = isdirector;
        }
        var reward_points = obj.reward_points;
        var initdata = {};
        if (dat.text == "New task") {
            initdata = {project_id: proId};
            data = {initdata: JSON.stringify(initdata), modal: 'modal-ezform-gantt'};
        } else if (dat.text == "edit") {
            data_id = dat.dataid;
            data = {dataid: data_id, modal: 'modal-ezform-gantt', initdata: btoa(JSON.stringify({task_approved: obj.approved,is_director:isdirector,main_shared:main_shared}))};
        } else {

            data_id = dat.response_id;
            taskid = dat.id;
            task_dataid = dat.dataid;
            ezform_work = dat.ezform_work;
            ezform_work_ops = dat.ezform_work_ops;

            data = {dataid: data_id, taskid: task_dataid, task_id: taskid, modal: 'modal-ezform-gantt', ezform_work: ezform_work, ezform_work_ops: ezform_work_ops, credit_points: credit_points, reward_points: reward_points, isreviewer: isreviewer, isapprover: isapprover};
        }

        var url = '';
        if (dat.text == 'edit') {
            url = '<?=
Url::to(['/gantt/gantt/pms-task-form',
    'ezf_id' => $activity_ezf_id,
    'other_ezforms' => base64_encode(\appxq\sdii\utils\SDUtility::array2String($other_ezforms)),
]);
?>';
        } else {
            url = '<?=
Url::to(['/gantt/pms-response/index',
    'page_from' => 'pms',
    'ezf_id' => $activity_ezf_id,
    'response_ezf_id' => $response_ezf_id,
    'other_ezforms' => base64_encode(\appxq\sdii\utils\SDUtility::array2String($other_ezforms)),
    'action' => 'view',
        //'reloadDiv'=>'display-gantt',
]);
?>';
        }

        if (obj.sub_type != '2' && obj.ezf_id !== '' && (obj.type_task == 'task' || obj.type_task == 'milestone') && (ownProject == 1 || $.inArray(obj.id, ownTask) !== -1 || (userId == obj.user_create || obj.isco_owner == '1'))) {

            $('#modal-ezform-gantt').modal();
            $('#modal-ezform-gantt').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
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

        } else if (isdirector == '1' || obj.isapprover == '1' || obj.isreviewer == '1' || obj.myassign_accept == '1') {
            $('#modal-ezform-gantt').modal();
            $('#modal-ezform-gantt').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
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

    $('.btn-request-task').on('click', function () {
        var request_type = $(this).attr('data-request_type');
        var dataid = $(this).attr('data-task_dataid');
        var taskid = $(this).attr('data-taskid');
        var target = $(this).attr('data-project_id');
        var myrequestid = $(this).attr('data-myrequestid');
        var module_id = '<?= $module_id ?>';
        var user_id = '<?= $user_id ?>';
        var task = gantt.getTask(taskid);
        var initdata = {user_request: user_id, task_owner: task.user_create};
        var old_url = $('#request_task_send').attr('data-url');
        old_url = old_url + "&task_dataid=" + dataid + "&taskid=" + taskid + "&target=" + target + "&module_id=" + module_id;
        $('#request_task_send').attr('data-url', old_url);
        var txt = "You confirm to get task?";
        var url = '<?=
Url::to(['/ezforms2/ezform-data/ezform',
    'modal' => 'modal-ezform-main',
    'ezf_id' => $request_form['ezf_id'],
    'reloadDiv' => 'request_task_send'
]);
?>';
        if (request_type == '2') {
            $('#modal-ezform-gantt').modal();
            $('#modal-ezform-gantt').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
            $.get(url, {modal: 'modal-ezform-gantt', dataid: myrequestid, task_dataid: dataid, taskid: taskid, target: dataid, initdata: btoa(JSON.stringify(initdata))}, function (result) {
                $('#modal-ezform-gantt').find('.modal-content').empty();
                $('#modal-ezform-gantt').find('.modal-content').html(result);
            });
        } else {
            if (checkEvent == 1)
                return;
            checkEvent = 1;
            yii.confirm(txt, function () {
                checkEvent = 0;
                $.get('/gantt/gantt/request-task-save', {task_dataid: dataid, taskid: taskid, target: target, task_ezf_id: '<?= $activity_ezf_id ?>', module_id: module_id, task_owner: task.user_create}, function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>;
                    gantt.clearAll();
                    onInitGantt();

                });
            }, function () {
                checkEvent = 0;
            });
        }
    });

    $('.btn-approve-task').on('click', function () {
        var request_type = $(this).attr('data-request_type');
        var dataid = $(this).attr('data-task_dataid');
        var taskid = $(this).attr('data-taskid');
        var target = $(this).attr('data-project_id');
        var myrequestid = $(this).attr('data-myrequestid');
        var module_id = '<?= $module_id ?>';
        var task = gantt.getTask(taskid);
        var txt = "When approved will can't edit credit points and reward point. Confirm approve task?";
        if (checkEvent == 1)
            return;
        yii.confirm(txt, function () {
            checkEvent = 0;
            $.get('/gantt/gantt/approve-task-director', {task_dataid: dataid, taskid: taskid, target: target, task_ezf_id: '<?= $activity_ezf_id ?>', module_id: module_id, task_owner: task.user_create}, function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>;
                gantt.clearAll();
                onInitGantt();

            });
        }, function () {
            checkEvent = 0;
        });

    });

    $('.btn-accept-task').on('click', function () {
        var request_type = $(this).attr('data-request_type');
        var dataid = $(this).attr('data-task_dataid');
        var taskid = $(this).attr('data-taskid');
        var target = $(this).attr('data-project_id');
        var myrequestid = $(this).attr('data-myrequestid');
        var module_id = '<?= $module_id ?>';
        var task = gantt.getTask(taskid);

        $.get('/gantt/gantt/accept-task', {task_dataid: dataid, taskid: taskid, target: target, task_ezf_id: '<?= $activity_ezf_id ?>', module_id: module_id, task_owner: task.user_create}, function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>;
            gantt.clearAll();
            onInitGantt();

        });
    });

    $('.btn-terminate-task').on('click', function () {
        var request_type = $(this).attr('data-request_type');
        var dataid = $(this).attr('data-task_dataid');
        var taskid = $(this).attr('data-taskid');
        var target = $(this).attr('data-project_id');
        var myrequestid = $(this).attr('data-myrequestid');
        var module_id = '<?= $module_id ?>';
        var task = gantt.getTask(taskid);

        $.get('/gantt/gantt/terminate-task', {task_dataid: dataid, taskid: taskid, target: target, task_ezf_id: '<?= $activity_ezf_id ?>', module_id: module_id, task_owner: task.user_create}, function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>;
            gantt.clearAll();
            onInitGantt();

        });
    });

    $('.btn-pending-task').on('click', function () {
        var request_type = $(this).attr('data-request_type');
        var dataid = $(this).attr('data-task_dataid');
        var taskid = $(this).attr('data-taskid');
        var target = $(this).attr('data-project_id');
        var myrequestid = $(this).attr('data-myrequestid');
        var module_id = '<?= $module_id ?>';
        var task = gantt.getTask(taskid);

        $.get('/gantt/gantt/pending-task', {task_dataid: dataid, taskid: taskid, target: target, task_ezf_id: '<?= $activity_ezf_id ?>', module_id: module_id, task_owner: task.user_create}, function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>;
            gantt.clearAll();
            onInitGantt();

        });
    });

    $('.btn-decline-task').on('click', function () {
        var request_type = $(this).attr('data-request_type');
        var dataid = $(this).attr('data-task_dataid');
        var taskid = $(this).attr('data-taskid');
        var target = $(this).attr('data-project_id');
        var myrequestid = $(this).attr('data-myrequestid');
        var module_id = '<?= $module_id ?>';
        var task = gantt.getTask(taskid);

        var url = '<?=
Url::to(['/ezforms2/ezform-data/ezform',
    'modal' => 'modal-ezform-gantt',
    'ezf_id' => \backend\modules\gantt\Module::$formsId['decline_task_form'],
]);
?>';
        $('#modal-ezform-gantt').modal();
        $('#modal-ezform-gantt').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {modal: 'modal-ezform-gantt', task_dataid: dataid, taskid: taskid, target: dataid}, function (result) {
            $('#modal-ezform-gantt').find('.modal-content').empty();
            $('#modal-ezform-gantt').find('.modal-content').html(result);
        });
    });

    $('.btn-refer-task').on('click', function () {
        var request_type = $(this).attr('data-request_type');
        var dataid = $(this).attr('data-task_dataid');
        var taskid = $(this).attr('data-taskid');
        var target = $(this).attr('data-project_id');
        var myrequestid = $(this).attr('data-myrequestid');
        var module_id = '<?= $module_id ?>';
        var task = gantt.getTask(taskid);

        var url = '<?=
Url::to(['/ezforms2/ezform-data/ezform',
    'modal' => 'modal-ezform-gantt',
    'ezf_id' => \backend\modules\gantt\Module::$formsId['refer_task'],
]);
?>';
        $('#modal-ezform-gantt').modal();
        $('#modal-ezform-gantt').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {modal: 'modal-ezform-gantt', task_dataid: dataid, taskid: taskid, target: dataid}, function (result) {
            $('#modal-ezform-gantt').find('.modal-content').empty();
            $('#modal-ezform-gantt').find('.modal-content').html(result);
        });
    });

    function requestOfTask(e) {
        var dataid = $(e).attr('data-task_dataid');
        var taskid = $(e).attr('data-taskid');
        var request_ezf_id = '<?= \backend\modules\gantt\Module::$formsId['request_task_form'] ?>';
        var module_id = '<?= $module_id ?>';
        var task_ezf_id = '<?= $activity_ezf_id ?>';
        var project_id = '<?= $project_id ?>';

        $('#modal-ezform-config').modal();
        $('#modal-ezform-config').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get('/gantt/gantt/request-list', {task_dataid: dataid, taskid: taskid, request_ezf_id: request_ezf_id, task_ezf_id: task_ezf_id, module_id: module_id, project_id: project_id}, function (result) {

            $('#modal-ezform-config').find('.modal-content').empty();
            $('#modal-ezform-config').find('.modal-content').html(result);
        });
    }

    function addNewtask(ezf_id, id, parent, e) {
        var cate_id = id;
        if (id === '') {
            id = $(e).attr('data-taskid');
            cate_id = $(e).attr('data-task_dataid');
        }
        var task_selected;
        if (!e)
            task_selected = gantt.getTask(gantt.getSelectedId());
        else
            task_selected = gantt.getTask($(e).attr('data-taskid'));

        var project_id = '<?= $project_id ?>';
        var url = '<?=
Url::to(['/gantt/gantt/add-new-task',
    'require' => 'new_task',
    'task_type' => 'task',
    'activity_ezf_id' => $activity_ezf_id,
])
?>';
        if (!task_selected)
            task_selected = gantt.getTask(project_id);
        gantt.config.sort = false;
        var date = new Date();

        if (id == project_id) {
            if (task_selected.priority == '2' || task_selected.priority == '1') {
                id = task_selected.id;
            } else {
                var pos_parent = gantt.getTask(task_selected.parent);
                id = pos_parent.id;
            }
        }

        $.get(url, {cate_id: cate_id, parentid: id, target: parent}, function (result) {
            if (result.status == "success") {
//                    gantt.clearAll();
//                    onInitGantt();
                var taskId = gantt.addTask({
                    id: result.taskid,
                    dataid: result.dataid,
                    text: result.task_name,
                    start_date: new Date(result.start_date),
                    type: 'task',
                    type_task: 'task',
                    ezf_id: '<?= $activity_ezf_id ?>',
                    duration: 7,
                    priority: 3,
                    new_task: 1,
                    sub_type: null,
                    user_create: '<?= $user_id ?>',
                }, id, result.order_node);

<?= SDNoty::show('result.message', 'result.status') ?>;
            } else {
<?= SDNoty::show('result.message', 'result.error') ?>;
            }
            gantt.config.sort = true;
        });
    }

    function addNewMilestone(ezf_id, id, parent, e) {
        var cate_id = id;
        if (id === '') {
            id = $(e).attr('data-taskid');
            cate_id = $(e).attr('data-task_dataid');
        }
        var task_selected = gantt.getTask(gantt.getSelectedId());
        var project_id = '<?= $project_id ?>';
        var url = '<?=
Url::to(['/gantt/gantt/add-new-task',
    'require' => 'new_task',
    'activity_ezf_id' => $activity_ezf_id,
    'task_type' => 'milestone',
    'initdata' => ['sent_to' => '1', 'sent_module_2' => '1'],
])
?>';
        if (!task_selected)
            task_selected = gantt.getTask(project_id);
        if (id == project_id) {
            if (task_selected.priority == '2' || task_selected.priority == '1') {
                id = task_selected.id;
            } else {
                var pos_parent = gantt.getTask(task_selected.parent);
                id = pos_parent.id;
            }
        }

        var date = new Date();
        gantt.config.sort = false;
        $.get(url, {cate_id: cate_id, parentid: id, target: parent}, function (result) {
            if (result.status == "success") {
//                    gantt.clearAll();
//                    onInitGantt();
                var taskId = gantt.addTask({
                    id: result.taskid,
                    dataid: result.dataid,
                    text: result.task_name,
                    start_date: new Date(result.start_date),
                    type: 'milestone',
                    type_task: 'milestone',
                    ezf_id: '<?= $activity_ezf_id ?>',
                    duration: result.duration,
                    priority: 3,
                    new_task: 1,
                    user_create: '<?= $user_id ?>',
                    sub_type: null,
                }, id, result.order_node);
<?= SDNoty::show('result.message', 'result.status') ?>;
            } else {
<?= SDNoty::show('result.message', 'result.error') ?>;
            }
            gantt.config.sort = true;
        });
    }

    function addDataEzform(dat) {
        var url = '<?=
Url::to(['/ezforms2/ezform-data/ezform',
    'modal' => 'modal-ezform-main',
]);
?>';
        var initData = {group_id_init: dat.group_id, schedule_id_init: dat.schedule_id, visit_name: dat.visit_id};
        var data = {ezf_id: dat.ezf_id, target: dat.target, initdata: btoa(JSON.stringify(initData))};
        if (dat.dataid) {
            data = {ezf_id: dat.ezf_id, dataid: dat.dataid, initdata: btoa(JSON.stringify(initData))};
        }
        $('#modal-ezform-gantt').modal();
        $('#modal-ezform-gantt').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, data, function (result) {
            $('#modal-ezform-gantt').find('.modal-content').empty();
            $('#modal-ezform-gantt').find('.modal-content').html(result);
        });
    }

    function addNewCategory(ezf_id, id, e) {
        if (id === '') {
            id = $(e).attr('data-taskid');
        }
        var task_selected = gantt.getTask(gantt.getSelectedId());
        var project_id = '<?= $project_id ?>';
//        var url = '<?=
Url::to(['/ezforms2/ezform-data/ezform',
    'modal' => 'modal-ezform-gantt',
    'require' => 'new_task',
]);
?>//';
        var url = '<?=
Url::to(['/gantt/gantt/add-new-subtask',
    'require' => 'new_task',
    'task_type' => 'sub-task',
    'cate_ezf_id' => $cate_ezf_id,
])
?>';
        if (!task_selected)
            task_selected = gantt.getTask(project_id);
        if (id == project_id) {
            if (task_selected.priority == '2' || task_selected.priority == '1') {
                id = task_selected.id;
            } else {
                var pos_parent = gantt.getTask(task_selected.parent);
                id = pos_parent.id;
            }
        }
        gantt.config.sort = false;
        $.get(url, {parent: id, target: project_id}, function (result) {
            if (result.status == "success") {
//                    gantt.clearAll();
//                    onInitGantt();
                var taskId = gantt.addTask({
                    id: result.taskid,
                    dataid: result.dataid,
                    text: result.cate_name,
                    type: 'project',
                    type_task: 'project',
                    ezf_id: '<?= $cate_ezf_id ?>',
                    target: project_id,
                    category: result.taskid,
                    priority: 2,
                    new_task: 1,
                    open: true,
                    user_create: '<?= $user_id ?>',
                }, id, result.order_node);
<?= SDNoty::show('result.message', 'result.status') ?>;
            } else {
<?= SDNoty::show('result.message', 'result.error') ?>;
            }
            gantt.config.sort = true;
        });
//        var data = {ezf_id: ezf_id, target: id, reloadDiv: 'display-gantt'};
//        $('#modal-ezform-gantt').modal();
//        $.ajax({
//            url: url,
//            method: 'get',
//            data: data,
//            cache: true,
//            success: function (data) {
//                $('#modal-ezform-gantt').find('.modal-content').empty();
//                $('#modal-ezform-gantt').find('.modal-content').html(data);
//                $('form#ezform-1520711949087879800').submit(function () {
//                    gantt.clearAll();
//                });
//            }
//        });
    }

    function onDragEndItem(dat) {

        if (dat === null) {
            gantt.refreshData();
        } else {
            var drop_target = dat.$drop_target;
            var new_order = dat.$index;
            var parent = dat.parent;
            var child_all = gantt.getChildren(parent);
            var data_task = [];
            $.each(child_all, function (i, e) {
                var data = gantt.getTask(e);
                data_task.push({id: e, order: data.$index});

            });

            var url = '<?=
Url::to(['/gantt/gantt/dragitem-update',
    'modal' => 'modal-ezform-gantt',
    'reloadDiv' => $reloadDiv,
]);
?>';
            //console.log(data_task);
            var ezf_id = dat.ezf_id;
            var newParent = gantt.getTask(dat.parent);

            if (newParent.priority == '2' || newParent.priority == '1') {
                var data = {ezf_id: ezf_id, target: dat.id, type: dat.type, parent: dat.parent, drop_target: drop_target, new_order: new_order, data_task: btoa(JSON.stringify(data_task))};
                $.ajax({
                    url: url,
                    method: 'get',
                    data: data,
                    cache: true,
                    success: function (result) {

                    }
                });
            }
            gantt.refreshData();
        }

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
                    gantt.clearAll();
                    onInitGantt();
                    //getReloadDiv($('#<?= $reloadDiv ?>').attr('data-url') + '&project_id=<?= $project_id ?>', '<?= $reloadDiv ?>');
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
        var project_id = '<?= $project_id ?>';
        var url = "/gantt/timeline-milestone/index";
        $('#modal-ezform-project').modal();
        $('#modal-ezform-project').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {dataid: dataid, project_id: project_id}, function (result) {
            $('#modal-ezform-project').find('.modal-content').empty();
            $('#modal-ezform-project').find('.modal-content').html(result);
        });
    });

    $(document).on('click', '.task_name_column', function () {
        var permiss = '<?= $permission ?>';
        var ownProject = '<?= $ownProject ?>';
        var ownTask = JSON.parse('<?= $myTask ?>');
        var parent = $(this).parent();
        var div = $(this);
        var dataid = div.attr('data-dataid');
        var taskid = div.attr('data-taskid');
        var type = div.attr('data-data_type');
        var task_type = div.attr('data-data_task_type');
        var text = div.find('.gantt_grid_text').html();
        var user_create = div.attr('data-user_create');
        var userId = '<?= $user_id ?>';

        if ((permiss == '1' && ownProject == '1') || userId == user_create || $.inArray(taskid, ownTask) !== -1) {

            var placeholder = "New sub-task item";
            if (type == '3') {
                placeholder = "New task item";
            }
            parent.empty();
            div.empty();
            var input_tag = "<input id='input-task" + taskid + "' type='text' value='" + text + "' placeholder='" + placeholder + "' class='form-control'>";
            parent.append(input_tag);

            $('#input-task' + taskid).on('change', function () {
                if (checkEvent == 1)
                    return;

                checkEvent = 1;
                var activity_ezf = '<?= $activity_ezf_id ?>';
                var cate_ezf = '<?= $cate_ezf_id ?>';
                var project_ezf = '<?= $project_ezf_id ?>';
                var new_text = $(this).val();

                if (!new_text && new_text == '')
                    new_text = text;
                div.html(new_text);
                parent.find('#input-task' + taskid).remove();
                parent.append(div);
                var data = {type: type, task_type: task_type, text: new_text, dataid: dataid, taskid: taskid, activity_ezf: activity_ezf, cate_ezf: cate_ezf, project_ezf: project_ezf};
                $.get('/gantt/gantt/update-attribute-gantt', data, function (result) {
                    checkEvent = 0;
                    if (result.status == "success") {
                        //gantt.clearAll();
                        //onInitGantt();
                        var task = gantt.getTask(taskid);
                        task.text = new_text;
                        gantt.updateTask(taskid);
                        gantt.refreshData();

<?= SDNoty::show('result.message', 'result.status') ?>;
                    } else {
<?= SDNoty::show('result.message', 'result.error') ?>;
                    }
                });

            });
        }

    });

    $(document).on('click', '.task_start_date_column', function () {
        var permiss = '<?= $permission ?>';
        var ownProject = '<?= $ownProject ?>';
        var ownTask = JSON.parse('<?= $myTask ?>');

        var parent = $(this).parent();
        var div = $(this);
        var dataid = div.attr('data-dataid');
        var taskid = div.attr('data-taskid');
        var type = div.attr('data-data_type');
        var start_date = div.attr('data-value');
        var task_type = div.attr('data-data_task_type');
        var date = new Date(start_date);
        start_date = getDateInputFormat(date);
        var user_create = div.attr('data-user_create');
        var userId = '<?= $user_id ?>';

        if ((permiss == '1' && ownProject == '1') || userId == user_create || $.inArray(taskid, ownTask) !== -1) {
            parent.empty();
            div.empty();

            var input_tag = "<input id='input-task-start-date" + taskid + "' type='text' value='" + start_date + "' class='form-control' >";
            parent.append(input_tag);
            $(function () {
                $('#input-task-start-date' + taskid).datetimepicker();
            });
            $('#input-task-start-date' + taskid).on('dp.hide', function (e) {
                if (checkEvent == 1)
                    return;

                checkEvent = 1;
                var activity_ezf = '<?= $activity_ezf_id ?>';
                var cate_ezf = '<?= $cate_ezf_id ?>';
                var project_ezf = '<?= $project_ezf_id ?>';
                var new_start_date = $(this).val();
                var new_dateutc = new Date(new_start_date);
                var new_date = getDateoFormat(new_dateutc);

                div.html(new_date);
                parent.find('#input-task-start-date' + taskid).remove();
                parent.html(div);

                var data = {type: type, task_type: task_type, start_date: new_date, dataid: dataid, taskid: taskid, activity_ezf: activity_ezf, cate_ezf: cate_ezf, project_ezf: project_ezf};
                $.get('/gantt/gantt/update-attribute-gantt', data, function (result) {
                    checkEvent = 0;
                    if (result.status == "success") {
                        //gantt.clearAll();
                        //onInitGantt();
                        console.log(new_dateutc);
                        var task = gantt.getTask(taskid);
                        task.start_date = new_dateutc;
                        gantt.updateTask(taskid);
                        gantt.refreshData();
<?= SDNoty::show('result.message', 'result.status') ?>;
                    } else {
<?= SDNoty::show('result.message', 'result.error') ?>;
                    }
                });

            });
        }
    });

    $(document).on('click', '.task_end_date_column', function () {
        var permiss = '<?= $permission ?>';
        var ownProject = '<?= $ownProject ?>';
        var ownTask = JSON.parse('<?= $myTask ?>');

        var parent = $(this).parent();
        var div = $(this);
        var dataid = div.attr('data-dataid');
        var taskid = div.attr('data-taskid');
        var type = div.attr('data-data_type');
        var task_type = div.attr('data-data_task_type');
        var end_date = div.attr('data-value');
        var date = new Date(end_date);
        end_date = getDateInputFormat(date);
        var user_create = div.attr('data-user_create');
        var userId = '<?= $user_id ?>';

        if ((permiss == '1' && ownProject == '1') || userId == user_create || $.inArray(taskid, ownTask) !== -1) {
            parent.empty();
            div.empty();
            var input_tag = "<input id='input-task-end-date" + taskid + "' type='text' value='" + end_date + "' >";
            parent.append(input_tag);
            $(function () {
                $('#input-task-end-date' + taskid).datetimepicker();
                //$('#input-task-end-date'+taskid).datepicker();
            });
            $('#input-task-end-date' + taskid).on('dp.hide', function () {
                if (checkEvent == 1)
                    return;

                checkEvent = 1;
                var activity_ezf = '<?= $activity_ezf_id ?>';
                var cate_ezf = '<?= $cate_ezf_id ?>';
                var project_ezf = '<?= $project_ezf_id ?>';
                var new_end_date = $(this).val();
                var new_dateutc = new Date(new_end_date);
                var new_date = getDateoFormat(new_dateutc);
                div.html(new_date);
                parent.find('#input-task-end-date' + taskid).remove();
                parent.append(div);
                var data = {type: type, task_type: task_type, end_date: new_date, dataid: dataid, taskid: taskid, activity_ezf: activity_ezf, cate_ezf: cate_ezf, project_ezf: project_ezf};
                $.get('/gantt/gantt/update-attribute-gantt', data, function (result) {
                    checkEvent = 0;
                    if (result.status == "success") {
                        //gantt.clearAll();
                        //onInitGantt();
                        var task = gantt.getTask(taskid);
                        task.end_date = new_dateutc;
                        task.finish_date = new_date;
                        gantt.updateTask(taskid);
                        gantt.refreshData();
<?= SDNoty::show('result.message', 'result.status') ?>;
                    } else {
<?= SDNoty::show('result.message', 'result.error') ?>;
                    }
                });

            });
        }
    });

    function copyAndShareTask(e) {
        var id = $(e).attr('data-taskid');
        var ezf_id = $(e).attr('data-ezf_id');
        var task_type = $(e).attr('data-task_type');
        var url = '<?=
Url::to([
    '/gantt/gantt/copy-and-share'
    , 'activity_ezf' => $activity_ezf_id
    , 'cate_ezf' => $cate_ezf_id
    , 'main_ezf' => $project_ezf_id
    , 'project_id' => $project_id
])
?>';
        url += "&dataid=" + id + "&task_type=" + task_type;
        var modal = $('#modal-copy-share');
        modal.find('.modal-content').empty();
        modal.modal();
        modal.find('.modal-content').load(url);
    }

    function onShareTask(id, ezf_id, task_type, target, parent, main_from) {
        var url = '/gantt/gantt/sharing-task';
        var data = {dataid: id, ezf_id: ezf_id, task_type: task_type, target: target, parent: parent, main_from: main_from};
        $.get(url, data, function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>;
        });
    }

    function onCopyTask(e) {
        var id = $(e).attr('data-taskid');
        var ezf_id = $(e).attr('data-ezf_id');
        var task_type = $(e).attr('data-task_type');
        var widget_id = $('#data-widget_id').val();
        var schedule_id = $('#data-schedule_id').val();
        var activity_ezf = '<?= $activity_ezf_id ?>';
        var cate_ezf = '<?= $cate_ezf_id ?>';
        var project_ezf = '<?= $project_ezf_id ?>';
        var url = '/gantt/gantt/copy-task';

        var task_dat = gantt.getTask(id);
        $.ajax({
            url: url,
            method: "get",
            type: 'JSON',
            cache: true,
            data: {dataid: id, activity_ezf: activity_ezf, cate_ezf: cate_ezf, project_ezf: project_ezf, task_type: task_type, schedule_id: schedule_id},
            success: function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>
                if (task_type == 'task' || task_type == 'milestone') {
                    var taskId = gantt.addTask({
                        id: result.taskid,
                        dataid: result.dataid,
                        text: task_dat.text + " Copy",
                        start_date: task_dat.start_date,
                        type: task_type,
                        type_task: task_type,
                        ezf_id: '<?= $activity_ezf_id ?>',
                        duration: task_dat.duration,
                        new_task: 1,
                        priority: 3,
                        sub_type: null,
                        user_create: '<?= $user_id ?>',
                    }, task_dat.parent, task_dat.order + 1);
                } else if (task_type == 'sub-task') {
                    var taskId = gantt.addTask({
                        id: result.taskid,
                        dataid: result.dataid,
                        text: task_dat.text + " Copy",
                        type: 'project',
                        type_task: 'project',
                        ezf_id: '<?= $cate_ezf_id ?>',
                        target: task_dat.target,
                        category: task_dat.category,
                        priority: 2,
                        new_task: 1,
                        sub_type: 1,
                        user_create: '<?= $user_id ?>',
                    }, 0, task_dat.order + 1);

                    $.each(result.taskModels, function (i, e) {

                        if (e.id) {
                            var taskId = gantt.addTask({
                                id: e.id,
                                dataid: e.dataid,
                                text: e.task_name,
                                start_date: new Date(e.start_date),
                                type: 'task',
                                type_task: 'task',
                                ezf_id: '<?= $activity_ezf_id ?>',
                                end_date: new Date(e.finish_date),
                                priority: 3,
                                user_create: '<?= $user_id ?>',
                            }, result.taskid, i);
                        }
                    });
                }
            }
        });
    }

    $('.checkbox_grid_head').on('change', function () {
        var gantt_grid = $(this).parents('gantt_grid_top');
        gantt_grid.find('.gantt_grid_head_cell').each(function (i, e) {
            var ck_box = $(e).find('.checkbox_grid_head');
        });

        var ischeck = $(this).is(':checked');
        var column_id = $(this).parent().attr('column_id');
        var project_id = '<?= $project_id ?>';
        $.get('/gantt/gantt/set-head-options', {ischeck: ischeck, column_id: column_id, project_id: project_id}, function (result) {

        });
    });

    function getDateInputFormat(date) {
        var ii = date.getMinutes();
        var hh = date.getHours();
        var mm = date.getMonth() + 1;
        var dd = date.getDate();
        if (ii < 10)
            ii = "0" + ii;
        if (hh < 10)
            hh = "0" + hh;
        if (mm < 10)
            mm = "0" + mm;
        if (dd < 10)
            dd = "0" + dd;
        //return date.getFullYear() + "/" + mm + "/" + dd + " " + hh + ":" + ii;
        return  mm + "/" + dd + "/" + date.getFullYear() + " " + hh + ":" + ii;

    }

    function getDateoFormat(date) {
        var ii = date.getMinutes();
        var hh = date.getHours();
        var mm = date.getMonth() + 1;
        var dd = date.getDate();
        if (ii < 10)
            ii = "0" + ii;
        if (hh < 10)
            hh = "0" + hh;
        if (mm < 10)
            mm = "0" + mm;
        if (dd < 10)
            dd = "0" + dd;
        return date.getFullYear() + "-" + mm + "-" + dd + " " + hh + ":" + ii;
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>