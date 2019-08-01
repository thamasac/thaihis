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
    <div class="modal-header config-widget-header"><h4 class="modal-title" id="itemModalLabel">Config Form</h4></div>
    <div class="col-md-12" style="margin-top:5px">
        <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
            <li role="presentation" class="active"><a href="#general-tab" aria-controls="home" role="tab"
                                                      data-toggle="tab"><?= Yii::t('ezmodule', 'General Settings') ?></a>
            </li>
            <li role="presentation"><a href="#advanced-tab" aria-controls="profile" role="tab"
                                       data-toggle="tab"><?= Yii::t('ezmodule', 'Advanced Settings') ?></a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="general-tab">
                <div class="form-group row">
                    <div class="col-md-3">
                        <?= Html::label(Yii::t('ezform', 'Icon'), 'options[radio_check]', ['class' => 'control-label']) ?>
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
                        <?=
                        Html::textInput('options[position][width]', isset($options['position']['width']) ? $options['position']['width'] : '350', ['class' => 'form-control', 'type' => 'number']);
                        ?>
                    </div>
                    <!--position end-->
                    <div class="col-md-3">
                        <?= EzformWidget::checkbox('options[radio_check]', isset($options['radio_check']) ? $options['radio_check'] : true, ['label' => 'Hide Checkbox search']) ?>
                    </div>
                    <div class="col-md-9">
                        <?= EzformWidget::checkbox('options[btn_report]', isset($options['btn_report']) ? $options['btn_report'] : false, ['label' => 'Show Button Report']) ?>
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
                    <div class="col-md-12">
                        <?php
                        $attrname_fields = 'options[fields]';
                        $value_fields = isset($options['fields']) && is_array($options['fields']) ? \appxq\sdii\utils\SDUtility::array2String($options['fields']) : '{}';
                        ?>
                        <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
                        <div id="ref_field_box">

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

                        </div>
                    </div>
                    <div class="col-md-6 sdbox-col">
                        <?php
                        $attrname_bdate_field = 'options[bdate_field]';
                        $value_bdate_field = isset($options['bdate_field']) ? $options['bdate_field'] : '';
                        ?>
                        <?= Html::label(Yii::t('ezform', 'Birthdate Field'), $attrname_bdate_field, ['class' => 'control-label']) ?>
                        <div id="bdate_field_box">

                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6 check_dept">
                        <?php
                        $attrname_dept_field = 'options[dept_field]';
                        $value_dept_field = isset($options['dept_field']) ? $options['dept_field'] : '';
                        ?>
                        <?= Html::label(Yii::t('ezform', 'Department Field'), $attrname_dept_field, ['class' => 'control-label']) ?>
                        <div id="dept_field_box">

                        </div>
                    </div>
                    <div class="col-md-6 sdbox-col ">
                        <?php
                        $attrname_doc_field = 'options[doc_field]';
                        $value_doc_field = isset($options['doc_field']) ? $options['doc_field'] : '';
                        ?>
                        <?= Html::label(Yii::t('ezform', 'Doctor Field'), $attrname_dept_field, ['class' => 'control-label']) ?>
                        <div id="doc_field_box">

                        </div>
                    </div>
                    <div class="col-md-12">
                        <?= EzformWidget::checkbox('options[split_permission]', isset($options['split_permission']) ? $options['split_permission'] : '', ['label' => Yii::t('queue', 'Split permissions by doctor field')]) ?>
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
                        $value_fields_search_one = isset($options['fields_search_one']) && is_array($options['fields_search_one']) ? \appxq\sdii\utils\SDUtility::array2String($options['fields_search_one']) : '{}';
                        ?>
                        <?= Html::label(Yii::t('ezform', 'Search box'), $attrname_fields_search_one, ['class' => 'control-label']) ?>
                        <div id="ref_field_search_one_box">

                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <?php
                        $attrname_fields_search_multi = 'options[fields_search_multi]';
                        $value_fields_search_multi = isset($options['fields_search_multi']) && is_array($options['fields_search_multi']) ? \appxq\sdii\utils\SDUtility::array2String($options['fields_search_multi']) : '{}';
                        ?>
                        <?= Html::label(Yii::t('ezform', 'Advanced Search By Field'), $attrname_fields_search_multi, ['class' => 'control-label']) ?>
                        <div id="ref_field_search_multi_box">

                        </div>
                    </div>
                </div>

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
                            <label>Value</label>
                        </div>
                    </div>
                    <div id="div-condition">
                        <?php
                        $value_condition = isset($options['condition']) ? $options['condition'] : [];
                        if (isset($value_condition) && is_array($value_condition) && !empty($value_condition)) {
                            $value_ezf_ref = SDUtility::string2Array($value_ref);
                            $value_ezf_ref[] = $value_ezf_id;
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
                    <div id="div_group_by" class="col-md-5">

                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <?php
                        $attrname_order_by_field = 'options[order_by][field]';
                        $value_order_by_field = isset($options['order_by']['field']) ? $options['order_by']['field'] : '';
                        echo Html::label(Yii::t('ezform', 'Order by field'), 'options[order_by]', ['class' => 'control-label'])
                        ?>
                    </div>
                    <div id="div_order_by" class="col-md-6">

                    </div>
                    <div class="col-md-3 sdbox-col">
                        <?php
                        $attrname_order_by_type = 'options[order_by][type]';
                        $value_order_by_type = isset($options['order_by']['type']) ? $options['order_by']['type'] : '';
//                        echo Html::label(Yii::t('ezform', 'Order by field'), 'options[order_by]', ['class' => 'control-label'])
                        echo \kartik\select2\Select2::widget([
                            'id' => 'select-order-type',
                            'name' => 'options[order_by][type]',
                            'value' => $value_order_by_type,
                            'data' => ['3'=>'DESC','4'=>'ASC'],
//                            'options' => ['placeholder' => Yii::t('ezform', 'Select Type ...')],
//                            'pluginOptions' => [
//                                'allowClear' => true,
//                            ]
                        ]);
                        ?>
                    </div>
                    <div class="clearfix"></div>
                </div>



                <div class="form-group row">
                    <div class="modal-header"><h4>Config Event Click</h4></div>
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
                        <div class="col-md-2">

                        </div>

                    </div>
                    <div id="div_param_box">
                        <?php
                        $ezf_id = $value_ref != '{}' ? appxq\sdii\utils\SDUtility::string2Array($value_ref) : [];
                        $ezf_id[] = $value_ezf_id;
                        $ezf_id = array_merge($ezf_id, isset($options['refform_level2']) ? $options['refform_level2'] : [], isset($options['refform']) ? $options['refform'] : []);
                        $dataFields = (new \yii\db\Query())->select(['ezf_field_id as id', 'CONCAT(ezf_field_name," (",ezf_field_label,")") as name', 'ezf_id'])
                            ->from('ezform_fields')
                            ->where('(ezf_field_type <> 0 OR  ezf_field_name = \'id\')')
                            ->andWhere(['ezf_id' => $ezf_id])->all();
                        $dataForm = [];
                        foreach ($dataFields as $vField) {
                            $dataEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformById($vField['ezf_id']);
                            $dataForm[$dataEzf['ezf_name']][$vField['id']] = $vField['name'];
                        }
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
            </div>
        </div>
    </div>
    <!-- Custom Label hidden -->
    <div class="form-group hidden" style="margin-bottom: 5%;margin-top: 3%">
        <div class="row" style="margin-bottom: 2%">
            <div class="col-md-4">
                <div class="header-items-add btn btn-success"><i
                            class="glyphicon glyphicon-plus"></i> <?= Yii::t('ezmodule', 'Custom Label') ?></div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-3"><label><?= Yii::t('ezform', 'Varname') ?></label></div>
            <div class="col-md-6 sdbox-col"><label><?= Yii::t('ezform', 'Label') ?></label></div>
            <div class="col-md-2"></div>
        </div>
        <div id="header-item-box">
            <?php
            if (isset($options['custom_label']) && is_array($options['custom_label']) && !empty($options['custom_label'])) {
                foreach ($options['custom_label'] as $key_header => $value_header) {
                    ?>
                    <div id="<?= $key_header ?>" class="row" style="margin-bottom: 10px;">
                        <div class="col-md-3"><input type="text" class="form-control varname-input"
                                                     name="options[custom_label][<?= $key_header ?>][varname]"
                                                     value="<?= isset($value_header['varname']) ? $value_header['varname'] : '' ?>">
                        </div>
                        <div class="col-md-6 sdbox-col"><input type="text" class="form-control label-input"
                                                               name="options[custom_label][<?= $key_header ?>][label]"
                                                               value="<?= isset($value_header['label']) ? $value_header['label'] : '' ?>">
                        </div>
                        <div class="col-md-2 sdbox-col"><a href="#" class="header-items-del btn btn-danger"><i
                                        class="glyphicon glyphicon-remove"></i></a></div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>

    </div>
    <!--end Custom Label hidden -->

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
    
//    if('" . $value_dept_field . "' == ''){
//        $('.dept_field').hide();
//    }
   
    
    
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
          fields('',$('#config_ezf_id').val());
          fields_search_one('',$('#config_ezf_id').val());
          fields_search_multi('',$('#config_ezf_id').val());
          pic_fields('',$('#config_ezf_id').val());
          bdate_fields('',$('#config_ezf_id').val());
          dept_fields('',$('#config_ezf_id').val());
          group_by('',$('#config_ezf_id').val());
          order_by('',$('#config_ezf_id').val());
          doc_fields('',$('#config_ezf_id').val());
          if($('#config_ezf_id').val() == ''){
            $('#div-condition').html('');
            $('#div_param_box').html('');
            $('#header-item-box').html('');
          }
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
              fields(val,$('#config_ezf_id').val());
              fields_search_one(val,$('#config_ezf_id').val());
              fields_search_multi(val,$('#config_ezf_id').val());
              pic_fields(val,$('#config_ezf_id').val());
              bdate_fields(val,$('#config_ezf_id').val());
              dept_fields(val,$('#config_ezf_id').val());
              group_by(val,$('#config_ezf_id').val());
              order_by(val,$('#config_ezf_id').val());
              doc_fields(val,$('#config_ezf_id').val());
              if($('#ref_field_box_v2').has('.form-ref').length <= 0 || val.length <= 0){
                $('#div-condition').html('');
                $('#div_param_box').html('');
                $('#header-item-box').html('');
              }
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
              fields(val,$('#config_ezf_id').val());
              fields_search_one(val,$('#config_ezf_id').val());
              fields_search_multi(val,$('#config_ezf_id').val());
              pic_fields(val,$('#config_ezf_id').val());
              bdate_fields(val,$('#config_ezf_id').val());
              dept_fields(val,$('#config_ezf_id').val());
              group_by(val,$('#config_ezf_id').val());
              order_by(val,$('#config_ezf_id').val());
              doc_fields(val,$('#config_ezf_id').val());
              if($('#ref_field_box_v2').has('.form-ref').length <= 0 || val.length <= 0){
                $('#div_param_box').html('');
                $('#header-item-box').html('');
                $('#div-condition').html('');
              }
              delayID = null;
         }, 100);
    });
    
    
    $('#config_ezf_id').change(function(){
        $('#ref_field_box_v2').html('');
        fields('0',$(this).val());
        fields_search_one('0',$(this).val());
        fields_search_multi('0',$(this).val());
        pic_fields('0',$(this).val());
        bdate_fields('0',$(this).val());
        dept_fields('0',$(this).val());
        group_by('0',$(this).val());
        order_by('0',$(this).val());
        doc_fields('0',$(this).val());
        if($(this).val() == ''){
          $('#div_param_box').html('');
          $('#header-item-box').html('');
          $('#div-condition').html('');
        }
    });

   
    
   
    
    function fields(ezf_id,main_ezf_id){
        var value = " . $value_fields . ";
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
       
        $('#ref_field_box').html('กำลังรีโหลดข้อมูล');
        $.post('" . Url::to(['/queue/default/get-fields-forms']) . "',{ main_ezf_id:main_ezf_id,ezf_id: value_ref, multiple:1, name: '{$attrname_fields}', value: value ,id:'config_fields'}
          ).done(function(result){
             $('#ref_field_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    //select field trigger to search box
    $('#ref_field_box').on('select2:select', '#config_fields', function(e){
        $('#config_fields_search_one').val($(this).val()).trigger('change');
    });
    $('#ref_field_box').on('select2:unselect', '#config_fields', function(e){
        $('#config_fields_search_one').val($(this).val()).trigger('change');
    });
    
    function fields_search_one(ezf_id,main_ezf_id){
        var value = " . $value_fields_search_one . ";
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
       
        $('#ref_field_search_one_box').html('กำลังรีโหลดข้อมูล');
        $.post('" . Url::to(['/queue/default/get-fields-forms']) . "',{ main_ezf_id:main_ezf_id,ezf_id: value_ref, multiple:1, name: '{$attrname_fields_search_one}', value: value ,id:'config_fields_search_one'}
          ).done(function(result){
             $('#ref_field_search_one_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function fields_search_multi(ezf_id,main_ezf_id){
        var value = " . $value_fields_search_multi . ";
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
       
        $('#ref_field_search_multi_box').html('กำลังรีโหลดข้อมูล');
        $.post('" . Url::to(['/queue/default/get-fields-forms']) . "',{ main_ezf_id:main_ezf_id,ezf_id: value_ref, multiple:1, name: '{$attrname_fields_search_multi}', value: value ,id:'config_fields_search_multi'}
          ).done(function(result){
             $('#ref_field_search_multi_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }

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
    
    
                 
    
    function pic_fields(ezf_id,main_ezf_id){
        var value = '{$value_image_field}';
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
       
        $('#pic_field_box').html('กำลังรีโหลดข้อมูล');
        $.post('" . Url::to(['/queue/default/get-fields-forms']) . "',{ main_ezf_id:main_ezf_id,ezf_id: value_ref,ezf_field_type:[71], multiple:0, name: '{$attrname_image_field}', value: value ,id:'config_pic_fields'}
          ).done(function(result){
             $('#pic_field_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function bdate_fields(ezf_id,main_ezf_id){
        var value = '{$value_bdate_field}';
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
       
        $('#bdate_field_box').html('กำลังรีโหลดข้อมูล');
        $.post('" . Url::to(['/queue/default/get-fields-forms']) . "',{ main_ezf_id:main_ezf_id,ezf_id: value_ref,ezf_field_type:[], multiple:0, name: '{$attrname_bdate_field}', value: value ,id:'config_bdate_fields'}
          ).done(function(result){
             $('#bdate_field_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
   
    
    function dept_fields(ezf_id,main_ezf_id){
        var value = '{$value_dept_field}';
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
       
        $('#dept_field_box').html('กำลังรีโหลดข้อมูล');
        $.post('" . Url::to(['/queue/default/get-fields-forms']) . "',{ main_ezf_id:main_ezf_id,ezf_id: value_ref, multiple:0, name: '{$attrname_dept_field}', value: value ,id:'config_dept_fields'}
          ).done(function(result){
             $('#dept_field_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function doc_fields(ezf_id,main_ezf_id){
        var value = '{$value_doc_field}';
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
       
        $('#doc_field_box').html('กำลังรีโหลดข้อมูล');
        $.post('" . Url::to(['/queue/default/get-fields-forms']) . "',{ main_ezf_id:main_ezf_id,ezf_id: value_ref, multiple:0, name: '{$attrname_doc_field}', value: value ,id:'config_doc_fields'}
          ).done(function(result){
             $('#doc_field_box').html(result);
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
    
    function order_by(ezf_id,main_ezf_id){
        var value = '{$value_order_by_field}';
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
       
        $('#div_order_by').html('กำลังรีโหลดข้อมูล');
        $.post('" . Url::to(['/queue/default/get-fields-forms']) . "',{ main_ezf_id:main_ezf_id,ezf_id: value_ref, multiple:0, name: '{$attrname_order_by_field}', value: value ,id:'config_order_by',date_system:1}
          ).done(function(result){
             $('#div_order_by').html(result);
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
        $('#$modalId .modal-content').html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
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