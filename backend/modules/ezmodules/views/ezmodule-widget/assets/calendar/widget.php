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
if($tab=='2' ){
    echo \backend\modules\ezforms2\classes\CalendarBuilder::calendarWidget()
            ->ezf_id($options['ezf_id'])
            ->buildCalendar();
}
?>
