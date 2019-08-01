<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\classes\EzformWidget;
use yii\db\Query;
use backend\modules\ezforms2\classes\EzfQuery;
use kartik\select2\Select2;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];

//$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
$itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevAll();
?>
<style>
  .config-widget-header {
      background-color: #CBCAC6;
  }
</style>

<!--Config Form Start-->
<div class="form-group row">
  <div class="modal-header config-widget-header"><h4 class="modal-title" id="itemModalLabel">Config Widget</h4>
  </div>
  <div class="col-md-12" style="margin-top:5px">


    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="general-tab">       
        <div class="form-group row">
            <?= Html::hiddenInput('options[reloadDiv]', isset($options['reloadDiv']) ? $options['reloadDiv'] : 'visit-' . SDUtility::getMillisecTime()); ?>

          <div class="col-md-3">
              <?= Html::label(Yii::t('ezform', 'Button Icon'), 'options[btn_icon]', ['class' => 'control-label']) ?>
              <?=
              dominus77\iconpicker\IconPicker::widget([
                  'name' => 'options[btn_icon]',
                  'value' => isset($options['btn_icon']) ? $options['btn_icon'] : '',
                  'options' => ['class' => 'dicon-input form-control', 'id' => 'config_icon'],
                  'clientOptions' => [
                      'hideOnSelect' => true,
                  ]
              ])
              ?>
          </div>
          <div class="col-md-3 sdbox-col">
              <?= Html::label(Yii::t('ezform', 'Button Text'), 'options[btn_text]', ['class' => 'control-label']) ?>
              <?= Html::textInput('options[btn_text]', (isset($options['btn_text']) ? $options['btn_text'] : Yii::t('ezform', 'Button Text')), ['class' => 'form-control']) ?>
          </div>

          <div class="col-md-3 sdbox-col">
              <?= Html::label(Yii::t('ezform', 'Button Color'), 'options[btn_color]', ['class' => 'control-label']) ?>
              <?=
              Html::dropDownList('options[btn_color]', (isset($options['btn_color']) ? $options['btn_color'] : 'btn-default'), [
                  'btn-default' => 'Default',
                  'btn-primary' => 'Primary',
                  'btn-success' => 'Success',
                  'btn-info' => 'Info',
                  'btn-warning' => 'Warning',
                  'btn-danger' => 'Danger'
                      ], ['class' => 'form-control'])
              ?>
          </div>
          <div class="col-md-3 sdbox-col">
              <?= Html::label(Yii::t('ezform', 'Button Style'), 'options[btn_style]', ['class' => 'control-label']) ?>
              <?=
              Html::dropDownList('options[btn_style]', (isset($options['btn_style']) ? $options['btn_style'] : 'btn-block'), [
                  'btn-block' => 'Block',
                  'btn-lg' => 'Large',
                  'btn-md' => 'Medium',
                  'btn-sm' => 'Small',
                  'btn-xs' => 'XSmall',
                      ], ['class' => 'form-control'])
              ?>
          </div>

        </div>
      </div>

      <div class="form-group row">
        <div class="col-md-6">
            <?php
            $attrname_ezf_id = 'options[ezf_id]';
            $value_ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : 0;
            ?>
            <?= Html::label(Yii::t('thaihis', 'Form Main'), $attrname_ezf_id, ['class' => 'control-label']) ?>
            <?php
            echo kartik\select2\Select2::widget([
                'name' => $attrname_ezf_id,
                'value' => $value_ezf_id,
                'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_ezf_id'],
                'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);

            $attrname_ref = 'options[refform]';
            ?>
        </div>
        <div class="col-md-3">
            <?= Html::radioList('options[action_report]', (isset($options['action_report']) ? $options['action_report'] : '1'), ['1' => 'action 1', '2' => 'action 2']) ?>
        </div>
      </div>
      <!--Add Reference Form start-->
      <div class="form-group row">
        <div class="col-md-12">
          <div class="btn btn-success pull-left" id="btn-add-ref">Add Reference Form</div>
        </div>

        <div id="ref_field_box_v2">
            <?php
            $value_ref = [];
            if (isset($options['refform']) && is_array($options['refform']) && !empty($options['refform'])) {
                $i = 0;
                $vEzf = '';
                foreach ($options['refform'] as $key => $vRef) {
                    if ($i == 0) {
                        $vEzf = $value_ezf_id;
                    } else {
                        if (isset($vRef['id'])) {
                            if (!is_array($vRef['id'])) {
                                $vEzf = SDUtility::string2Array($vRef['id']);
                            } else {
                                $vEzf = $vRef['id'];
                            }
                        }
                    }
                    if (isset($vRef['value'])) {
                        if (!empty($vRef['value'])) {
                            foreach ($vRef['value'] as $v) {
                                $value_ref[] = $v;
                            }
                        }
                    }

                    $model1 = new Query();
                    $model1->select('ezf.ezf_id as `id`,ezf.ezf_name as `name`')
                            ->from('ezform_fields ezff')
                            ->innerJoin('ezform ezf', 'ezf.ezf_id=ezff.ezf_id')
                            ->where(['ezff.ref_ezf_id' => $vEzf])
                            ->andWhere('ezff.ezf_field_type=79  OR ezff.ezf_field_type=80');

                    $model2 = new Query();
                    $model2->select('ezf.ezf_id as `id`,ezf.ezf_name as `name`')
                            ->from('ezform_fields ezff')
                            ->innerJoin('ezform ezf', 'ezf.ezf_id=ezff.ref_ezf_id')
                            ->where(['ezff.ezf_id' => $vEzf])
                            ->andWhere('ezff.ezf_field_type=79  OR ezff.ezf_field_type=80');
                    $result = $model1->union($model2->createCommand()->rawSql);

                    $dataForm = $result->all();
                    ?>
                  <div class='col-md-12 divMainRef' style="margin-top: 2%">
                    <div class="col-md-2">
                      <label>Type Join</label>
                      <select class='form-control'
                              name="<?= $i == 0 ? $attrname_ref . '[' . $i . '][type_join]' : $attrname_ref . '[' . $i . '][type_join]' ?>">
                        <option value="Inner Join" <?= isset($vRef['type_join']) && $vRef['type_join'] == 'Inner Join' ? 'selected' : '' ?>>
                          Inner Join
                        </option>
                        <option value="Right Join" <?= isset($vRef['type_join']) && $vRef['type_join'] == 'Right Join' ? 'selected' : '' ?>>
                          Right Join
                        </option>
                        <option value="Left Join" <?= isset($vRef['type_join']) && $vRef['type_join'] == 'Left Join' ? 'selected' : '' ?>>
                          Left Join
                        </option>
                      </select>

                    </div>
                    <div class='col-md-9'><label>Reference Form Level <?= $i == 0 ? '' : $i ?></label>
                        <?php
                        echo \yii\helpers\Html::hiddenInput($i == 0 ? $attrname_ref . '[' . $i . '][id]' : $attrname_ref . '[' . $i . '][id]', is_array($vEzf) ? SDUtility::array2String($vEzf) : $vEzf);
                        echo \kartik\select2\Select2::widget([
                            'id' => 'config_ref_form_' . $i,
                            'name' => $i == 0 ? $attrname_ref . '[' . $i . '][value]' : $attrname_ref . '[' . $i . '][value]',
                            'value' => isset($vRef['value']) ? $vRef['value'] : '',
                            'data' => ArrayHelper::map($dataForm, 'id', 'name'),
//                              'maintainOrder' => true,
                            'options' => ['placeholder' => Yii::t('ezform', 'Please select a form.'), 'multiple' => true, 'class' => 'form-ref'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'tags' => true,
                                'tokenSeparators' => [',', ' '],
                            ]
                        ]);
                        $i++;
                        ?>
                    </div>
                    <div class='col-md-1'>
                      <div style="margin-top: 58%" class="btn btn-danger btnRemoveRef">×</div>
                    </div>
                  </div>
                  <?php
              }
          }

          $value_ref = !empty($value_ref) ? SDUtility::array2String($value_ref) : '{}';
          //                \appxq\sdii\utils\VarDumper::dump($value_ref);
          ?>
        </div>
      </div>
      <!--Add Reference Form end-->


      <div class="form-group row">
        <div class="col-md-2">
          <div class="btn btn-success pull-left" id="btn-add-condition">Add Condition</div>
        </div>
        <div class="col-md-6">
          <!--                        <div class="form-group"> -->
          <?php //echo EzformWidget::checkbox('', '', ['label' => 'Condition Date', 'id' => 'condition-date']) ?><!--</div>-->
        </div>
      </div>

      <div class="form-group row">
        <div id="header-condition" class="col-md-12" style="margin-top: 5px;">
          <div class="col-md-2 ">
            <label>Condition</label>
          </div>
          <div class="col-md-4 sdbox-col">
            <label>Field</label>
          </div>
          <div class="col-md-2 sdbox-col">
            <label>Operator</label>
          </div>
          <div class="col-md-3 sdbox-col">
            <label>Value</label>
          </div>
          <div class="col-md-1 sdbox-col">

          </div>
        </div>
        <div id="div-condition">
            <?php
            $value_ezf_ref = SDUtility::string2Array($value_ref);
            $value_ezf_ref[] = $value_ezf_id;
            $value_condition = isset($options['condition']) ? $options['condition'] : [];
            if (isset($value_condition) && is_array($value_condition) && !empty($value_condition)) {
                $dataFields = (new Query())->select(['ezf_field_id as id', 'CONCAT(ezf_field_name," (",ezf_field_label,")") as name', 'ezf_id', 'ezf_field_type'])
                                ->from('ezform_fields')
                                ->where('(ezf_field_type <> 0 OR  ezf_field_name = \'id\')')
                                ->andWhere(['ezf_id' => $value_ezf_ref])->all();
                $dataForm = [];
                $dataFormDate = [];
                foreach ($dataFields as $vField) {
                    $dataEzf = EzfQuery::getEzformById($vField['ezf_id']);
                    $dataForm[$dataEzf['ezf_name']][$vField['id']] = $vField['name'];
                    if ($vField['ezf_field_type'] == '63' || $vField['ezf_field_type'] == '64') {
                        $dataFormDate[$dataEzf['ezf_name']][$vField['id']] = $vField['name'];
                    }
                }

                foreach ($value_condition as $kCon => $vCon) {
                    $id_condition = SDUtility::getMillisecTime();
                    ?>
                  <div class="col-md-12 divMainCondition" style="margin-top:2%">

                    <div class="col-md-2 ">
                        <?php
                        echo Select2::widget([
                            'id' => 'select-value-con-' . $id_condition,
                            'name' => 'options[condition][' . $kCon . '][condition]',
                            'value' => isset($vCon['condition']) ? $vCon['condition'] : '',
                            'data' => ['and' => 'AND', 'or' => 'OR'],
                            'hideSearch' => true,
                        ]);
                        ?>
                    </div>
                    <div class="col-md-4 sdbox-col">
                        <?php
                        echo Select2::widget([
                            'id' => 'select-value-field-' . $id_condition,
                            'name' => 'options[condition][' . $kCon . '][field]',
                            'value' => isset($vCon['field']) ? $vCon['field'] : '',
                            'data' => $vCon['value'] != 'NOW()' ? $dataForm : $dataFormDate,
                            'options' => ['placeholder' => Yii::t('ezform', 'Select field ...')],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ]);
                        ?>
                    </div>
                    <div class="col-md-2 sdbox-col">
                        <?php
                        $itemCondition = ['>' => 'มากกว่า', '<' => 'น้อยกว่า', '>=' => 'มากกว่าหรือเท่ากับ',
                            '<=' => 'น้อยกว่าหรือเท่ากับ', '=' => 'เท่ากับ', '!=' => 'ไม่เท่ากับ'];

                        echo Select2::widget([
                            'id' => 'select-value-ope-' . $id_condition,
                            'name' => 'options[condition][' . $kCon . '][operator]',
                            'value' => isset($vCon['operator']) ? $vCon['operator'] : '',
                            'data' => $itemCondition,
                            'hideSearch' => true,
                            'options' => ['placeholder' => Yii::t('ezform', 'Select condition ...')],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ]);
                        ?>
                    </div>
                    <div class="col-md-3 sdbox-col">
                        <?php
                        //                                        if ($vCon['value'] != 'NOW()') {
                        ?>
                      <div class="input-group">
                          <?= Html::textInput('options[condition][' . $kCon . '][value]', isset($vCon['value']) ? $vCon['value'] : '', ['class' => 'form-control', 'id' => 'val-con-' . $id_condition]) ?>
                        <span class="input-group-btn">
                            <?= Html::button(Yii::t('queue', 'Constant'), ['class' => 'btn btn-success btn-condition-constant', 'data-input-id' => 'val-con-' . $id_condition]) ?>
                        </span>
                      </div>
                      <?php
                      //                                            echo Html::tag('div','',['class'=>'btn btn-success btn-condition-constant','data-input-id'=>'val-con-'.$id_condition]);
                      //                                            echo Html::textInput('options[condition][' . $kCon . '][value]', isset($vCon['value']) ? $vCon['value'] : '', ['class' => 'form-control','id'=>'val-con-'.$id_condition]);
                      //                                        } else {
                      //                                            echo Html::hiddenInput('options[condition][' . $kCon . '][value]', 'NOW()');
                      //                                            echo Html::textInput('', 'วันนี้', ['class' => 'form-control', 'disabled' => true]);
                      //                                        }
                      ?>
                    </div>

                    <div class="col-md-1 sdbox-col">
                        <?php echo Html::tag('div', '<i class="glyphicon glyphicon-remove"></i>', ['class' => 'btn btn-danger btn-remove-condition']) ?>
                    </div>

                  </div>
                  <?php
              }
          }

          if (isset($options) && is_array($options) && !empty($options)) {
              $dataOptions = SDUtility::array2String($options);
          } else {
              $dataOptions = '{}';
          }
          ?>
        </div>
      </div>
      <!-- end condition -->
      <div class="form-group row">
        <div class="col-md-12">
            <?php
            $attrname_group_by = 'options[group_by]';
            $value_group_by = isset($options['group_by']) ? $options['group_by'] : '';
            echo Html::label(Yii::t('ezform', 'Group by field'), 'options[template_content]', ['class' => 'control-label'])
            ?>
        </div>
        <div id="div_group_by" class="col-md-12">

        </div>
      </div>

      <div class="form-group row">
        <div class="modal-header config-widget-header"><h4 class="modal-title" id="itemModalLabel">Matching Field</h4>
        </div>

        <div id="div_match_field" class="col-md-12" style="margin-top: 10px;">

        </div>
      </div>


      <!--                <div class="form-group row">-->
      <!--                    <div class="col-md-12">-->
      <!--                        --><?php
////                        echo Html::label(Yii::t('ezform', 'Template'), 'options[template_content]', ['class' => 'control-label']);
////                        echo
////                        \appxq\sdii\widgets\FroalaEditorWidget::widget([
////                            'name' => 'options[template_content]',
////                            'value' => isset($options['template_content'])? $options['template_content']:'',
////                            //'id'=>'froala-editor',
////                            'options' => ['id' => 'template_content'],
////                            'clientOptions' => [
////                                'zIndex' => 1,
////                                'height' => '300',
////                                //'theme' => 'gray', //optional: dark, red, gray, royal
////                                'language' => 'th',
////                            ]
////                        ]);
//                        
      ?>
      <!--                    </div>-->
      <!--                </div>-->

    </div>
  </div>
</div>


<!--config end-->

<?php
$modalId = 'modal-add-constant';
$modalConstant = \appxq\sdii\widgets\ModalForm::widget([
            'id' => $modalId,
            'size' => 'modal-sm'
        ]);


$this->registerJS("    
    if($('#config_action').val() == '1'){
        $('.divUrl').show();
        $('.divElement').hide();
    }else if($('#config_action').val() == '3'){
        $('.divUrl').show();
         $('.divElement').show();
    }else{
        $('.divUrl').hide();
        $('.divElement').hide();
    }
    $('#config_action').change(function(){
        if($(this).val() == '1'){
            $('.divUrl').show();
            $('.divElement').hide();
        }else if($(this).val() == '3'){
            $('.divUrl').show();
            $('.divElement').show();
        }else{
            $('.divUrl').hide();
            $('.divElement').hide();
        }
    });
    
    if($('#position_type').val() == '1'){
        $('#position-static').show();
    }else{
        $('#position-fixed').show();
    }
    $('#position_type').change(function(){
        if($(this).val() == '1'){ 
            $('#position-static').show();
            $('#position-fixed').hide();
        }else{
            $('#position-fixed').show();
            $('#position-static').hide();
        }
    });
    
    $('#status_field_box').on('change','#config_status_fields',function(){
        if($(this).val() == ''){
            $('.status_field').hide();
        }else{
            $('.status_field').show();
        }
    });
    
    $('#div_param_box').on('click','.check_box_active',function(){
            $('#div_param_box').find('.check_box_active').not(this).prop('checked', false);
           
    });
    
    
     setTimeout( ()=>{ 
          group_by('',$('#config_ezf_id').val());
          match_field('',$('#config_ezf_id').val());
      },1000);
    
    var delayID = null;
    $('#btn-add-ref').click(function(){
        var num = 1;
        var val = [];
        if (delayID) {
            clearTimeout(delayID);
        }
        if($('#ref_field_box_v2').has('.form-ref').length > 0){
            $('#ref_field_box_v2').find('.form-ref').each(function (k, v) {
                num = num+1;
                val = $(this).val();
            });
            if(val.length > 0){
                delayID = setTimeout(function () {
                    form_ref(val,'0',num);
                    delayID = null;
                }, 1000);
            }else{
                " . \appxq\sdii\helpers\SDNoty::show('"กรุณาเลือกฟอร์มหลัก"', '"error"') . "
                delayID = true;
            }
        }else{
            if($('#config_ezf_id').val() != ''){
                form_ref($('#config_ezf_id').val(),'0','1');
            }else{
                " . \appxq\sdii\helpers\SDNoty::show('"กรุณาเลือกฟอร์มหลัก"', '"error"') . "
            }
        }
    });
    
    $('#btn-add-condition').click(function(){
        getFormCondition('','');
    });
    
    $('#div-condition').on('click', '.btn-remove-condition', function(){
        $(this).parents('.divMainCondition').remove();
    });
    
    function getFormCondition(param_name,param_value){
        var condition_date = [];
        if($('#condition-date').is(':checked')){
            condition_date = [63,64];
        }
        var valC = [];
        var val = []; 
        var main_ezf_id = $('#config_ezf_id').val();
        $('#ref_field_box_v2').find('.form-ref').each(function (k, v) {
            var valC = $(this).val();
            if(valC.length > 0){
                for(var i in valC){
                    val.push(valC[i]);
                }
            }
         });
        
        if(val.length <= 0){
            value_ref = " . $value_ref . ";
        }else{
            value_ref = val;
        }
        
        $.post('" . Url::to(['/queue/default/get-form-condition']) . "',{main_ezf_id:main_ezf_id,ezf_id: value_ref,param_name:param_name,param_value:param_value,type:condition_date}
          ).done(function(result){
             $('#div-condition').append(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
     $('#ref_field_box_v2').on('change','.form-ref',function(){
        if (delayID) {
            clearTimeout(delayID);
        }
        var valC = [];
        var val = []; 
        $('#ref_field_box_v2').find('.form-ref').each(function (k, v) {
            var valC = $(this).val();
            if(valC.length > 0){
                for(var i in valC){
                    val.push(valC[i]);
                }
            }
         });
         delayID = setTimeout(function () {
               group_by(val,$('#config_ezf_id').val());
               match_field(val,$('#config_ezf_id').val());
              delayID = null;
         }, 1000);
    });
    
    $('#ref_field_box_v2').on('click','.btnRemoveRef',function(){
        var valC = [];
        var val = []; 
        $(this).parents('.divMainRef').remove();
        if (delayID) {
            clearTimeout(delayID);
        }
       
        $('#ref_field_box_v2').find('.form-ref').each(function (k, v) {
            var valC = $(this).val();
            if(valC.length > 0){
                for(var i in valC){
                    val.push(valC[i]);
                }
            }
         });
         delayID = setTimeout(function () {
              group_by(val,$('#config_ezf_id').val());
              match_field(val,$('#config_ezf_id').val());
              delayID = null;
         }, 100);
    });
    
    
    $('#config_ezf_id').change(function(){
        group_by('0',$(this).val());
        match_field('0',$(this).val());
    });

   
    function form_ref(ezf_id,main_ezf_id,num){ 
//        var value = " . $value_ref . ";
//        var type_join = $(\"input[name='join']:checked\").val();
//        if(type_join == '' || typeof(type_join) == \"undefined\"){
//            type_join = 'Inner Join';
//        }
        let select = `<select class='form-control' name='{$attrname_ref}`+`[`+num+`][type_join]'>
          <option value=\"Inner Join\">Inner Join</option>
          <option value=\"Right Join\">Right Join</option>
          <option value=\"Left Join\">Left Join</option>
        </select>`;
        $('#add-condition').html('กำลังรีโหลดข้อมูล');
        $.post('" . Url::to(['/queue/default/get-form-ref']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_ref}'+'['+num+'][value]',name_data: '{$attrname_ref}'+'['+num+'][id]', value_ref: '' ,id:'config_ref_form_'+num}
          ).done(function(result){
             $('#ref_field_box_v2').append(`<div class='col-md-12 divMainRef'><div class='col-md-2'style='margin-top: 2%'><label>Type Join</label>`+select+`</div><div class='col-md-9'  style='margin-top: 2%'><label>Reference Form Level `+num+`</label> `+result+`</div><div class='col-md-1'> <div  style='margin-top: 94%' class=\"btn btn-danger btnRemoveRef \">×</div></div></div>`);
             //$('#add-condition').attr('data-ezf_id',$('#config_ezf_id').val());
             //fields($('#ezf_target_id').val());
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }    
    
    
    function match_field(ezf_id,main_ezf_id){
         var options = " . $dataOptions . ";
         var value_ref = " . $value_ref . ";
            if(ezf_id){
                value_ref = ezf_id;
            }
         $('#div_match_field').html('กำลังรีโหลดข้อมูล');
         $.post('" . Url::to(['/thaihis/btn-report/get-match-field']) . "',{main_ezf_id:main_ezf_id,ezf_id: value_ref,options:options}
          ).done(function(result){
             $('#div_match_field').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }         
    
   
    
    function group_by(ezf_id,main_ezf_id){
        var value = '{$value_group_by}';
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
       
        $('#div_group_by').html('กำลังรีโหลดข้อมูล');
        $.post('" . Url::to(['/queue/default/get-fields-forms']) . "',{ main_ezf_id:main_ezf_id,ezf_id: value_ref, multiple:0, name: '{$attrname_group_by}', value: value ,id:'config_group_by'}
          ).done(function(result){
             $('#div_group_by').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }

    
    function getFormParam(param_name,param_value){
        var valC = [];
        var val = []; 
        var main_ezf_id = $('#config_ezf_id').val();
        $('#ref_field_box_v2').find('.form-ref').each(function (k, v) {
            var valC = $(this).val();
            if(valC.length > 0){
                for(var i in valC){
                    val.push(valC[i]);
                }
            }
         });
        
        if(val.length <= 0){
            value_ref = " . $value_ref . ";
        }else{
            value_ref = val;
        }
        
        $.post('" . Url::to(['/queue/default/get-form-param']) . "',{main_ezf_id:main_ezf_id,ezf_id: value_ref,param_name:param_name,param_value:param_value}
          ).done(function(result){
             $('#div_param_box').append(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    $('.btn-add-param').click(function(){
        getFormParam('','');
    });
    
    
    $('#div_param_box').on('click', '.btn-remove-param', function(){
        $(this).parents('.divMainParam').remove();
    });
    
    $('.header-items-add').on('click', function(){
        getWidget();
    });
    
    $('#header-item-box').on('click', '.header-items-del', function(){
        $(this).parent().parent().remove();
    });
    
    var hasMyModal = $( 'body' ).has( '#$modalId' ).length;
    if(!hasMyModal){
        $('.page-column').append(`$modalConstant`);
    }
    $('#modal-add-constant').on('hidden.bs.modal', function(e){
        if($('body .modal').hasClass('in')){
            $('body').addClass('modal-open');
        }
     });
    
    $('#div-condition').on('click','.btn-condition-constant',function(){
//        alert($(this).attr('data-input-id'));
        $('#$modalId .modal-content').html('');
        $('#$modalId').modal('show').find('.modal-content').load('/queue/default/add-constant?input='+$(this).attr('data-input-id'));
    });
    
    $('#$modalId').on('click','.btn-constant-val',function(){
        let input = $(this).attr('data-input');
        let value = $(this).attr('data-val');
        $('#div-condition').find('#'+input).val(value);
        $('#$modalId').modal('hide');
        
    });

    function getWidget() {
        $.ajax({
            method: 'POST',
            url: '" . Url::to(['/ezmodules/ezmodule-tab/get-widget', 'view' => '/ezmodule-widget/assets/queue/_form_header']) . "',
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#header-item-box').append(result);
            }
        });
    }
    
    
    
//    function check_status(value){
//    console.log(value);
//        if(value){
//            $('.check_status').show();
//        }else{
//            $('.check_status').hide();
//        }
//    }
//    
//    function check_dept(value){
//    console.log(value);
//        if(value){
//            $('.check_dept').show();
//        }else{
//            $('.check_dept').hide();
//        }
//    }
//    
//    function check_date(value){
//    console.log(value);
//        if(value){
//            $('.check_date').show();
//        }else{
//            $('.check_date').hide();
//        }
//    }
//    
//    

");
?>