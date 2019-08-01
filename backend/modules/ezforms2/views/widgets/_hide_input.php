<?php
use appxq\sdii\utils\SDUtility;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Url;
use yii\helpers\Html;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$share_options = SDUtility::string2Array($modelField['share_options']);

$action['id'] = $form->id;
$action['action'] = $form->action;
$action['options'] = $form->options;

$specific = SDUtility::string2Array($modelField['ezf_field_specific']);
$options = SDUtility::string2ArrayJs($modelField['ezf_field_options']);
unset($options['specific']);

$widgetOption = [];
if(isset($options['widgetOption'])){
    $widgetOption = $options['widgetOption'];
}
unset($options['widgetOption']);

$bgformClass = 'bgform-area';
$style_color = '';
if ($modelField['ezf_field_color'] != '') {
    $style_color = "background-color: {$modelField['ezf_field_color']};";
}

$hide = $modelField['ezf_field_type'] == 0 ? 'display: none;' : '';

if(isset($widgetOption['class'])){
    $widgetOption['class'] .= " col-md-{$modelField['ezf_field_lenght']} $bgformClass ";
} else {
    $widgetOption['class'] = "col-md-{$modelField['ezf_field_lenght']} $bgformClass ";
}

if(isset($widgetOption['style'])){
    $widgetOption['style'] .= " position: relative;{$hide}{$style_color} ";
} else {
    $widgetOption['style'] = "position: relative;{$hide}{$style_color} ";
}

$widgetOption['item-id'] = $modelField['ezf_field_id'];
?>
<div id="ezpw-<?=$modelField['ezf_id']?>-<?=$modelField['ezf_field_id']?>">
<?php
echo Html::beginTag('div', $widgetOption);
?>
<div class="form-group">  
<label class="control-label" for="<?=$modelField->ezf_field_name?>">
<?php if($share_options['hide']==0):?>
<?=$modelField->ezf_field_label?>
<?php endif;?>
  &nbsp;
</label>

<div style="min-height: 34px;">
  <?php if($share_options['alert']==1):?>
  <div class="alert alert-warning"><?= Yii::t('ezform', 'For authorized person only')?> <?= ($type==2)?Html::a('<i class="glyphicon glyphicon-lock"></i>', '#', ['class'=>'btn btn-default btn-sm btn-pw']):''?> </div>
  <?php endif;?>
</div>
</div>  
<?=\yii\helpers\Html::endTag('div')?>
</div>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    $('.btn-pw').click(function(){
        var btn = $(this);
        
        $.ajax({
            method: 'POST',
            url:'<?=Url::to(['/ezforms2/ezform/form-oauthe', 'ezf_id'=>$modelField['ezf_id'], 'v'=>$v, 'field'=>$modelField['ezf_field_id'], 'dataid'=> $model->id, 'action'=> \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($action)])?>',
            dataType: 'JSON',
            success: function(result, textStatus) {
                if(result.status == 'success') {
                    btn.parent().parent().parent().html(result.html);
                } else {
                    <?=SDNoty::show('result.message', 'result.status')?>
                }
            }
        });
        
        return false;
    });
</script>
<?php \richardfan\widget\JSRegister::end(); 
?>
