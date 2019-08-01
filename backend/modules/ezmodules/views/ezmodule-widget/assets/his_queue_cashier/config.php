<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzformWidget;
use kartik\select2\Select2;
use appxq\sdii\utils\SDUtility;
use yii\db\Query;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\queue\classes\QueueFunc;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];

//start ezf_id get field in ezform
$value_ezf_id = [
    backend\modules\patient\Module::$formID['visit'],
    backend\modules\patient\Module::$formID['profile'],
    backend\modules\patient\Module::$formID['patientright'],
];

$dataFields = QueueFunc::getFieldFormById($value_ezf_id);

$dataForm = [];
$dataFormDate = [];
$dataFormImg = [];
foreach ($dataFields as $vField) { //loop แยก field date,pic
    $dataForm[$vField['ezf_name']][$vField['id']] = $vField['name'];
    if ($vField['ezf_field_type'] == '63' || $vField['ezf_field_type'] == '64') {
        $dataFormDate[$vField['ezf_name']][$vField['id']] = $vField['name'];
    }
    if ($vField['ezf_field_type'] == '71') {
        $dataFormImg[$vField['ezf_name']][$vField['id']] = $vField['name'];
    }
}

$ezf_main_id = backend\modules\patient\Module::$formID['visit'];
$ezf_id_forjava = "'" . implode("','", $value_ezf_id) . "'";
echo Html::hiddenInput('options[ezf_main_id]', $ezf_main_id);
//echo Html::hiddenInput('options[ezf_ref_id]', '["' . implode('","', $value_ezf_id) . '"]');
?>
<!--config start-->
<div class="hidden">
    <?php
    $value_ezf_id = [
        backend\modules\patient\Module::$formID['visit'],
        backend\modules\patient\Module::$formID['profile'],
        backend\modules\patient\Module::$formID['patientright'],
        backend\modules\patient\Module::$formID['order_header'],
        backend\modules\patient\Module::$formID['order_tran'],
        backend\modules\patient\Module::$formID['pis_order'],
        backend\modules\patient\Module::$formID['pis_order_tran'],
    ];

    $dataRefForm = QueueFunc::getFormRefMainForm($value_ezf_id);
    echo Select2::widget([
        'id' => 'select-ezf-ref-id',
        'name' => "options[ezf_ref_id]",
        'value' => $value_ezf_id,
        'data' => ArrayHelper::map($dataRefForm, "id", "name"),
        'options' => ['placeholder' => Yii::t('ezform', 'Select field ...'), 'multiple' => true],
        'pluginOptions' => [
            'allowClear' => true,
        ]
    ]);
    ?>
</div>
<style>
  .config-widget-header{
      background-color:#CBCAC6;
  }
</style>

<!--Config Form Start-->
<div class="form-group row">
  <div class="modal-header config-widget-header">
    <h4 class="modal-title" id="itemModalLabel">Config Form</h4>
  </div> 
  <div class="col-md-12" style="margin-top:5px">
    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;border-bottom: 1px solid #ddd;">
      <li role="presentation" class="active"><a href="#general-tab" aria-controls="home" role="tab" data-toggle="tab"><?= Yii::t('ezmodule', 'General Settings') ?></a></li>
      <li role="presentation"><a href="#advanced-tab" aria-controls="profile" role="tab" data-toggle="tab"><?= Yii::t('ezmodule', 'Advanced Settings') ?></a></li>    
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="general-tab">
        <div class="form-group row">  
          <div class="col-md-3">
              <?= Html::label(Yii::t('ezform', 'Icon'), 'options[icon]', ['class' => 'control-label']) ?>
              <?=
              dominus77\iconpicker\IconPicker::widget([
                  'name' => 'options[icon]',
                  'value' => isset($options['icon']) ? $options['icon'] : '',
                  'options' => ['class' => 'dicon-input form-control', 'id' => 'config_icon'],
                  'clientOptions' => [
                      'hideOnSelect' => true,
                  ]
              ])
              ?>

          </div>
          <div class="col-md-3 sdbox-col">
              <?= Html::label(Yii::t('ezform', 'Title'), 'options[title]', ['class' => 'control-label']) ?>
              <?= Html::textInput('options[title]', (isset($options['title']) ? $options['title'] : Yii::t('ezform', 'Title')), ['class' => 'form-control']) ?>
          </div>

          <!--position start-->
          <div class="col-md-2">
              <?= Html::label(Yii::t('ezform', 'Position Type'), 'options[position][position_type]', ['class' => 'control-label']) ?>
              <?=
              Html::dropDownList('options[position][position_type]', isset($options['position']['position_type']) ? $options['position']['position_type'] : '1', ['1' => 'Static', '2' => 'Fixed'], ['class' => 'form-control', 'id' => 'position_type']);
              ?>
          </div>
          <div class="col-md-2 sdbox-col" id='position-static' style="display:none">
              <?= Html::label(Yii::t('ezform', 'Height'), 'options[position][height_static]', ['class' => 'control-label']) ?>
              <?=
              Html::dropDownList('options[position][height_static]', isset($options['position']['height_static']) ? $options['position']['height_static'] : '100', ['25' => '25%', '50' => '50%', '75' => '75%', '100' => '100%'], ['class' => 'form-control']);
              ?>
          </div>

          <div class="col-md-2 sdbox-col" id='position-fixed' style="display:none">
              <?= Html::label(Yii::t('ezform', 'Position'), 'options[position][fixed_position]', ['class' => 'control-label']) ?>
              <?=
              Html::dropDownList('options[position][fixed_position]', isset($options['position']['fixed_position']) ? $options['position']['fixed_position'] : '1', ['1' => 'Left', '2' => 'Right', '3' => 'Top - Left', '4' => 'Bottom - left', '5' => 'Top - Right', '6' => 'Bottom - Right'], ['class' => 'form-control']);
              ?>
          </div>

          <div class="col-md-2 sdbox-col" id='position-fixed'>
              <?= Html::label(Yii::t('ezform', 'Width') . ' (px)', 'options[position][width]', ['class' => 'control-label']) ?>
              <?= Html::textInput('options[position][width]', isset($options['position']['width']) ? $options['position']['width'] : '350', ['class' => 'form-control', 'type' => 'number']); ?>
          </div>
          <!--position end-->
        </div>        

        <div class="form-group row">          
          <div class="col-md-12">
              <?php
              $attrname_fields = 'options[fields]';
              $value_fields = isset($options['fields']) && is_array($options['fields']) ? $options['fields'] : [];
              ?>
              <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
            <div id="ref_field_box">
                <?php
                echo Select2::widget([
                    'id' => 'select-field',
                    'name' => $attrname_fields,
                    'value' => $value_fields,
                    'data' => $dataForm,
                    'options' => ['placeholder' => Yii::t('ezform', 'Select field ...'), 'multiple' => true],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]);
                ?>
            </div>
          </div>
        </div>        

        <div class="form-group row">
          <div class="col-md-6">
              <?php
              $attrname_image_field = 'options[image_field]';
              $value_image_field = isset($options['image_field']) ? $options['image_field'] : '';
              ?>
              <?= Html::label(Yii::t('ezform', 'Image Field'), $attrname_image_field, ['class' => 'control-label']) ?>
            <div id="pic_field_box">
                <?php
                echo Select2::widget([
                    'id' => 'select-field-img-' . SDUtility::getMillisecTime(),
                    'name' => $attrname_image_field,
                    'value' => $value_image_field,
                    'data' => $dataFormImg,
                    'options' => ['placeholder' => Yii::t('ezform', 'Select field ...')],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]);
                ?>
            </div>
          </div>
          <div class="col-md-6 sdbox-col">
              <?php
              $attrname_bdate_field = 'options[bdate_field]';
              $value_bdate_field = isset($options['bdate_field']) ? $options['bdate_field'] : '';
              ?>
              <?= Html::label(Yii::t('ezform', 'Birthdate Field'), $attrname_bdate_field, ['class' => 'control-label']) ?>
            <div id="bdate_field_box">
                <?php
                echo Select2::widget([
                    'id' => 'select-field-bdate-' . SDUtility::getMillisecTime(),
                    'name' => $attrname_bdate_field,
                    'value' => $value_bdate_field,
                    'data' => $dataForm,
                    'options' => ['placeholder' => Yii::t('ezform', 'Select field ...')],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]);
                ?>
            </div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-12 ">
              <?= Html::label(Yii::t('ezform', 'Template Content'), 'options[template_content]', ['class' => 'control-label']) ?>
              <?= Html::textarea('options[template_content]', isset($options['template_content']) ? $options['template_content'] : '', ['class' => 'form-control', 'row' => 3]) ?>
          </div>
        </div>        
      </div>

      <div role="tabpanel" class="tab-pane" id="advanced-tab">   
        <div class="form-group row">
          <div class="col-md-12">
              <?php
              $attrname_fields_search_one = 'options[fields_search_one]';
              $value_fields_search_one = isset($options['fields_search_one']) ? $options['fields_search_one'] : '';
              ?>
              <?= Html::label(Yii::t('ezform', 'Fields Search one box'), $attrname_fields_search_one, ['class' => 'control-label']) ?>
            <div id="ref_field_search_one_box">
                <?php
                echo Select2::widget([
                    'id' => 'select-search-one',
                    'name' => $attrname_fields_search_one,
                    'value' => $value_fields_search_one,
                    'data' => $dataForm,
                    'options' => ['placeholder' => Yii::t('ezform', 'Select field ...'), 'multiple' => true],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]);
                ?>
            </div>
          </div>          
        </div>

        <div class="form-group row">
          <div class="col-md-3 ">
              <?= Html::label(Yii::t('ezform', 'Action'), '', ['class' => 'control-label'])
              ?>
              <?= Html::dropDownList('options[action]', isset($options['action']) ? $options['action'] : '1', ['1' => 'Redirect', '2' => 'Open Form', '3' => 'Ajax', '4' => 'None'], ['class' => 'form-control', 'id' => 'config_action']) ?>
          </div>
          <div class="col-md-6 sdbox-col divUrl">
              <?php
              $attrname_url = 'options[data_url]';
              $value_url = isset($options['data_url']) ? $options['data_url'] : '';
              ?>
              <?= Html::label(Yii::t('ezform', 'URL'), $attrname_url, ['class' => 'control-label']) ?>
              <?= Html::textarea($attrname_url, isset($options['data_url']) ? $options['data_url'] : '', ['class' => 'form-control', 'row' => 3]) ?>
          </div>
          <div class="col-md-3 sdbox-col">
            <div class="divElement">
                <?= Html::label(Yii::t('ezform', 'Render To (Element ID)'), 'optioms[element_id]', ['class' => 'control-label']) ?>
                <?= Html::textInput('options[element_id]', isset($options['element_id']) ? $options['element_id'] : '', ['class' => 'form-control']) ?>
            </div>
            <?php
            $value_cleardiv = isset($options['fields_search_cleardiv']) ? $options['fields_search_cleardiv'] : '';
            echo Html::label(Yii::t('ezform', 'OnChange ClearDiv'), '', ['class' => 'control-label']);
            echo Html::textInput('options[fields_search_cleardiv]', $value_cleardiv, ['class' => 'form-control varname-input']);
            ?>
          </div>
        </div>
        <div class="form-group row">
            <?php
            $value_param = isset($options['param']) ? $options['param'] : [];
            $value_param_active = isset($options['param_active']) ? $options['param_active'] : [];
            ?>
          <div class="col-md-12">
            <div class="btn btn-success btn-add-param"><i
                  class="glyphicon glyphicon-plus"></i> <?= Yii::t('ezform', 'Add Parameter') ?></div>
          </div>
          <div class="col-md-12 divMainParam" style="margin-top:2%">
            <div class="col-md-1">
              <label> <?= Yii::t('ezform', 'Active') ?></label>
            </div>
            <div class="col-md-4 text-center">
              <label><?= Yii::t('ezform', 'Parameter name') ?></label>
            </div>
            <div class="col-md-5 text-center">
              <label><?= Yii::t('ezform', 'Parameter value') ?></label>
            </div>
          </div>
          <div id="div_param_box">
              <?php
              if (is_array($value_param) && !empty($value_param)) {
                  foreach ($value_param as $k => $v) {
                      ?>

                    <div class="col-md-12 divMainParam" style="margin-top:2%">
                      <div class="col-md-1">
                          <?= Html::checkbox('options[param][' . $k . '][param_active]', isset($v['param_active']) && $v['param_active'] == 1 ? 1 : 0, ['class' => 'check_box_active']); ?>
                      </div>
                      <div class="col-md-4">
                          <?php echo Html::textInput('options[param][' . $k . '][name]', $v['name'], ['class' => 'form-control']); ?>
                      </div>
                      <div class="col-md-5">
                          <?php
                          echo \kartik\select2\Select2::widget([
                              'id' => 'select-value-param-' . SDUtility::getMillisecTime(),
                              'name' => 'options[param][' . $k . '][value]',
                              'value' => $v['value'],
                              'data' => $dataForm,
                              'options' => ['placeholder' => Yii::t('ezform', 'Select field ...')],
                              'pluginOptions' => [
                                  'allowClear' => true,
                              ]
                          ]);
                          ?>
                      </div>
                      <div class="col-md-2">
                          <?php echo Html::tag('div', '<i class="glyphicon glyphicon-remove"></i>', ['class' => 'btn btn-danger btn-remove-param']) ?>
                      </div>

                    </div>
                    <?php
                }
            }
            ?>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-md-12">
            <?= Html::radioList('options[oipd_type]', (isset($options['oipd_type']) ? $options['oipd_type'] : 'OPD'), ['OPD' => 'OPD', 'IPD' => 'IPD']); ?>
          </div>
        </div>
      </div>
    </div>      
  </div>

  <!--config end-->

  <?php
  \richardfan\widget\JSRegister::begin();
  ?>
  <script>

      if ($('#config_action').val() == '1') {
        $('.divUrl').show();
        $('.divElement').hide();
      } else if ($('#config_action').val() == '3') {
        $('.divUrl').show();
        $('.divElement').show();
      } else {
        $('.divUrl').hide();
        $('.divElement').hide();
      }
      $('#config_action').change(function () {
        if ($(this).val() == '1') {
          $('.divUrl').show();
          $('.divElement').hide();
        } else if ($(this).val() == '3') {
          $('.divUrl').show();
          $('.divElement').show();
        } else {
          $('.divUrl').hide();
          $('.divElement').hide();
        }
      });

      if ($('#position_type').val() == '1') {
        $('#position-static').show();
      } else {
        $('#position-fixed').show();
      }
      $('#position_type').change(function () {
        if ($(this).val() == '1') {
          $('#position-static').show();
          $('#position-fixed').hide();
        } else {
          $('#position-fixed').show();
          $('#position-static').hide();
        }
      });

      $('#div_param_box').on('click', '.check_box_active', function () {
        $('#div_param_box').find('.check_box_active').not(this).prop('checked', false);
      });

      function getFormParam(param_name, param_value) {
        const main_ezf_id = <?= $ezf_main_id ?>;
        const value_ref = [<?= $ezf_id_forjava ?>];
        $.post('<?= Url::to(['/queue/default/get-form-param']) ?>', {main_ezf_id: main_ezf_id, ezf_id: value_ref, param_name: param_name, param_value: param_value}
        ).done(function (result) {
          $('#div_param_box').append(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>
          console.log('server error');
        });
      }

      $('.btn-add-param').click(function () {
        getFormParam('', '');
      });


      $('#div_param_box').on('click', '.btn-remove-param', function () {
        $(this).parents('.divMainParam').remove();
      });


      $('.header-items-add').on('click', function () {
        getWidget();
      });

      $('#header-item-box').on('click', '.header-items-del', function () {
        $(this).parent().parent().remove();
      });

      function getWidget() {
        $.ajax({
          method: 'POST',
          url: '<?= Url::to(['/ezmodules/ezmodule-tab/get-widget', 'view' => '/ezmodule-widget/assets/queue/_form_header']) ?>',
          dataType: 'HTML',
          success: function (result, textStatus) {
            $('#header-item-box').append(result);
          }
        });
      }

      $('#btn-add-condition').click(function () {
        getFormCondition('', '');
      });

      $('#div-condition').on('click', '.btn-remove-condition', function () {
        $(this).parents('.divMainCondition').remove();
      });

      function getFormCondition(param_name, param_value) {
        const main_ezf_id = '<?= $ezf_main_id ?>';
        const value_ref = [<?= $ezf_id_forjava ?>];
        var condition_date = [];
        if ($('#condition-date').is(':checked')) {
          condition_date = [63, 64];
        }
        $.post('<?= Url::to(['/queue/default/get-form-condition']) ?>', {main_ezf_id: main_ezf_id, ezf_id: value_ref, param_name: param_name, param_value: param_value, type: condition_date}
        ).done(function (result) {
          $('#div-condition').append(result);
        }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>
          console.log('server error');
        });
      }

      //select field trigger to search boxselect-field
      $('#ref_field_box').on('select2:select', '#select-field', function (e) {
        $('#select-search-one').val($(this).val()).trigger('change');
      });

      $('#ref_field_box').on('select2:unselect', '#select-field', function (e) {
        $('#select-search-one').val($(this).val()).trigger('change');
      });
  </script>

  <?php \richardfan\widget\JSRegister::end(); ?>
