<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

$options = isset($options) ? \appxq\sdii\utils\SDUtility::string2Array($options) : [];
$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if ($target) {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}
?>

<div class="mainDiv">
    <hr>
    <div class="form-group row">
        <div class="col-md-6">
            <?= Html::label(Yii::t('ezform', 'Title'), 'options[title]', ['class' => 'control-label']) ?>
            <?= Html::textInput('options[title][]', Yii::t('ezform', 'Title'), ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-6">
            <div class="pull-right">
                <?=
                Html::button('<i class="glyphicon glyphicon-plus"></i>', [
//                    'id' => 'btnAdd',
                    'class' => 'btn btn-success btnAdd',
                    'style' => 'margin-right:5px;'
                ]);
                ?>
                <?= Html::button('<i class="glyphicon glyphicon-remove"></i>', ['class' => 'btn btn-danger btn-remove']) ?>
            </div>
        </div>
    </div>

    <div class="form-group row">

        <!--<div class="clearfix"></div>-->
        <div class="col-md-6 ">
            <?php
            $attrname_ezf_id = 'options[ezf_id]';
            $value_ezf_id = '';
            ?>
            <?= Html::label(Yii::t('ezform', 'Form'), $attrname_ezf_id, ['class' => 'control-label']) ?>
            <?php
            echo kartik\select2\Select2::widget([
                'name' => $attrname_ezf_id,
                'value' => $value_ezf_id,
                'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_ezf_id' . $id],
                'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-md-6 ">
            <?php
            $attrname_fields = 'options[fields][]';
            $value_fields = '{}';
            ?>
            <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
            <div id="ref_field_box_<?= $id ?>">

            </div>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-6">
            <?php
            $attrname_fields_search = 'options[fields_search][]';
            $value_fields_search = '{}';
            ?>
            <?= Html::label(Yii::t('ezform', 'Fields Search'), $attrname_fields_search, ['class' => 'control-label']) ?>
            <div id="fields_search_box_<?= $id ?>">

            </div>
        </div>
        <div class="col-md-6 sdbox-col">
            <?php
            $attrname_image_field = 'options[image_field][]';
            $value_image_field = isset($options['image_field']) ? $options['image_field'] : '';
            ?>
            <?= Html::label(Yii::t('ezform', 'Image Field'), $attrname_image_field, ['class' => 'control-label']) ?>
            <div id="pic_field_box_<?= $id ?>">

            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::label(Yii::t('ezform', 'Template Items'), 'options[template_items]', ['class' => 'control-label']) ?>
        <?= Html::textarea('options[template_items][]', '', ['class' => 'form-control', 'row' => 3]) ?>
    </div>

    <div class="form-group">
        <?= Html::label(Yii::t('ezform', 'Template Selection'), 'options[template_selection]', ['class' => 'control-label']) ?>
        <?= Html::textarea('options[template_selection][]', '', ['class' => 'form-control', 'row' => 3]) ?>
    </div>
</div>

<?php
$this->registerJS("
    

     var ezf_id_" . $id . " = 'config_ezf_id" . $id . "';
    var id_" . $id . " = '" . $id . "';
    fields($('#'+ezf_id_" . $id . ").val(),id_" . $id . "," . $value_fields . ");
    pic_fields($('#'+ezf_id_" . $id . ").val(),id_" . $id . "," . $value_image_field . ");
    fields_search($('#'+ezf_id_" . $id . ").val(),id_" . $id . "," . $value_fields_search . ");
        
    $('#'+ezf_id_" . $id . ").on('change',function(){
      var ezf = $(this).val();
      fields(ezf,$(this).attr('id'),'" . $value_fields . "');
      fields_search(ezf,$(this).attr('id'),'" . $value_fields_search . "');
      pic_fields(ezf,$(this).attr('id'),'" . $value_image_field . "');
          
    });
    
    function fields(ezf_id,id,value){
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_fields}', value: value ,id:'config_fields'+id}
          ).done(function(result){
             $('#ref_field_box_'+id).html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function fields_search(ezf_id,id,value){
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_fields_search}', value: value ,id:'config_fields_search'+id}
          ).done(function(result){
             $('#fields_search_box_'+id).html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function pic_fields(ezf_id,id,value){
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_image_field}', value: value ,id:'config_pic_fields'+id}
          ).done(function(result){
             $('#pic_field_box_'+id).html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
");
?>


