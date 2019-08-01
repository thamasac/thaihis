<?php
//create by joker
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
<h4><?= Yii::t('chanpan','My Workbench')?></h4><hr/>
<div id="<?= $module?>"></div>
<?php \richardfan\widget\JSRegister::begin();?>
<script>

</script>
<?php \richardfan\widget\JSRegister::end();?>
