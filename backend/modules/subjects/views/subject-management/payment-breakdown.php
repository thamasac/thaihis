<?php 
use yii\helpers\Url;
use yii\helpers\Html;
?>
<div id="show-payment-breakdown" data-url="<?= Url::to([
            '/subjects/subject-management/payment-breakdown-grid',
            'widget_id' => $widget_id,
            'schedule_id' => $schedule_id,
            'financial_id' => $financial_id,
            'procedure_id' => $procedure_id,
            'options' => $options,
            'user_create' => $user_create,
            'user_update' => $user_update,
            'reloadDiv' => 'show-payment-breakdown',
            'module_id' => $module_id,
        ])?>">
    
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
        var div_show = $('#show-payment-breakdown');
        div_show.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var url = '<?= yii\helpers\Url::to([
            '/subjects/subject-management/payment-breakdown-grid',
            'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'financial_id' => $financial_id,
                    'procedure_id' => $procedure_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'reloadDiv' => 'show-payment-breakdown',
                    'module_id' => $module_id,
        ])?>';
                
        $.get(url,function(result){
            div_show.html(result);
        })
    });
    $
</script>
<?php \richardfan\widget\JSRegister::end(); ?>