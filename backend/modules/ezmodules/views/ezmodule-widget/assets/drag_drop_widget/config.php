<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
\backend\modules\ezforms2\assets\DadAsset::register($this);
$this->registerCss("
    .draggable {
        cursor: grab;
    }
");

$options = isset($model->options)?\appxq\sdii\utils\SDUtility::string2Array($model->options):[];

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);

if(isset($model->ezm_id)){
    $modelWidget = \backend\modules\ezmodules\models\EzmoduleWidget::find()->where("ezm_id=:ezm_id AND widget_varname<>:widget ", [':ezm_id'=>$model->ezm_id, ':widget'=>$model->widget_varname])->all();
} else {
    $modelWidget = \backend\modules\ezmodules\models\EzmoduleWidget::find()->where("ezm_id=:ezm_id ", [':ezm_id'=>$ezm_id])->all();
}


$widget_list = ArrayHelper::map($modelWidget, 'widget_varname', 'widget_name');
?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget List')?></h4>
</div>
<?php
foreach ($modelWidget as $key => $value) {
    echo \yii\bootstrap\Html::button('<i class="fa fa-plus"></i> '.$value['widget_name'], [
        'data-id'=>$value['widget_varname'],
        'data-name'=>$value['widget_name'],
        'class'=>'btn btn-warning btn-add-widget'
    ]).' ';
}
?>
<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Set default show')?></h4>
</div>
<!--config start-->
<div id="box-set-default" class="row" style="padding: 15px;">
    <?php
    if(isset($options['set_show']['varname'])){
        foreach ($options['set_show']['varname'] as $key => $value) {
            $col = $options['set_show']['col'][$key];
            
            $html = '<div class="dads-children col-md-'.$col.'" data-id="'.$value.'" data-col="'.$col.'" style="border: 1px solid #ccc; border-radius: 4px;">'.
            '<div class="button-item">'.
            '<button class="btn btn-default btn-sm btn-size-small" ><i class="fa fa-compress"></i></button>'.
            '<button class="btn btn-default btn-sm btn-size" ><i class="fa fa-expand"></i></button>'.
            '<button class="btn btn-default btn-sm btn-delete" ><i class="fa fa-trash"></i></button>'.
            '</div>'.
            '<div class="draggable">'.
            '<label>'.$widget_list[$value].'</label>'.
            '<input type="hidden" name="options[set_show][varname][]" value="'.$value.'" class="input-varname" >'.
            '<input type="hidden" name="options[set_show][col][]" value="'.$col.'" class="input-col" >'.
            '</div>'.
            '</div>';
            echo $html;
        }
    }
    ?>
</div>

<!--config end-->

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    $('#box-set-default').on('click', '.btn-delete', function(){
        $(this).parent().parent().remove();
        
        return false;
    });
    
    $('#box-set-default').on('click', '.btn-size', function(){
        let box = $(this).parent().parent();
        let col = box.find('.input-col');
        let col_num = box.attr('data-col');

        if(col.val()<12){
            box.removeClass('col-md-'+col_num);
            
            col.val(parseInt(col.val())+1);
            box.attr('data-col', col.val());
            
            box.addClass('col-md-'+col.val());
        }
        
        return false;
    });
    
    $('#box-set-default').on('click', '.btn-size-small', function(){
        let box = $(this).parent().parent();
        let col = box.find('.input-col');
        let col_num = box.attr('data-col');
        
        if(col.val()>1){
            box.removeClass('col-md-'+col_num);
            
            col.val(parseInt(col.val())-1);
            box.attr('data-col', col.val());
            
            box.addClass('col-md-'+col.val());
        }
        
        return false;
    });
    
    $('.btn-add-widget').click(function(){
        let col = 12;
        let id = $(this).attr('data-id');
        let name = $(this).attr('data-name');
        
        $('#box-set-default').append(
            '<div class="dads-children col-md-'+col+'" data-id="'+id+'" data-col="'+col+'" style="border: 1px solid #ccc; border-radius: 4px;">'+
            '<div class="button-item">'+
            '<button class="btn btn-default btn-sm btn-size-small" ><i class="fa fa-compress"></i></button>'+
            '<button class="btn btn-default btn-sm btn-size" ><i class="fa fa-expand"></i></button>'+
            '<button class="btn btn-default btn-sm btn-delete" ><i class="fa fa-trash"></i></button>'+
            '</div>'+
            '<div class="draggable">'+
            '<label>'+name+'</label>'+
            '<input type="hidden" name="options[set_show][varname][]" value="'+id+'" class="input-varname" >'+
            '<input type="hidden" name="options[set_show][col][]" value="'+col+'" class="input-col" >'+
            '</div>'+
            '</div>'
        );
    });
    
    $('#box-set-default').dad({
    draggable:'.draggable',
    callback:function(e){
	var positionArray = [];
	$('#box-set-default').find('.dads-children').each(function(){
	    positionArray.push($(this).attr('data-id'));
	});
    }
});
</script>
<?php \richardfan\widget\JSRegister::end(); ?>