<?php
// start widget builder
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

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
$itemEzf = isset($options['ezf_id'])?$options['ezf_id']:[];
$type = isset($options['type'])?$options['type']:0;
$ezf_id = isset($_GET['ezf_id'])?$_GET['ezf_id']:0;
$key = isset($_GET['key'])?$_GET['key']:1;
$tab = isset($_GET['tab'])?$_GET['tab']:0;

$dataEzf = [];
if($type==1){
    if(!empty($itemEzf)){
        
        $dataEzf = backend\modules\ezmodules\classes\ModuleQuery::getFormsSelect(implode(',', $itemEzf));
        if($ezf_id==0){
            $ezf_id = $dataEzf[0]['ezf_id'];
        }
        
    } 
} else {
    $dataEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevDb2();
    if($ezf_id==0){
        $ezf_id = $dataEzf[0]['ezf_id'];
    }
}


$reloadDiv = 'dd2-'.$widget_config['widget_varname'];

?>
<?php
            $form = ActiveForm::begin([
                    'id' => 'jump_menu-'.$reloadDiv,
                    'action'=> \yii\helpers\Url::to(['/ezmodules/ezmodule/view', 'id'=>$module, 'addon'=>$addon, 'tab'=>$tab]),
                    'method' => 'get',
                    'options' => ['class'=>'col-md-12']	    
                ]);?>


<div class="form-group" style="margin-bottom: 15px;">
  <label class="control-label col-sm-1 text-right" >EzForm</label>
  <div class="col-sm-6">
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
    </div>
</div>
                <?php ActiveForm::end(); ?>
<!-- Nav tabs -->
<ul class="nav nav-tabs" >
  <li role="presentation" class="<?=$key==1?'active':''?>"><a href="<?= Url::current(['ezf_id'=>$ezf_id, 'key'=>1])?>"><?= Yii::t('ezmodule', 'Key Operator 1')?></a></li>
  <li role="presentation" class="<?=$key==2?'active':''?>"><a href="<?= Url::current(['ezf_id'=>$ezf_id, 'key'=>2])?>"><?= Yii::t('ezmodule', 'Key Operator 2')?></a></li>
</ul>

  <!-- Tab panes -->
  <div class="tab-content" style="margin-top: 15px;">
    
        <?php
        
    if($key==1){
        echo Html::beginTag('div', ['style'=>'margin-bottom: 10px;']);
        echo \backend\modules\ezforms2\classes\EzfHelper::btn($ezf_id)->reloadDiv($reloadDiv)->buildBtnAdd();
        echo Html::endTag('div');
        
         echo \backend\modules\ezforms2\classes\EzfHelper::ui($ezf_id)
        ->reloadDiv($reloadDiv)
       ->buildGrid();
    } else {
         echo \backend\modules\ezforms2\classes\EzfHelper::ui($ezf_id)
        ->reloadDiv($reloadDiv)
       ->buildDb2Grid();
    }

   
    ?>
    
  </div>


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
