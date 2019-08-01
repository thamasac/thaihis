<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\tabs\TabsX;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use backend\modules\ezforms2\classes\EzfHelper;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$itemFilter[0]['id'] = '0';
$itemFilter[0]['filter_name'] = Yii::t('subjects', "All Task");

$itemFilter[1]['id'] = '1';
$itemFilter[1]['filter_name'] = Yii::t('subjects', "Completed Task");

$itemFilter[2]['id'] = '2';
$itemFilter[2]['filter_name'] = Yii::t('subjects', "Not Complete Task");

$itemFilter[3]['id'] = '3';
$itemFilter[3]['filter_name'] = Yii::t('subjects', "Assign To me");

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
$projectForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($maintask_ezf_id);
$projectList = \backend\modules\subjects\classes\SubjectManagementQuery::GetTableData($projectForm, 'pms_type="' . $tab . '" AND (project_sharing=2 OR user_create="' . $user_id . '"' . $assignUser . '' . $manageUser . ')');

?>

<div class="row">
   <?= Html::button('<i class="fa fa-search-plus" aria-hidden="true"></i> '.Yii::t('gantt', 'Display all month of year'),['class'=>'btn btn-primary pull-left','id'=>'btn_display_allmonth','data-action'=>'year'])?> 
</div>

<br/>
<div id="display-calendar" data-url="<?=
     yii\helpers\Url::to([
         '/gantt/pms-calendar/calendar-render',
         'modal' => $modal,
         'reloadDiv' => $reloadDiv,
         'target' => $target,
         'now_date' => $now_date,
         'forms' => $forms,
         'eventSources' => $eventSources,
         'defaultView' => $defaultView,
         'view_menu' => $view_menu,
         'search_cal' => isset($search_cal)?$search_cal:null,
         'tab'=>$tab,
         'response_ezf_id' => $response_ezf_id,
         'subtask_ezf_id' => $subtask_ezf_id,
         'maintask_ezf_id' => $maintask_ezf_id,
         'project_id'=>$project_id,
         'response_actual_field'=>$response_actual_field,
     ])
     ?>">

</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $(function () {
        var project_id = '<?= $project_id ?>';

        var div = $('#display-calendar');
        var url = $('#display-calendar').attr('data-url')+ '&project_id=' + project_id;
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');

        $.get(url, {}, function (result) {
            console.log('success');
            div.empty();
            div.html(result);
        })
    });

    $('.tab_pms_type').on('click', function () {
        var tab = $(this).attr('data-value');
        var project_id = $.cookie("project_id");
        getReloadDiv($('#display-calendar').attr('data-url') + '&project_id=' + project_id + '&tab=' + tab, 'display-calendar');
    });

    $('#config_main_task').on('change', function () {
        var project_id = $('#config_main_task').val();
        var tab = '<?= $tab ?>';
        document.cookie = 'project_id=' + project_id + '; path=/';
        document.cookie = 'project_id=' + project_id + '; path=/ezmodules/ezmodule';
        getReloadDiv($('#display-calendar').attr('data-url') + '&project_id=' + project_id + '&tab=' + tab, 'display-calendar');
    });

    $('.scale_filter_selector').change(function () {
        var project_id = $.cookie("project_id");
        var tab = '<?= $tab ?>';
        getReloadDiv($('#display-calendar').attr('data-url') + '&project_id=' + project_id + '&tab=' + tab, 'display-calendar');
    });


    function getReloadDiv(url, div) {
        var proID = $('.project-selector').val();
        var scale_filter = $('.scale_filter_selector').val();
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            cache: false,
            data: {project_id: proID, scale_filter: scale_filter},
            success: function (result) {
                $('#' + div).html(result);
            }
        });
    }
    
    $('#btn_display_allmonth').click(function(){
        var project_id = '<?= $project_id ?>';
        var tab = '<?= $tab ?>';
        var action = $(this).attr('data-action');
        var url = '';
        if(action === 'year'){
            $(this).attr('data-action','month');
            $(this).html('<i class="fa fa-search-minus" aria-hidden="true"></i> Display current month');
            $(this).addClass('btn-success');
            $(this).removeClass('btn-primary');
            
            url = $('#display-calendar').attr('data-url')+ '&project_id=' + project_id+ '&tab=' + tab+"&viewYear=1";
            $('#display-calendar').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
            getReloadDiv(url, 'display-calendar');
        }else{
            $(this).attr('data-action','year');
            $(this).html('<i class="fa fa-search-minus" aria-hidden="true"></i> Display all month of year');
            $(this).addClass('btn-primary');
            $(this).removeClass('btn-success');
            url = $('#display-calendar').attr('data-url')+ '&project_id=' + project_id+ '&tab=' + tab;
            $('#display-calendar').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
            getReloadDiv(url, 'display-calendar');
        }
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>