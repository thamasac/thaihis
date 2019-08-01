<?php
// start widget builder
use backend\modules\patient\classes\PatientHelper;
use appxq\sdii\widgets\ModalForm;
use backend\modules\patient\classes\PatientQuery;

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
\backend\modules\patient\assets\PatientAsset::register($this);
\backend\modules\cpoe\assets\CpoeAsset::register($this);

$ezfAdmit_id = \backend\modules\patient\Module::$formID['admit'];
$ezfBed_id = \backend\modules\patient\Module::$formID['ward_bed'];
$unit_id = Yii::$app->user->identity->profile->attributes['department'];
//Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
//$dept = PatientQuery::getDepartmentOne($sect_code);
$dept = PatientQuery::getWorkingUnit($unit_id);

$tab = isset($options['tab'])?$options['tab']:'';

?>

<div class="ward-bed-<?=$reloadDiv?>" style="margin-top: 15px">
  <?= PatientHelper::uiWardBed($ezfBed_id, $dept['unit_id'], 'ward-bed-'.$reloadDiv, $module, $tab); ?>
</div>
