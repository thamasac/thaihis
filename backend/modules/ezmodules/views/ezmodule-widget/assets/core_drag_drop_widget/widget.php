<?php
// start widget builder
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

//\backend\modules\ezforms2\assets\GridStackAsset::register($this);
\backend\modules\ezforms2\assets\DadAsset::register($this);

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
$this->registerCss("
    .dads-children {
        padding: 10px;
    }
    
");


$op_params = [
    'model'=>$model,
    'modelOrigin'=>$modelOrigin,
    'menu'=>$menu,
    'module'=>$module,
    'addon'=>$addon,
    'filter'=>$filter,
    'reloadDiv'=>$reloadDiv,
    'dataFilter'=>$dataFilter,
    'modelFilter'=>$modelFilter,
    'target'=>$target,
];
$editor = isset($_GET['editor'])?$_GET['editor']:0;

$editor_value = $editor==1?0:1;
$editor_color = $editor==1?'primary':'default';
?>
<div style="margin-bottom: 10px;">
    <div class=" text-right">
      <button id="btn-reload" class="btn btn-default btn-sm btn-size " ><i class="fa fa-refresh"></i> Set default</button>  
        <a href="<?= Url::current(['editor'=>$editor_value])?>" class="btn btn-<?=$editor_color?> btn-sm btn-size" ><i class="fa fa-edit"></i> <?=$editor==1?'Preview mode':'Edit mode'?></a>
    </div>
</div>

<?php
echo \yii\helpers\Html::beginTag('div', ['id'=>'widget-drag-box', 'class'=>'grid-stack row']);
$modelWidget = \backend\modules\ezmodules\models\EzmoduleWidget::find()->where("ezm_id=:ezm_id AND widget_varname<>:widget ", [':ezm_id'=>$module, ':widget'=>$widget_config['widget_varname']])->all();
$widget_list = ArrayHelper::map($modelWidget, 'widget_varname', 'widget_name');
$setDefault = $options['set_show'];
$setShow = [];
if(isset($_COOKIE['set_show']) && !empty($_COOKIE['set_show'])){
    $setShow = appxq\sdii\utils\SDUtility::string2Array($_COOKIE['set_show']);
} else {
    $val = appxq\sdii\utils\SDUtility::array2String($options['set_show']);
    setcookie('set_show', $val, time() + (86400 * 365), "/");
    $setShow = $options['set_show'];
}
//setcookie('set_show', '', time() + (86400 * 365), "/");
if(isset($setShow['varname'])){
    foreach ($setShow['varname'] as $key => $value) {
        $widget = [];
        foreach ($modelWidget as $keyWidget => $valueWidget) {
            if($value==$valueWidget['widget_varname']){
                $widget = $valueWidget;
                break;
            }
        }
        if(empty($widget)){
            continue;
        }
        $op_params['reloadDiv'] = $reloadDiv.'-'.$widget['widget_varname'].'-'.$key;

        if($widget['widget_attribute'] == 0){
            if(isset($widget['widget_render']) && !empty($widget['widget_render'])){
                if($widget['widget_render']=='_widget_dynamic'){
                    $widget['widget_render'] = '/ezmodule/'.$widget['widget_render'];
                }
                if($editor){
                ?>
                    <div class="dad-items col-md-<?=$setShow['col'][$key]?> " data-dad-id="<?=$widget['widget_varname']?>" data-dad-col="<?=$setShow['col'][$key]?>" >
                      <div class="button-item">
                        <button class="btn btn-default btn-sm btn-size-small" ><i class="fa fa-compress"></i></button>
                        <button class="btn btn-default btn-sm btn-size" ><i class="fa fa-expand"></i></button>
                        <button class="btn btn-default btn-sm btn-delete" ><i class="fa fa-trash"></i></button>
                      </div>
                        <div class="draggable">
                        <?= $this->render($widget['widget_render'], \yii\helpers\ArrayHelper::merge($op_params, ['widget_config'=>$widget]))?>
                        </div>
                    </div>
                <?php
                } else {
                    ?>
                    <div class="col-md-<?=$setShow['col'][$key]?> " data-dad-id="<?=$widget['widget_varname']?>" data-dad-col="<?=$setShow['col'][$key]?>" >
                      <?= $this->render($widget['widget_render'], \yii\helpers\ArrayHelper::merge($op_params, ['widget_config'=>$widget]))?>
                    </div>
                    <?php
                } 
            }
        } 
    }
}
?>

<?php
echo \yii\helpers\Html::endTag('div');

if($editor){
?>
<div class="row" >
    <div class=" col-md-12 text-right" style="margin-top: 10px;">
      <label>Add Widget : </label>
        <?php
        foreach ($modelWidget as $key => $value) {
    echo \yii\bootstrap\Html::button('<i class="fa fa-plus"></i> '.$value['widget_name'], [
        'data-id'=>$value['widget_varname'],
        'data-name'=>$value['widget_name'],
        'class'=>'btn btn-success btn-add-widget-action'
    ]).' ';
}
        ?>
    </div>
</div>
<?php
}

$draggable = '';
if($editor){
    $draggable = "$('#widget-drag-box').dad({
    draggable:'.draggable',
    callback:function(e){
	
        setTimeout(function(){ 
            let varname = [];
            let col = [];
            
            $('#widget-drag-box .dad-items').each(function(){
                varname.push($(this).attr('data-dad-id'));
                col.push($(this).attr('data-dad-col'));
            });
            set_show = {varname:varname, col:col};
            
            $.cookie('set_show', JSON.stringify(set_show), { path: '/' });
        }, 500);
	
    }
});";
}
?>


<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    
    var set_show = JSON.parse($.cookie('set_show'));
    
    $('.btn-add-widget-action').click(function(){
        let id = $(this).attr('data-id');

        let varname = set_show['varname'];
        let col = set_show['col'];
//        let op = '<?= \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($op_params)?>';
//        
//        $.ajax({
//                method: 'POST',
//                url:'<?= Url::to(['/ezmodules/ezmodule-widget/render-widget', 'ezm_id'=>$module])?>',
//                data: {op:op, varname:$(this).attr('data-name')},
//                dataType: 'Html',
//                success: function(result, textStatus) {
//                    $('#widget-drag-box').append(result);
//                }
//        });
        
        varname.push(id);
        col.push(12);
        
        set_show = {varname:varname, col:col};
        $.cookie('set_show', JSON.stringify(set_show), { path: '/' });
        
        location.reload();
    });
    
    $('#btn-reload').on('click', function(){
        $.cookie('set_show', '', { path: '/' });
        location.reload();
    });
    
    $('#widget-drag-box').on('click', '.btn-delete', function(){
        let box = $(this).parent().parent();
        let varname = box.attr('data-dad-id');
        let col = box.attr('data-dad-col');
        let key;
        for (key in set_show['varname']) {
            if(set_show['varname'][key] == varname){
                set_show['varname'].splice(key, 1);
                set_show['col'].splice(key, 1);
            }
        }
        
        box.remove();
        
        $.cookie('set_show', JSON.stringify(set_show), { path: '/' });

        return false;
    });
    
    $('#widget-drag-box').on('click', '.btn-size', function(){
        let box = $(this).parent().parent();
        let varname = box.attr('data-dad-id');
        let col = 0;
        let col_num = box.attr('data-dad-col');

        if(col_num<12){
            box.removeClass('col-md-'+col_num);
            
            col = parseInt(col_num)+1;
            let key;
            for (key in set_show['varname']) {
                if(set_show['varname'][key] == varname){
                    set_show['col'][key] = col;
                }
            }
            
            box.attr('data-dad-col', col);
            
            box.addClass('col-md-'+col);
            $.cookie('set_show', JSON.stringify(set_show), { path: '/' });
        }
        
        return false;
    });
    
    $('#widget-drag-box').on('click', '.btn-size-small', function(){
        let box = $(this).parent().parent();
        let varname = box.attr('data-dad-id');
        let col = 0;
        let col_num = box.attr('data-dad-col');
        
        if(col_num>1){
            box.removeClass('col-md-'+col_num);
            
            col = parseInt(col_num)-1;
            let key;
            for (key in set_show['varname']) {
                if(set_show['varname'][key] == varname){
                    set_show['col'][key] = col;
                }
            }
            
            box.attr('data-dad-col', col);
            
            box.addClass('col-md-'+col);
            $.cookie('set_show', JSON.stringify(set_show), { path: '/' });
        }
        
        return false;
    });
    
    <?=$draggable?>
</script>
<?php \richardfan\widget\JSRegister::end(); ?>