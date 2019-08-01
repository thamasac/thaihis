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
 
<?php 
\backend\modules\ezforms2\classes\EzfStarterWidget::begin();

\backend\modules\ezforms2\classes\EzfStarterWidget::end();
?>


<div class="text-right">
    <?= yii\helpers\Html::button("<i class='fa fa-plus'></i>", ['class'=>'btn btn-success' ,'id'=>'btnPlus'])?>
</div>

<?php 
//    echo appxq\sdii\widgets\ModalForm::widget([
//       'id' => 'modal-assign',
//       'size' => 'modal-lg',
//   ]);
?>

<?php JSRegister::begin(); ?>
<script>
     
    $('#btnPlus').click(function(){
        let url = '<?= Url::to(['/ezforms2/assign-role'])?>';
        $('#modal-assign .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-ezform-main').modal('show');
        $.get(url,{ezf_id:'1519707087068015000'},function(data){
            $('#modal-ezform-main .modal-content').html(data);
  
        });
        
    });
    
    
</script>
<?php JSRegister::end(); ?>