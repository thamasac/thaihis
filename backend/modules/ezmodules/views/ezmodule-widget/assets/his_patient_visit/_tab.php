<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzformWidget;
use yii\web\JsExpression;

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

$itemWidgets = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetByModule($ezm_id);

$this->registerCss("
    
    .nav-tabs {
        border-bottom: 0 solid #ddd;
    }
    .tab-primary{
        color:#ffffff;
        border-color: #ddd;
    }
    
    .tab-active {
        background-color: #ffffff;
        color:#337ab7;
	border-color: #ddd;
	border-bottom-color: transparent;
    }
    .nav > .nav-item > a:focus {
        background-color: #ffffff;
    }
");

$tab_type = isset($type) ? $type : '1';
$tab_item = ['1' => 'Ezform', '2' => 'Ezwidget', '3' => 'HTML Content', '4' => 'Ajax Request'];
$tab_keys = [];
$attrname_field_display = [];
$main_field_display = [];
$attrname_field_pic = [];
$main_field_pic = [];

$key_index = isset($key_index) ? $key_index : 0;


$val = isset($tabs[$key_index]) ? $tabs[$key_index] : null;
if ($act == 'addNew') {
    $display = 'display:block';
} else {
    if ($firstIndex == $key_index) {
        $display = 'display:block';
    } else {
        $display = 'display:none';
    }
}

$type_ezform = 'display:none;';
$type_widget = 'display:none;';
$type_content = 'display:none;';
$type_ajax = 'display:none;';
if ($val) {
    if ($val['tab_type'] == 2) {
        $type_widget = 'display:block;';
    } else if ($val['tab_type'] == 3) {
        $type_content = 'display:block;';
    } else if ($val['tab_type'] == 4) {
        $type_ajax = 'display:block;';
    } else {
        $type_ezform = 'display:block;';
    }
} else {
    $type_ezform = 'display:block;';
}

$subquery = isset($val['subquery']) ? $val['subquery'] : [];
$variables = isset($val['variables']) ? $val['variables'] : [];
$subcontent = isset($val['subcontent']) ? $val['subcontent'] : [];
?>

<div  id="content_config_tab<?= $key_index ?>" style="<?= $display ?>">
  <div class="col-md-12" style="margin-top:5px">
    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
      <li role="presentation" class="active"><a href="#general-tab<?= $key_index ?>" aria-controls="home" role="tab" data-toggle="tab"><?= Yii::t('ezmodule', 'General Settings') ?></a></li>
      <li role="presentation"><a href="#advanced-tab<?= $key_index ?>" aria-controls="profile" role="tab" data-toggle="tab"><?= Yii::t('ezmodule', 'Advanced Settings') ?></a></li>    
    </ul>

    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="general-tab<?= $key_index ?>">
        <!--icon start-->
        <div class="form-group row">
          <div class="col-md-4">
              <?php
              $attrname_icon = 'options[tabs][' . $key_index . '][icon]';
              $value_icon = isset($val['icon']) ? $val['icon'] : null;
              ?>
              <?= Html::label(Yii::t('ezform', 'Icon'), $attrname_icon, ['class' => 'control-label']) ?>
              <?=
              dominus77\iconpicker\IconPicker::widget([
                  'name' => $attrname_icon,
                  'value' => $value_icon,
                  'options' => ['class' => 'dicon-input form-control', 'id' => 'config_icon' . $key_index],
                  'clientOptions' => [
                      'hideOnSelect' => true,
                  ]
              ])
              ?>

          </div>
          <div class="col-md-4 sdbox-col">
              <?= Html::label(Yii::t('ezform', 'Title'), 'tab_title') ?>
              <?= Html::textInput('options[tabs][' . $key_index . '][tab_title]', $val['tab_title'], ['class' => 'form-control', 'id' => 'title_input' . $key_index, 'data-key_index' => $key_index]) ?>
          </div>
          <div class="col-md-4 sdbox-col">
              <?= Html::label(Yii::t('ezform', 'Type'), 'tab_type') ?>
              <?= Html::dropDownList('options[tabs][' . $key_index . '][tab_type]', $val['tab_type'], $tab_item, ['class' => 'form-control tab_type_input', 'id' => 'tab_type_' . $key_index, 'data-key_index' => $key_index]) ?>
          </div>
        </div>
        <!--icon end-->
        <!--form main, visit, tran start-->
        <div class="form-group row" >
          <div class="col-md-12" id="display_ezform<?= $key_index ?>" style="<?= $type_ezform ?>">
            <div class="form-group row">
              <div class="col-md-4">
                  <?php
                  $attrname_main_ezf_id = 'options[tabs][' . $key_index . '][main_ezf_id]';
                  $value_main_ezf_id = isset($val['main_ezf_id']) ? $val['main_ezf_id'] : null;
                  ?>
                  <?= Html::label(Yii::t('ezmodule', 'Forms <code>*</code>'), $attrname_main_ezf_id, ['class' => 'control-label']) ?>
                  <?php
                  echo kartik\select2\Select2::widget([
                      'name' => $attrname_main_ezf_id,
                      'value' => $value_main_ezf_id,
                      'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_ezf_id' . $key_index, 'key_index' => $key_index],
                      'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                      'pluginOptions' => [
                          'allowClear' => true
                      ],
                  ]);
                  ?>
              </div>
              <div class="col-md-4 sdbox-col">
                  <?php
                  $attrname_ezf_id = 'options[tabs][' . $key_index . '][ezf_id]';
                  $value_ezf_id = isset($val['ezf_id']) ? $val['ezf_id'] : null;
                  ?>
                  <?= Html::label(Yii::t('ezmodule', 'Visit Forms <code>*</code>'), $attrname_ezf_id, ['class' => 'control-label']) ?>
                  <?php
                  echo kartik\select2\Select2::widget([
                      'name' => $attrname_ezf_id,
                      'value' => $value_ezf_id,
                      'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_visit_ezf_id' . $key_index, 'key_index' => $key_index],
                      'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                      'pluginOptions' => [
                          'allowClear' => true
                      ],
                  ]);
                  ?>
              </div>

              <div class="col-md-4 sdbox-col">
                  <?php
                  $attrname_tran_ezf_id = 'options[tabs][' . $key_index . '][tran_ezf_id]';
                  $value_tran_ezf_id = isset($val['tran_ezf_id']) ? $val['tran_ezf_id'] : 0;
                  ?>
                  <?= Html::label(Yii::t('thaihis', 'Form Visit Transection'), $attrname_tran_ezf_id, ['class' => 'control-label']) ?>
                  <?php
                  echo kartik\select2\Select2::widget([
                      'name' => $attrname_tran_ezf_id,
                      'value' => $value_tran_ezf_id,
                      'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_tran_ezf_id' . $key_index, 'key_index' => $key_index],
                      'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                      'pluginOptions' => [
                          'allowClear' => true
                      ],
                  ]);
                  ?>
              </div> 
            </div>
            <!--form main,visit, tran end-->
            <!--ref form start-->
            <div class="form-group row">
              <div class="col-md-12">
                  <?php
                  $attrname_ref = 'options[tabs][' . $key_index . '][refform]';
                  $value_ref = isset($val['refform']) && is_array($val['refform']) ? $val['refform'] : null;
                  ?>
                  <?= Html::label(Yii::t('ezform', 'Reference Form <code>*</code>'), '', ['class' => 'control-label']) ?>
                <div id="ref_form_box<?= $key_index ?>" key_index="<?= $key_index ?>">

                </div>
              </div>
            </div>
            <!--ref form end-->
            <!--image, age start-->
            <div class="form-group row" >
              <div class="col-md-6">
                  <?php
                  $attrname_field_bdate = 'options[tabs][' . $key_index . '][field_bdate]';
                  $main_field_bdate = isset($val['field_bdate']) ? $val['field_bdate'] : null;
                  ?>
                  <?= Html::label(Yii::t('ezform', 'Field of birthdate'), '', ['class' => 'control-label']) ?>
                <div id="ref_field_bdate_<?= $key_index ?>">

                </div>
              </div>


              <div class="col-md-6 sdbox-col">
                  <?php
                  $attrname_field_pic = 'options[tabs][' . $key_index . '][field_pic]';
                  $main_field_pic = isset($val['field_pic']) ? $val['field_pic'] : null;
                  ?>
                  <?= Html::label(Yii::t('ezform', 'Image Field'), '', ['class' => 'control-label']) ?>
                <div id="ref_field_pic_<?= $key_index ?>">

                </div>
              </div>
            </div>
            <!--image, age end-->
            <!--field select strat--> 
            <div class="form-group row" >
              <div class="col-md-12">
                  <?php
                  $attrname_field_display = 'options[tabs][' . $key_index . '][field_display]';
                  $main_field_display = isset($val['field_display']) && is_array($val['field_display']) ? $val['field_display'] : null;
                  ?>
                  <?= Html::label(Yii::t('ezform', 'Fields <code>*</code>'), 'options[tabs][' . $key_index . '][field_display]', ['class' => 'control-label']) ?>
                <div id="ref_field_box_<?= $key_index ?>">

                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12" id="display_widget_content<?= $key_index ?>" style="<?= $type_widget ?>">
            <div class="col-md-12 sdbox-col">
                <?= Html::label(Yii::t('ezform', 'Widget'), 'options[tabs][' . $key_index . '][widget_id]') ?>
                <?php
                $attrname_widget_id = 'options[tabs][' . $key_index . '][widget_id]';
                $value_widget_id = isset($val['widget_id']) ? $val['widget_id'] : '';
                echo kartik\select2\Select2::widget([
                    'name' => $attrname_widget_id,
                    'value' => $value_widget_id,
                    'options' => ['placeholder' => Yii::t('ezmodule', 'Widgets'), 'id' => 'config_widget_id_' . $key_index, 'key_index' => $key_index],
                    'data' => ArrayHelper::map($itemWidgets, 'widget_id', 'widget_name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-12 sdbox-col">
                <?= Html::label(Yii::t('ezform', 'Custom Action (URL)'), 'options[tabs][' . $key_index . '][custom_action]', ['class' => 'control-label']) ?>
                <?= Html::textarea('options[tabs][' . $key_index . '][custom_action]', isset($tabs[$key_index]['custom_action']) ? $tabs[$key_index]['custom_action'] : '', ['class' => 'form-control', 'row' => 3]) ?>
            </div>
          </div>
          <div class="col-md-12"  id="display_content<?= $key_index ?>" style="<?= $type_content ?>">
              <?= \appxq\sdii\widgets\FroalaEditorWidget::widget(['name' => 'options[tabs][' . $key_index . '][tab_content]', 'id' => 'tab-type-content' . $key_index]) ?>
          </div>

          <div class="col-md-12" id="display_ajax<?= $key_index ?>" style="<?= $type_ajax ?>">
              <?= Html::label(Yii::t('ezform', 'URL Request <code>Params ที่จะถูกส่งไปด้วย visitid, target</code>'), 'options[tabs][' . $key_index . '][url_request]', ['class' => 'control-label']) ?>
              <?= Html::textarea('options[tabs][' . $key_index . '][url_request]', isset($val['url_request']) ? $val['url_request'] : '', ['class' => 'form-control', 'row' => 3]) ?>
              <?= Html::label(Yii::t('ezform', 'URL Request Popup <code>Params ที่จะถูกส่งไปด้วย visitid, target</code>'), 'options[tabs][' . $key_index . '][url_request_popup]', ['class' => 'control-label']) ?>
              <?= Html::textarea('options[tabs][' . $key_index . '][url_request_popup]', isset($val['url_request_popup']) ? $val['url_request_popup'] : '', ['class' => 'form-control', 'row' => 3]) ?>

            <?= Html::checkbox('options[tabs][' . $key_index . '][url_target_blank]', isset($val['url_target_blank']) ? $val['url_target_blank'] : 0) ?>
            <?= Html::label(Yii::t('ezform', 'Target -> Blank'), 'options[tabs][' . $key_index . '][url_target_blank]', ['class' => 'control-label']) ?>
          </div>
        </div>
        <!--field select end-->

        <!--btn action start-->
        <div class="form-group row">
          <div class="col-md-6 " >
              <?= Html::label(Yii::t('ezform', 'Action'), 'options[tabs][' . $key_index . '][action]', ['class' => 'control-label']) ?>
              <?=
              kartik\select2\Select2::widget([
                  'id' => 'config_action' . $key_index,
                  'name' => 'options[tabs][' . $key_index . '][action]',
                  'value' => isset($tabs[$key_index]['action']) ? $tabs[$key_index]['action'] : ['create', 'update', 'delete', 'view', 'search'],
                  'data' => backend\modules\ezmodules\classes\ModuleFunc::itemAlias('action'),
//                  'maintainOrder' => true,
                  'options' => ['placeholder' => Yii::t('ezform', 'Select action ...'), 'multiple' => true],
                  'pluginOptions' => [
                      'allowClear' => true,
                      'tags' => true,
                      'tokenSeparators' => [',', ' '],
                  ]
              ]);
              ?>
          </div>
          <div class="col-md-6 sdbox-col" >
              <?= Html::label(Yii::t('ezform', 'Column'), 'options[tabs][' . $key_index . '][column]', ['class' => 'control-label']) ?>
              <?= Html::textInput('options[tabs][' . $key_index . '][column]', $val['column'], ['class' => 'form-control', 'type' => 'number', 'id' => 'column_' . $key_index]) ?>
          </div>
        </div>
        <!--btn action end-->
      </div>
      <div role="tabpanel" class="tab-pane" id="advanced-tab<?= $key_index ?>">
        <!--Template Content Start-->
        <div class="form-group row" >
          <div class="col-md-12">
              <?= Html::label(Yii::t('ezform', 'Template Content'), 'options[tabs][' . $key_index . '][template_content]', ['class' => 'control-label']) ?>
              <?= Html::textarea('options[tabs][' . $key_index . '][template_content]', isset($val['template_content']) ? $val['template_content'] : '', ['class' => 'form-control', 'row' => 3]) ?>
          </div>
        </div>
        <!--Template Content end-->
        <!--left form start-->
        <div class="form-group row">
          <div class="col-md-12">
              <?php
              $attrname_leftref = 'options[tabs][' . $key_index . '][left_refform]';
              $value_leftref = isset($val['left_refform']) && is_array($val['left_refform']) ? $val['left_refform'] : null;
              ?>
              <?= Html::label(Yii::t('ezform', 'Form to left join'), '', ['class' => 'control-label']) ?>
            <div id="left_ref_form_box<?= $key_index ?>" key_index="<?= $key_index ?>">

            </div>
          </div>
        </div>
        <!--left form end`-->       
        <!--Sub query custom start-->
        <div class="form-group row">
          <div class="col-md-12">
            <div class="panel panel-warning">
              <div class="panel-heading">
                  <?= Html::label(Yii::t('thaihis', 'Sub query custom')) ?>
                  <?= Html::button("<i class='fa fa-plus'></i>", ['class' => 'btn btn-success', 'id' => 'btn-add-subquery' . $key_index]) ?>
              </div>
              <div class="panel-body">
                <div class="sdbox-col" id="display-subquery<?= $key_index ?>">
                    <?php
                    if (isset($subquery) && is_array($subquery)):
                        $firstIndex = null;
                        foreach ($subquery as $key => $val):
                            $sub_index = $key;
                            if (!$firstIndex)
                                $firstIndex = $key;

                            echo $this->renderAjax('_subquery_custom', [
                                'key_index' => $key_index,
                                'sub_index' => $sub_index,
                                'subquery' => $subquery,
                                'firstIndex' => $firstIndex,
                                'ezf_id' => $ezf_id,
                                'act' => 'onLoad'
                            ]);
                        endforeach;
                    endif;
                    ?>

                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Sub query custom end -->

        <div class="form-group row">
          <div class="col-md-12">
            <div class="panel panel-warning">
              <div class="panel-heading">
                  <?= Html::label(Yii::t('thaihis', 'Variable custom')) ?>
                  <?= Html::button("<i class='fa fa-plus'></i>", ['class' => 'btn btn-success', 'id' => 'btn-add-variable' . $key_index]) ?>
              </div>
              <div class="panel-body">
                <div class="sdbox-col" id="display-variable<?= $key_index ?>">
                    <?php
                    if (isset($variables) && is_array($variables)):
                        $firstIndex = null;
                        foreach ($variables as $key => $val):
                            $sub_index = $key;
                            if (!$firstIndex)
                                $firstIndex = $key;

                            echo $this->renderAjax('_variable_custom', [
                                'key_index' => $key_index,
                                'sub_index' => $sub_index,
                                'variables' => $variables,
                                'firstIndex' => $firstIndex,
                                'ezf_id' => $ezf_id,
                                'act' => 'onLoad'
                            ]);
                        endforeach;
                    endif;
                    ?>

                </div>
              </div>
            </div>
          </div>
        </div>
        <!--Variable custom end-->
        <!--Sub content start-->
        <div class="form-group row">
          <div class="col-md-12">
            <div class="panel panel-warning">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-md-8">
                      <?= Html::label(Yii::t('thaihis', 'Sub content'), '') ?>
                      <?= Html::button("<i class='fa fa-plus'></i>", ['class' => 'btn btn-success', 'id' => 'btn-add-subcontent' . $key_index]) ?>
                  </div>
                  <div class="col-md-4">
                      <?= EzformWidget::checkbox('options[tabs][' . $key_index . '][require_data_subc]', isset($tabs[$key_index]['require_data_subc']) ? $tabs[$key_index]['require_data_subc'] : 1, ['label' => 'Require data of main box']) ?>
                  </div>
                </div>
              </div>
              <div class="panel-body">
                <div class="sdbox-col" id="display-subcontent<?= $key_index ?>">
                    <?php
                    if (isset($subcontent) && is_array($subcontent)):
                        $firstIndex = null;
                        foreach ($subcontent as $key => $val):
                            $sub_index = $key;
                            if (!$firstIndex)
                                $firstIndex = $key;

                            echo $this->renderAjax('_sub_content', [
                                'key_index' => $key_index,
                                'ezf_id' => $ezf_id,
                                'sub_index' => $sub_index,
                                'subcontent' => $subcontent,
                                'act' => 'onLoad'
                            ]);
                        endforeach;
                    endif;
                    ?>

                </div>
              </div>
            </div>
          </div>
        </div>
        <!--Sub content end-->

      </div>
    </div>
  </div>
  <div class="form-group row">
    <div class="col-md-3">
        <?php
        $condis_val = (isset($tabs[$key_index]['condition_display']) ? $tabs[$key_index]['condition_display'] : 0);
        ?>
        <?= yii\bootstrap\Html::hiddenInput('options[tabs][' . $key_index . '][condition_display]', 0) ?>
        <?= EzformWidget::checkbox('options[tabs][' . $key_index . '][condition_display]', $condis_val, ['label' => 'Condition to display', 'id' => 'check_condition_display' . $key_index]) ?>      
    </div>
    <div class="col-md-9 sdbox-col " id="condition_display_content<?= $key_index ?>" style="display:<?= ($condis_val == '1' ? 'block' : 'none') ?>;">
      <div class="col-md-4 sdbox-col">
          <?= Html::label(Yii::t('thaihis', 'Parameter name')) ?>
          <?= Html::textInput('options[tabs][' . $key_index . '][param_name]', (isset($tabs[$key_index]['param_name']) ? $tabs[$key_index]['param_name'] : ''), ['class' => 'form-control']) ?>
      </div>
      <div class="col-md-4 sdbox-col">
          <?php
          $items = ['1' => 'เท่ากับ', '2' => 'มากกว่า', '3' => 'น้อยกว่า', '4' => 'ไม่เท่ากับ'];
          ?>
          <?= Html::label(Yii::t('thaihis', 'Condition')) ?>
          <?= Html::dropDownList('options[tabs][' . $key_index . '][condition]', (isset($tabs[$key_index]['condition']) ? $tabs[$key_index]['condition'] : ''), $items, ['class' => 'form-control']) ?>
      </div>
      <div class="col-md-4 sdbox-col">
          <?= Html::label(Yii::t('thaihis', 'Value')) ?>
          <?= Html::textInput('options[tabs][' . $key_index . '][value]', (isset($tabs[$key_index]['value']) ? $tabs[$key_index]['value'] : ''), ['class' => 'form-control']) ?>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <div class="col-md-6">
        <?php
        $editdat_val = (isset($tabs[$key_index]['edit_data_own']) ? $tabs[$key_index]['edit_data_own'] : 0);
        ?>
        <?= EzformWidget::checkbox('options[tabs][' . $key_index . '][edit_data_own]', $editdat_val, ['label' => 'Edit data by own only', 'id' => 'edit_data_own' . $key_index]) ?>      
    </div>
  </div>
  <div class="form-group row">
    <div class="col-md-3">
        <?php
        $dept_display = (isset($tabs[$key_index]['dept_display']) ? $tabs[$key_index]['dept_display'] : 0);
        $css_display = isset($dept_display) && $dept_display == '1' ? 'display:block;' : 'display:none;';
        ?>
        <?= yii\bootstrap\Html::hiddenInput('options[tabs][' . $key_index . '][dept_display]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[tabs][' . $key_index . '][dept_display]', $dept_display, ['label' => 'Tab of department ', 'id' => 'dept_same_display' . $key_index]) ?>      
    </div>
    <div class="col-md-6" id="department_selector<?= $key_index ?>" style="<?= $css_display ?>">
      <?php
      $dept_list = (isset($tabs[$key_index]['dept_list']) ? $tabs[$key_index]['dept_list'] : null);
      $deptData = [];
      if ($dept_list) {
          $deptjoin = is_array($dept_list) ? join($dept_list, ',') : '';
          $deptData = backend\modules\thaihis\classes\ThaiHisQuery::getTableData("zdata_working_unit", "id IN($deptjoin)");
      }
      ?>
      <?php
//            \appxq\sdii\utils\VarDumper::dump($value_form_list);
      echo kartik\select2\Select2::widget([
          'name' => 'options[tabs][' . $key_index . '][dept_list]',
          'value' => $dept_list,
          'options' => ['placeholder' => Yii::t('ezmodule', 'Select form (s) a department'), 'id' => 'config_form_list' . $key_index, 'multiple' => '1'],
          'data' => ($deptData) ? ArrayHelper::map($deptData, 'id', 'unit_name') : [],
          'pluginOptions' => [
              'allowClear' => true,
              'ajax' => [
                  'url' => '/thaihis/patient-visit/search-dept?sht=',
                  'dataType' => 'json',
                  'data' => new JsExpression('function(params) { return {q:params.term}; }')
              ],
              'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
              'templateResult' => new JsExpression('function(city) { return city.text; }'),
              'templateSelection' => new JsExpression('function (city) { return city.text; }'),
          ],
      ]);
      ?>     
    </div>

    <!--end tab config--> 
  </div>

</div>
<?php
$this->registerJs("
    $(function(){
    
        var key_index = '$key_index';
        var variables = " . json_encode($variables) . ";
        var subcontent = " . json_encode($subcontent) . ";
        var ref_form1 = $('#config_ref_form'+key_index).val();
        var ref_form2 = $('#config_left_ref_form'+key_index).val();
        var visit_form = $('#config_visit_ezf_id'+key_index).val();
        
        form_ref$key_index($('#config_ezf_id'+key_index).val(),visit_form,null);
        form_leftref$key_index($('#config_visit_ezf_id'+key_index).val(),visit_form,null,null);
        
        fields$key_index($('#config_ezf_id'+key_index).val(),visit_form,ref_form1,ref_form2);
        field_pic$key_index($('#config_ezf_id'+key_index).val(),visit_form,ref_form1,ref_form2);
        field_bdate$key_index($('#config_ezf_id'+key_index).val(),visit_form,ref_form1,ref_form2);


    });
    $('#dept_same_display$key_index').on('change',function(){
        if($(this).is(':checked')){ 
            $('#department_selector$key_index').css('display','block');
        }else{
            $('#department_selector$key_index').css('display','none');
        }
    });
    
    
    $('#check_condition_display$key_index').on('change',function(){
        var content = $('#condition_display_content$key_index');
        if($(this).is(':checked')){
            content.css('display','block');
        }else{
            content.css('display','none');
        }
    });
    
    $('#content_config_tab$key_index').on('change','#config_ezf_id$key_index',function(){
      var ezf_id = $(this).val();
      var key_index = '$key_index';
      var ref_form1 = $('#config_ref_form'+key_index).val();
      var ref_form2 = $('#config_left_ref_form'+key_index).val();
      var visit_form = $('#config_visit_ezf_id'+key_index).val();
      
      form_ref$key_index(ezf_id,visit_form,ref_form1);
      //form_leftref$key_index(ezf_id,ref_form1,ref_form1,ref_form2);
      fields$key_index(ezf_id,visit_form,ref_form1,ref_form2);
      field_pic$key_index(ezf_id,visit_form,ref_form1,ref_form2);
      field_bdate$key_index(ezf_id,visit_form,ref_form1,ref_form2);
    });
    
    $('#content_config_tab$key_index').on('change','#config_ref_form$key_index',function(){
        var key_index = '$key_index';
        var ref_form1 = $(this).val();
        var ezf_id = $('#config_ezf_id'+key_index).val();
        var ref_form2 = $('#config_left_ref_form$key_index').val();
        var visit_form = $('#config_visit_ezf_id'+key_index).val();

        form_ref$key_index(ezf_id,visit_form,ref_form1);
        form_leftref$key_index(ezf_id,visit_form,ref_form1,ref_form2);
        fields$key_index(ezf_id,visit_form,ref_form1,ref_form2);
        field_pic$key_index(ezf_id,visit_form,ref_form1,ref_form2);
        field_bdate$key_index(ezf_id,visit_form,ref_form1,ref_form2);
    });
    
    $('#content_config_tab$key_index').on('change','#config_left_ref_form$key_index',function(){
        var key_index = '$key_index';
        var ref_form2 = $(this).val();
        var ezf_id = $('#config_ezf_id'+key_index).val();
        var ref_form1 = $('#config_ref_form$key_index').val();
        var visit_form = $('#config_visit_ezf_id'+key_index).val();
        
        form_leftref$key_index(ezf_id,visit_form,ref_form1,ref_form2);
        fields$key_index(ezf_id,visit_form,ref_form1,ref_form2);
        field_pic$key_index(ezf_id,visit_form,ref_form1,ref_form2);
        field_bdate$key_index(ezf_id,visit_form,ref_form1,ref_form2);
    });
    
    function form_ref$key_index(ezf_id,visit_form,value_ref){ 
        var value = " . json_encode($value_ref) . ";
        var name = '" . $attrname_ref . "';
        if(value_ref){
            value=value_ref;
        }
        var value_merge=value;
        if(visit_form){
            if(value_merge)value_merge.push(visit_form);
            else value_merge = visit_form;
        }
        
        
        $.post('" . Url::to(['/thaihis/configs/get-form-ref2']) . "',{ ezf_id: ezf_id, multiple:1, name: name, value_ref: value,value_merge:value_merge ,id:'config_ref_form$key_index'}
          ).done(function(result){
             $('#ref_form_box$key_index').html(result);
             //$('#add-condition').attr('data-ezf_id',$('#config_ezf_id').val());
             //fields($('#ezf_target_id').val());
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    } 
    
    function form_leftref$key_index(ezf_id,visit_form,value_ref,value_ref2){ 
        var value = " . json_encode($value_leftref) . ";
        var name = '" . $attrname_leftref . "';
        var ezf_ref1 = " . json_encode($value_ref) . ";
        if(value_ref){
            ezf_ref1 = value_ref;
        }
        
        var value_merge=ezf_ref1;
        if(visit_form){
            if(value_merge)value_merge.push(visit_form);
            else value_merge = visit_form;
        }
        if(value_ref2){
            value = value_ref2;
            value_merge= $.merge(value_merge,value_ref2);
        }

        $.post('" . Url::to(['/thaihis/configs/get-form-ref2']) . "',{ ezf_id: ezf_id, multiple:1, name: name, value_ref: value ,value_merge:value_merge,id:'config_left_ref_form$key_index'}
          ).done(function(result){
             $('#left_ref_form_box$key_index').html(result);
             //$('#add-condition').attr('data-ezf_id',$('#config_ezf_id').val());
             //fields($('#ezf_target_id').val());
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    } 
    function fields$key_index(ezf_id,visit_form,value_ref,value_ref2){
        var value = " . json_encode($main_field_display) . ";
        var name = '" . $attrname_field_display . "';
        var ezf_ref1 = " . json_encode($value_ref) . ";
        var ezf_ref2 = " . json_encode($value_leftref) . ";
            
            
        if(value_ref){
            ezf_ref1 = value_ref;
        }
        if(value_ref2){
            ezf_ref2 = value_ref2;
        }

        var value_merge = ezf_ref1;
        if(visit_form){
            if(value_merge)value_merge.push(visit_form);
            else value_merge = visit_form;
        }
        
        if(ezf_ref2){
            value_merge= $.merge(value_merge,ezf_ref2);
        }
        
        $.post('" . Url::to(['/thaihis/configs/get-fields-forms']) . "',{ ezf_id: value_merge,main_ezf_id:ezf_id, multiple:1, name: name, value: value ,id:'config_fields_$key_index'}
          ).done(function(result){
             $('#ref_field_box_$key_index').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function field_pic$key_index(ezf_id,visit_form,value_ref,value_ref2){
        var value = '" . $main_field_pic . "';
        var name = '" . $attrname_field_pic . "';
        var ezf_ref1 = " . json_encode($value_ref) . ";
        var ezf_ref2 = " . json_encode($value_leftref) . ";
            
            
        if(value_ref){
            ezf_ref1 = value_ref;
        }
        if(value_ref2){
            ezf_ref2 = value_ref2;
        }
        
        var value_merge = ezf_ref1;
        if(visit_form){
            if(value_merge)value_merge.push(visit_form);
            else value_merge = visit_form;
        }
        if(ezf_ref2){
            value_merge= $.merge(value_merge,ezf_ref2);
        }
        
        $.post('" . Url::to(['/thaihis/patient-visit/get-fields-forms2']) . "',{ ezf_id:  value_merge ,main_ezf_id:ezf_id, multiple:0, name: name, value: value ,id:'config_field_pic_$key_index'}
          ).done(function(result){
             $('#ref_field_pic_$key_index').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    } 
    
    function field_bdate$key_index(ezf_id,visit_form,value_ref,value_ref2){
        var value = " . json_encode($main_field_bdate) . ";
        var name = " . json_encode($attrname_field_bdate) . ";
        var ezf_ref1 = " . json_encode($value_ref) . ";
        var ezf_ref2 = " . json_encode($value_leftref) . ";
            
            
        if(value_ref){
            ezf_ref1 = value_ref;
        }
        if(value_ref2){
            ezf_ref2 = value_ref2;
        }
        
        var value_merge = ezf_ref1;
        if(visit_form){
            if(value_merge)value_merge.push(visit_form);
            else value_merge = visit_form;
        }
        if(ezf_ref2){
            value_merge= $.merge(value_merge,ezf_ref2);
        }
        
        $.post('" . Url::to(['/thaihis/patient-visit/get-fields-forms2']) . "',{ ezf_id:  value_merge,main_ezf_id:ezf_id, multiple:0, name: name, value: value ,id:'config_field_bdate_$key_index'}
          ).done(function(result){
             $('#ref_field_bdate_$key_index').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    $('.tab_type_input').change(function(){
        var type = $(this).val();
        var key_index = $(this).attr('data-key_index');
        console.log(key_index);
        if(type == 1){
            $('#display_ezform'+key_index).css('display','block');
            $('#display_widget_content'+key_index).css('display','none');
            $('#display_content'+key_index).css('display','none');
            $('#display_ajax'+key_index).css('display','none');
        }else if(type == 2){
            $('#display_ezform'+key_index).css('display','none');
            $('#display_widget_content'+key_index).css('display','block');
            $('#display_content'+key_index).css('display','none');
            $('#display_ajax'+key_index).css('display','none');
        }else if(type == 3){
            $('#display_ezform'+key_index).css('display','none');
            $('#display_widget_content'+key_index).css('display','none');
            $('#display_content'+key_index).css('display','block');
            $('#display_ajax'+key_index).css('display','none');
        }else if(type == 4){
        $('#display_widget_content'+key_index).css('display','none');
            $('#display_ezform'+key_index).css('display','none');
            $('#display_content'+key_index).css('display','none');
            $('#display_ajax'+key_index).css('display','block');
        }
    });
    
    $(document).on('change','[id^=title_input]',function(){
        
        var key_index = $(this).attr('data-key_index');
        $('#tablist_config').find('#btn-tab'+key_index).html($(this).val());
    });
    
 $('#btn-add-subquery$key_index').on('click',function(){
        var key_index = getMilisecTime();
        onLoadSubquery$key_index(key_index,'addNew');
    });
    
    function onLoadSubquery$key_index(index,act){
        var selects = $('#i_select_amt$key_index').val();
        var key_index = '$key_index';
        var div_subquery = $('#display-subquery$key_index');
        var ezf_id = $('#config_ezf_id'+key_index).val();

        var url = '" . Url::to(['/thaihis/configs/add-new-subquery', 'subquery' => $subquery]) . "';
        $.get(url,{ezf_id:ezf_id,act:act,key_index:key_index,sub_index:index},function(result){
            div_subquery.append(result);
        });
    }
    
    $('#btn-add-variable$key_index').on('click',function(){
        var key_index = getMilisecTime();
        onLoadVariable$key_index(key_index,'addNew');
    });
    
    function onLoadVariable$key_index(index,act){
        var selects = $('#i_select_amt$key_index').val();
        var key_index = '$key_index';
        var div_select = $('#display-variable$key_index');
        var ezf_id = $('#config_ezf_id'+key_index).val();

        var url = '" . Url::to(['/thaihis/configs/add-new-variable', 'variables' => $variables]) . "';
        $.get(url,{ezf_id:ezf_id,act:act,key_index:key_index,sub_index:index},function(result){
            div_select.append(result);
        });
    }
    
    $('#btn-add-subcontent$key_index').on('click',function(){
        var key_index = getMilisecTime();
        onLoadSubcontent$key_index(key_index,'addNew');
    });
    
    function onLoadSubcontent$key_index(index,act){
        var selects = $('#i_select_amt$key_index').val();
        var key_index = '$key_index';
        var div_content = $('#display-subcontent$key_index');
        var ezf_id = $('#config_ezf_id'+key_index).val();

        var url = '" . Url::to(['/thaihis/configs/add-new-subcontent', 'subcontent' => $subcontent]) . "';
        $.get(url,{ezf_id:ezf_id,act:act,key_index:key_index,sub_index:index},function(result){
            div_content.append(result);
        });
    }

");
?>