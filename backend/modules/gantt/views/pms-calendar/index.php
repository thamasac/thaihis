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

$fields = backend\modules\ezforms2\classes\EzfQuery::getFieldByName($maintask_ezf_id, 'pms_type');
$pms_type = \appxq\sdii\utils\SDUtility::string2Array($fields['ezf_field_data']);
if (!isset($tab) || $tab=='')
    $tab = '1';
$items = [];
foreach ($pms_type['items'] as $key => $value) {
    $items[] = [
        'label' => $value,
        'headerOptions' => ['style' => 'font-weight:bold', 'data-value' => $key, 'id' => 'tab_pms_type_' . $key, 'class' => 'tab_pms_type'],
        'active' => $tab == $key ? true : false,
    ];
}

echo TabsX::widget([
    'position' => TabsX::POS_ABOVE,
    'align' => TabsX::ALIGN_LEFT,
    'encodeLabels' => false,
    //'enableStickyTabs' => true,
    'items' => $items,
]);

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

$project_id = isset($_COOKIE['project_id']) ? $_COOKIE['project_id'] : $projectList[0]['id'];
if(isset($projectid) && $projectid!=''){
    $project_id = $projectid;
}
?>
<div class="row">
    <div class="col-sm-4 " id="dropdown_pms_maintask">
        <?= Html::label(Yii::t('ezmodule', 'Select Main Task'), 'project_name', ['class' => ' control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => 'project_name',
            'value' => $project_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Select Main Task'), 'class' => 'form-control', 'id' => 'config_main_task'],
            'data' => ArrayHelper::map($projectList, 'id', 'project_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-sm-4 sdbox-col " style="margin-top: 25px;">
        <?php
        if (EzfAuthFuncManage::auth()->accessBtn($module_id,1)) {
            echo EzfHelper::btn($maintask_ezf_id)->label(Yii::t('ezmodule', '<i class="fa fa-plus"></i> Add new Main Task'))->options(['class'=>'btn btn-success','style'=>'margin-right:5px;'])->reloadDiv($reloadDiv)->initdata(['pms_type' => $tab])->buildBtnAdd();
            if(isset($project_id) && $project_id != '')
                echo EzfHelper::btn($maintask_ezf_id)->label(Yii::t('ezmodule', '<i class="fa fa-trash"></i> Remove Main Task'))->reloadDiv($reloadDiv)->buildBtnDelete($project_id);
        }
        ?>

    </div>
    <div class="col-md-3 pull-right">
        <?= Html::label(Yii::t('subjects', "Filter Task"), 'filter_name') ?>
        <?= Html::dropDownList('scale_unit', isset($scale_filter) ? $scale_filter : '0', ArrayHelper::map($itemFilter, 'id', 'filter_name'), ['class' => 'form-control scale_filter_selector pull-left']) ?>
    </div>
    <div class="clearfix"></div>
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
        var projectid = '<?= $projectid ?>';

        var proId = $.cookie("project_id");

        if (proId) {
            project_id = proId;
        }

        if(projectid && projectid != ''){
            project_id = projectid;
        }
        
        if (project_id) {
            document.cookie = 'project_id=' + project_id + '; path=/';
            document.cookie = 'project_id=' + project_id + '; path=/ezmodules/ezmodule';
        }

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


</script>
<?php \richardfan\widget\JSRegister::end(); ?>