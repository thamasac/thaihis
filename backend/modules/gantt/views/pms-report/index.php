<?php
use yii\helpers\Url;
use yii\helpers\Html;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="col-md-8 sdbox-col" id="display-report-status" data-url="<?=
     yii\helpers\Url::to([
         '/gantt/pms-report/pms-report-status',
         'project_id'=>$project_id,
         'response_ezf_id' => $response_ezf_id,
         'task_ezf_id' => $task_ezf_id,
         'subtask_ezf_id' =>$subtask_ezf_id,
         'maintask_ezf_id' => $maintask_ezf_id,
         'response_actual_field'=> $response_actual_field,
         'start_date' => $start_date,
         'finish_date' => $finish_date,
         'progress' => $progress,
         'project_name' => $project_name,
         'cate_name' => $cate_name,
         'task_name' => $task_name,
         'reloadDiv'=>'display-report-status',
     ])
     ?>">

</div>
<div class="clearfix"></div>
<br/>
<div id="display-report-grid-user" data-url="<?=
     yii\helpers\Url::to([
         '/gantt/pms-report/pms-report-user',
         'project_id'=>$project_id,
          'response_ezf_id' => $response_ezf_id,
         'task_ezf_id' => $task_ezf_id,
         'subtask_ezf_id' =>$subtask_ezf_id,
         'maintask_ezf_id' => $maintask_ezf_id,
         'response_actual_field'=> $response_actual_field,
         'start_date' => $start_date,
         'finish_date' => $finish_date,
         'progress' => $progress,
         'project_name' => $project_name,
         'cate_name' => $cate_name,
         'task_name' => $task_name,
         'reloadDiv'=>'display-report-grid-user',
     ])
     ?>">

</div>
<br/>
<div id="display-report-grid" data-url="<?=
     yii\helpers\Url::to([
         '/gantt/pms-report/pms-report',
         'project_id'=>$project_id,
          'response_ezf_id' => $response_ezf_id,
         'task_ezf_id' => $task_ezf_id,
         'subtask_ezf_id' =>$subtask_ezf_id,
         'maintask_ezf_id' => $maintask_ezf_id,
         'response_actual_field'=> $response_actual_field,
         'start_date' => $start_date,
         'finish_date' => $finish_date,
         'progress' => $progress,
         'project_name' => $project_name,
         'cate_name' => $cate_name,
         'task_name' => $task_name,
         'reloadDiv'=>'display-report-grid',
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
    $(function(){
        var url = $('#display-report-status').attr('data-url');
        $('#display-report-status').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url,function(result){
            $('#display-report-status').empty();
            $('#display-report-status').html(result);
        });
        
        var url = $('#display-report-grid').attr('data-url');
        $('#display-report-grid').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url,function(result){
            $('#display-report-grid').empty();
            $('#display-report-grid').html(result);
        });
        
        var url = $('#display-report-grid-user').attr('data-url');
        $('#display-report-grid-user').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url,function(result){
            $('#display-report-grid-user').empty();
            $('#display-report-grid-user').html(result);
        });
    });
    
    function getReloadDiv(url, reloadDiv) {
        $.get(url, function (result) {
            $('#' + reloadDiv).empty();
            $('#' + reloadDiv).html(result);
        });
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>