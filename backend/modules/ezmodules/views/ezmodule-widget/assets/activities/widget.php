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
//$header =  $options['header'];
//\appxq\sdii\utils\VarDumper::dump($options);
//appxq\sdii\utils\VarDumper::dump($options);
$tab = $_GET['tab'];
if(!isset($tab))$tab=1;
if($tab=='2' ){
echo \backend\modules\ezforms2\classes\ActivityBuilder::activityWidget()
        ->ezf_id($options['ezf_id'])
        ->fields($fields)
        ->title('My All Activities')
        ->buildActivity();
}
?>
