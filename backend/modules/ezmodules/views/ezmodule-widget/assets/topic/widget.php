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

?>
<div id="help-<?= $widget_config['widget_id']?>">
    <div class="sdloader"><i class="sdloader-icon"></i></div>
    <?php
        $widget_id = isset($widget_config['widget_id']) ? $widget_config['widget_id'] : '';
        $select_topic=isset($options['select_topic']) ? $options['select_topic'] : '';
        $module_id = isset($module) ? $module : '';
        $panel=isset($options['panel']) ? $options['panel'] : '';
        $panel_type=isset($options['panel_type']) ? $options['panel_type'] : '';
        $icon = isset($options['icon']) ? $options['icon'] : '';
//        echo $panel_type;exit();
        $this->registerJs("            
            var widget_id = '".$widget_id."';
            var select_topic = '".$select_topic."';
            var module_id = '".$module_id."'; 
            var panel = '".$panel."';
            var panel_type = '".$panel_type."';
            var icon = '".$icon."';
            var options = {icon:icon,select_topic:select_topic, module_id:module_id,widget_id:widget_id,panel:panel,panel_type:panel_type};
            
            if('".$select_topic."' == 1){                 
                var url = '".yii\helpers\Url::to(['/topic/topic/index'])."';
                $.get(url, {options:options}, function(data){ 
                    $('#help-".$widget_config['widget_id']."').html(data); 
                });
            }else{
                let url = '".yii\helpers\Url::to(['/topic/topic-multi/index'])."';
                $.get(url, {options:options}, function(data){
                   $('#help-".$widget_config['widget_id']."').html(data);
                });
            }            
        ");
    ?>
</div>



