<?php

use kartik\tabs\TabsX;
use yii\helpers\Url;
use appxq\sdii\assets\FroalaEditorAsset;
$this->registerJsFile(
        '@web/js-gantt/dhtmlxgantt.js?48', ['depends' => [\yii\web\JqueryAsset::className()]]
);

$this->registerJsFile(
        '@web/js-gantt/api.js?3', ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
        '@web/js-gantt/dhtmlxgantt_marker.js?8', ['depends' => [\yii\web\JqueryAsset::className()]]
);


$this->registerCssFile("@web/js-gantt/dhtmlxgantt.css");
$this->registerCssFile("@web/js-gantt/gantt-custom-style.css");
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$items = [];
if ($page_from == "other") {
    $items = [
        [
            'label' => 'PMS',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'pms_tab'],
        ],
        [
            'label' => 'Task Detail',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'task_setting_tab'],
        ],
        [
            'label' => 'Task Response',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'task_response_tab'],
            'active' => true,
        ],
//        [
//            'label' => 'Task Related',
//            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'task_related_tab'],
//        ]
    ];
} else {
    $items = [
        [
            'label' => 'Task Detail',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'task_setting_tab'],
        ],
        [
            'label' => 'Task Response',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'task_response_tab'],
            'active' => isset($ezf_tab) && $ezf_tab ? false : true,
        ],
//        [
//            'label' => 'Task Related',
//            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'task_related_tab'],
//        ]
    ];
}

foreach ($ezformList as $value) {
    $icon = '<i class="fa fa-exclamation-circle" aria-hidden="true" style="color:orange;"></i>';
    if (isset($value['dataid']) && $value['dataid'] != null) {
        $icon = '<i class="fa fa-check-circle" aria-hidden="true" style="color:green;"></i>';
    }
    $items[] = [
        'label' => $value['ezf_name'] . " " . $icon,
        'headerOptions' => ['style' => 'font-weight:bold'
            , 'id' => 'task_ezform_work_tab' . $value['ezf_id']
            , 'data-data_id' => $value['dataid']
            , 'data-ezf_id' => $value['ezf_id']
            , 'data-task_dataid' => $value['task_dataid']
        ],
        'active' => $ezf_tab == $value['ezf_id'] ? true : false,
    ];
}

if ($ezform_work_ops && $ezform_work_ops != '') {
    $items[] = [
        'label' => "Optional form",
        'headerOptions' => ['style' => 'font-weight:bold'
            , 'id' => 'task_optional_tab'
        ],
        'active' => false,
    ];
}

?>


<style>
    .modal-content{
        box-shadow:none;
    }
</style>
<?php backend\modules\ezforms2\classes\EzfStarterWidget::begin(); ?>
<div class="modal-header">
    <?php if ($page_from != "other"): ?><button type="button" class="close-modal pull-right btn btn-default" data-dismiss="modal">Close</button><?php endif; ?>
    <h4>Task Item Settings and Assignments</h4>
</div>
<div class="modal-body">
    <?=
    \kartik\tabs\TabsX::widget([
        'position' => TabsX::POS_ABOVE,
        'align' => TabsX::ALIGN_LEFT,
        'encodeLabels' => false,
        //'enableStickyTabs' => true,
        'items' => $items,
    ]);
    ?>
</div>
<div id="update_pmslink" data-url="<?= yii\helpers\Url::to(['/gantt/pms-response/update-pmslink', 'taskid' => $taskid]) ?>"></div>
<div id="content_setting_pms"></div>
<div id="display_task_assignment" data-url="<?= Url::to(['/gantt/pms-response/task-response',
        'taskid'=>$taskid,
        'task_dataid'=>$task_dataid,
        'task_ezf_id'=>$ezf_id,
        'reloadDiv'=>'display_task_assignment',
        'credit_points'=>$credit_points,
        'reward_points'=>$reward_points,
        'isreviewer'=>$isreviewer,
        'isapprover'=>$isapprover,
    
    ])?>" >

</div>
<input type="hidden" id="onchange_value_check" name="onchange_value_check" value="0">
<div id="content_optional_show"></div>
<div class="container-fluid">
    <div id="display-ezform-work">
        <div class="modal-content" style="border: 0px solid gray;">

        </div>
        <br/>
    </div>
    <?php foreach ($ezformList as $value) : ?>
    <div id="display-ezform-work-<?=$value['ezf_id']?>" style="display:none;">
            <div class="modal-content" style="border: 0px solid gray;" isloaded="0"></div>
            <br/>
        </div>
    <?php endforeach;?>
</div>
<?php backend\modules\ezforms2\classes\EzfStarterWidget::end(); ?>

<?=

appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-ezform-gantt',
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
]);
?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    $(function () {
        var ezf_tab = '<?= $ezf_tab ?>';
        if (ezf_tab == 'null' || ezf_tab == '' || !ezf_tab) {
            var div = $('#display_task_assignment');
            var url = div.attr('data-url');
            var dataid = '<?= $dataid ?>';
            
            div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
            var data = {};
            if (dataid && dataid !== '') {
                data = {dataid: dataid};
            }

            $.get(url, data, function (result) {
                div.empty();
                div.html(result);
            });
        } else {
            $('#task_ezform_work_tab' + ezf_tab).trigger('click');
        }
        
    });

    $('#pms_tab').on('click', function () {
        if (gantt.isTaskExists())
            gantt.clearAll();
        var url = '/gantt/pms-response/task-assign';
        var taskid = '<?= $taskid ?>';
        var ezf_id = '<?= $ezf_id ?>';
        $('#display-ezform-work').css('display','none');
        $('#display_task_assignment').css('display','none');
        var div = $('#content_setting_pms');
        
        var formLsit = <?= json_encode($ezformList)?>;
        $.each(formLsit,function(i,e){
            $('#display-ezform-work-'+e.ezf_id).css('display','none');
        });
        
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {taskid: taskid, ezf_id: ezf_id}, function (result) {
            div.empty();
            div.html(result);
        });
    });

    $('#task_setting_tab').on('click', function () {
        var url = '/gantt/pms-response/task-setting';
        var taskid = '<?= $taskid ?>';
        var task_dataid = '<?= $task_dataid ?>';
        var ezf_id = '<?= $ezf_id ?>';
        
        var change = $('#onchange_value_check').val();
        
        $('#content_optional_show').empty();
        $('#display-ezform-work').css('display','none');
        $('#display_task_assignment').css('display','none');
        
        var formLsit = <?= json_encode($ezformList)?>;
        $.each(formLsit,function(i,e){
            $('#display-ezform-work-'+e.ezf_id).css('display','none');
        });
        
        var div = $('#content_setting_pms');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {taskid: taskid, task_dataid: task_dataid, ezf_id: ezf_id}, function (result) {
            div.empty();
            div.html(result);
        });
    });
    
    function setOnchange(){
        $('#onchange_value_check').val('1');
    }

    $('#task_response_tab').on('click', function () {
        var dataid = '<?= $dataid ?>';
        $('#content_optional_show').empty();
        $('#content_setting_pms').empty();
        $('#display-ezform-work').css('display','none');
        $('#display_task_assignment').css('display','block');
        var formLsit = <?= json_encode($ezformList)?>;
        $.each(formLsit,function(i,e){
            $('#display-ezform-work-'+e.ezf_id).css('display','none');
        });
        var div = $('#display_task_assignment');
        var url = div.attr('data-url');
        var data = {};
        if (dataid && dataid !== '') {
            data = {dataid: dataid,};
        }
        div.find('#asset_content_response').remove();
        $.get('/gantt/pms-response/asset-froala', {}, function (result) {
            div.prepend(result);
        });
        //$('#ez1532333795009058500-response_detail').froalaEditor();
//        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
//        $.get(url, data, function (result) {
//            div.empty();
//            div.html(result);
//        });
    });

    $('#task_related_tab').on('click', function () {
        var url = '/gantt/pms-response/task-related';
        var taskid = '<?= $task_dataid ?>';
        var dataid = '<?= $dataid ?>';
        var ezf_id = '<?= $response_ezf_id ?>';
        var div = $('#display_task_assignment');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {taskid: taskid, ezf_id: ezf_id, dataid: dataid}, function (result) {
            div.empty();
            div.html(result);
        });
    });

    $('[id^=task_ezform_work_tab]').on('click', function () {
        //$(document).find('.btn-open-form').removeClass('active');
        //$(this).addClass('active');
        var url = '/ezforms2/ezform-data/ezform';
        var ezf_id = $(this).attr('data-ezf_id');
        var dataid = $(this).attr('data-data_id');
        var pmslink = $(this).attr('data-task_dataid');
        var task_dataid = '<?= $task_dataid ?>';
        
        var formLsit = <?= json_encode($ezformList)?>;
        $.each(formLsit,function(i,e){
            $('#display-ezform-work-'+e.ezf_id).css('display','none');
        });
        
        $('#display-ezform-work-'+ezf_id).css('display','block');
        var divData = $('#display-ezform-work-'+ezf_id);
        var url_pms = $('#update_pmslink').attr('data-url');
        $('#content_setting_pms').empty();
        $('#content_optional_show').empty();
        url_pms = url_pms + '&ezf_id=' + ezf_id;
        $('#update_pmslink').attr('data-url', url_pms);
        $('#display_task_assignment').css('display','none');

        var data = {
            ezf_id: ezf_id,
            reloadDiv: 'update_pmslink',
            modal: 'display-ezform-work',
            initdata: btoa(JSON.stringify({pmslink: pmslink})),
        }

        if (dataid && dataid != '') {
            data = {
                ezf_id: ezf_id,
                reloadDiv: 'update_pmslink',
                modal: 'display-ezform-work',
                dataid: dataid,
            }
        }
        
        divData.find('#asset_content_response').remove();
        $.get('/gantt/pms-response/asset-froala', {}, function (result) {
            divData.prepend(result);
        });
        if(divData.find('.modal-content').attr('isloaded') == '0'){
            divData.find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
            $.ajax({
                url: url,
                type: 'html',
                method: 'get',
                data: data,
                success: function (result) {
                    divData.find('.modal-content').html(result);
                    divData.find('.modal-content').attr('isloaded','1');
                    var btn = divData.find('.modal-content').find('.btn-submit');

                    $(btn).click(function () {
                        setTimeout(function () {
                            console.log("reload");
                            reloadModal(ezf_id);
                        }, 2500);

                    });
                    $(document).find(".glyphicon-remove").parent().remove();
                    $(document).find(".close").remove();
                }
            });
        }else{
            
        }
    });

    $('#content_optional_show').on('hidden.bs.modal', '#modal-optional-list', function () {
        var url = '/gantt/pms-response/view-optional';
        var ezf_list = <?= json_encode($ezform_work_ops) ?>;
        var task_dataid = '<?= $task_dataid ?>';
        var data = {ezf_list: ezf_list, task_dataid: task_dataid, reloadDiv: 'update_pmslink'};
        var div_ops = $('#content_optional_show');
        var divData = $('#display-ezform-work');
        divData.find('.modal-content').empty();
        $('.display_task_assignment').empty();
        
        var formLsit = <?= json_encode($ezformList)?>;
        $.each(formLsit,function(i,e){
            $('#display-ezform-work-'+e.ezf_id).css('display','none');
        });
        
        div_ops.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.ajax({
            url: url,
            type: 'html',
            method: 'get',
            data: data,
            success: function (result) {
                div_ops.empty();
                div_ops.html(result);
            }
        });
    });

    $('#task_optional_tab').click(function () {
        var url = '/gantt/pms-response/view-optional';
        var ezf_list = <?= json_encode($ezform_work_ops) ?>;
        var task_dataid = '<?= $task_dataid ?>';
        var data = {ezf_list: ezf_list, task_dataid: task_dataid, reloadDiv: 'content_optional_show'};
        $('#content_optional_show').attr('data-url', url + '?ezf_list=' + ezf_list + '&task_dataid=' + task_dataid + '&reloadDiv=content_optional_show');
        var div_ops = $('#content_optional_show');
        var divData = $('#display-ezform-work');
        divData.find('.modal-content').empty();
        $('.display_task_assignment').empty();
        
        var formLsit = <?= json_encode($ezformList)?>;
        $.each(formLsit,function(i,e){
            $('#display-ezform-work-'+e.ezf_id).css('display','none');
        });
        
        div_ops.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.ajax({
            url: url,
            type: 'html',
            method: 'get',
            data: data,
            success: function (result) {
                div_ops.empty();
                div_ops.html(result);
            }
        });
    });

    function reloadModal(ezf_id) {
        var url = '<?=
Url::to(['/gantt/pms-response/index',
    'page_from' => 'pms',
    'ezf_id' => $ezf_id,
    'response_ezf_id' => $response_ezf_id,
    'action' => 'view',
        //'reloadDiv'=>'display-gantt',
]);
?>';
        var data_id = '<?= $dataid ?>';
        var taskid = '<?= $taskid ?>';
        var task_dataid = '<?= $task_dataid ?>';
        var ezform_work = '<?= $ezform_work ?>';
        var ezform_work_ops = <?= json_encode($ezform_work_ops) ?>;
        var data = {dataid: data_id, taskid: task_dataid, task_id: taskid, modal: 'modal-ezform-gantt', ezform_work: ezform_work, ezform_work_ops: ezform_work_ops, ezf_tab: ezf_id};
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

    function getUiAjax(url, reloadDiv) {
        $.get(url, function (result) {
            $('#' + reloadDiv).empty();
            $('#' + reloadDiv).html(result);
        });
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>

