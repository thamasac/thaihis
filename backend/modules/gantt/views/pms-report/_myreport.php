<?php
use yii\helpers\Url;
use yii\helpers\Html;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<style>
    .active-select{
        background-color: #fbf069;
    } 
</style>
<div class="modal-header">
    <h3 class="modal-title pull-left"> <i class='fa fa-address-card'></i>  My Report</h3>
    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="container-fluid">
        
        <div class="alert alert-primary">
            <div class="col-md-3" >
                <label style="font-size: 18px;"><i class="fa fa-trophy" style="font-size:22px;"></i> Credit Points: </label>
                <label style="font-size: 18px;"><?= number_format($creditPoints) ?></label>
            </div>
            <div class="col-md-3" >
                <label style="font-size: 20px;"><i class="fa fa-money" style="font-size:22px;"></i> Reward Points: </label>
                <label style="font-size: 20px;"><?= number_format($rewardPoints) ?></label>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-md-3 sdbox-col">
        <div class="list-group task_list_group">
            <a  class="list-group-item list-group-item-info" data-keystore="all_tasks" style="text-align: right;font-size: 18px;">All Tasks <span style="color:blue">(<?= $taskAmt ?>)</span></a>
            <a  class="list-group-item " data-keystore="in_progress" style="text-align: right;font-size: 18px;">In progress <span style="color:blue">(<?= $taskOngoing ?>)</span></a>
            <a  class="list-group-item " data-keystore="completed" style="text-align: right;font-size: 18px;">Completed <span style="color:blue">(<?= $taskDone ?>)</span></a>
            <a  class="list-group-item " data-keystore="overdue" style="text-align: right;font-size: 18px;">Overdue <span style="color:blue">(<?= $taskOverdue ?>)</span></a>
            <a  class="list-group-item " data-keystore="reviewing" style="text-align: right;font-size: 18px;">Reviewing <span style="color:blue">(<?= $taskReviewing ?>)</span></a>
            <a  class="list-group-item " data-keystore="approving" style="text-align: right;font-size: 18px;">Approving <span style="color:blue">(<?= $taskApproving ?>)</span></a>
            <a  class="list-group-item " data-keystore="myreview" style="text-align: right;font-size: 18px;">Review <span style="color:blue">(<?= $creditPoints ?>)</span></a>
            <a  class="list-group-item " data-keystore="myapproved" style="text-align: right;font-size: 18px;">Approved <span style="color:blue">(<?= $creditPoints ?>)</span></a>
            <a  class="list-group-item " data-keystore="waiting_review" style="text-align: right;font-size: 18px;">Waiting review <span style="color:blue">(<?= $creditPoints ?>)</span></a>
            <a  class="list-group-item " data-keystore="waiting_approve" style="text-align: right;font-size: 18px;">Waiting approve <span style="color:blue">(<?= $creditPoints ?>)</span></a>
        </div>
            </div>
        <div class="col-md-9 sdbox-col" style="padding-right:0;">
            <div id="report_grid_content">
                
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        Close
    </button>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    $(function(){
        var keystore = "all_tasks";
        var url = '<?=yii\helpers\Url::to(['/gantt/pms-report/myreport-grid',

        ])
        ?>';
        var div = $('#report_grid_content');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');       
        $.get(url,{keystore:keystore},function(result){
            div.html(result);
        });
    });
    $('.task_list_group').on('click', '.list-group-item', function () {
        $('.task_list_group').find('.list-group-item-info').removeClass('list-group-item-info');
        $(this).addClass('list-group-item-info');
        var keystore = $(this).attr('data-keystore');

        var url = '<?=
yii\helpers\Url::to(['/gantt/pms-report/myreport-grid',

])
?>';
        var div = $('#report_grid_content');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');       
        $.get(url,{keystore:keystore},function(result){
            div.html(result);
        });
    });


</script>
<?php \richardfan\widget\JSRegister::end(); ?>