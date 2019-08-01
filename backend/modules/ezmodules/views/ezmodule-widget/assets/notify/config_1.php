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
?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>
<div class="form-group row">
    <div class="col-md-6">
        <?= Html::label(Yii::t('ezform', 'Title'), 'options[main_title]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[main_title]', (isset($options['main_title']) && $options['main_title'] != '' ? $options['main_title'] : Yii::t('ezform', 'Notification Center ')), ['class' => 'form-control']) ?>
    </div>

</div>
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#menu-my">All My Notifications</a></li>
    <li><a data-toggle="tab" href="#menu-member">All Notifications of all member</a></li>
</ul>


<div class="tab-content">
    <div id="menu-my" class="tab-pane fade in active" >
        <br>
        <div class="form-group row">
            <div class="col-md-6">
                <?= Html::label(Yii::t('ezform', 'Tab Title'), 'options[title_my]', ['class' => 'control-label']) ?>
                <?= Html::textInput('options[title_my]', (isset($options['title_my']) && $options['title_my'] != '' ? $options['title_my'] : Yii::t('ezform', 'All My Notifications')), ['class' => 'form-control']) ?>
            </div>
            <div class="col-md-6 sdbox-col" >
                <?php // echo Html::label(Yii::t('ezform', 'Column'), 'options[column_my]', ['class' => 'control-label']) ?>
                <?php // echo Html::textInput('options[column_my]', (isset($options['column_my']) ? $options['column_my'] : '2'), ['class' => 'form-control', 'type' => 'number']) ?>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-6 ">
                <?php
                $attrname_ezf_id_my = 'options[ezf_id_my]';
                $value_ezf_id_my = isset($options['ezf_id_my']) ? $options['ezf_id_my'] : '';
                ?>
                <?= Html::label(Yii::t('ezform', 'Form'), $attrname_ezf_id_my, ['class' => 'control-label']) ?>
                <?php
                echo kartik\select2\Select2::widget([
                    'name' => $attrname_ezf_id_my,
                    'value' => $value_ezf_id_my,
                    'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_ezf_id_my'],
                    'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-6 sdbox-col">
                <?php
                $attrname_fields_my = 'options[fields_my]';
                $value_fields_my = isset($options['fields_my']) && is_array($options['fields_my']) ? \appxq\sdii\utils\SDUtility::array2String($options['fields_my']) : '{}';
                ?>
                <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields_my, ['class' => 'control-label']) ?>
                <div id="ref_field_box_my">

                </div>
            </div>

        </div>

        <div class="form-group row">
            <!--            <div class="col-md-6">
            <?php
//                $attrname_fields_search_my = 'options[fields_search_my]';
//                $value_fields_search_my = isset($options['fields_search_my']) && is_array($options['fields_search_my']) ? \appxq\sdii\utils\SDUtility::array2String($options['fields_search_my']) : '{}';
//                
            ?>
            <?php // echo Html::label(Yii::t('ezform', 'Fields Search'), $attrname_fields_search_my, ['class' => 'control-label'])  ?>
                            <div id="fields_search_box_my">
            
                            </div>
                        </div>-->

            <div class="col-md-2">
                <?= Html::label(Yii::t('ezform', 'Page Size'), 'options[page_size_my]', ['class' => 'control-label']) ?>
                <?= Html::textInput('options[page_size_my]', (isset($options['page_size_my']) ? $options['page_size_my'] : Yii::t('ezform', 'Page Size')), ['class' => 'form-control', 'type' => 'number']) ?>
            </div>
            <div class="col-md-2 sdbox-col"></div>
            <div class="col-md-10 sdbox-col">
                <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[action]', (isset($options['action']) ? $options['action'] : 0), ['label' => 'Action']) ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="form-group">
            <?php // Html::label(Yii::t('ezform', 'Template Content'), 'options[template_content_my]', ['class' => 'control-label']) ?>
            <?php // Html::textarea('options[template_content_my]', isset($options['template_content_my']) ? $options['template_content_my'] : '', ['class' => 'form-control', 'row' => 3]) ?>
        </div>

        <div class="form-group">
            <?php // Html::label(Yii::t('ezform', 'Template Box'), 'options[template_box_my]', ['class' => 'control-label']) ?>
            <?php // Html::textarea('options[template_box_my]', isset($options['template_box_my']) ? $options['template_box_my'] : '', ['class' => 'form-control', 'row' => 3]) ?>
        </div>



    </div>

    <div id="menu-member" class="tab-pane fade" >
        <br>
        <div class="form-group row">
            <div class="col-md-6">
                <?= Html::label(Yii::t('ezform', 'Tab Title'), 'options[title_mem]', ['class' => 'control-label']) ?>
                <?= Html::textInput('options[title_mem]', (isset($options['title_mem']) && $options['title_mem'] != '' ? $options['title_mem'] : Yii::t('ezform', 'All Notifications of all member')), ['class' => 'form-control']) ?>
            </div>
            <div class="col-md-6 sdbox-col" >
                <?php // Html::label(Yii::t('ezform', 'Column'), 'options[column_mem]', ['class' => 'control-label']) ?>
                <?php // Html::textInput('options[column_mem]', (isset($options['column_mem']) ? $options['column_mem'] : '2'), ['class' => 'form-control', 'type' => 'number']) ?>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-6 ">
                <?php
                $attrname_ezf_id_mem = 'options[ezf_id_mem]';
                $value_ezf_id_mem = isset($options['ezf_id_mem']) ? $options['ezf_id_mem'] : '';
                ?>
                <?= Html::label(Yii::t('ezform', 'Form'), $attrname_ezf_id_mem, ['class' => 'control-label']) ?>
                <?php
                echo kartik\select2\Select2::widget([
                    'name' => $attrname_ezf_id_mem,
                    'value' => $value_ezf_id_mem,
                    'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_ezf_id_mem'],
                    'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-6 sdbox-col">
                <?php
                $attrname_fields_mem = 'options[fields_mem]';
                $value_fields_mem = isset($options['fields_mem']) && is_array($options['fields_mem']) ? \appxq\sdii\utils\SDUtility::array2String($options['fields_mem']) : '{}';
                ?>
                <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields_mem, ['class' => 'control-label']) ?>
                <div id="ref_field_box_mem">

                </div>
            </div>
        </div>

        <div class="form-group row">
            <!--            <div class="col-md-6">
            <?php
//                $attrname_search_fields_mem = 'options[fields_search_mem]';
//                $value_search_fields_mem = isset($options['fields_search_mem']) && is_array($options['fields_search_mem']) ? \appxq\sdii\utils\SDUtility::array2String($options['fields_search_mem']) : '{}';
//                
            ?>
            <?php // echo Html::label(Yii::t('ezform', 'Fields'), $attrname_search_fields_mem, ['class' => 'control-label'])  ?>
                            <div id="search_box_my">
            
                            </div>
                        </div>-->

            <div class="col-md-2">
                <?= Html::label(Yii::t('ezform', 'Page Size'), 'options[page_size_mem]', ['class' => 'control-label']) ?>
                <?= Html::textInput('options[page_size_mem]', (isset($options['page_size_mem']) ? $options['page_size_mem'] : Yii::t('ezform', 'Page Size')), ['class' => 'form-control', 'type' => 'number']) ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="form-group">
            <?php //  Html::label(Yii::t('ezform', 'Template Content'), 'options[template_content_my]', ['class' => 'control-label']) ?>
            <?php // Html::textarea('options[template_content_my]', isset($options['template_content_my']) ? $options['template_content_my'] : '', ['class' => 'form-control', 'row' => 3]) ?>
        </div>

        <div class="form-group">
            <?php // Html::label(Yii::t('ezform', 'Template Box'), 'options[template_box_my]', ['class' => 'control-label']) ?>
            <?php // Html::textarea('options[template_box_my]', isset($options['template_box_my']) ? $options['template_box_my'] : '', ['class' => 'form-control', 'row' => 3]) ?>
        </div>


    </div>
</div>

<?php
$this->registerJS("
    fields($('#config_ezf_id_my').val()," . $value_fields_my . ",'ref_field_box_my','config_fields_my','{$attrname_fields_my}');
//  
    fields($('#config_ezf_id_mem').val()," . $value_fields_mem . ",'ref_field_box_mem','config_fields_mem','{$attrname_fields_mem}');
//  
    $('#config_ezf_id_my').on('change',function(){
        var ezf_id = $(this).val();
        fields(ezf_id," . $value_fields_my . ",'ref_field_box_my','config_fields_my','{$attrname_fields_my}');
//       
    });
    
    $('#config_ezf_id_mem').on('change',function(){
        var ezf_id = $(this).val();
        fields(ezf_id," . $value_fields_mem . ",'ref_field_box_mem','config_fields_mem','{$attrname_fields_mem}');
//       
    });
    
    function fields(ezf_id,value,div,id,name){
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name:name, value: value ,id:id}
          ).done(function(result){
             $('#'+div).html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function search_fields(ezf_id,value,div,id,name){
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name:name, value: value ,id:id}
          ).done(function(result){
             $('#'+div).html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
");
?>