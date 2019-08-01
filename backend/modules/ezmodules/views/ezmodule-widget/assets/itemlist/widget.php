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
$tab = $_GET['tab'];
if(!isset($tab))$tab=1;
if($tab=='1' ){
   echo backend\modules\ezforms2\classes\ItemListBuilder::itemListWidget()
           ->ezf_id($ezf_id)
           ->title('All Form')
   ->buildItemList();
}
?>
