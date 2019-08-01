<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\gantt\Module;
use appxq\sdii\helpers\SDNoty;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$pmsModId = backend\modules\gantt\Module::$pmsModuleId;
$pmsUrl = "/ezmodules/ezmodule/view?id=".$pmsModId;
$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;width:60px;'],
        'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
    ],
    [
        'header' => 'PMS main task',
        'attribute' => 'main_task_name',
        'format' => 'raw',
        'value'=>function($model) use ($pmsUrl){
            $html = Html::a($model['main_task_name'], $pmsUrl."&pmsid=".$model['target'], ['target'=>'_blank']);
            return $html;
        },
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:100px;text-align: right;'],
    ],
    [
        'header' => 'Task Name',
        'attribute' => 'task_name',
        'format' => 'raw',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:100px;text-align: right;'],
    ],
    [
        'header' => 'Start Date',
        'attribute' => 'start_date',
        'format' => 'raw',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:100px;text-align: center;'],
    ],
    [
        'header' => 'End Date',
        'attribute' => 'end_date',
        'format' => 'raw',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:100px;text-align: center;'],
    ],
    [
        'header' => 'Progress',
        'attribute' => 'progress',
        'format' => 'raw',
        'value' => function($model) {
            $progress_style = "progress-bar-info";
            if ($model['progress'] < 25) {
                $progress_style = "progress-bar-danger";
            } else if ($model['progress'] >= 25 && $model['progress'] < 50) {
                $progress_style = "progress-bar-warning";
            } else if ($model['progress'] == 100) {
                $progress_style = "progress-bar-success";
            }
            $progress = 0;
            if (isset($model['progress']) && $model['progress'] != '')
                $progress = number_format($model['progress'], 1);
            else
                $progress = isset($model['progress']) ? $model['progress'] : 0;

            if ($progress == '')
                $progress = 0;
            $html = '<div class="col-md-12 "><div class="progress">
                    <div class="progress-bar ' . $progress_style . '" role="progressbar" aria-valuenow="' . $model['progress'] . '"
                         aria-valuemin="0" aria-valuemax="100" style="width:' . $model['progress'] . '%">
                    </div>
                    
                </div><label style="position: absolute;top:0px;">' . $progress . ' % </label></div>';
            return $html;
        },
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:100px;text-align: center;'],
    ],
    [
        'header' => 'Response',
        'attribute' => 'task_response',
        'format' => 'raw',
        'value' => function($model) {
            $checkAccept = false;
            $user_id = Yii::$app->user->id;
            $assign_user = \appxq\sdii\utils\SDUtility::string2Array($model['assign_user']);
            $assign_user_accept = \appxq\sdii\utils\SDUtility::string2Array($model['assign_user_accept']);
            if(in_array($user_id, $assign_user)){
                if(in_array($user_id, $assign_user_accept)){
                    $checkAccept = true;
                }
            }
            if($checkAccept){
                $html = Html::button(Yii::t('gantt', "Response Task"), ['class' => 'btn btn-info btn_task_response'
                            , 'data-task_dataid' => isset($model['dataid']) ? $model['dataid'] : ''
                            , 'data-response_id' => isset($model['response_id']) ? $model['response_id'] : ''
                ]);
            }else{
               $html = Html::button(Yii::t('gantt', "<i class='fa fa-thumbs-o-up'></i> Accept Task"), ['class' => 'btn btn-success btn_accept_task'
                            , 'data-task_dataid' => isset($model['dataid']) ? $model['dataid'] : ''
                            , 'data-taskid' => isset($model['id']) ? $model['id'] : ''
                            , 'data-ezf_id' => isset($model['ezf_id']) ? $model['ezf_id'] : ''
                            , 'data-target' => isset($model['target']) ? $model['target'] : ''
                            , 'data-user_create' => isset($model['user_create']) ? $model['user_create'] : ''
                ]); 
               $html .= Html::button(Yii::t('gantt', "<i class='fa fa-hand-paper-o'></i> Decline Task"), ['class' => 'btn btn-warning btn_decline_task'
                            , 'style'=>'margin-left:5px;'
                            , 'data-task_dataid' => isset($model['dataid']) ? $model['dataid'] : ''
                            , 'data-taskid' => isset($model['id']) ? $model['id'] : ''
                            , 'data-ezf_id' => isset($model['ezf_id']) ? $model['ezf_id'] : ''
                            , 'data-target' => isset($model['target']) ? $model['target'] : ''
                            , 'data-user_create' => isset($model['user_create']) ? $model['user_create'] : ''
                ]); 
            }
            return $html;
        },
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:100px;text-align: center;'],
    ]
];
?>

<?=
yii\grid\GridView::widget([
    'id' => "pms-workbrench-grid",
    'dataProvider' => $dataProvider,
    'filterModel' => isset($searchModel) ? $searchModel : null,
    'columns' => $columns,
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
    $('.btn_task_response').click(function () {
        onModalDialog(this);
    });
    function onModalDialog(e) {
        var task_dataid = $(e).attr('data-task_dataid');
        var response_id = $(e).attr('data-response_id');

        var data = {taskid: task_dataid, modal: 'modal-ezform-main'};
        if (response_id) {
            var data = {dataid: response_id, taskid: task_dataid, modal: 'modal-ezform-main'};
        }


        var url = '<?=
Url::to(['/gantt/pms-response/index',
    'page_from' => 'pms',
    'ezf_id' => $activity_ezf_id,
    'response_ezf_id' => $response_ezf_id,
    'other_ezforms' => base64_encode(\appxq\sdii\utils\SDUtility::array2String($other_ezforms)),
    'action' => 'view',
        //'reloadDiv'=>'display-gantt',
]);
?>';

    $('#modal-ezform-main').modal();
    $('#modal-ezform-main').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
    $.ajax({
        url: url,
        method: 'get',
        data: data,
        cache: true,
        success: function (data) {
            $('#modal-ezform-main').find('.modal-content').empty();
            $('#modal-ezform-main').find('.modal-content').html(data);

        }
    });

    }
    
        $('.btn_accept_task').on('click', function () {
        var dataid = $(this).attr('data-task_dataid');
        var taskid = $(this).attr('data-taskid');
        var target = $(this).attr('data-target');
        var ezf_id = $(this).attr('data-ezf_id');
        var user_create = $(this).attr('data-user_create');

        $.get('/gantt/gantt/accept-task', {task_dataid: dataid, taskid: taskid, target: target, task_ezf_id:ezf_id, task_owner: user_create}, function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>;
        getReloadDiv($('#<?=$reloadDiv?>').attr('data-url'),'<?=$reloadDiv?>');
        });
    });

    $('.btn_decline_task').on('click', function () {
        var dataid = $(this).attr('data-task_dataid');
        var taskid = $(this).attr('data-taskid');
        var target = $(this).attr('data-target');
        var ezf_id = $(this).attr('data-ezf_id');
        var user_create = $(this).attr('data-user_create');

        var url = '<?=
Url::to(['/ezforms2/ezform-data/ezform',
    'modal' => 'modal-ezform-main',
    'ezf_id' => \backend\modules\gantt\Module::$formsId['decline_task_form'],
]);
?>';
        $('#modal-ezform-main').modal();
        $('#modal-ezform-main').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {modal: 'modal-ezform-main', task_dataid: dataid, taskid: taskid, target: dataid,reloadDiv:'<?=$reloadDiv?>'}, function (result) {
            $('#modal-ezform-main').find('.modal-content').empty();
            $('#modal-ezform-main').find('.modal-content').html(result);
        });
    });
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>