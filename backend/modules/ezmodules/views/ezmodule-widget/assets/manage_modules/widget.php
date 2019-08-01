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
<h4><?= Yii::t('chanpan','All Modules of the Project')?></h4><hr/>
<div id="<?= $module?>"></div>
<?php \richardfan\widget\JSRegister::begin();?>
<script>
    function loadManageModule(){
        let url = '<?= Url::to(['/manage_modules/default/index', 'status'=>'view', 'module_id'=>$module]);?>';
        $('#'+'<?= $module; ?>').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
        $.get(url, function(data){
           $('#'+'<?= $module; ?>').html(data);
        });
    }
    loadManageModule();
</script>
<?php \richardfan\widget\JSRegister::end();?>
