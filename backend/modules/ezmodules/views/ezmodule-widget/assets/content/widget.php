<?php
if(isset($options['target']) && !empty($options['target']) && $options['target']!='target'){
    $target = isset($_GET[$options['target']])?$_GET[$options['target']]:'';
} 

$conentDisplay = backend\modules\ezforms2\classes\ContentBuilder::contentDisplay()
        ->ezf_id(isset($options['ezf_id'])?$options['ezf_id']:0)
        ->target($target)
        ->fields(isset($options['fields'])?$options['fields']:[])
        ->title(isset($options['title'])?$options['title']:'')
        ->initdata(isset($options['initdata'])?$options['initdata']:[])
        ->disabled_box(isset($options['disabled_box'])?$options['disabled_box']:1)
        ->column(isset($options['column'])?$options['column']:[])
        ->action(isset($options['action'])?$options['action']:[])
        ->image_field(isset($options['image_field'])?$options['image_field']:'')
        ->template_content(isset($options['template_content'])?$options['template_content']:'')
        ->template_box(isset($options['template_box'])?$options['template_box']:'')
        ->display(isset($options['display'])?$options['display']:'')
        ->theme(isset($options['theme'])?$options['theme']:'');
        
    echo $conentDisplay->buildBox();
?>