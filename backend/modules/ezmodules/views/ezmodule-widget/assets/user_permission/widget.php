<?php
use \richardfan\widget\JSRegister;
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

<!-- config start -->

<!-- config end-->
<div class="text-right">
<?php 
    echo \backend\modules\ezforms2\classes\EzfHelper::btn()
            ->ezf_id('1519034841046921800')
            ->label('<i class="fa fa-plus"></i> ')
            ->reloadDiv('div_permission')
            ->buildBtnAdd();
?>
</div>    
<div id="div_permission">
  <?= \backend\modules\ezforms2\classes\EzfHelper::ui()
        ->ezf_id("1519034841046921800")
        ->reloadDiv('div_permission')
         ->default_column(FALSE)
        ->buildGrid();?>  
</div>