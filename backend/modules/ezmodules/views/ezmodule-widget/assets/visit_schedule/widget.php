<?php

use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use \appxq\sdii\helpers\SDNoty;

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
    'id' => 'modal-ezform-group',
    'size' => 'modal-lg',
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
<?php
$module_id = isset($_GET['id'])?$_GET['id']:'';
echo \backend\modules\subjects\classes\ScheduleBuilder::ui()
        ->widget_id($widget_config->widget_id)
        ->moduleId($module_id)
        ->main_ezf_id($options['11111']['main_ezf_id'])
        ->randomize_id($options['22222']['random_ezf_id'])
        ->reloadDiv('show-schedule')
        ->user_create($widget_config->created_by)
        ->user_update($widget_config->updated_by)
        ->options($options)
        ->buildSchedule();