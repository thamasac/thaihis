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
 $widget_id = $widget_config['widget_id'];
// \appxq\sdii\utils\VarDumper::dump($options);
?>

<div id="tour-<?= $widget_id?>"></div>


<span class="<?= isset($options['position_type']) ? $options['position_type'] : '';?>">
    <button class="btnStart btn btn-<?= isset($options['btn_type']) ? $options['btn_type'] : '';?> <?= isset($options['btn_block']) ? $options['btn_block'] : ''?>  <?= isset($options['position_size']) ? $options['position_size'] : ''?> ">
        <i class="fa <?= isset($options['icon']) ? $options['icon'] : 'fa-home'?>"></i> <?= isset($options['btn_name']) ? $options['btn_name'] : 'My Tour'?>
    </button>
</span>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    
    $('.btnStart').on('click' , function(){
        console.log('ok');
        localStorage.removeItem('tour_current_step');
        localStorage.removeItem('tour_end');
        let url = '/eztour/get-tour';
        $.post(url ,{widget_id:'<?= $widget_id?>'}, function(data){
            $('#tour-<?= $widget_id?>').html(data);
        });
        return false;
    });
//    setTimeout(function(){
//        getTour();
//        console.log('start tour');
//    }, 1000);
</script>
<?php \richardfan\widget\JSRegister::end(); ?>