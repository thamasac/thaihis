<?php

use backend\modules\gantt\Module;
use backend\modules\gantt\classes\GanttQuery;
use backend\modules\subjects\classes\SubjectManagementQuery;
use appxq\sdii\utils\VarDumper;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// $taskid is task_dataid
$user_id = Yii::$app->user->id;
$url_link = null;
$ezform_to = null;
$ezmodule_to = null;
$ezform_to_data = null;
$response_own_id = Module::$formsId ['res_own_ezf_id'];
$res_own_form = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($response_own_id);
$ezformTask = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($task_ezf_id);
$dataTask = \backend\modules\subjects\classes\SubjectManagementQuery::GetTableDataNotEzform('pms_task_target', [
            'dataid' => $taskid
                ], 'one');

$OwnerData = GanttQuery::getMyCreatedData($taskid);

$checkOwner = 0;
if ($OwnerData)
    $checkOwner = 1;

$res_own_dataid = '';
$res_own_data = SubjectManagementQuery::GetTableData($res_own_form, "target='{$taskid}' ", 'one');

if (isset($res_own_data ['id'])) {
    $res_own_dataid = $res_own_data ['id'];
}

$reviewed = '0';
$approved = '0';
$assign_state = '0';
$accept_state = '0';
$task_status = $dataTask['task_status'];
$task_ezform = EzfQuery::getEzformOne($task_ezf_id);
$dataDetail = EzfQuery::getTarget($task_ezform['ezf_table'], $taskid);

if ($dataTask) {
    $url_link = $dataTask ['url_link'];
    $ezmodule_work = $dataTask ['ezmodule_work'];

    $user_review = appxq\sdii\utils\SDUtility::string2Array($dataTask['user_review']);

    $user_approve = appxq\sdii\utils\SDUtility::string2Array($dataTask['user_approver']);
    $user_review_name = [];
    $user_approve_name = [];

    foreach ($user_review as $val) {
        $userData = \backend\modules\ezforms2\classes\EzfQuery::getUserProfileById($val);
        $user_review_name[] = $userData['firstname'] . " " . $userData['lastname'];
    }

    $assign_user = appxq\sdii\utils\SDUtility::string2Array($dataTask['assign_user']);
    $assign_user_accept = appxq\sdii\utils\SDUtility::string2Array($dataTask['assign_user_accept']);

    if (in_array($user_id, $user_review)) {
        $reviewed = '1';
    }

    if (in_array($user_id, $user_approve)) {
        $approved = '1';
    }


    foreach ($user_approve as $val) {
        $userData = \backend\modules\ezforms2\classes\EzfQuery::getUserProfileById($val);
        $user_approve_name[] = $userData['firstname'] . " " . $userData['lastname'];
    }

    $user_review_str = count($user_review_name) > 0 ? join($user_review_name, ',') : '';
    $user_approve_str = count($user_approve_name) > 0 ? join($user_approve_name, ',') : '';

    if (in_array($user_id, $assign_user)) {
        $assign_state = '1';
        if (in_array($user_id, $assign_user_accept)) {
            $accept_state = '1';
        }
    }
}

?>


<?php if (Yii::$app->user->can('administrator') || $checkOwner == '1' || $assign_state == '1' || $isreviewer == '1' || $isapprover == '1'): ?>
    <?php if ($assign_state == '1' && $accept_state == '0'): ?>
        <div class="container-fluid">
            <div class="col-md-12" style="text-align:center;">
                <div class="container-fluid">
                    <div id="display-detail">
                    <?= isset($dataDetail['detail'])?$dataDetail['detail']:'' ?>
                    </div>
                </div><br/>
                <h4>You can see Task detail at Task detail tab</h4>
                <?php

                $html = Html::button(Yii::t('gantt', "<i class='fa fa-thumbs-o-up'></i> Accept Task"), ['class' => 'btn btn-success btn_accept_task'
                            , 'data-task_dataid' => isset($dataTask['dataid']) ? $dataTask['dataid'] : ''
                            , 'data-taskid' => isset($dataTask['id']) ? $dataTask['id'] : ''
                            , 'data-ezf_id' => isset($dataTask['ezf_id']) ? $dataTask['ezf_id'] : ''
                            , 'data-target' => isset($dataTask['target']) ? $dataTask['target'] : ''
                            , 'data-user_create' => isset($dataTask['user_create']) ? $dataTask['user_create'] : ''
                ]);
                $html .= Html::button(Yii::t('gantt', "<i class='fa fa-hand-paper-o'></i> Decline Task"), ['class' => 'btn btn-warning btn_decline_task'
                            , 'style' => 'margin-left:5px;'
                            , 'data-task_dataid' => isset($dataTask['dataid']) ? $dataTask['dataid'] : ''
                            , 'data-taskid' => isset($dataTask['id']) ? $dataTask['id'] : ''
                            , 'data-ezf_id' => isset($dataTask['ezf_id']) ? $dataTask['ezf_id'] : ''
                            , 'data-target' => isset($dataTask['target']) ? $dataTask['target'] : ''
                            , 'data-user_create' => isset($dataTask['user_create']) ? $dataTask['user_create'] : ''
                ]);

                echo $html;
                ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <br/>
    <?php else: ?>
        <div class="col-md-12" style="padding-left:20px;">
            <?php if ($url_link != null): ?>
                <?= \yii\helpers\Html::button(Yii::t('ezform', 'URL Link to Perform'), ['id' => 'btn_goto_urllink', 'class' => 'btn btn-info', 'data-url' => $url_link]) ?>
            <?php endif; ?>
            <?php
            if ($ezmodule_work != null):
                $moduleData = SubjectManagementQuery::GetTableDataNotEzform('ezmodule', ['ezm_id' => $ezmodule_work], 'one');
                ?>
                <div >
                    <?php
                    $image = "";
                    if (isset($moduleData['ezm_icon']) && !empty($moduleData['ezm_icon'])) {
                        $image = Html::img(Yii::getAlias('@storageUrl/module') . '/' . $moduleData['ezm_icon'], ['class' => 'img-rounded', 'class' => 'image', 'style' => 'width:70px;']);
                    } else {
                        $image = Html::img(ModuleFunc::getNoIconModule(), ['class' => 'img-rounded', 'class' => 'image', 'style' => 'width:70px;']);
                    }
                    ?>
                    <a href="/ezmodules/ezmodule/view?id=<?= $moduleData['ezm_id'] ?>" target="_blank"  title="<?= $moduleData['ezm_name'] ?>" data-id="<?= $moduleData['ezm_id'] ?>">
                        <?= $image ?>
                        <br/>
                        <?= $moduleData['ezm_short_title'] ?>

                    </a>
                </div>
            <?php endif; ?>
        </div>
        <div class="clearfix"></div><br/>

        <div class="container-fluid">
            <div id="display-ezform">
                <div class="modal-content" style="border: 0px solid gray;"></div>
            </div>
            <br />
        </div>
        </div>
        <div class="container-fluid">
            <div id="display-ezform-woner">
                <div class="modal-content" style="border: 0px solid gray;">
                </div>
            </div>
        </div>
        <div class="container-fluid pull-right">
            <?= yii\helpers\Html::label(Yii::t('gantt', "Number of Review: "), '', ['style' => 'font-size:16px;']) ?>
            <?= yii\helpers\Html::label('', '', ['id' => 'label_number_review', 'class' => 'label label-warning', 'style' => 'font-size:16px;', 'title' => 'Review by: ' . $user_review_str]) ?>
            <?php if ($isreviewer == '1'): ?>
                <?= $reviewed == '0' && $task_status != '3' ? yii\helpers\Html::button("<i class='fa fa-eye'></i> " . Yii::t('gantt', 'Reviewing'), ['class' => 'btn btn-warning btn_reviewing']) : '' ?>
            <?php endif; ?>
            <?= " <label>||</label> ".yii\helpers\Html::label(Yii::t('gantt', "Number of Approved: "), '', ['style' => 'font-size:16px;']) ?>
            <?= yii\helpers\Html::label('', '', ['id' => 'label_number_approve', 'class' => 'label label-success', 'style' => 'font-size:16px;', 'title' => 'Approved by: ' . $user_approve_str]) ?>
            
            <?php if ($isapprover == '1'): ?>
                <?= $approved == '0' && $task_status != '3' ? yii\helpers\Html::button("<i class='fa fa-gavel'></i> " . Yii::t('gantt', 'Approving'), ['class' => 'btn btn-success btn_approving']) : '' ?>
            <?php endif; ?>
        </div>
        <div class="clearfix"></div>
        <br />
        <div class="row form-group ">
            <div class="contianer">
                <div class="col-md-12" id="task_response_forum"></div>
            </div>

        </div>
    <?php endif; ?>


<?php else: ?>
    <div class="container-fluid">
        <div class="col-md-12" style="text-align:center;">
            <h2>Permission Denied</h2>
            <a href="/ezmodules/ezmodule/view?id=1521801182077746900" class="btn btn-default"><<< Back to PMS</a>
        </div>
    </div>
    <div class="clearfix"></div>
    <br/>
<?php endif; ?>
<?php
\richardfan\widget\JSRegister::begin([
    // 'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

// $project_id = "1520742111042203500";
?>
<script>

    $(function () {
        $(document).find('.btn-open-form').removeClass('active');
        $(this).addClass('active');
        var url = '/ezforms2/ezform-data/ezform';
        var ezf_id = '<?= $ezf_id ?>';
        var taskid = '<?= $taskid ?>';
        var dataid = '<?= $dataid ?>';
        var task_dataid = '<?= $task_dataid ?>';
        var res_own_ezf_id = '<?= $response_own_id ?>';
        var own_task = '<?= $checkOwner ?>';
        var res_own_dataid = '<?= $res_own_dataid ?>';
        var credit_points = '<?= $credit_points ?>';
        var reward_points = '<?= $reward_points ?>';
        var isapprover = '<?= $isapprover ?>';
        var isreviewer = '<?= $isreviewer ?>';
        var myassign = '<?= $accept_state ?>';
        if (own_task != '1' && myassign != '1' && (isreviewer == '1' || isapprover == '1')) {
            url = '/ezforms2/ezform-data/ezform-view';
        }

        $.get('/gantt/pms-response/get-number-reviewing', {task_dataid: task_dataid}, function (result) {
            $('#label_number_review').html(" " + result.review_max + " / " + result.reviewing);
            if (result.review_max == result.reviewing)
                $('.btn_reviewing').remove();
        });

        $.get('/gantt/pms-response/get-number-approving', {task_dataid: task_dataid}, function (result) {
            $('#label_number_approve').html("<span>" + result.approve_max + "</span> / " + result.approving);
            if (result.approve_max == result.approving)
                $('.btn_approving').remove();
        });


        var divData = $('#display-ezform');
        divData.find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');

        var data = {
            ezf_id: ezf_id,
            modal: 'display-ezform',
            target: taskid,
        }

        if (dataid && dataid != '') {
            data = {
                ezf_id: ezf_id,
                modal: 'display-ezform',
                target: taskid,
                dataid: dataid,
            }
        }

        $.ajax({
            url: url,
            type: 'html',
            method: 'get',
            data: data,
            success: function (result) {
                divData.find('.modal-content').html(result);
            }
        });

        if (isapprover == '1') {
            $('#display-ezform-woner').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
            var data_own = {ezf_id: res_own_ezf_id, target: taskid, modal: 'display-ezform', initdata: btoa(JSON.stringify({max_credit_points: credit_points, max_reward_points: reward_points}))};
            if (res_own_dataid != '') {
                data_own = {ezf_id: res_own_ezf_id, target: taskid, dataid: res_own_dataid, modal: 'display-ezform'};
            }
            $.ajax({
                url: '/ezforms2/ezform-data/ezform',
                type: 'html',
                method: 'get',
                data: data_own,
                success: function (result) {
                    $('#display-ezform-woner').find('.modal-content').html(result);
                }
            });
        }

        $('#task_response_forum').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url_forum = '/gantt/gantt-forum/view';

        $.ajax({
            url: url_forum,
            type: 'html',
            method: 'get',
            data: {parent_id: task_dataid},
            success: function (result) {
                $('#task_response_forum').html(result);
            }
        });

    });


    $('#btn_goto_ezmodule').click(function () {
        var url = "/ezmodules/ezmodule/view?id=" + $(this).attr('data-ezm_id');
        window.open(url);
    });

    $('#btn_goto_urllink').click(function () {
        var url = $(this).attr('data-url');
        window.open(url);
    });

    $('.btn_approving').on('click', function () {
        var taskid = '<?= $taskid ?>';
        yii.confirm('<?= Yii::t('app', 'Are you sure you want to approve this task?') ?>', function () {
            $.get(
                    '/gantt/pms-response/approver-approve?target=' + taskid
                    ).done(function (result) {
                $.get('/gantt/pms-response/get-number-approving', {task_dataid: taskid}, function (result) {
                    $('#label_number_approve').html("Number of Approved: " + result.approving + " / " + result.approve_max);
                });
<?= SDNoty::show('result.message', 'result.status') ?>;
            });
        }, function () {
        });
    });

    $('.btn_reviewing').on('click', function () {
        var taskid = '<?= $taskid ?>';
        var btn = $(this);
        btn.prop('disabled', true);
        yii.confirm('<?= Yii::t('app', 'Are you sure you want to reviewing?') ?>', function () {
            $.get(
                    '/gantt/pms-response/reviewing-save?task_dataid=' + taskid
                    ).done(function (result) {
                $.get('/gantt/pms-response/get-number-reviewing', {task_dataid: taskid}, function (result) {
                    $('#label_number_review').html("Number of Review " + result.reviewing + " / " + result.review_max);
                });
<?= SDNoty::show('result.message', 'result.status') ?>;
            });
        }, function () {
            btn.prop('disabled', false);
        });
    });

    $('.btn_accept_task').on('click', function () {
        var dataid = $(this).attr('data-task_dataid');
        var taskid = $(this).attr('data-taskid');
        var target = $(this).attr('data-target');
        var ezf_id = $(this).attr('data-ezf_id');
        var user_create = $(this).attr('data-user_create');

        $.get('/gantt/gantt/accept-task', {task_dataid: dataid, taskid: taskid, target: target, task_ezf_id: ezf_id, task_owner: user_create}, function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>;
            getReloadDiv($('#<?= $reloadDiv ?>').attr('data-url'), '<?= $reloadDiv ?>');
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
    'reloadDiv'=>$reloadDiv,
    'ezf_id' => \backend\modules\gantt\Module::$formsId['decline_task_form'],
]);
?>';
        $('#modal-ezform-main').modal();
        $('#modal-ezform-main').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, { task_dataid: dataid, taskid: taskid, target: dataid}, function (result) {
            $('#modal-ezform-main').find('.modal-content').empty();
            $('#modal-ezform-main').find('.modal-content').html(result);
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