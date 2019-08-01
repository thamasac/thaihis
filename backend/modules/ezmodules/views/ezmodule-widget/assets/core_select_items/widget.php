<?php
// start widget builder

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
'options' => $options,
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

$params_all = [];
if(isset($_GET)){
    $params_all = $_GET;
}

$sql_id = isset($options['sql'])?$options['sql']:0;
$title = isset($options['title'])?$options['title']:'';
$width = isset($options['width'])?$options['width']:300;
$image_wigth = isset($options['image_wigth'])?$options['image_wigth']:64;
$image = isset($options['image'])?$options['image']:'';
$search = isset($options['search'])?$options['search']:'';
$page_size = isset($options['page_size'])?$options['page_size']:10;
$key_id = isset($options['key_id'])?$options['key_id']:'';
$template_content = isset($options['template_content'])?$options['template_content']:'';
$template_items = isset($options['template_items'])?$options['template_items']:'';
$key_name = isset($options['key_name'])?$options['key_name']:'dataid';
$tab = isset($_GET['tab'])?$_GET['tab']:'';
$placeholder = isset($options['placeholder'])?$options['placeholder']:'Search ...';
$fields = isset($options['fields'])?$options['fields']:[];

$reload_widget = isset($options['reload_widget'])?$options['reload_widget']:'';
$ezf_id = isset($options['ezf_id'])?$options['ezf_id']:'';
$save_ezf_id = isset($options['save_ezf_id'])?$options['save_ezf_id']:'';
$after_save_url = isset($options['after_save_url'])?$options['after_save_url']:'';

if(isset($options['target']) && !empty($options['target']) && $options['target']!='target'){
    $target = isset($_GET[$options['target']])?$_GET[$options['target']]:'';
}
$reloadDiv = 'select-item-'.$widget_config['widget_varname'];
$current_url = \yii\helpers\Url::to(['/ezmodules/ezmodule/view','id'=>$module,'addon'=>$addon,'tab'=>$tab]);
if($sql_id>0){
    $conentDisplay = \backend\modules\ezforms2\classes\SelectItemBuilder::selectItem()
        ->sql_id($sql_id)
        ->target($target)
        ->reloadDiv($reloadDiv)
        ->title($title)
        ->width($width)
        ->image($image)
        ->data_column($fields)
        ->image_wigth($image_wigth)
        ->search($search)
        ->params($params_all)
        ->page_size($page_size)
        ->key_id($key_id)
        ->key_name($key_name)
        ->placeholder($placeholder)
        ->current_url($current_url)
        ->reload_widget($reload_widget)
        ->ezf_id($ezf_id)
        ->save_ezf_id($save_ezf_id)
        ->after_save_url($after_save_url)
        ->template_items($template_items);
        
    echo $conentDisplay->buildMenu();
} else {
    echo '<div class="alert alert-danger" role="alert"> <strong>Oop!</strong> Please select sql. </div>';
}
?>
