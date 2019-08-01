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
$query_params = isset($options['query_params'])?$options['query_params']:'target={target}&dataid={key_id}';
$width = isset($options['width'])?$options['width']:400;
$image_wigth = isset($options['image_wigth'])?$options['image_wigth']:64;
$image = isset($options['image'])?$options['image']:'';
$search = isset($options['search'])?$options['search']:'';
$page_size = isset($options['page_size'])?$options['page_size']:50;
$key_id = isset($options['key_id'])?$options['key_id']:'';
$template_content = isset($options['template_content'])?$options['template_content']:'';
$align = isset($options['align'])?$options['align']:'';

$reloadDiv = 'dropdown-menu-'.$widget_config['widget_varname'];

if($sql_id>0){
    $conentDisplay = backend\modules\ezforms2\classes\DropdownMenuBuilder::contentDisplay()
        ->sql_id($sql_id)
        ->target($target)
        ->reloadDiv($reloadDiv)
        ->title($title)
        ->query_params($query_params)
        ->width($width)
        ->image($image)
        ->image_wigth($image_wigth)
        ->search($search)
        ->params($params_all)
        ->page_size($page_size)
        ->key_id($key_id)
        ->align($align)
        ->template_content($template_content);
        
    echo $conentDisplay->buildMenu();
} else {
    echo '<div class="alert alert-danger" role="alert"> <strong>Oop Dropdown Menu!</strong> Please select sql. </div>';
}
?>
