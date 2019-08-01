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
    'id' => 'modal-ezform-financial',
    'size' => 'modal-xxl',
]);
?>
<?php
$module_id = isset($_GET['id'])?$_GET['id']:'';
$status = isset($_GET['status'])?$_GET['status']:'';
$maintab = isset($_GET['maintab'])?$_GET['maintab']:'';
$subtab = isset($_GET['subtab'])?$_GET['subtab']:'';
echo backend\modules\subjects\classes\FinancialBuilder::ui()
        ->widget_id($widget_config->widget_id)
        ->reloadDiv('show-financial')
        ->moduleId($module_id)
        ->user_create($widget_config->created_by)
        ->user_update($widget_config->updated_by)
        ->maintab($maintab)
        ->subtab($subtab)
        ->status($status)
        ->options($options)
        ->buildFinancial();