<?php

use backend\modules\ezforms2\classes\EzfQuery;
use \appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfHelper;
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
    'id' => 'modal-ezform-procedure',
    'size' => 'modal-xxl',
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
echo backend\modules\subjects\classes\ProcedureBuilder::ui()
        ->widget_id($widget_config->widget_id)
        ->reloadDiv('show-procedure')
        ->moduleId($module_id)
        ->scheduleId($options['widget_id'])
        ->user_create($widget_config->created_by)
        ->user_update($widget_config->updated_by)
        ->options($options)
        ->buildProcedure();

