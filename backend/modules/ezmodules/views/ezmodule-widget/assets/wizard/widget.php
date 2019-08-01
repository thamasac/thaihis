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
<div id="view-wizard"></div>
<?php 
    $this->registerJs("
        WizardInit=function(){
            let url='".yii\helpers\Url::to(['/manageproject/step/index#step-1'])."';
            $.get(url, function(data){
                $('#view-wizard').html(data);
            })
        }
        WizardInit();
    ");
?>