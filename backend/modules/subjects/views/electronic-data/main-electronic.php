<?php

use kartik\tabs\TabsX;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo \appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-export',
    'size' => 'modal-sm',
]);
?>

<?=
\kartik\tabs\TabsX::widget([
    'position' => TabsX::POS_ABOVE,
    'align' => TabsX::ALIGN_LEFT,
    'items' => [
        [
            'label' => 'Dashboard EDC',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'dashboard_edc'],
            'active' => true,
        ],
        [
            'label' => 'Electronic Data Capture',
            'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'electronic_data'],
        ],
    ]
]);
?>

<div id="display_group_edc" data-url="<?=
     Url::to([
         '/subjects/electronic-data/electronic-data',
         'widget_id' => $widget_id,
         'schedule_id' => $schedule_id,
         'procedure_id' => $procedure_id,
         'financial_id' => $financial_id,
         'options' => $options,
         'reloadDiv' => $reloadDiv,
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
    $(function () {
        var div_show = $('#display_group_edc');
        var url = div_show.attr('data-url');
        div_show.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {view:'group-electronic'},function(result){
            div_show.html(result);
        });
    });

    $('#dashboard_edc').click(function () {
        var div_show = $('#display_group_edc');
        var url = div_show.attr('data-url');
        div_show.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {view:'group-electronic'},function(result){
            div_show.html(result);
        });
    });

    $('#electronic_data').click(function () {
        var div_show = $('#display_group_edc');
        var url = div_show.attr('data-url');
        div_show.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {view:'electronic-data'},function(result){
            div_show.empty();
            div_show.html(result);
        })
    });


</script>
<?php \richardfan\widget\JSRegister::end(); ?>
