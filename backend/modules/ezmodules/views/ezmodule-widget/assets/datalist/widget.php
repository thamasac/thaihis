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
   
$default_column = isset($options['default_column'])?$options['default_column']:1;
$pagesize = isset($options['pagesize'])?$options['pagesize']:50;
$order = isset($options['order'])?$options['order']:[];
$order_by = isset($options['order_by'])?$options['order_by']:4;
$db2 = isset($options['db2'])?$options['db2']:0;
$fields = isset($options['fields'])?$options['fields']:[];
$ezf_id = isset($options['ezf_id'])?$options['ezf_id']:0;
$header = isset($options['header'])?$options['header']:[];
$title_parnel = isset($options['title'])?$options['title']:'';
$actions = isset($options['actions'])?$options['actions']:[];
$disabled = isset($options['disabled'])?$options['disabled']:0;
$header = \yii\helpers\ArrayHelper::map($header, 'varname', 'label');

if(isset($options['target']) && !empty($options['target']) && $options['target']!='target'){
    $target = isset($_GET[$options['target']])?$_GET[$options['target']]:'';
}

$reloadDiv = $reloadDiv.'-custom';
?>

    <?php
    $uiView = \backend\modules\ezforms2\classes\EzfHelper::ui($ezf_id)
        ->data_column($fields)
        ->reloadDiv($reloadDiv)
        ->default_column($default_column)
        ->pageSize($pagesize)
        ->order_column($order)
        ->header($header)
        ->target($target)
        ->disabled($disabled)
        ->actions($actions)
        ->title($title_parnel)
        ->orderby($order_by);
    
    if($db2==1){
        echo $uiView->buildDb2Grid();
    } else {
        echo $uiView->buildGrid();
    }
    
    ?>
