<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$options = isset($widget_config['options']) ? \appxq\sdii\utils\SDUtility::string2Array($widget_config['options']) : [];

echo $this->render('../../../ezmodules/views/ezmodule-widget/assets/' . $widget_config['widget_type'].'/widget', [
    'options' => isset($options)?$options:null,
    'widget_config' => isset($widget_config)?$widget_config:null,
    'model' => isset($model)?$model:null, //$modelModule
    'modal' => isset($modal)?$modal:null, //$modelModule
    'modelOrigin'=>isset($modelOrigin)?$modelOrigin:null,
    'menu' => isset($menu)?$menu:null,
    'module' => isset($module)?$module:null,
    'addon' => isset($addon)?$addon:null,
    'filter' => isset($filter)?$filter:null,
    'reloadDiv' => isset($reloadDiv)?$reloadDiv:null,
    'dataFilter' => isset($dataFilter)?$dataFilter:null,
    'modelFilter' => isset($modelFilter)?$modelFilter:null,
    'target' => isset($target)?$target:null,
    'readonly' => isset($readonly)?$readonly:null,
]);
?>
