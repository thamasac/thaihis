
<?php

use appxq\sdii\widgets\ModalForm;
use yii\helpers\Url;
?>
<div class="gantt-default-index">      
    <section class="content">     
        <div class="sdbox-header">
            <h3><?= $project->project; ?></h3>
        </div>

        <div class="gantt_grid_scale" role="row" style="height: 35px; line-height: 34px; width: inherit;"><div class="gantt_grid_head_cell gantt_grid_head_text  " style="width:156px;" role="columnheader" aria-label="Task name" column_id="text">Task name</div><div class="gantt_grid_head_cell gantt_grid_head_start_date  " style="width:90px;" role="columnheader" aria-label="Start time" column_id="start_date">Start time</div><div class="gantt_grid_head_cell gantt_grid_head_duration  " style="width:70px;" role="columnheader" aria-label="Duration" column_id="duration">Duration</div><div class="gantt_grid_head_cell gantt_grid_head_add gantt_last_cell " style="width:43px;" role="button" aria-label="New task" column_id="add"></div></div>

        <div class="row">
            <div id="gantt_here" style='width:100%; height:1000px;'></div>
        </div>
    </section>   

</div>

<script src="/js-gantt/htmlgantt.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="/js-gantt/dhtmlxgantt.css" type="text/css" media="screen" title="no title" charset="utf-8">
<style type="text/css">
    html, body{ height:100%; padding:0px; margin:0px; overflow: scroll;}
</style>
<?=
ModalForm::widget([
    'id' => 'modal-ezform-gantt',
    'size' => 'modal-lg',
]);
?>

<?php
$this->registerJsFile(
        '@web/js-gantt/htmlgantt.js', ['depends' => [\yii\web\JqueryAsset::className()]]
);
//
//$this->registerCssFile("@web/js-gantt/dhtmlxgantt.css", [
//    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
//    'media' => 'print',
//], 'css-print-theme')
?>

<?php $this->registerJs("
    gantt.config.xml_date = '%Y-%m-%d %H:%i:%s';
    gantt.init('gantt_here');
    gantt.load('/gantt/default/connector?mid=<?= $project->id; ?>');

    var dp = new gantt.dataProcessor('/gantt/default/connector?mid=<?= $project->id; ?>');
    dp.init(gantt);
");
?>
