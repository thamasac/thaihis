<?php
if(isset($options['target']) && !empty($options['target']) && $options['target']!='target'){
    $target = isset($_GET[$options['target']])?$_GET[$options['target']]:'';
} 

$dataid = '';
if(isset($options['dataid']) && !empty($options['dataid'])){
    $dataid = isset($_GET[$options['dataid']])?$_GET[$options['dataid']]:'';
} 

$initdate = (isset($options['initdate']) && !empty($options['initdate']) && !empty($target))?$options['initdate']:'';
$ezf_id = isset($options['ezf_id'])?$options['ezf_id']:0;
$show = isset($options['show'])?$options['show']:0;
                
$reloadDiv = 'content-'.$widget_config['widget_varname'];

$conentDisplay = backend\modules\ezforms2\classes\ContentBuilder::contentDisplay()
        ->ezf_id($ezf_id)
        ->target($target)
        ->dataid($dataid)
        ->reloadDiv($reloadDiv)
        ->fields(isset($options['fields'])?$options['fields']:[])
        ->title(isset($options['title'])?$options['title']:'')
        ->initdata((isset($options['initdata']) && !empty($target))?$options['initdata']:FALSE)
        ->initdate($initdate)
        ->disabled_box(isset($options['disabled_box'])?$options['disabled_box']:0)
        ->column(isset($options['column'])?$options['column']:[])
        ->action(isset($options['action'])?$options['action']:[])
        ->image_field(isset($options['image_field'])?$options['image_field']:'')
        ->template_content(isset($options['template_content'])?$options['template_content']:'')
        ->template_box(isset($options['template_box'])?$options['template_box']:'')
        ->display(isset($options['display'])?$options['display']:'')
        ->theme(isset($options['theme'])?$options['theme']:'');
   

    if($show){
        if(!empty($target) || !empty($dataid)){
            echo $conentDisplay->buildBox();
        }
    } else {
        echo $conentDisplay->buildBox();
    }
    
?>