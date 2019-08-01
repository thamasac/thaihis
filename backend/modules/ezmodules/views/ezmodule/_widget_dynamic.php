<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$options = isset($widget_config['options']) ? \appxq\sdii\utils\SDUtility::string2Array($widget_config['options']) : [];

echo $this->render('/ezmodule-widget/assets/' . $widget_config['widget_type'].'/widget', [
    'options' => $options,
    'widget_config' => $widget_config,
    'model' => $model, //$modelModule
    'modelOrigin'=>$modelOrigin,
    'menu' => $menu,
    'module' => $module,
    'addon' => $addon,
    'filter' => $filter,
    'reloadDiv' => $reloadDiv,
    'dataFilter' => $dataFilter,
    'modelFilter' => $modelFilter,
    'target' => $target,
]);
?>
