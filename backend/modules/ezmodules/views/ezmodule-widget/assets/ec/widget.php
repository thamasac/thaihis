<?php

// start widget builder
//echo dms\aomruk\widgets\DSNotifyinput::widget([
//    'name'=>'test'
//]);
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
//   appxq\sdii\utils\VarDumper::dump($options);
$default_column = isset($options['default_column']) ? $options['default_column'] : 0;
$pagesize = isset($options['pagesize']) ? $options['pagesize'] : 50;
$order_by = isset($options['order_by']) ? $options['order_by'] : 4;
$reloadDiv = $reloadDiv . '-custom';
//appxq\sdii\utils\VarDumper::dump(\backend\modules\ezforms2\classes\ListData::getRoleList());
?>


<?php

$widget = backend\modules\ce\classes\EcWidget::ui()
        ->ezf_type_id($options['ezf_cat_id'])
        ->data_column_type($options['cat_fields'])
//        ->order_column_type($options['type_order'])
        ->ezf_name_id($options['ezf_subcat_id'])
        ->data_column_name($options['subcat_fields'])
//        ->order_column_name($options['name_order'])
        ->ezf_detail_id($options['ezf_event_id'])
        ->data_column_detail(isset($options['event_fields']) ? $options['event_fields'] : [])
//        ->order_column_detail($options['detail_order'])
        ->type_field_value($options['cat_field_value'])
        ->type_field_label($options['cat_field_label'])
//        ->ref_form_detail($options['ref_form_detail'])
        ->reloadDiv($reloadDiv)
        ->default_column($default_column)
        ->module($module)
        ->pageSize($pagesize)
        ->type_id(Yii::$app->request->get('type_id', '0'))
        ->data_id(Yii::$app->request->get('data_id', '0'))
        ->orderby($order_by);
if (backend\modules\ezforms2\classes\EzfAuthFuncManage::accessBtnGrid($module)) {
    $widget->disabled(true);
}
//backend\modules\ezforms2\classes\EzfAuthFuncManage::accessBtnGrid($module_id);
//backend\modules\ezforms2\classes\EzfAuthFuncManage::accessBtn($module_id);
echo $widget->buildGrid();
?>
