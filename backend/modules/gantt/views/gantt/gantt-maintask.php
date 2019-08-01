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
use yii\widgets\ListView;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
echo \appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-export',
    'size' => 'modal-sm',
]);

$href = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
$href .= "/ezmodules/ezmodule/view?id=" . $module_id;

$fields = backend\modules\ezforms2\classes\EzfQuery::getFieldByName($project_ezf_id, 'pms_type');
$pms_type = \appxq\sdii\utils\SDUtility::string2Array($fields['ezf_field_data']);
$url = Url::to(['/gantt/gantt/gantt-project',
            'widget_id' => $widget_id,
            'check_type' => $check_type,
            'schedule_id' => $schedule_id,
            'skin_name' => $skin_name,
            'project_ezf_id' => $project_ezf_id,
            'cate_ezf_id' => $cate_ezf_id,
            'activity_ezf_id' => $activity_ezf_id,
            'response_ezf_id' => $response_ezf_id,
            'project_name' => $project_name,
            'start_date' => $start_date,
            'finish_date' => $finish_date,
            'progress' => $progress,
            'cate_name' => $cate_name,
            'task_name' => $task_name,
            'reloadDiv' => $reloadDiv,
            'module_id' => $module_id,
            'calendar_widget_id' => $calendar_widget_id,
            'target' => isset($target) ? $target : null,
            'pms_type' => $pms_type,
            'pms_tab' => $pms_tab,
            'other_ezforms' => $other_ezforms,
        ]);


$items = [];
if ($pms_type) {
    foreach ($pms_type['items'] as $key => $value) {
        $items[] = [
            'label' => $value,
            'headerOptions' => ['style' => 'font-weight:bold', 'data-value' => $key, 'id' => 'tab_pms_type_' . $key, 'class' => 'tab_pms_type'],
            'active' => $pms_tab == $key ? true : false,
        ];
    }
}
$items[] = [
    'label' => '<i class="fa fa-bar-chart" aria-hidden="true" style="font-size:20px;"></i> Report Overall',
    'headerOptions' => ['style' => 'font-weight:bold;', 'data-value' => 'report', 'id' => 'tab_pms_report', 'class' => 'tab_pms_report pull-right'],
    'active' => $pms_tab == 'report' ? true : false,
];
echo TabsX::widget([
    'position' => TabsX::POS_ABOVE,
    'align' => TabsX::ALIGN_LEFT,
    'encodeLabels' => false,
    //'enableStickyTabs' => true,
    'items' => $items,
]);
?>
<div class="col-md-12 sdbox-col">
    <div class="col-md-6 sdbox-col">
        <?= EzfHelper::btn($project_ezf_id)->label("<i class='fa fa-plus'></i> Create New Main Task")->reloadDiv($reloadDiv)->initdata(['pms_type' => $pms_tab, 'flag_status' => '1'])->options(['class' => 'btn btn-success pull-left'])->buildBtnAdd() ?>
        <?= Html::button("<i class='glyphicon glyphicon-import'></i>  " . Yii::t('gantt', "Restore Main Task"), ['class' => 'btn btn-info pull-left', 'id' => 'btn_restore_pms', 'style' => 'margin-left:5px;']) ?>
        <?= Html::button("<i class='fa fa-address-card'></i>  " . Yii::t('gantt', "My Tasks Report"), ['class' => 'btn btn-primary pull-left', 'id' => 'btn_myreport', 'style' => 'margin-left:5px;']) ?>
        <a class="btn btn-warning" style="margin-left:5px;" id="btn_fms_pms" href="/ezmodules/ezmodule/view?id=1520807564095312900&maintab=3&subtab=pms" target="_blank"><i class="fa fa-money" aria-hidden="true"></i> FMS for PMS</a>
    </div>
    <div class="col-md-5 sdbox-col">
        <div class="col-md-1" style="font-size: 20px;text-align: right;"><a href="javascript:void(0)" id="btn_search"><i class='fa fa-search'></i></a></div>
        <div class="col-md-11 sdbox-col">
            <?= Html::textInput('search_input', '', ['class' => 'form-control pull-left', 'id' => 'search_pms_input', 'placeholder' => 'Search by Main Task name ...']) ?>
        </div>
    </div>
    <div class="col-md-1 sdbox-col">
        <button class="btn btn-default" id="btn_notify_setting"><i class="fa fa-bell" aria-hidden="true" title="Notification setting for PMS"></i></button>
    </div>

</div>
<div class="clearfix"></div><br/>
<div class="container-fluid mainhead_content" style="padding:0px;border-bottom:1px solid lightgray;color: gray;" >
    <div class="col-md-1 ">#</div>
    <div class="col-md-3 "><label style="font-size:14px;">Project or Main Task Name</label></div>
    <div class="col-md-1 "><label style="font-size:14px;"> Creator </label></div>
    <div class="col-md-1 "><label style="font-size:14px;"> Task Items </label></div>
    <div class="col-md-1 "><label style="font-size:14px;"> <i class='fa fa-shopping-cart' title='Ready for shopping' style='color:skyblue;font-size:18px;'></i>
            / <i class='fa fa-clock-o' title='Waiting approve by director' style='color:orange;font-size:18px;'></i></label></div>
    <div class="col-md-1 "><label style="font-size:14px;"> Completed </label></div>
    <div class="col-md-1 ">
        <label style="font-size:14px;">%Progress</label>
    </div>
    <div class="col-md-2">

    </div>
</div>
<div class="clearfix"></div>
<div class="col-md-12 sdbox-col " id="mainbody_content" style="padding-right:0;">
    <?php
    echo ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_maintask_view',
        'layout' => '<div class="list-group pms-list-parent" data-url="' . $url . '">{items}</div><div class="list-pager">{pager}</div>',
        'viewParams' => [
            'project_ezf_id' => $project_ezf_id,
            'subtask_ezf_id' => $cate_ezf_id,
            'task_ezf_id' => $activity_ezf_id,
            'reloadDiv' => $reloadDiv,
            'tab' => $pms_tab,
            'href' => $href,
            'task_amt' => $task_amt,
            'milestone_amt' => $milestone_amt,
            'fms_amt' => $fms_amt,
            'shopping_amt' => $shopping_amt,
            'complete_amt' => $complete_amt,
            'approve_amt' => $approve_amt,
        ],
    ]);
    ?>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modal-notification-setting">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notification Setting for PMS</h5>
                <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php 
                $user_id = Yii::$app->user->id;
                $resultNotify = GanttQuery::getNotifySetting($user_id);
                ?>
                <?= Html::checkbox('notify_system', $resultNotify['noti_sys']=='1'?true:false,['id'=>'notify_system','disabled'=>true]) ?>
                <?= Html::label('Notify on System') ?>

                <?= Html::checkbox('notify_email', $resultNotify['noti_email']=='1'?true:false,['id'=>'notify_email']) ?>
                <?= Html::label('Notify Email') ?>

                <?= Html::checkbox('notify_line', $resultNotify['noti_line']=='1'?true:false,['id'=>'notify_line']) ?>          
                <?= Html::label('Notify Line') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn_save_changes" data-dismiss="modal">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" >Close</button>
            </div>
        </div>
    </div>
</div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $(function () {
        var pms_updateid = '<?= $pms_updateid ?>';
        var main_ezf = '<?= $project_ezf_id ?>';
        var sub_ezf = '<?= $cate_ezf_id ?>';
        var task_ezf = '<?= $activity_ezf_id ?>';
        var response_ezf = '<?= $response_ezf_id ?>';
        var url = '/gantt/gantt/pms-update-task';
        var txt = 'Please wait checking pms new version...';
        if (pms_updateid && pms_updateid !== '') {
            onLoadBlock('body', txt);
            $.get(url, {pms_updateid: pms_updateid, main_ezf: main_ezf, sub_ezf: sub_ezf, task_ezf: task_ezf, response_ezf: response_ezf}, function (result) {
                location.reload();
                hideLoadBlock('body');
            });
        }
    });

    function onLoadBlock(ele, txt) {
        $(ele).waitMe({
            effect: 'facebook',
            text: txt,
            bg: 'rgba(255,255,255,0.8)',
            color: '#000',
            maxSize: '',
            waitTime: -1,
            textPos: 'vertical',
            fontSize: '',
            source: '',
            onClose: function () {}
        });
    }
    function hideLoadBlock(ele) {
        $(ele).waitMe("hide");
    }
    $('.tab_pms_type').on('click', function () {
        var val = $(this).attr('data-value');
        window.history.replaceState({}, $(this).html(), '<?= $href ?>&pms_tab=' + val);
        var div = $('.list-view');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        getUiAjax($('#gantt-content').attr('data-url') + '&pms_tab=' + val, 'gantt-content');

    });

    $('.tab_pms_report').on('click', function () {
        $('.mainhead_content').css('display', 'none');
        var val = $(this).attr('data-value');
        var url = "/gantt/pms-report/report-overall";
        window.history.replaceState({}, $(this).html(), '<?= $href ?>&pms_tab=' + val);
        var div = $('#mainbody_content');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        getUiAjax(url + '?pms_tab=' + val, 'mainbody_content');

    });

    $('#gantt-content222').on('click', '.pagination li a', function () { //Next
        var val;
        $('.tab_pms_type').each(function (i, e) {
            if ($(e).hasClass('avtive')) {
                val = $(e).attr('data-value');
            }
        });

        var url = $(this).attr('href');
        getUiAjax(url + '&tab=' + val, 'gantt-content');
        return false;
    });

    $('#search_pms_input').on('change', function () { //Next
        var search = $(this).val();
        var val;
        $('.nav-tabs').find('.tab_pms_type').each(function (i, e) {
            if ($(e).hasClass('avtive')) {
                val = $(e).attr('data-value');
            }
        });

        var url = $('#gantt-content').attr('data-url');
        getUiAjax(url + '&tab=' + val + "&search=" + search, 'gantt-content');
        return false;
    });
    $('#btn_search').on('click', function () { //Next
        var search = $('#search_pms_input').val();
        var val;
        $('.nav-tabs').find('.tab_pms_type').each(function (i, e) {
            if ($(e).hasClass('avtive')) {
                val = $(e).attr('data-value');
            }
        });

        var url = $('#gantt-content').attr('data-url');
        getUiAjax(url + '&tab=' + val + "&search=" + search, 'gantt-content');
        return false;
    });

    $('.btn_clone_maintask').on('click', function () {
        var url = "/gantt/gantt/clone-maintask";
        var project_id = $(this).attr('data-dataid');
        var project_ezf_id = '<?= $project_ezf_id ?>';
        var subtask_ezf_id = '<?= $cate_ezf_id ?>';
        var task_ezf_id = '<?= $activity_ezf_id ?>';
        var response_ezf_id = '<?= $response_ezf_id ?>';
        var txt = 'Please wait cloning main task...';
        onLoadBlock('body', txt);
        $.get(url, {project_id: project_id, project_ezf_id: project_ezf_id, subtask_ezf_id: subtask_ezf_id, task_ezf_id: task_ezf_id, response_ezf_id: response_ezf_id}, function (result) {
            hideLoadBlock('body');
            getUiAjax($('#<?= $reloadDiv ?>').attr('data-url'), '<?= $reloadDiv ?>');
        });
    });

    $('.btn_backup_maintask').on('click', function () {
        var url = "/gantt/gantt/backup-maintask";
        var project_id = $(this).attr('data-dataid');
        var project_ezf_id = '<?= $project_ezf_id ?>';
        var subtask_ezf_id = '<?= $cate_ezf_id ?>';
        var task_ezf_id = '<?= $activity_ezf_id ?>';
        var response_ezf_id = '<?= $response_ezf_id ?>';
        $.get(url, {project_id: project_id, project_ezf_id: project_ezf_id, subtask_ezf_id: subtask_ezf_id, task_ezf_id: task_ezf_id, response_ezf_id: response_ezf_id}, function (result) {
            var data = JSON.parse(result);
<?= SDNoty::show('data.message', 'data.status') ?>;
            $('#modal-export .modal-content').html(data.html);
            $('#modal-export').modal('hide');
        });
    });

    $('#btn_restore_pms').on('click', function () {
        var url = '<?=
\yii\helpers\Url::to(["/gantt/gantt/restore-modal",
    'project_ezf_id' => $project_ezf_id,
    'subtask_ezf_id' => $cate_ezf_id,
    'task_ezf_id' => $activity_ezf_id,
    'response_ezf_id' => $response_ezf_id,
])
?>';
        $('#modal-ezform-config .modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('#modal-ezform-config').modal('show')
                .find('.modal-content')
                .load(url);
    });

    $('#btn_myreport').click(function () {
        var url = '<?=
\yii\helpers\Url::to(["/gantt/pms-report/myreport",
])
?>';
        $('#modal-ezform-gantt .modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('#modal-ezform-gantt').modal('show')
                .find('.modal-content')
                .load(url);
    });

    $('#btn_notify_setting').click(function () {
        $('#modal-notification-setting').modal();
    });
    
    $('.btn_save_changes').click(function(){
        var noti_sys = $('#notify_system').is(':checked');
        var noti_email = $('#notify_email').is(':checked');
        var noti_line = $('#notify_line').is(':checked');
        
        var url = "/gantt/gantt/notify-setting";
        $.post(url,{noti_sys:noti_sys,noti_email:noti_email,noti_line:noti_line},function(result){
            <?= SDNoty::show('result.message', 'result.status') ?>;
        });
        
    });

    function getUiAjax(url, reloadDiv) {
        var div = $('#' + reloadDiv);
        //div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, function (result) {
            div.empty();
            div.html(result);
        });
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>
