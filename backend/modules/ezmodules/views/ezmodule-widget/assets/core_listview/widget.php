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
$query_params = isset($options['query_params'])?$options['query_params']:'target={target}&dataid={key_id}';
$image_wigth = isset($options['image_wigth'])?$options['image_wigth']:64;
$image = isset($options['image'])?$options['image']:'';
$search = isset($options['search'])?$options['search']:'';
$page_size = isset($options['page_size'])?$options['page_size']:50;
$key_id = isset($options['key_id'])?$options['key_id']:'';
$template_content = isset($options['template_content'])?$options['template_content']:'';

$class_css = isset($options['class_css'])?$options['class_css']:'';
$style = isset($options['style'])?$options['style']:'';
$layout = isset($options['layout'])?$options['layout']:'';
$placeholder = isset($options['placeholder'])?$options['placeholder']:'';

$reloadDiv = 'listview-sql-'.$widget_config['widget_varname'];

if($sql_id>0){
    $conentDisplay = backend\modules\ezforms2\classes\ListViewBuilder::contentDisplay()
        ->sql_id($sql_id)
        ->target($target)
        ->reloadDiv($reloadDiv)
        ->query_params($query_params)
        ->image($image)
        ->image_wigth($image_wigth)
        ->search($search)
        ->params($params_all)
        ->page_size($page_size)
        ->key_id($key_id)
        ->class_css($class_css)
        ->style($style)
        ->layout($layout)
        ->placeholder($placeholder)
        ->template_content($template_content);
        
    echo $conentDisplay->buildListView();
} else {
    echo '<div class="alert alert-danger" role="alert"> <strong>Oop Side Menu!</strong> Please select sql. </div>';
}
?>
