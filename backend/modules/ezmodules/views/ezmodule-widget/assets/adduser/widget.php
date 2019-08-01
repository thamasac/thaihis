<?php
// start widget builder
use richardfan\widget\JSRegister; 
use yii\helpers\Url;
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
<h1><?= Yii::t('ezform2','Add User')?></h1>
<div id="addUser"></div>
<?php 
    $this->registerJs("
        function getUser(){
            let url = '".Url::to(['/ezforms2/add-user/index'])."';
            $('#addUser').html('Load...');
            $.get(url, function(data){
                $('#addUser').html(data);
            });
        }getUser();
    ");
?>