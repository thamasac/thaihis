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
$reject_form = backend\modules\ezforms2\classes\EzfQuery::getEzformOne(\backend\modules\gantt\Module::$formsId['reject_request_form']);
$user_id = Yii::$app->user->id;
$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center', 'width' => '50px'],
        'contentOptions' => ['style' => 'text-align: center'],
    ],
];

$columns[] = [
    'attribute' => "user_request",
    'header' => "User request",
    'format' => 'raw',
    'value' => function ($data) {
        $userData = EzfQuery::getUserProfile($data['user_request']);
        return $userData['firstname'] . " " . $userData['lastname'];
    },
    'headerOptions' => ['style' => 'text-align: left;'],
];

$columns[] = [
    'attribute' => "Manage",
    'header' => "Manage",
    'format' => 'raw',
    'value' => function ($data) use ($request_ezf_id,$reject_form) {
        $check_approve = SubjectManagementQuery::GetTableData('pms_task_target', ['dataid' => $data['id']], 'one');
        if ($data['status_request']=='2') {
            $hrml = \backend\modules\ezforms2\classes\EzfHelper::btn($request_ezf_id)->label("<i class='fa fa-pencil'></i> View")->options(['class' => 'btn btn-default'])->buildBtnView($data['id']);
            $user = appxq\sdii\utils\SDUtility::array2String($check_approve['assign_user']);
            $hrml .= Html::label("Approved", '', ['class' => 'label label-success', 'style' => 'margin-left:10px;']);
        }else if($data['status_request']=='3'){
            $hrml = \backend\modules\ezforms2\classes\EzfHelper::btn($request_ezf_id)->label("<i class='fa fa-pencil'></i> View")->options(['class' => 'btn btn-default'])->buildBtnView($data['id']);
            $hrml .= Html::label("Reject", '', ['class' => 'label label-warning', 'style' => 'margin-left:10px;']);
        } else {
            $hrml = \backend\modules\ezforms2\classes\EzfHelper::btn($request_ezf_id)->label("<i class='fa fa-pencil'></i> View")->options(['class' => 'btn btn-default'])->buildBtnView($data['id']);
            $hrml .= Html::button("<i class='fa fa-gavel'></i> Approve", ['class' => 'btn btn-success btn-approve-request', 'style' => 'margin-left:10px;'
                ,'data-dataid'=>$data['id'],'data-task_dataid'=>$data['target']]);
            $hrml .= \backend\modules\ezforms2\classes\EzfHelper::btn($reject_form['ezf_id'])->reloadDiv('grid_request_list')->target($data['id'])->label("<i class='fa fa-reply'></i> Reject")->options(['class' => 'btn btn-warning btn_reject_request','style'=>'margin-left:10px;'])->buildBtnAdd();
            
        }

        return $hrml;
    },
    'headerOptions' => ['style' => 'text-align: left;'],
];
?>

<?php //\yii\widgets\Pjax::begin();   ?>

<div class="panel-heading">
    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
    <label style="padding-top: 5px;padding-left: 10px;"> Request List</label>
</div>
<?php
echo GridView::widget([
    'id' => 'grid-request-list',
    'dataProvider' => $dataProvider,
    'columns' => $columns, // check the configuration for grid columns by clicking button above
]);
?>
</div>


<?php //\yii\widgets\Pjax::end();   ?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>

<script>
    $('#grid-request-list .pagination a').on('click', function () {
        return false;
    });

    $('.btn-group-action').on('click', function () {

    });
    $('.btn-approve-request').on('click', function () {
        var request_type = $(this).attr('data-request_type');
        var dataid = $(this).attr('data-dataid');
        var task_dataid = $(this).attr('data-task_dataid');
        var taskid = $(this).attr('data-taskid');
        var target = '<?=$project_id?>';
        var module_id = '<?=$module_id?>';
        var user_id = '<?=$user_id?>';
        var btn_request = $(this);
        var txt = "You confirm to approve?";
        yii.confirm(txt, function () {
            $.get('/gantt/gantt/request-approve-save', {task_dataid: task_dataid,dataid:dataid, taskid: taskid, target: target, task_ezf_id: '<?= $task_ezf_id ?>', module_id: module_id}, function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>;
                var btn_app = btn_request.parents('tbody').find('tr');
                btn_request.parent().append("<label class='label label-success' style='margin-left:10px;'>Approved</label>");
                btn_request.parent().find(".btn_reject_request").remove();
                btn_request.remove(); 
                btn_app.each(function(i,e){
                    $(e).find('td').find('.btn-approve-request').remove();
                    $(e).find('td').find('.btn-reject-request').remove();
                });
            });
        }, function () {
        });
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
