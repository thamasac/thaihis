<?php

//appxq\sdii\utils\VarDumper::dump($module);
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

$widget = \backend\modules\tmf\classes\TmfWidget::ui()
        ->ezf_type_id($options['ezf_type_id'])
        ->data_column_type($options['type_fields'])
//        ->order_column_type($options['type_order'])
        ->ezf_name_id($options['ezf_name_id'])
        ->data_column_name($options['name_fields'])
//        ->order_column_name($options['name_order'])
        ->ezf_detail_id($options['ezf_detail_id'])
        ->data_column_detail($options['detail_fields'])
//        ->order_column_detail($options['detail_order'])
        ->type_field_value($options['type_field_value'])
        ->type_field_label($options['type_field_label'])
        ->ref_form_detail($options['ref_form_detail'])
        ->reloadDiv($reloadDiv)
        ->default_column($default_column)
        ->type_id(Yii::$app->request->get('type_id', '0'))
        ->data_id(Yii::$app->request->get('data_id', '0'))
        ->module($module);
if (backend\modules\ezforms2\classes\EzfAuthFuncManage::accessBtnGrid($module)) {
    $widget->disabled(true);
}
echo $widget->pageSize($pagesize)
        ->orderby($order_by)
        ->buildGrid();
?>
