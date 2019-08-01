<?php
// start widget builder

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

?>
<div id="show-monitoring"></div>

<?php
\richardfan\widget\JSRegister::begin([
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    var options =<?= json_encode($options); ?>;
    $.ajax({
        method: 'get',
        url: '/usfinding/monitoring/index',
        dataType: 'HTML',
        data: options,
        success: function (result) {
            $('#show-monitoring').html(result);
        }
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>
