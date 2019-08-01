
<?php
if (!isset($modal)) {
    $modal = 'modal-content-widget' . $widget_config['widget_id'];
    echo appxq\sdii\widgets\ModalForm::widget([
        'id' => $modal,
        'size' => 'modal-xxl',
    ]);
}

$visit_id = Yii::$app->request->get('visitid');
$target = Yii::$app->request->get('target');
$reloadDiv = 'drug-content'.$widget_config['widget_id'];
echo \backend\modules\pis\classes\DrugOrderBuilder::contentBuilding()
        ->target($target)
        ->visitid($visit_id)
        ->modal($modal)
        ->reloadDiv($reloadDiv)
        ->fields($options['fields'])
        ->forms($options['refform'])
        ->visit_form($options['visit_ezf_id'])
        ->ezf_id($options['ezf_id'])
        ->buildBox('/pis/pis-item-order/grid-order');
?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
$urlDrugAll = yii\helpers\Url::to(['/pis/pis-item/drug-allergy']);
?>
<script>
    $(function(){
       $('#<?=$modal?>').on('hidden.bs.modal',function(){
           var url = $('#<?=$reloadDiv?>').attr('data-url');
           getUiAjax(url,'<?=$reloadDiv?>');
       }); 
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>