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

<?=
\appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-activity-form',
    'size' => 'modal-xl',
]);
?>

<?php
$module_id = isset($_GET['id'])?$_GET['id']:'';
echo backend\modules\subjects\classes\OpenActivityBuilder::ui()
        ->reloadDiv('show-open-activity')
        ->moduleId($module_id)
        ->subjectProfileEzf($options['subject_profile_ezf'])
        ->subjectDetailEzf($options['subject_detail_ezf'])
        ->profileColumn($options['profile_field_display'])
        ->fieldSubject($options['profile_field_subject'])
        ->detailColumn($options['detail_field_display'])
        ->detailColumn2($options['detail_field_display2'])
        ->scheduleId($options['schedule_widget_id'])
        ->moduleId($module)
        ->buildOpenActivity();
?>