<?php
// start widget builder
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
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
   
   
$pagesize = isset($options['pagesize'])?$options['pagesize']:50;
$order = isset($options['order'])?$options['order']:[];
$order_by = isset($options['order_by'])?$options['order_by']:4;
$itemEzf = isset($options['ezf_id'])?$options['ezf_id']:[];
$ezf_id = isset($_GET['ezf_id'])?$_GET['ezf_id']:0;
$tab = isset($_GET['tab'])?$_GET['tab']:0;


$dataEzf = [];
if(!empty($itemEzf)){
    if($ezf_id==0){
        $ezf_id = $itemEzf[0];
    }
    $dataEzf = backend\modules\ezmodules\classes\ModuleQuery::getFormsSelect(implode(',', $itemEzf));
} 

$reloadDiv = $reloadDiv.'-custom-compare';


?>

    <?php
            $form = ActiveForm::begin([
                    'id' => 'jump_menu-'.$reloadDiv,
                    'action'=> \yii\helpers\Url::to(['/ezmodules/ezmodule/view', 'id'=>$module, 'addon'=>$addon, 'tab'=>$tab]),
                    'method' => 'get',
                    //'options' => [ 'class'=>'col-md-12']	    
                ]);?>
                

  <?php 
        echo kartik\select2\Select2::widget([
            'name' => 'ezf_id',
            'value'=> $ezf_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Select Form'), 'id'=>'ezf_id-'.$reloadDiv],
            'data' => ArrayHelper::map($dataEzf,'ezf_id','ezf_name'),
            'pluginOptions' => [
                
            ],
        ]);
        ?>
    
                <?php ActiveForm::end(); ?>
<br>
    <?php
    
    echo \backend\modules\ezforms2\classes\EzfHelper::ui($ezf_id)
        ->reloadDiv($reloadDiv)
        ->pageSize($pagesize)
        ->order_column($order)
        ->orderby($order_by)
        ->title($options['title'])
        ->buildCompareGrid();
    ?>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    // JS script

    $('#ezf_id-<?=$reloadDiv?>').on('change', function(e) {
        $('form#jump_menu-<?=$reloadDiv?>').submit();
    });
    

</script>
<?php \richardfan\widget\JSRegister::end(); ?>
