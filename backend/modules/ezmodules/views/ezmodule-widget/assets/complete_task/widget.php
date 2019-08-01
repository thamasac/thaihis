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
<?php 
    //appxq\sdii\utils\VarDumper::dump($widget_config['widget_id']);
    $id = isset($widget_config['widget_id']) ? $widget_config['widget_id'] : '';
    
?>
<div id="<?= $id?>"></div>
<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    
    
    function completeTask(){
        let url = '/topic/ez-setup/get-ez-setup';
        $.get(url,{id:'<?= $id?>'}, function(data){
            $('#<?= $id?>').html(data);
        })
        
    }
    completeTask();
//    function completeTask(){
//        let url = '/topic/complete-task';
//        $.get(url,{id:'<?= $id?>'}, function(data){
//            $('#<?= $id?>').html(data);
//        })
//        
//    }
//    completeTask();
</script>
<?php \richardfan\widget\JSRegister::end(); ?>