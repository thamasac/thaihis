<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$url_link = null;
$ezform_to = null;
$ezmodule_to = null;
$ezform_to_data = null;
$ezformTask = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($task_ezf_id);
$dataTask = \backend\modules\subjects\classes\SubjectManagementQuery::GetTableData($ezformTask, ['id' => $task_dataid], 'one');

?>

<div class="clearfix"></div>
<br/>
<div class="container-fluid">
    <div id="display-ezform">
        <div class="modal-content">

        </div>
        <br/>
    </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
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
        var divData = $('#display-ezform');
        
        divData.find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');

        var data = {
            ezf_id: ezf_id,
            reloadDiv: '<?=$reloadDiv?>',
            modal: 'display-ezform',
            target: taskid,
        }

        if (dataid && dataid != '') {
            data = {
                ezf_id: ezf_id,
                reloadDiv: '<?=$reloadDiv?>',
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
                $(document).find(".glyphicon-remove").parent().remove();
                $(document).find(".close").remove();
            }
        });
    });
    $('#btn_goto_ezmodule').click(function () {
        var url = "/ezmodules/ezmodule/view?id="+$(this).attr('data-ezm_id');
        window.open(url);
    });

    $('#btn_goto_urllink').click(function () {
        var url = $(this).attr('data-url');
        window.open(url);
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>