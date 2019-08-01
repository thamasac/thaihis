<?php

use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfQuery;
use appxq\sdii\utils\SDUtility;
use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\widgets\ModalForm;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div id="display_grid_group" style="margin-top: -20px" class="col-md-6" data-url="<?=
Url::to([
    '/subjects/schedule-config/grid-group',
    'module_id'=>$module_id,
    'widget' => $widget_id,
    'options' => $options,
    'user_create' => $user_create,
    'user_update' => $user_update,
    'schedule_id' => $options['schedule_id'],
    'reloadDiv' => 'display_grid_group',
])
?>">

</div>
<div class="clearfix"></div>
<div id="display_grid_visit" style="margin-top: -20px" class="col-md-12" data-url="<?=
Url::to([
    '/subjects/schedule-config/grid-visit',
    'widget' => $widget_id,
    'options' => $options,
    'user_create' => $user_create,
    'user_update' => $user_update,
    'schedule_id' => $options['schedule_id'],
    'reloadDiv' => 'display_grid_visit',
])
?>">
    <div class="clearfix"></div>
</div>
<?=
ModalForm::widget([
    'id' => 'modal-create-ezform',
    'size' => 'modal-lg',
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
    $(function () {
        var display_group = $('#display_grid_group');
        display_group.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = display_group.attr('data-url');
        display_group.attr('data-url', url)
        $.get(url, {}, function (result) {
            display_group.html(result);
        });


        var display_visit = $('#display_grid_visit');
        display_visit.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = display_visit.attr('data-url');
        display_visit.attr('data-url', url)
        $.get(url, {}, function (result) {
            display_visit.html(result);
        });
    });


</script>
<?php \richardfan\widget\JSRegister::end(); ?>