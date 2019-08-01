<?php
use appxq\sdii\widgets\ModalForm;
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
ModalForm::widget([
    'id' => 'modal-main-ezform',
    'size' => 'modal-xl',
    'tabindexEnable'=>false,
]);
?>
<?=
ModalForm::widget([
    'id' => 'modal-ezform-config',
    'size' => 'modal-lg',
    'tabindexEnable'=>false,
]);
?>
<?=

ModalForm::widget([
    'id' => 'modal-ezform-gantt',
    'size' => 'modal-xl',
    'tabindexEnable'=>false,
]);
?>
<?php
$module_id = isset($_GET['id'])?$_GET['id']:'';
echo \backend\modules\subjects\classes\ScheduleBuilder::ui()
        ->widget_id($widget_config->widget_id)
        ->moduleId($module_id)
        ->reloadDiv('show-schedule')
        ->user_create($widget_config->created_by)
        ->user_update($widget_config->updated_by)
        ->options($options)
        ->action('/subjects/schedule-config/index')
        ->buildSchedule();
?>

