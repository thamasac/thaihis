<?php
// start widget builder
use yii\helpers\Url;

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
'options' => $options,
'widget_config' => $widget_config,
'model' => $model, 
'modelOrigin'=>$modelOrigin,
'menu' => $menu,
'module' => $module,
'addon' => $addon,
'filter' => $filter,
'reloadDiv' => $reloadDiv,
'dataFilter' => $dataFilter,
'modelFilter' => $modelFilter,
'target' => $target,
    */
$target = Yii::$app->request->get('target');
$module_id = Yii::$app->request->get('id');
echo \yii\bootstrap\Modal::widget([
    'header' => '<h3 class="modal-title" id="itemModalLabel">Medical History </h3>',
    'footer' => \yii\helpers\Html::button('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']),
    'id' => 'modal-multiple-grid',
    'size' => 'modal-xxl',
]);

$itemReport = [];
if(isset($options['report_send']) && $options['report_send'] == '1'){
    $itemReport[0]['id'] = 'report-send';
    $itemReport[0]['name'] = 'Report send';
    $itemReport[0]['url'] = '/reports/report-checkup-send';
}

if(isset($options['checkup_config']) && $options['checkup_config'] == '1'){
    $itemReport[1]['id'] = 'checkup-config';
    $itemReport[1]['name'] = 'Checkup config';
    $itemReport[1]['url'] = '/reports/checkup-config';
}

if(isset($options['patient_config']) && $options['patient_config'] == '1'){
    $itemReport[2]['id'] = 'patient-config';
    $itemReport[2]['name'] = 'Paitent config';
    $itemReport[2]['url'] = '/reports/patent-config';
}

if(isset($options['report2doc']) && $options['report2doc'] == '1'){
    $itemReport[3]['id'] = 'report-to-doc';
    $itemReport[3]['name'] = 'Report to doctor';
    $itemReport[3]['url'] = '/reports/report-checkup/report-to-doc';
}

if(isset($options['report_opd']) && $options['report_opd'] == '1'){
    $itemReport[4]['id'] = 'report-opd';
    $itemReport[4]['name'] = 'Report OPD';
    $itemReport[4]['url'] = '/reports/report-admin/report-opd-index';
}

if(isset($options['report_app_checkup']) && $options['report_app_checkup'] == '1'){
    $itemReport[5]['id'] = 'report-appoint-checkup';
    $itemReport[5]['name'] = 'Report appoint checkup';
    $itemReport[5]['url'] = '/reports/report-admin/report-appoint-checkup';
}

?>
<div class="main-thaihis-report"></div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    // JS script
    $(function(){
       var div_content = $('.main-thaihis-report');
       var url = '<?= Url::to(['/reports/thaihis-report/index','item_report'=>$itemReport,'options'=>$options,])?>';
       div_content.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
       $.get(url,{'target':'<?=$target?>',module_id:'<?=$module_id?>'},function(result){
           div_content.html(result);
       });
    });
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>