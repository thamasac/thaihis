<?php
use yii\helpers\Url;
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
<div id="<?= $module?>"></div>
<?php \richardfan\widget\JSRegister::begin();?>
<script>
       
    function loadMyWorkbench(){
        let url = '<?= Url::to(['/manage_modules/default/index','module_id'=>$module]);?>';
        $('#'+'<?= $module; ?>').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
        $.get(url, function(data){
           $('#'+'<?= $module; ?>').html(data);
        });
    }
    loadMyWorkbench();
</script>
<?php \richardfan\widget\JSRegister::end();?>
