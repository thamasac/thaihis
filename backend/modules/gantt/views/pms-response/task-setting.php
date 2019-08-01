<?php

use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\ezforms2\classes\EzfQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$ezform = EzfQuery::getEzformOne($ezf_id);
$dataDetail = EzfQuery::getTarget($ezform['ezf_table'], $taskid);
?>
<div class="container-fluid">
    <div id="display-detail">
    <?= isset($dataDetail['detail'])?$dataDetail['detail']:'' ?>
    </div>
<?= yii\helpers\Html::a(Yii::t('ezform', '+ More'), 'javascript:void(0)', ['id'=>'click-more']) ?>
    <div id="display-ezform-task">
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

    $('#click-more').click(function () {
        $(document).find('.btn-open-form').removeClass('active');
        $(this).addClass('active');
        var url = '/ezforms2/ezform-data/ezform-view';
        var ezf_id = '<?= $ezf_id ?>';
        var data_id = '<?= $taskid ?>';
        var divData = $('#display-ezform-task');

        divData.find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');

        var data = {
            ezf_id: ezf_id,
            reloadDiv: 'show-data-table',
            dataid: data_id,
            modal: 'display-ezform-task',
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

</script>
<?php \richardfan\widget\JSRegister::end(); ?>