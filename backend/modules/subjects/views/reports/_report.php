<?php

use yii\helpers\Url;
use yii\helpers\Html;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="col-md-8 row">
    <div class="col-md-4">
        <?= Html::label(Yii::t('subjects', 'Start Date'), 'start_date') ?>
        <?=
        \kartik\date\DatePicker::widget([
            'name' => 'start_date',
            'id' => 'start_date',
            'value' => '',
        ])
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?= Html::label(Yii::t('subjects', 'End Date'), 'end_date') ?>
        <?=
        \kartik\date\DatePicker::widget([
            'name' => 'end_date',
            'id' => 'end_date',
            'value' => '',
        ])
        ?>
    </div>
    <div class="col-md-2 sdbox-col" style="margin-top:25px;">
<?= Html::button(Yii::t('subjects', Yii::t('subjects', 'Show Report')), ['class' => 'btn btn-primary btn-search']) ?>
    </div>

</div>
<div class="clearfix"></div>
<div id="show-report">

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
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var show_report = $('#show-report');
        show_report.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=
Url::to(['/subjects/reports/financial-report',
    'widget_id' => $widget_id,
    'schedule_id' => $schedule_id,
    'procedure_id' => $procedure_id,
    'options' => $options,
    'user_create' => $user_create,
    'user_update' => $user_update,
    'reloadDiv' => $reloadDiv,
])
?>';
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            success: function (data) {
                show_report.html(data);
            },
        });
    });
    
    $('.btn-search').click(function () {
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var show_report = $('#show-report');
        show_report.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?=
Url::to(['/subjects/reports/financial-report',
    'widget_id' => $widget_id,
    'schedule_id' => $schedule_id,
    'procedure_id' => $procedure_id,
    'options' => $options,
    'user_create' => $user_create,
    'user_update' => $user_update,
    'reloadDiv' => $reloadDiv,
])
?>';
        $.ajax({
            url: url,
            method: 'get',
            type: 'html',
            data:{start_date:start_date,end_date:end_date},
            success: function (data) {
                show_report.html(data);
            },
        });
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
