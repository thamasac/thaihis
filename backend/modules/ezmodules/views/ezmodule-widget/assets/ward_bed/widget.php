<?php
// start widget builder
use backend\modules\patient\classes\PatientHelper;

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
$admit_id = isset($_GET['admit_id'])?$_GET['admit_id']:0;
$visit_id = isset($_GET['visitid'])?$_GET['visitid']:0;
?>
<div class="card card-cpoe">
    <div class="card-block">
        <?= PatientHelper::uiBedTran($admit_id, $visit_id, 'view-bed-tran-show'); ?>
    </div>
  </div>    
