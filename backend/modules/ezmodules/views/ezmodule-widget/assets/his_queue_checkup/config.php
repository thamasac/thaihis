<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzformWidget;
use kartik\select2\Select2;
use appxq\sdii\utils\SDUtility;
use yii\db\Query;
use backend\modules\ezforms2\classes\EzfQuery;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];
$value_ezf_id= ['1503589101005614900','1506694193013273800','1536726852029196700','1504537671028647300','1514016599071774100','1503378440057007100'];
$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);

$dataFields = (new Query())->select(['ezf_field_id as id', 'CONCAT(ezf_field_name," (",ezf_field_label,")") as name', 'ezf_id', 'ezf_field_type'])
    ->from('ezform_fields')
    ->where('(ezf_field_type <> 0 OR  ezf_field_name = \'id\')')
    ->andWhere(['ezf_id' => $value_ezf_id])->all();
$dataForm = [];
$dataFormDate = [];
$dataFormImg = [];
foreach ($dataFields as $vField) {
    $dataEzf = EzfQuery::getEzformById($vField['ezf_id']);
    $dataForm[$dataEzf['ezf_name']][$vField['id']] = $vField['name'];
    if ($vField['ezf_field_type'] == '63' || $vField['ezf_field_type'] == '64') {
        $dataFormDate[$dataEzf['ezf_name']][$vField['id']] = $vField['name'];
    }
    if ($vField['ezf_field_type'] == '71') {
        $dataFormImg[$dataEzf['ezf_name']][$vField['id']] = $vField['name'];
    }
}

?>


<!--config start-->


<div class="form-group row">
    <div class="modal-header" style="background-color:#CBCAC6;margin-bottom: 2%"><h4>Config Widget</h4></div>
    <?= Html::hiddenInput('options[reloadDiv]', isset($options['reloadDiv']) ? $options['reloadDiv'] : 'counter-list-' . SDUtility::getMillisecTime()); ?>
    <div class="col-md-4">
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
    <div class="col-md-4 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Title'), 'options[title]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[title]', (isset($options['title']) ? $options['title'] : Yii::t('ezform', 'Title')), ['class' => 'form-control']) ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?= EzformWidget::checkbox('options[radio_check]', (isset($options['radio_check']) ? $options['radio_check'] : ''), ['label' => 'Hide Radio']) ?>
    </div>
</div>

<div class="form-group row">
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


</div>

<div class="form-group row">
    <div class="modal-header" style="background-color:#CBCAC6;margin-bottom: 2%"><h4>Config Fields</h4></div>
    <div class="col-md-12">
        <?php
        $attrname_fields = 'options[fields]';
        $value_fields = isset($options['fields']) && is_array($options['fields']) ? $options['fields'] : [];
        ?>
        <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
        <div id="ref_field_box">
            <?php
            echo Select2::widget([
                'id' => 'select-field-' . SDUtility::getMillisecTime(),
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
    <div class="col-md-3">
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
    <div class="col-md-3 sdbox-col">
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
                'data' => $dataFormDate,
                'options' => ['placeholder' => Yii::t('ezform', 'Select field ...')],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]);
            ?>
        </div>
    </div>
    <!--</div>-->

    <!--<div class="form-group row">-->
    <!--<div class="col-md-4">-->
    <?php // echo EzformWidget::checkbox('options[check_dept]', isset($options['check_dept']) ? $options['check_dept'] : null, ['label' => 'Check Department', 'id' => 'check_dept']);  ?>
    <!--</div>-->
    <div class="col-md-3 sdbox-col check_dept">
        <?php
        $attrname_dept_field = 'options[dept_field]';
        $value_dept_field = isset($options['dept_field']) ? $options['dept_field'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Department Field'), $attrname_dept_field, ['class' => 'control-label']) ?>
        <div id="dept_field_box">
            <?php
            echo Select2::widget([
                'id' => 'select-field-dept-' . SDUtility::getMillisecTime(),
                'name' => $attrname_dept_field,
                'value' => $value_dept_field,
                'data' => $dataForm,
                'options' => ['placeholder' => Yii::t('ezform', 'Select field ...')],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]);
            ?>
        </div>
    </div>
    <div class="col-md-3 sdbox-col ">
        <?php
        $attrname_doc_field = 'options[doc_field]';
        $value_doc_field = isset($options['doc_field']) ? $options['doc_field'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Doctor Field'), $attrname_dept_field, ['class' => 'control-label']) ?>
        <div id="doc_field_box">
            <?php
            echo Select2::widget([
                'id' => 'select-field-doc-' . SDUtility::getMillisecTime(),
                'name' => $attrname_doc_field,
                'value' => $value_doc_field,
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
    <!--<div class="col-md-4">-->
    <?php // echo EzformWidget::checkbox('options[check_dept]', isset($options['check_dept']) ? $options['check_dept'] : null, ['label' => 'Check Department', 'id' => 'check_dept']);  ?>
    <!--</div>-->
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
</div>
<div class="form-group" style="margin-bottom: 5%;margin-top: 3%">
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

<div class="form-group row">
    <div class="modal-header" style="background-color:#CBCAC6;margin-bottom: 2%"><h4>Config Search Field</h4></div>
    <div class="col-md-6">
        <?php
        $attrname_fields_search_one = 'options[fields_search_one]';
        $value_fields_search_one = isset($options['fields_search_one']) ? $options['fields_search_one'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Fields Search one box'), $attrname_fields_search_one, ['class' => 'control-label']) ?>
        <div id="ref_field_search_one_box">
            <?php
            echo Select2::widget([
                'id' => 'select-search-one-' . SDUtility::getMillisecTime(),
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
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_fields_search_multi = 'options[fields_search_multi]';
        $value_fields_search_multi = isset($options['fields_search_multi'])? $options['fields_search_multi'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Fields Search multi box'), $attrname_fields_search_multi, ['class' => 'control-label']) ?>
        <div id="ref_field_search_multi_box">
            <?php
            echo Select2::widget([
                'id' => 'select-search-multi-' . SDUtility::getMillisecTime(),
                'name' => $attrname_fields_search_multi,
                'value' => $value_fields_search_multi,
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
    <div class="modal-header" style="background-color:#CBCAC6;margin-bottom: 2%"><h4>Config Condition Check</h4>
    </div>
    <div class="col-md-12">
        <div class="btn btn-success pull-left" id="btn-add-condition">Add Condition</div>
        <div class="clearfix"></div>
        <div class="form-group"> <?= EzformWidget::checkbox('', '', ['label' => 'Condition Date', 'id' => 'condition-date']) ?></div>
    </div>
    <?php
    $itemCondition = [
        '>' => 'มากกว่า',
        '<' => 'น้อยกว่า',
        '>=' => 'มากกว่าหรือเท่ากับ',
        '<=' => 'น้อยกว่าหรือเท่ากับ',
        '=' => 'เท่ากับ',
        '!=' => 'ไม่เท่ากับ'
    ];
    ?>
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
        <div class="col-md-2 sdbox-col">
            <label>Value</label>
        </div>

        <div class="col-md-2 sdbox-col">

        </div>
    </div>
    <div id="div-condition">
        <?php
        $value_condition = isset($options['condition']) ? $options['condition'] : [];
        if (isset($value_condition) && is_array($value_condition) && !empty($value_condition)) {
            $dataFields = (new Query())->select(['ezf_field_id as id', 'CONCAT(ezf_field_name," (",ezf_field_label,")") as name', 'ezf_id', 'ezf_field_type'])
                ->from('ezform_fields')
                ->where('(ezf_field_type <> 0 OR  ezf_field_name = \'id\')')
                ->andWhere(['ezf_id' => $value_ezf_id])->all();
            $dataForm = [];
            $dataFormDate = [];
            foreach ($dataFields as $vField) {
                $dataEzf = EzfQuery::getEzformById($vField['ezf_id']);
                $dataForm[$dataEzf['ezf_name']][$vField['id']] = $vField['name'];
                if ($vField['ezf_field_type'] == '63' || $vField['ezf_field_type'] == '64') {
                    $dataFormDate[$dataEzf['ezf_name']][$vField['id']] = $vField['name'];
                }
            }
//            \appxq\sdii\utils\VarDumper::dump($value_condition);

            foreach ($value_condition as $kCon => $vCon) {
                ?>
                <div class="col-md-12 divMainCondition" style="margin-top:2%">

                    <div class="col-md-2 ">
                        <?php
                        echo Select2::widget([
                            'id' => 'select-value-con-' . SDUtility::getMillisecTime(),
                            'name' => 'options[condition][' . $kCon . '][condition]',
                            'value' => isset($vCon['condition']) ? $vCon['condition'] : '',
                            'data' => ['and' => 'AND' , 'or' => 'OR'],
                            'hideSearch' => true,
//                                'options' => ['placeholder' => Yii::t('ezform', 'Select condition ...')],
//                                'pluginOptions' => [
//                                    'allowClear' => true,
//
//                                ]
                        ]);
                        //        echo Html::textInput('options[condition][' . $id . '][con]', $condition_con, ['class' => 'form-control']);
                        ?>
                    </div>
                    <div class="col-md-4 sdbox-col">
                        <?php
                        echo Select2::widget([
                            'id' => 'select-value-field-' . SDUtility::getMillisecTime(),
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
                        echo Select2::widget([
                            'id' => 'select-value-ope-' . SDUtility::getMillisecTime(),
                            'name' => 'options[condition][' . $kCon . '][operator]',
                            'value' => isset($vCon['operator']) ? $vCon['operator'] : '',
                            'data' => $itemCondition,
                            'hideSearch' => true,
                            'options' => ['placeholder' => Yii::t('ezform', 'Select condition ...')],
                            'pluginOptions' => [
                                'allowClear' => true,

                            ]
                        ]);
                        //        echo Html::textInput('options[condition][' . $id . '][con]', $condition_con, ['class' => 'form-control']);
                        ?>
                    </div>
                    <div class="col-md-2 sdbox-col">
                        <?php
                        if ($vCon['value'] != 'NOW()') {
                            echo Html::textInput('options[condition][' . $kCon . '][value]', isset($vCon['value']) ? $vCon['value'] : '', ['class' => 'form-control']);
                        } else {
                            echo Html::hiddenInput('options[condition][' . $kCon . '][value]', 'NOW()');
                            echo Html::textInput('', 'วันนี้', ['class' => 'form-control', 'disabled' => true]);
                        }

                        ?>
                    </div>

                    <div class="col-md-2 sdbox-col">
                        <?php echo Html::tag('div', '<i class="glyphicon glyphicon-remove"></i>', ['class' => 'btn btn-danger btn-remove-condition']) ?>
                    </div>

                </div>
                <?php
            }
        }
        ?>
    </div>
</div>
<div class="form-group row">
    <div class="modal-header" style="background-color:#CBCAC6;margin-bottom: 2%"><h4>Config group by field</h4>
    </div>
    <div class="col-md-12">
        <?php
        $attrname_group_by = 'options[group_by]';
        $value_group_by = isset($options['group_by']) ? $options['group_by'] : '';
        echo Html::label(Yii::t('ezform', 'Group by field'), 'options[template_content]', ['class' => 'control-label'])
        ?>
    </div>
    <div id="div_group_by" class="col-md-5">
        <?php
        echo Select2::widget([
            'id' => 'select-field-groupby-' . SDUtility::getMillisecTime(),
            'name' => $attrname_group_by,
            'value' => $value_group_by,
            'data' => $dataForm,
            'options' => ['placeholder' => Yii::t('ezform', 'Select field ...')],
            'pluginOptions' => [
                'allowClear' => true,
            ]
        ]);
        ?>
    </div>
    <div class="clearfix"></div>
</div>

<div class="form-group row">
    <div class="modal-header" style="background-color:#CBCAC6;margin-bottom: 2%"><h4>Config Template</h4></div>
    <div class="col-md-12 ">
        <?= Html::label(Yii::t('ezform', 'Template Content'), 'options[template_content]', ['class' => 'control-label']) ?>
        <?= Html::textarea('options[template_content]', isset($options['template_content']) ? $options['template_content'] : '', ['class' => 'form-control', 'row' => 3]) ?>
    </div>
</div>

<div class="form-group row">
    <div class="modal-header" style="background-color:#CBCAC6;margin-bottom: 2%"><h4>Config Event Click</h4></div>
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
    <div class="col-md-3 sdbox-col divElement">

        <?= Html::label(Yii::t('ezform', 'Render To (Element ID)'), 'optioms[element_id]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[element_id]', isset($options['element_id']) ? $options['element_id'] : '', ['class' => 'form-control']) ?>
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

        $dataFields = (new \yii\db\Query())->select(['ezf_field_id as id', 'CONCAT(ezf_field_name," (",ezf_field_label,")") as name', 'ezf_id'])
            ->from('ezform_fields')
            ->where('(ezf_field_type <> 0 OR  ezf_field_name = \'id\')')
            ->andWhere(['ezf_id' => $value_ezf_id])->all();
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
                        <?=Html::checkbox('options[param][' . $k . '][param_active]',isset( $v['param_active']) && $v['param_active'] == 1 ? 1 : 0,['class'=>'check_box_active']);?>
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
            <?php }
        } ?>
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

    $('#div_param_box').on('click','.check_box_active',function(){
        $('#div_param_box').find('.check_box_active').not(this).prop('checked', false);

    });

    function getFormParam(param_name,param_value){
        const main_ezf_id = '1503589101005614900';
        const value_ref = ['1506694193013273800','1536726852029196700','1504537671028647300','1514016599071774100','1503378440057007100'];
        $.post('<?=Url::to(['/queue/default/get-form-param']) ?>',{main_ezf_id:main_ezf_id,ezf_id: value_ref,param_name:param_name,param_value:param_value}
    ).done(function(result){
            $('#div_param_box').append(result);
        }).fail(function(){
            <?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>
            console.log('server error');
        });
    }

    $('.btn-add-param').click(function(){
        getFormParam('','');
    });


    $('#div_param_box').on('click', '.btn-remove-param', function(){
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

    $('#btn-add-condition').click(function(){
        getFormCondition('','');
    });

    $('#div-condition').on('click', '.btn-remove-condition', function(){
        $(this).parents('.divMainCondition').remove();
    });

    function getFormCondition(param_name,param_value){
        const main_ezf_id = '1503589101005614900';
        const value_ref = ['1506694193013273800','1536726852029196700','1504537671028647300','1514016599071774100','1503378440057007100'];
        var condition_date = [];
        if($('#condition-date').is(':checked')){
            condition_date = [63,64];
        }
        $.post('<?= Url::to(['/queue/default/get-form-condition']) ?>',{main_ezf_id:main_ezf_id,ezf_id: value_ref,param_name:param_name,param_value:param_value,type:condition_date}
    ).done(function(result){
            $('#div-condition').append(result);
        }).fail(function(){
            <?=\appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>
            console.log('server error');
        });
    }
</script>

<?php \richardfan\widget\JSRegister::end(); ?>
