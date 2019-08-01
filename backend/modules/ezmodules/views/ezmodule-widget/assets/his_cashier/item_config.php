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

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if ($target) {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}

$key_index = isset($key_index) ? $key_index : \appxq\sdii\utils\SDUtility::getMillisecTime();
$items = isset($items) ? $items : null;
$conditions = isset($items[$key_index]['conditions']) ? $items[$key_index]['conditions'] : [];
$selects = isset($items[$key_index]['selects']) ? $items[$key_index]['selects'] : [];
$summarys = isset($items[$key_index]['summarys']) ? $items[$key_index]['summarys'] : [];
?>
<div id="content-config-order<?= $key_index ?>" >
  <div class="modal-header" style="background-color:#CBCAC6;margin-bottom: 2%">
    <h4 class="pull-left">Order header</h4>
    <?= Html::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-danger btn-remove-config-item-order pull-right', 'data-key_index' => $key_index]) ?>
  </div>
  <div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_ezf_id = 'options[items][' . $key_index . '][ezf_id]';
        $value_ezf_id = isset($items[$key_index]['ezf_id']) ? $items[$key_index]['ezf_id'] : 0;
        ?>
        <?= Html::label(Yii::t('thaihis', 'Begin form'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value' => $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Begin form'), 'id' => 'item_config_ezf_id' . $key_index, 'data-key_index' => $key_index],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
  </div> 
  <div class="form-group row">
    <div class="col-md-12">
        <?php
        $attrname_ref = 'options[items][' . $key_index . '][refform]';
        $value_ref = isset($items[$key_index]['refform']) && is_array($items[$key_index]['refform']) ? \appxq\sdii\utils\SDUtility::array2String($items[$key_index]['refform']) : '{}';
        ?>
        <?= Html::label(Yii::t('ezform', 'Reference Form'), $attrname_ref, ['class' => 'control-label']) ?>
      <div id="item_ref_forms_box<?= $key_index ?>">

      </div>
    </div>
  </div>
  <div class="form-group row">
    <div class="col-md-12">
        <?php
        $attrname_fields = 'options[items][' . $key_index . '][fields]';
        $value_fields = isset($items[$key_index]['fields']) && is_array($items[$key_index]['fields']) ? \appxq\sdii\utils\SDUtility::array2String($items[$key_index]['fields']) : '{}';
        ?>
        <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
      <div id="item_ref_fields_box<?= $key_index ?>">

      </div>
    </div>
  </div>

  <div class="form-group row col-md-12 ">
    <div class="panel panel-warning">
      <div class="panel-heading">
        <div class="sdbox-col">
            <?= Html::label(Yii::t('thaihis', 'Select custom ')) ?>
            <?= Html::button("<i class='fa fa-plus'></i>", ['class' => 'btn btn-success', 'id' => 'btn-add-item-select' . $key_index]) ?>
        </div>
      </div>
      <div class="panel-body">

        <div class="sdbox-col" id="display-item-select<?= $key_index ?>">
            <?php
            if (isset($selects) && is_array($selects)):
                foreach ($selects as $key => $val):

                    $sub_index = $key;

                    echo $this->renderAjax('_item_select', [
                        'key_index' => $key_index,
                        'sub_index' => $sub_index,
                        'ezf_id' => \appxq\sdii\utils\SDUtility::string2Array($value_ref),
                        'main_ezf_id' => $value_ezf_id,
                        'val' => $val, 'selects' => $selects, 'act' => ''
                    ]);
                endforeach;
            endif;
            ?>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <div class="col-md-6">
        <?php
        $attrname_cashier_status = 'options[items][' . $key_index . '][cashier_status]';
        $value_cashier_status = isset($items[$key_index]['cashier_status']) ? $items[$key_index]['cashier_status'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Cashier status field'), $attrname_cashier_status, ['class' => 'control-label']) ?>
      <div id="item_ref_cashier_status_box<?= $key_index ?>">

      </div>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_receipt_id = 'options[items][' . $key_index . '][receipt_id]';
        $value_receipt_id = isset($items[$key_index]['receipt_id']) ? $items[$key_index]['receipt_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Receipt Id'), $attrname_receipt_id, ['class' => 'control-label']) ?>
      <div id="item_receipt_id<?= $key_index ?>">

      </div>
    </div>
    
    <div class="col-md-6">
        <?php
        $attrname_visit_field = 'options[items][' . $key_index . '][visit_field]';
        $value_visit_field = isset($items[$key_index]['visit_field']) ? $items[$key_index]['visit_field'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Visit Field'), $attrname_visit_field, ['class' => 'control-label']) ?>
      <div id="item_ref_visit_field_box<?= $key_index ?>">

      </div>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_order_fin_code = 'options[items][' . $key_index . '][order_fin_code]';
        $value_order_fin_code = isset($items[$key_index]['order_fin_code']) ? $items[$key_index]['order_fin_code'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Financial code field'), $attrname_order_fin_code, ['class' => 'control-label']) ?>
      <div id="item_order_fin_code_box<?= $key_index ?>">

      </div>
    </div>
    
  </div>
  <!--begin SummaryField-->
  <div class="form-group row col-md-12">
    <div class="panel panel-warning">
      <div class="panel-heading">
        <div class="sdbox-col">
            <?= Html::label(Yii::t('thaihis', 'Summary Field')) ?>
            <?= Html::button("<i class='fa fa-plus'></i>", ['class' => 'btn btn-success', 'id' => 'btn-add-summary-items' . $key_index]) ?>
        </div>
      </div>
      <div class="panel-body">

        <div class="sdbox-col" id="display-summary-items<?= $key_index ?>">
            <?php
            if (isset($summarys) && is_array($summarys)):
                foreach ($summarys as $key => $val):
                    $sub_index = $key;

                    echo $this->renderAjax('_form_1', [
                        'key_index' => $key_index,
                        'sub_index' => $sub_index,
                        'ezf_id' => \appxq\sdii\utils\SDUtility::string2Array($value_ref),
                        'main_ezf_id' => $value_ezf_id,
                        'val' => $val, 'nameType' => 'items'
                    ]);
                endforeach;
            endif;
            ?>
        </div>
      </div>
    </div>
  </div>
  <!--end SummaryField-->
  <div class="form-group row col-md-12">
    <div class="  panel panel-warning">
      <div class="panel-heading">
        <div class="sdbox-col">
            <?= Html::label(Yii::t('thaihis', 'Condition custom ')) ?>
            <?= Html::button("<i class='fa fa-plus'></i>", ['class' => 'btn btn-success', 'id' => 'btn-add-item-condition' . $key_index]) ?>
        </div>
      </div>
      <div class="panel-body">

        <div class="sdbox-col" id="display-item-condition-<?= $key_index ?>">
          <?php
          if (isset($conditions) && is_array($conditions)):
              foreach ($conditions as $key => $val):
                  $sub_index = $key;
                  echo $this->renderAjax('_form', [
                      'key_index' => $key_index,
                      'sub_index' => $sub_index,
                      'ezf_id' => \appxq\sdii\utils\SDUtility::string2Array($value_ref),
                      'main_ezf_id' => $value_ezf_id,
                      'val' => $val, 'nameType' => $nameType
                  ]);
              endforeach;
          endif;
          ?>
        </div>
      </div>
    </div>
  </div>

</div>
<?php
$attrname_con_amt = 'options[configs][' . $key_index . '][i_condition_amt]';
$value_con_amt = isset($configs[$key_index]['i_condition_amt']) ? $configs[$key_index]['i_condition_amt'] : count($conditions);
$attrname_sel_amt = 'options[configs][' . $key_index . '][i_select_amt]';
$value_sel_amt = isset($configs[$key_index]['i_select_amt']) ? $configs[$key_index]['i_select_amt'] : count($selects);
?>
<?= Html::hiddenInput($attrname_con_amt, $value_con_amt, ['id' => 'i_condition_amt' . $key_index]) ?>
<?= Html::hiddenInput($attrname_sel_amt, $value_sel_amt, ['id' => 'i_select_amt' . $key_index]) ?>

<?php
$this->registerJS("
    itemFormRef$key_index($('#config_ezf_id$key_index').val());
    itemFields$key_index($('#config_ref_form$key_index').val(),$('#item_config_ezf_id$key_index').val());
    itemCashierStatus$key_index($('#config_ref_form$key_index').val(),$('#item_config_ezf_id$key_index').val());
    itemReceiptId$key_index($('#config_ref_form$key_index').val(),$('#item_config_ezf_id$key_index').val());
    itemVisitField$key_index($('#config_ref_form$key_index').val(),$('#item_config_ezf_id$key_index').val());
    itemOrderFinCode$key_index($('#config_ref_form$key_index').val(),$('#item_config_ezf_id$key_index').val());
    var i_conditions = " . count($conditions) . ";
    var i_selects = " . count($selects) . ";
 
    $('#item_config_ezf_id$key_index').on('change',function(){
      itemFormRef$key_index($(this).val());
    });
    
    $('#item_ref_forms_box$key_index').on('change','#item_config_ref_forms$key_index',function(){
      var key_index = $('#item_config_ezf_id$key_index').attr('data-key_index');
      itemFormRef$key_index($('#item_config_ezf_id$key_index').val(),$(this).val(),key_index);
      itemFields$key_index($(this).val(),$('#item_config_ezf_id$key_index').val());
      itemCashierStatus$key_index($(this).val(),$('#item_config_ezf_id$key_index').val());
      itemVisitField$key_index($(this).val(),$('#item_config_ezf_id$key_index').val());
      itemOrderFinCode$key_index($(this).val(),$('#item_config_ezf_id$key_index').val());
      itemReceiptId$key_index($(this).val(),$('#item_config_ezf_id$key_index').val());
    });
    
    $(function(){
        var i_conditions = " . json_encode($conditions) . ";
        var i_selects = " . json_encode($selects) . ";
        if(i_conditions.length > 0){
            setTimeout(function(){
                $.each(i_conditions,function(i,e){
                    onLoadItemCondition$key_index(i,'onLoad');
                });
            },1000);
            
        }
        
        if(i_selects.length > 0){
            setTimeout(function(){
                $.each(i_selects,function(i,e){
                    onLoadItemSelect$key_index(i,'onLoad');
                });
            },1000);
            
        }
    });
    function itemFields$key_index(ezf_id,main_ezf_id){
       
        var value = " . $value_fields . ";
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
        $.post('" . Url::to(['/thaihis/configs/get-fields-forms']) . "',{ ezf_id: value_ref,main_ezf_id:main_ezf_id, multiple:1, name: '{$attrname_fields}', value: value ,id:'item_config_fields$key_index'}
          ).done(function(result){
             $('#item_ref_fields_box$key_index').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function itemCashierStatus$key_index(ezf_id, main_ezf_id){
        var value = '" . $value_cashier_status . "';
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
        $.post('" . Url::to(['/thaihis/configs/get-fields-forms2']) . "',{ ezf_id: value_ref,main_ezf_id:main_ezf_id, multiple:0, name: '{$attrname_cashier_status}', value: value ,id:'item_config_order_visit_status$key_index'}
          ).done(function(result){
            console.log(main_ezf_id);
            $('#item_ref_cashier_status_box$key_index').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function itemReceiptId$key_index(ezf_id, main_ezf_id){
        var value = '" . $value_receipt_id . "';
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
        $.post('" . Url::to(['/thaihis/configs/get-fields-forms2']) . "',{ ezf_id: value_ref,main_ezf_id:main_ezf_id, multiple:0, name: '{$attrname_receipt_id}', value: value ,id:'select_receipt_id$key_index'}
          ).done(function(result){
            console.log(main_ezf_id);
            $('#item_receipt_id$key_index').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function itemVisitField$key_index(ezf_id, main_ezf_id){
        var value = '" . $value_visit_field . "';
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
        $.post('" . Url::to(['/thaihis/configs/get-fields-forms2']) . "',{ ezf_id: value_ref,main_ezf_id:main_ezf_id, multiple:0, name: '{$attrname_visit_field}', value: value ,id:'item_config_visit_field$key_index'}
          ).done(function(result){
             $('#item_ref_visit_field_box$key_index').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function itemOrderFinCode$key_index(ezf_id, main_ezf_id){
        var value = '" . $value_order_fin_code . "';
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
        $.post('" . Url::to(['/thaihis/configs/get-fields-forms2']) . "',{ ezf_id: value_ref,main_ezf_id:main_ezf_id, multiple:0, name: '{$attrname_order_fin_code}', value: value ,id:'item_config_order_fin_code$key_index'}
          ).done(function(result){
             $('#item_order_fin_code_box$key_index').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }

    function itemFormRef$key_index(ezf_id, value_ref, key_index){ 
        var value = " . $value_ref . ";
        if(value_ref){
            value=value_ref;
        }
        
        $.post('" . Url::to(['/thaihis/configs/get-form-ref2']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_ref}', value_ref: value ,id:'item_config_ref_forms$key_index'}
          ).done(function(result){
             $('#item_ref_forms_box$key_index').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }                  
    
    $('.btn-remove-config-item-order').on('click',function(){
        var key_index = $(this).attr('data-key_index');
        var div = $('#content-config-order'+key_index);
        div.remove();
    });
    
    $('#btn-add-item-condition$key_index').on('click',function(){
        var key_index = getMilisecTime();
        onLoadCondition$key_index(key_index,'addNew');
    });

    function onLoadItemCondition$key_index(index,act){
        var conditions = $('#i_condition_amt$key_index').val();
        var key_index = '$key_index';
        var ezf_id = $('#item_config_ref_form$key_index').val();
        var main_ezf_id = $('#item_config_ezf_id$key_index').val();
        var value_ref = " . $value_ref . ";
        var div_condition = $('#display-item-condition$key_index');
        if(ezf_id){
            value_ref = ezf_id;
        }

        var url = '" . Url::to(['/thaihis/configs/add-new-itemcondition', 'conditions' => $conditions]) . "';
        $.get(url,{ezf_id:value_ref,main_ezf_id:main_ezf_id,act:act,key_index:key_index,sub_index:index,nameType:'items'},function(result){
            if(act=='onLoad')
                div_condition.html(result);
            else
                div_condition.append(result);
        });
    }
    
    $('#btn-add-item-select$key_index').on('click',function(){
        var key_index = getMilisecTime();
        onLoadItemSelect$key_index(key_index,'addNew');
    });

    function onLoadItemSelect$key_index(index,act){
        var selects = $('#i_select_amt$key_index').val();
        var key_index = '$key_index';
        var ezf_id = $('#item_config_ref_form2$key_index').val();
        var main_ezf_id = $('#item_config_ezf_id$key_index').val();
        var value_ref = " . $value_ref . ";
        var div_select = $('#display-item-select$key_index');
        if(ezf_id){
            value_ref = ezf_id;
        }

        var url = '" . Url::to(['/thaihis/configs/add-new-itemselect', 'selects' => $selects]) . "';
        $.get(url,{ezf_id:value_ref,main_ezf_id:main_ezf_id,act:act,key_index:key_index,sub_index:index,nameType:'items'},function(result){
            div_select.append(result);
        });
    }
    
    $('#btn-add-summary-items$key_index').on('click',function(){
        var summarys = $('#summary_amt-items$key_index').val();
        var key_index = getMilisecTime();
        onLoadSummaryItems$key_index(key_index,'addNew');
    });

    function onLoadSummaryItems$key_index(index,act){
        var summarys = $('#summary_amt-items$key_index').val();
        var key_index = '$key_index';
        var ezf_id = $('#config_ref_form2$key_index').val();
        var main_ezf_id = $('#config_ezf_id$key_index').val();
        var value_ref = " . $value_ref . ";
        var div_summary = $('#display-summary-items$key_index');
        if(ezf_id){
            value_ref = ezf_id;
        }

        var url = '" . Url::to(['/thaihis/patient-visit/add-newsummary', 'summarys' => $summarys]) . "';
        $.get(url,{ezf_id:value_ref,main_ezf_id:main_ezf_id,act:act,key_index:key_index,sub_index:index,nameType:'items'},function(result){
            $('#summary_amt-items$key_index').val(summarys);
            div_summary.append(result);
        });
    }
");
?>
