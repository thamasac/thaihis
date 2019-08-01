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

echo \backend\modules\subjects\classes\SubjectPaymentBuilder::ui()
        ->widget_id($widget_config->widget_id)
        ->reloadDiv('show-subject-payment')
        ->user_create($widget_config->created_by)
        ->user_update($widget_config->updated_by)
        ->options($options)
        ->buildSubjectPayment();
?>