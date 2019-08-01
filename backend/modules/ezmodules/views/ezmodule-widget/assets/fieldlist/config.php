<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];

$itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll("");

?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title"><?= Yii::t('ezmodule', 'FieldList Config') ?></h4>
</div>

<div class="panel panel-default" id="Filed List">
    <div class="panel-heading">
        <div>
            Add Field
            <buttoon class="btn" id="btnAdd">Add</buttoon>
        </div>
    </div>
    <div class="panel-body" id="rootDiv">

        <div class="form-group row">
            <div class="col-md-6 ">
                <?= Html::label(Yii::t('ezform', 'Form'), $attrname_ezf_id, ['class' => 'control-label']) ?>
                <?php
                $attrname_ezf_id = 'options[ezf_id]';
                $value_ezf_id = isset($ezf_id) ? $ezf_id : '';
                ?>
                <?php
                try {
                    echo kartik\select2\Select2::widget([
                        'name' => $attrname_ezf_id,
                        'value' => $options["ezf_id"],
                        'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => "config_ezf_id"],
                        'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                } catch (Exception $e) {
                    echo "Widget not work property.";
                }
                ?>
            </div>
            <div class="col-md-3 sdbox-col">
                <?php
                $attrname_search_fields = 'options[search_field]';
                $value_search_fields = isset($options['search_field'])?\appxq\sdii\utils\SDUtility::array2String([$options['search_field']]):'{}';
                ?>
                <?= Html::label(Yii::t('ezform', 'Search Fields'), $attrname_search_fields, ['class' => 'control-label']) ?>
                <div id="ref_field_box">

                </div>
            </div>
            <div class="col-md-3">
                <?= Html::label(Yii::t('ezform', 'Title'), 'options[title]', ['class' => 'control-label']) ?>
                <?= Html::textInput('options[title]', (isset($options['title'])?$options['title']:Yii::t('ezform', 'Title')), ['class'=>'form-control'])?>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-1 "></div>

            <div class="col-md-3 sdbox-col">
                <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
            </div>
            <div class="col-md-2 sdbox-col">
                <b>Label</b>
            </div>
        </div>
    </div>

</div>

<?php

$optionJson = json_encode($options);
$this->registerJS("
    searchFields    ($('#config_ezf_id').val());

    $('#config_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      searchFields(ezf_id);
    });
    function searchFields(ezf_id){
        var value = ".$value_search_fields.";
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_search_fields}', value: value ,id:'config_search_fields'}
          ).done(function(result){
             $('#ref_field_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function addRow(_ezf_id,_field,_label){
        $.post('" . Url::to(['/ezwidget/ez-widget/get-fieldlist-option']) . "',{ 'ezf_id': _ezf_id, 'field': _field, 'label': _label})
        .done(function(result){
            $('#rootDiv').append(result);
        }).fail(function(){
            " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
            console.log('server error');
        });
    }
    
    (function(){
      var optionJs = $optionJson;
      if(optionJs['fields'] != undefined){
          for(var i = 0; i < optionJs['fields'].length;i++){
                addRow( optionJs['ezf_id'] , optionJs['fields'][i] , optionJs['labels'][i] );
          }
      }
    })();
    
    $( '#btnAdd' ).click(function() {
        addRow('','',1);
    });
"
);
?>
