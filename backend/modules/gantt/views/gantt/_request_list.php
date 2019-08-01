<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\subjects\classes\JKDate;
use appxq\sdii\helpers\SDNoty;
use yii\grid\GridView;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 */
?>

<div id="grid_request_list" data-url="<?=
     Url::to(['/gantt/gantt/request-grid',
         'task_dataid' => $task_dataid,
         'taskid' => $taskid,
         'request_ezf_id' => $request_ezf_id,
         'task_ezf_id' => $task_ezf_id,
         'module_id' => $module_id,
         'project_id' => $project_id,
     ])
     ?>"></div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>

<script>
    $(function () {
        $('#grid_request_list').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        //setTimeout(function () {
            var url = $('#grid_request_list').attr('data-url');

            $.get(url, {}, function (result) {
                $('#grid_request_list').empty();
                $('#grid_request_list').html(result);
            });
        //}, 1000);

    });

    $('#grid-request-list .pagination a').on('click', function () {
        return false;
    });

    $('.btn-group-action').on('click', function () {

    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
