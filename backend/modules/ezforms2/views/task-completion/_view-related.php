<?php

use backend\modules\ezforms2\classes\EzActiveForm;
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\subjects\classes\SubjectManagementQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$this->registerCssFile("@web/css/checkbox-style.css?2");
?>
<?php
$form = EzActiveForm::begin([
            'id' => 'form-submit',
            'action' => ['/ezforms2/task-completion/save-task-related',
                'main_task' => isset($main_task) ? $main_task : null,
            ],
            'options' => [
                'enctype' => 'multipart/form-data',
            ]
        ]);
?>
<div class="modal-header">
    <button type="button" class="close btn-close-modal2" data-dismiss-modal="modal2">&times;</button>
    <h4>Select Task Related</h4>
</div>
<div class="modal-body">

    <?php
    
    foreach ($dataAll as $key => $value):
        
        $taskAmt = [];
        $taskChkAmt = [];
        $taskAmt[$value['id']] = count($value['taskall']);
        ?>
        <div class="row" style="margin-left: 50px">
            <div class="col-md-6 sdbox-col" id="subtask-<?= $key ?>">
                <div class="checkbox1 checkbox1-success">

                    <?=
                    Html::checkbox('checkbox[' . $key . '][id]', '', [
                        'id' => 'checkbox-' . $key,
                        'value' => $value['id'],
                        'class' => "check-subtask-active",
                    ])
                    ?>
                    <?= Html::label($value['sub_name'], 'checkbox-' . $key, ['style' => 'font-weight:bold;font-size:18px;']) ?>
                    <?= Html::hiddenInput('checkbox[' . $key . '][name]', $value['sub_name']) ?>
                </div>
                <div class="col-md-8 task_childen" >
                    <?php foreach ($value['taskall'] as $key2 => $value2):
                        $checkState = SubjectManagementQuery::GetTableData('task_related', ['maintask'=>$main_task,'subtask'=>$value['id'],'task_id'=>$value2['id'],'dataid'=>$dataid],'one');
                        
                        $checked = '';
                        if($checkState)
                        {
                            $taskChkAmt[$value['id']] ++;
                            $checked=true;
                        }
                        ?>
                        <div class="checkbox1 checkbox1-success">
                            <?=
                            Html::checkbox('checkbox[' . $key . '][task_all][' . $value2['id'] . '][id]', $checked, [
                                'id' => 'checkbox-' . $key . '-' . $value2['id'],
                                'value' => $value2['id'],
                                'class' => "check-task-active",
                                'data-sub_task' => $value['id'],
                            ]);
                            ?>
                            <?= Html::label($value2['task_name'], 'checkbox-' . $key . '-' . $value2['id']) ?>
                            <?= Html::hiddenInput('checkbox[' . $key . '][task_all][' . $value2['id'] . '][name]', $value2['task_name'], []) ?>
                        </div>

                    <?php endforeach; ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php endforeach; ?>

</div>
<div class="modal-footer">
    <?= Html::button("Close", ['class' => 'btn btn-defualt pull-right', 'data-dismiss' => 'modal']); ?>
</div>
<?php EzActiveForm::end(); ?>

<?php
\richardfan\widget\JSRegister::begin([
//'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>

<script>
    $('button[data-dismiss-modal = modal2]').click(function () {
        $('#modal-ezform-community').modal('hide');
    });
    $('.check-subtask-active').click(function () {
        var childen = $(this).parent().parent().find(".task_childen");
        if ($(this).is(":checked")) {
            childen.find(".check-task-active").each(function (i, e) {
                $(e).prop('checked', true);
            });
        } else {

            childen.find(".check-task-active").each(function (i, e) {
                $(e).prop('checked', false);
            });
        }
    });

    $('.check-task-active').on('change', function () {
        var checked = $(this).is(':checked');
        var dataid = '<?= $dataid ?>';
        var taskid = $(this).val();
        var subtask = $(this).attr('data-sub_task');
        var maintask = '<?= $main_task ?>';
        var ezf_id = '<?=$ezf_id?>';
        var url = '/ezforms2/task-completion/save-task-related';
        $.get(url, {maintask: maintask, subtask: subtask, taskid: taskid, dataid: dataid, checked: checked,ezf_id:ezf_id}, function (result) {
            if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>;
                $('#form-submit .btn-submit').attr('disabled', true);
            } else {
<?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ?>;

            }
        });
    });

    $('form#form-submit').on('beforeSubmit', function (e) {

        var $form = $(this);
        var formData = new FormData($(this)[0]);
        $('#form-submit .btn-submit').attr('disabled', false);
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'JSON',
            enctype: 'multipart/form-data',
            processData: false, // tell jQuery not to process the data
            contentType: false, // tell jQuery not to set contentType
            success: function (result) {
                if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>;
                    $('#form-submit .btn-submit').attr('disabled', true);
                } else {
<?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ?>;

                }
            },
            error: function () {
<?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ?>;
            }
        });
        return false;
    });
    $('.check-subtask-active').on('click', function () {

    });
    $('.check-task-active').on('click', function () {

    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>