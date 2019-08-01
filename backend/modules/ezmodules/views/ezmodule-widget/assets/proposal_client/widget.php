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
    'id' => 'modal-ezform-proposal',
    'size' => 'modal-xl',
    'tabindexEnable'=>false,
]);
?>
<?php
echo \backend\modules\proposal\classes\ProposalClientBuildder::ui()
        ->moduleId($module)
        ->reloadDiv('display-proposal')
        ->options($options)->buildPropersalClient();
?>
