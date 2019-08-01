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

$configs = isset($configs) ? $configs : null;
$conditions = isset($configs[$key_index]['conditions']) ? $configs[$key_index]['conditions'] : [];
$summarys = isset($configs[$key_index]['summarys']) ? $configs[$key_index]['summarys'] : [];
$selects = isset($configs[$key_index]['selects']) ? $configs[$key_index]['selects'] : [];
?>
<div id="content-config-order<?= $key_index ?>" >
  <div class="modal-header" style="background-color:#CBCAC6;margin-bottom: 2%">
    <h4 class="pull-left">Order config</h4>
    <?= Html::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-danger btn-remove-config-order pull-right', 'data-key_index' => $key_index]) ?>
  </div>
  <div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_ezf_id = 'options[configs][' . $key_index . '][ezf_id]';
        $value_ezf_id = isset($configs[$key_index]['ezf_id']) ? $configs[$key_index]['ezf_id'] : 0;
        ?>
        <?= Html::label(Yii::t('thaihis', 'Begin form'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value' => $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Begin form'), 'id' => 'config_ezf_id' . $key_index, 'data-key_index' => $key_index],
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
        $attrname_ref = 'options[configs][' . $key_index . '][refform]';
        $value_ref = isset($configs[$key_index]['refform']) && is_array($configs[$key_index]['refform']) ? \appxq\sdii\utils\SDUtility::array2String($configs[$key_index]['refform']) : '{}';
        ?>
        <?= Html::label(Yii::t('ezform', 'Reference Form'), $attrname_ref, ['class' => 'control-label']) ?>
      <div id="ref_forms_box<?= $key_index ?>">

      </div>
    </div>
  </div>
  <div class="form-group row">
    <div class="col-md-12">
        <?php
        $attrname_fields = 'options[configs][' . $key_index . '][fields]';
        $value_fields = isset($configs[$key_index]['fields']) && is_array($configs[$key_index]['fields']) ? \appxq\sdii\utils\SDUtility::array2String($configs[$key_index]['fields']) : '{}';
        ?>
        <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
      <div id="ref_fields_box<?= $key_index ?>">

      </div>
    </div>
  </div>

  <div class="form-group row col-md-12 ">
    <div class="panel panel-warning">
      <div class="panel-heading">
        <div class="sdbox-col">
            <?= Html::label(Yii::t('thaihis', 'Select custom ')) ?>
            <?= Html::button("<i class='fa fa-plus'></i>", ['class' => 'btn btn-success', 'id' => 'btn-add-header-select' . $key_index]) ?>
        </div>
      </div>
      <div class="panel-body">
        <div class="col-md-6">
            <?= Html::label(Yii::t('ezform', 'Field'), '', ['class' => 'control-label']) ?>
        </div>
        <div class="col-md-3 sdbox-col">
            <?= Html::label(Yii::t('ezform', 'Custom value'), '', ['class' => 'control-label']) ?>
        </div>
        <div class="col-md-3">
            <?= Html::label(Yii::t('ezform', 'Alias Name'), '', ['class' => 'control-label']) ?>
        </div>
        <div class="sdbox-col" id="display-header-select<?= $key_index ?>">
            <?php
            if (isset($selects) && is_array($selects)):
                foreach ($selects as $key => $val):

                    $sub_index = $key;

                    echo $this->renderAjax('_form_select', [
                        'key_index' => $key_index,
                        'sub_index' => $sub_index,
                        'ezf_id' => \appxq\sdii\utils\SDUtility::string2Array($value_ref),
                        'main_ezf_id' => $value_ezf_id,
                        'val' => $val,
                    ]);
                endforeach;
            endif;
            ?>
        </div>
      </div>
    </div>
  </div>

  <div class="form-group row col-md-12 ">
    <div class="panel panel-warning">
      <div class="panel-heading">
        <div class="sdbox-col">
            <?= Html::label(Yii::t('thaihis', 'Summary Field')) ?>
            <?= Html::button("<i class='fa fa-plus'></i>", ['class' => 'btn btn-success', 'id' => 'btn-add-summary' . $key_index]) ?>
        </div>
      </div>
      <div class="panel-body">

        <div class="sdbox-col" id="display-summary<?= $key_index ?>">
            <?php
            if (isset($summarys) && is_array($summarys)):
                foreach ($summarys as $key => $val):
                    $sub_index = $key;

                    echo $this->renderAjax('_form_1', [
                        'key_index' => $key_index,
                        'sub_index' => $sub_index,
                        'ezf_id' => \appxq\sdii\utils\SDUtility::string2Array($value_ref),
                        'main_ezf_id' => $value_ezf_id,
                        'val' => $val, 'nameType' => 'configs'
                    ]);
                endforeach;
            endif;
            ?>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <div class="col-md-4 ">
        <?php
        $attrname_cashier_status = 'options[configs][' . $key_index . '][cashier_status]';
        $value_cashier_status = isset($configs[$key_index]['cashier_status']) ? $configs[$key_index]['cashier_status'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Cashier status field'), $attrname_cashier_status, ['class' => 'control-label']) ?>
      <div id="ref_cashier_status_box<?= $key_index ?>">

      </div>
    </div>
    <div class="col-md-4 sdbox-col ">
        <?php
        $attrname_receipt_id = 'options[configs][' . $key_index . '][receipt_id]';
        $value_receipt_id = isset($configs[$key_index]['receipt_id']) ? $configs[$key_index]['receipt_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Receipt Id'), $attrname_receipt_id, ['class' => 'control-label']) ?>
      <div id="config_ref_cashier_id_box<?= $key_index ?>">

      </div>
    </div>
    <div class="col-md-4 sdbox-col ">
        <?php
        $attrname_visit_field = 'options[configs][' . $key_index . '][visit_field]';
        $value_visit_field = isset($configs[$key_index]['visit_field']) ? $configs[$key_index]['visit_field'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Visit Field'), $attrname_visit_field, ['class' => 'control-label']) ?>
      <div id="ref_visit_field_box<?= $key_index ?>">

      </div>
    </div>    
  </div>

  <div class="form-group row col-md-12">
    <div class="  panel panel-warning">
      <div class="panel-heading">
        <div class="sdbox-col">
            <?= Html::label(Yii::t('thaihis', 'Condition')) ?>
            <?= Html::button("<i class='fa fa-plus'></i>", ['class' => 'btn btn-success', 'id' => 'btn-add-condition' . $key_index]) ?>
        </div>
      </div>
      <div class="panel-body">

        <div class="sdbox-col" id="display-condition<?= $key_index ?>">
            <?php
            if (isset($conditions) && is_array($conditions)):
                foreach ($conditions as $key => $val):
                    $sub_index = $key;
                    echo $this->renderAjax('_form', [
                        'key_index' => $key_index,
                        'sub_index' => $sub_index,
                        'ezf_id' => \appxq\sdii\utils\SDUtility::string2Array($value_ref),
                        'main_ezf_id' => $value_ezf_id,
                        'val' => $val,
                    ]);
                endforeach;
            endif;
            ?>
        </div>
      </div>
    </div>
  </div>

  <div class="form-group row">
    <div class="col-md-12 ">
        <?php
        $attrname_group_field = 'options[configs][' . $key_index . '][group_field]';
        $value_group_field = isset($configs[$key_index]['group_field']) && is_array($configs[$key_index]['group_field']) ? \appxq\sdii\utils\SDUtility::array2String($configs[$key_index]['group_field']) : '{}';
        ?>
        <?= Html::label(Yii::t('ezform', 'Group Field'), $attrname_group_field, ['class' => 'control-label']) ?>
      <div id="ref_group_field_box<?= $key_index ?>">

      </div>
    </div>
  </div>
  <?php
  $orderJS = "";
  if ($key_index == 0):
      ?>
      <div class="form-group row">
        <div class="col-md-12">
            <?php
            $attrname_order_by = 'options[configs][0][order_by][field]';
            $value_order_by = isset($configs[$key_index]['order_by']['field']) && is_array($configs[$key_index]['order_by']['field']) ? \appxq\sdii\utils\SDUtility::array2String($configs[$key_index]['order_by']['field']) : '{}';
            echo Html::label(Yii::t('ezform', 'Order by field'), '', ['class' => 'control-label'])
            ?>
        </div>
        <div id="div_order_by" class="col-md-5">

        </div>
        <div class="col-md-3">
            <?php
            $attrname_sort = 'options[configs][0][order_by][sort]';
            $value_sort = isset($configs[$key_index]['order_by']['sort']) ? $configs[$key_index]['order_by']['sort'] : '';
            echo Html::dropDownList($attrname_sort, $value_sort, ['ASC' => 'ASC', 'DESC' => 'DESC'], ['class' => 'form-control'])
            ?>
        </div>
        <div class="clearfix"></div>
      </div>

      <?php
      $orderJS = "
          order_by$key_index($('#config_ref_form$key_index').val(),$('#config_ezf_id$key_index').val());
              
        function order_by$key_index(ezf_id, main_ezf_id){
                    var value = " . $value_order_by . ";
                    var value_ref = " . $value_ref . ";
                    if(ezf_id){
                        value_ref = ezf_id;
                    }
                    $.post('" . Url::to(['/thaihis/patient-visit/get-fields-forms2']) . "',{ ezf_id: value_ref,main_ezf_id:main_ezf_id, multiple:1, name: '{$attrname_order_by}', value: value ,id:'config_orderby_fields$key_index'}
                      ).done(function(result){
                         $('#div_order_by').html(result);
                      }).fail(function(){
                          " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
                          console.log('server error');
                      });
                }
          ";
  endif;
  ?>
  <?php
  $attrname_con_amt = 'options[configs][' . $key_index . '][condition_amt]';
  $value_con_amt = isset($configs[$key_index]['condition_amt']) ? $configs[$key_index]['condition_amt'] : count($conditions);
  $attrname_sum_amt = 'options[configs][' . $key_index . '][summary_amt]';
  $value_sum_amt = isset($configs[$key_index]['summary_amt']) ? $configs[$key_index]['summary_amt'] : count($summarys);
  $attrname_sel_amt = 'options[configs][' . $key_index . '][select_amt]';
  $value_sel_amt = isset($configs[$key_index]['select_amt']) ? $configs[$key_index]['select_amt'] : count($selects);
  ?>
  <?= Html::hiddenInput($attrname_con_amt, $value_con_amt, ['id' => 'condition_amt' . $key_index]) ?>
  <?= Html::hiddenInput($attrname_sum_amt, $value_sum_amt, ['id' => 'summary_amt' . $key_index]) ?>
  <?= Html::hiddenInput($attrname_sel_amt, $value_sel_amt, ['id' => 'select_amt' . $key_index]) ?>
</div>

<?php
$this->registerJS("
    $orderJS
    form_ref$key_index($('#config_ezf_id$key_index').val());
    fields$key_index($('#config_ref_form$key_index').val(),$('#config_ezf_id$key_index').val());
    group_field$key_index($('#config_ref_form$key_index').val(),$('#config_ezf_id$key_index').val());
    cashierStatus$key_index($('#config_ref_form$key_index').val(),$('#config_ezf_id$key_index').val());
    visitField$key_index($('#config_ref_form$key_index').val(),$('#config_ezf_id$key_index').val()); 
    itemReceiptId$key_index($('#config_ref_form$key_index').val(),$('#item_config_ezf_id$key_index').val());
    
    $('#config_ezf_id$key_index').on('change',function(){
      form_ref$key_index($(this).val());
    });
    
    $('#ref_forms_box$key_index').on('change','#config_ref_forms$key_index',function(){
      var key_index = $('#config_ezf_id$key_index').attr('data-key_index');
      form_ref$key_index($('#config_ezf_id$key_index').val(),$(this).val(),key_index);
      fields$key_index($(this).val(),$('#config_ezf_id$key_index').val());
      group_field$key_index($(this).val(),$('#config_ezf_id$key_index').val());
      cashierStatus$key_index($(this).val(),$('#config_ezf_id$key_index').val());
      visitField$key_index($(this).val(),$('#config_ezf_id$key_index').val());
      itemReceiptId$key_index($(this).val(),$('#item_config_ezf_id$key_index').val());
    });
    
    $(function(){
        var summarys = " . json_encode($summarys) . ";
        var conditions = " . json_encode($conditions) . ";
        var selects = " . json_encode($selects) . ";

    });
    
    function fields$key_index(ezf_id,main_ezf_id){       
        var value = " . $value_fields . ";
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
        $.post('" . Url::to(['/thaihis/patient-visit/get-fields-forms']) . "',{ ezf_id: value_ref,main_ezf_id:main_ezf_id, multiple:1, name: '{$attrname_fields}', value: value ,id:'config_fields$key_index'}
          ).done(function(result){
             $('#ref_fields_box$key_index').html(result);
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
        $.post('" . Url::to(['/thaihis/configs/get-fields-forms2']) . "',{ ezf_id: value_ref,main_ezf_id:main_ezf_id, multiple:0, name: '{$attrname_receipt_id}', value: value ,id:'config_receipt_id$key_index'}
          ).done(function(result){
            console.log(main_ezf_id);
            $('#config_ref_cashier_id_box$key_index').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function group_field$key_index(ezf_id, main_ezf_id){
        var value = " . $value_group_field . ";
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
        $.post('" . Url::to(['/thaihis/patient-visit/get-fields-forms2']) . "',{ ezf_id: value_ref,main_ezf_id:main_ezf_id, multiple:1, name: '{$attrname_group_field}', value: value ,id:'config_group_fields$key_index'}
          ).done(function(result){
             $('#ref_group_field_box$key_index').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function cashierStatus$key_index(ezf_id, main_ezf_id){
        var value = '" . $value_cashier_status . "';
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
        $.post('" . Url::to(['/thaihis/patient-visit/get-fields-forms2']) . "',{ ezf_id: value_ref,main_ezf_id:main_ezf_id, multiple:0, name: '{$attrname_cashier_status}', value: value ,id:'config_order_visit_status$key_index'}
          ).done(function(result){
             $('#ref_cashier_status_box$key_index').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function visitField$key_index(ezf_id, main_ezf_id){
        var value = '" . $value_visit_field . "';
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
        $.post('" . Url::to(['/thaihis/patient-visit/get-fields-forms2']) . "',{ ezf_id: value_ref,main_ezf_id:main_ezf_id, multiple:0, name: '{$attrname_visit_field}', value: value ,id:'config_visit_field$key_index'}
          ).done(function(result){
             $('#ref_visit_field_box$key_index').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }

    function form_ref$key_index(ezf_id, value_ref, key_index){ 
        var value = " . $value_ref . ";
        if(value_ref){
            value=value_ref;
        }
        
        $.post('" . Url::to(['/thaihis/patient-visit/get-form-ref2']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_ref}', value_ref: value ,id:'config_ref_forms$key_index'}
          ).done(function(result){
             $('#ref_forms_box$key_index').html(result);
             //$('#add-condition').attr('data-ezf_id',$('#config_ezf_id').val());
             //fields($('#ezf_target_id').val());
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }                  
    
    $('.btn-remove-config-order').on('click',function(){
        var key_index = $(this).attr('data-key_index');
        var div = $('#content-config-order'+key_index);
        div.remove();
    });
    
    $('#btn-add-condition$key_index').on('click',function(){
        var conditions = $('#condition_amt$key_index').val();
        var key_index = '$key_index';
        onLoadCondition$key_index((key_index+conditions+'con'),'addNew');
    });

    function onLoadCondition$key_index(index,act){
        var conditions = $('#condition_amt$key_index').val();
        var key_index = '$key_index';
        var ezf_id = $('#config_ref_form$key_index').val();
        var main_ezf_id = $('#config_ezf_id$key_index').val();
        var value_ref = " . $value_ref . ";
        var div_condition = $('#display-condition$key_index');
        if(ezf_id){
            value_ref = ezf_id;
        }

        var url = '" . Url::to(['/thaihis/patient-visit/add-newcondition', 'conditions' => $conditions]) . "';
        $.get(url,{ezf_id:value_ref,main_ezf_id:main_ezf_id,act:act,key_index:key_index,sub_index:index},function(result){
            if(act == 'addNew')conditions ++;
            $('#condition_amt$key_index').val(conditions);
            if(act=='onLoad')
                div_condition.html(result);
            else
                div_condition.append(result);
        });
    }
    
    $('#btn-add-summary$key_index').on('click',function(){
        var summarys = $('#summary_amt$key_index').val();
        var key_index = getMilisecTime();
        onLoadSummary$key_index(key_index,'addNew');
    });

    function onLoadSummary$key_index(index,act){
        var summarys = $('#summary_amt$key_index').val();
        var key_index = '$key_index';
        var ezf_id = $('#config_ref_form2$key_index').val();
        var main_ezf_id = $('#config_ezf_id$key_index').val();
        var value_ref = " . $value_ref . ";
        var div_summary = $('#display-summary$key_index');
        if(ezf_id){
            value_ref = ezf_id;
        }

        var url = '" . Url::to(['/thaihis/patient-visit/add-newsummary', 'summarys' => $summarys]) . "';
        $.get(url,{ezf_id:value_ref,main_ezf_id:main_ezf_id,act:act,key_index:key_index,sub_index:index,nameType:'items'},function(result){
            $('#summary_amt$key_index').val(summarys);
            div_summary.append(result);
        });
    }
    
    $('#btn-add-header-select$key_index').on('click',function(){
        var selects = $('#select_amt$key_index').val();
        var key_index = getMilisecTime();
        onLoadHeaderSelect$key_index(key_index,'addNew');
    });

    function onLoadHeaderSelect$key_index(index,act){
        var selects = $('#select_amt$key_index').val();
        var key_index = '$key_index';
        var ezf_id = $('#config_ref_form2$key_index').val();
        var main_ezf_id = $('#config_ezf_id$key_index').val();
        var value_ref = " . $value_ref . ";
        var div_select = $('#display-header-select$key_index');
        if(ezf_id){
            value_ref = ezf_id;
        }
        
        var url = '" . Url::to(['/thaihis/configs/add-new-headerselect', 'selects' => $selects]) . "';
        $.get(url,{ezf_id:value_ref,main_ezf_id:main_ezf_id,act:act,key_index:key_index,sub_index:index,getFiledForm:'1'},function(result){
            $('#select_amt$key_index').val(selects);
            div_select.append(result);
        });
    }
");
?>
