<?php
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfStarterWidget;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\helpers\Url;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="col-md-6 row">
    <?= yii\helpers\Html::button(Yii::t('subjects', 'Generated invoice'),['class'=>'btn btn-primary invoice_request'])?>
    <?= EzfHelper::btn($options['invoice_ezf_id'])->label('<i class="fa fa-plus"></i> '.Yii::t('subjects', 'Tracking Invoice'))->buildBtnAdd();?>
</div>
<div class="clearfix"></div>
<br/>
<?php
EzfStarterWidget::begin();
    echo EzfHelper::ui($options['invoice_ezf_id'])->default_column(false)->data_column($options['invoice_fields'])->addbtn(false)->title("Invoice")->buildGrid();
EzfStarterWidget::end();
?>
<?= appxq\sdii\widgets\ModalForm::widget([
    'id'=>'modal_tracking_invoice',
    'size'=>'modal-xl',
]);?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>

    $('.invoice_request').click(function () {
        var url = '<?=
Url::to(['/subjects/subject-management/group-subject-payment',
    'reloadDiv' => $reloadDiv,
    'schedule_id' => $schedule_id,
    'widget_id' => $widget_id,
    'options' => $options,
    'user_create' => $user_create,
    'user_update' => $user_update,
    'view'=>'group-subject-payment-invoice',
    ]);
?>';
        $('#modal_tracking_invoice').modal('show');
        $('#modal_tracking_invoice').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('#modal_tracking_invoice').find('.modal-content').load(url);
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>