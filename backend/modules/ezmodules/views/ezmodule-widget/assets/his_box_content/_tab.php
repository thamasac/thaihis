<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

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
$key_index = isset($key_index_tab) ? $key_index_tab : '0';
$tab_item = ['1' => 'Ezform', '2' => 'Ezwidget', '3' => 'HTML Content', '4' => 'Ajax Request'];
$tab_keys = [];
$attrname_field_display = [];
$main_field_display = [];
$attrname_field_pic = [];
$main_field_pic = [];
?>

<?php
if (isset($tabs) && is_array($tabs)):

    foreach ($tabs as $key => $val):
        $display = "display:none;";
        $type_ezform = 'display:none;';
        $type_widget = 'display:none;';
        $type_content = 'display:none;';
        $type_ajax = 'display:none;';
        if ($val['tab_type'] == 2) {
            $type_widget = 'display:block;';
        } else if ($val['tab_type'] == 3) {
            $type_content = 'display:block;';
        } else if ($val['tab_type'] == 4) {
            $type_ajax = 'display:block;';
        } else {
            $type_ezform = 'display:block;';
        }
        $tab_keys[] = $key;
        if ($key == '0')
            $display = "display:block;";
        ?>

        <div  id="content_config_tab<?= $key ?>" style="<?= $display ?>">
            <div class="form-group row">
                <div class="col-md-4">
                    <?= Html::label(Yii::t('ezform', 'Tab Title'), 'tab_title') ?>
                    <?= Html::textInput('options[tabs][' . $key . '][tab_title]', $val['tab_title'], ['class' => 'form-control', 'id' => 'title_input' . $key, 'data-key_index' => $key]) ?>
                </div>
                <div class="col-md-4 sdbox-col">
                    <?= Html::label(Yii::t('ezform', 'Tab Type'), 'tab_type') ?>
                    <?= Html::dropDownList('options[tabs][' . $key . '][tab_type]', $val['tab_type'], $tab_item, ['class' => 'form-control tab_type_input', 'id' => 'tab_type_' . $key, 'data-key_index' => $key]) ?>
                </div>
                <div class="col-md-4 sdbox-col" >
                    <?= Html::label(Yii::t('ezform', 'Column'), 'options[tabs][' . $key . '][column]', ['class' => 'control-label']) ?>
                    <?= Html::textInput('options[tabs][' . $key . '][column]', $val['column'], ['class' => 'form-control', 'type' => 'number', 'id' => 'column_' . $key]) ?>
                </div>
            </div>
            <div class="form-group row" >
                <div class="col-md-12" id="display_ezform<?= $key ?>" style="<?= $type_ezform ?>">
                    <div class="col-md-4 sdbox-col">
                        <?php
                        $attrname_ezf_id = 'options[tabs][' . $key . '][ezf_id]';
                        $value_ezf_id = isset($val['ezf_id'])?$val['ezf_id']:null;
                        ?>
                        <?= Html::label(Yii::t('ezmodule', 'Forms'), $attrname_ezf_id, ['class' => 'control-label']) ?>
                        <?php
                        echo kartik\select2\Select2::widget([
                            'name' => $attrname_ezf_id,
                            'value' => $value_ezf_id,
                            'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_ezf_id_' . $key, 'key_index' => $key],
                            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                        ?>
                    </div>
                    <div class="col-md-4 sdbox-col">
                        <?php
                        $attrname_field_bdate[$key] = 'options[tabs][' . $key . '][field_bdate]';
                        $main_field_bdate[$key] = isset($val['field_bdate'])?$val['field_bdate']:null;
                        ?>
                        <?= Html::label(Yii::t('ezform', 'Field of birthdate'), '', ['class' => 'control-label']) ?>
                        <div id="ref_field_bdate_<?= $key ?>">

                        </div>
                    </div>


                    <div class="col-md-4 sdbox-col">
                        <?php
                        $attrname_field_pic[$key] = 'options[tabs][' . $key . '][field_pic]';
                        $main_field_pic[$key] = isset($val['field_pic'])?$val['field_pic']:null;
                        ?>
                        <?= Html::label(Yii::t('ezform', 'Image Field'), '', ['class' => 'control-label']) ?>
                        <div id="ref_field_pic_<?= $key ?>">

                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12 sdbox-col">
                        <?php
                        $attrname_field_display[$key] = 'options[tabs][' . $key . '][field_display]';
                        $main_field_display[$key] = isset($val['field_display']) ? $val['field_display'] : '';
                        ?>
                        <?= Html::label(Yii::t('ezform', 'Fields'), 'options[tabs][' . $key . '][field_display]', ['class' => 'control-label']) ?>
                        <div id="ref_field_box_<?= $key ?>">

                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12 sdbox-col">
                        <?= Html::label(Yii::t('ezform', 'Template Content'), 'options[tabs][' . $key . '][template_content]', ['class' => 'control-label']) ?>
                        <?= Html::textarea('options[tabs][' . $key . '][template_content]', isset($val['template_content']) ? $val['template_content'] : '', ['class' => 'form-control', 'row' => 3]) ?>
                    </div>
                </div>
                <div class="col-md-12" id="display_widget_content<?= $key ?>" style="<?= $type_widget ?>">
                    <div class="col-md-12 sdbox-col">
                        <?= Html::label(Yii::t('ezform', 'Widget'), 'options[tabs][' . $key . '][widget_id]') ?>
                        <?php
                        $attrname_widget_id = 'options[tabs][' . $key . '][widget_id]';
                        $value_widget_id = isset($val['widget_id']) ? $val['widget_id'] : '';
                        echo kartik\select2\Select2::widget([
                            'name' => $attrname_widget_id,
                            'value' => $value_widget_id,
                            'options' => ['placeholder' => Yii::t('ezmodule', 'Widgets'), 'id' => 'config_widget_id_' . $key, 'key_index' => $key],
                            'data' => ArrayHelper::map($itemWidgets, 'widget_id', 'widget_name'),
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                        ?>
                    </div>
                    <div class="col-md-12 sdbox-col">
                        <?= Html::label(Yii::t('ezform', 'Custom Action (URL)'), 'options[tabs][' . $key . '][custom_action]', ['class' => 'control-label']) ?>
                        <?= Html::textarea('options[tabs][' . $key . '][custom_action]', isset($tabs[$key]['custom_action']) ? $tabs[$key]['custom_action'] : '', ['class' => 'form-control', 'row' => 3]) ?>
                    </div>
                </div>
                <div class="col-md-12" id="display_content<?= $key ?>" style="<?= $type_content ?>">
                    <?= \appxq\sdii\widgets\FroalaEditorWidget::widget(['name' => 'options[tabs][' . $key . '][tab_content]', 'id' => 'tab-type-content' . $key]) ?>
                </div>
                <div class="col-md-12" id="display_ajax<?= $key ?>" style="<?= $type_ajax ?>">
                    <?= Html::label(Yii::t('ezform', 'URL Request <code>Params ที่จะถูกส่งไปด้วย visitid, target</code>'), 'options[tabs][' . $key . '][url_request]', ['class' => 'control-label']) ?>
                    <?= Html::textarea('options[tabs][' . $key . '][url_request]', isset($val['url_request']) ? $val['url_request'] : '', ['class' => 'form-control', 'row' => 3]) ?>
                    <?= Html::label(Yii::t('ezform', 'URL Request Popup <code>Params ที่จะถูกส่งไปด้วย visitid, target</code>'), 'options[tabs][' . $key . '][url_request_popup]', ['class' => 'control-label']) ?>
                    <?= Html::textarea('options[tabs][' . $key . '][url_request_popup]', isset($val['url_request_popup']) ? $val['url_request_popup'] : '', ['class' => 'form-control', 'row' => 3]) ?>
                </div>
            </div> 

        </div>

        <?php
    endforeach;
else:

    $display = "display:none;";
    $type_ezform = 'display:block;';
    $type_widget = 'display:none;';
    $type_content = 'display:none;';
    $type_ajax = 'display:none;';
    ?>
    <div id="content_config_tab<?= $key_index ?>">
        <div class="form-group row">
            <div class="col-md-4 ">
                <?= Html::label(Yii::t('ezform', 'Tab Title'), 'tab_type') ?>
                <?= Html::textInput('options[tabs][' . $key_index . '][tab_title]', isset($tab_title) ? $tab_title : '', ['class' => 'form-control', 'id' => 'title_input' . $key_index, 'data-key_index' => $key_index]) ?>
            </div>
            <div class="col-md-4 sdbox-col">
                <?= Html::label(Yii::t('ezform', 'Tab Type'), 'tab_type') ?>
                <?= Html::dropDownList('options[tabs][' . $key_index . '][tab_type]', $tab_type, $tab_item, ['class' => 'form-control tab_type_input', 'id' => 'tab_type_' . $key_index, 'data-key_index' => $key_index]) ?>
            </div>
            <div class="col-md-4 sdbox-col" >
                <?= Html::label(Yii::t('ezform', 'Column'), 'options[tabs][' . $key_index . '][column]', ['class' => 'control-label']) ?>
                <?= Html::textInput('options[tabs][' . $key_index . '][column]', (isset($tabs[$key_index]['column']) ? $tabs[$key_index]['column'] : '2'), ['class' => 'form-control', 'type' => 'number']) ?>
            </div>
        </div>
        <div class="form-group row" >
            <div class="col-md-12" id="display_ezform<?= $key_index ?>" style="<?= $type_ezform ?>">
                <div class="col-md-4 sdbox-col">
                    <?php
                    $attrname_ezf_id = 'options[tabs][' . $key_index . '][ezf_id]';
                    $value_ezf_id = isset($tabs[$key_index]['ezf_id']) ? $tabs[$key_index]['ezf_id'] : '';
                    ?>
                    <?= Html::label(Yii::t('ezmodule', 'Forms'), $attrname_ezf_id, ['class' => 'control-label']) ?>
                    <?php
                    echo kartik\select2\Select2::widget([
                        'name' => $attrname_ezf_id,
                        'value' => $value_ezf_id,
                        'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_ezf_id_' . $key_index, 'key_index' => $key_index],
                        'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-md-4 sdbox-col">
                    <?php
                    $attrname_field_bdate[$key_index] = 'options[tabs][' . $key_index . '][field_bdate]';
                    $main_field_bdate[$key_index] = isset($tabs[$key_index]['field_bdate']) ? $tabs[$key_index]['field_bdate'] : '';
                    ?>
                    <?= Html::label(Yii::t('ezform', 'Field of birthdate'), '', ['class' => 'control-label']) ?>
                    <div id="ref_field_bdate_<?= $key_index ?>">

                    </div>
                </div>

                <div class="col-md-4 sdbox-col">
                    <?php
                    $attrname_field_pic[$key_index] = 'options[tabs][' . $key_index . '][field_pic]';
                    $main_field_pic[$key_index] = isset($tabs[$key_index]['field_pic']) ? $tabs[$key_index]['field_pic'] : '';
                    ?>
                    <?= Html::label(Yii::t('ezform', 'Image Field'), 'options[tabs][' . $key_index . '][field_pic]', ['class' => 'control-label']) ?>
                    <div id="ref_field_pic_<?= $key_index ?>">

                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12 sdbox-col">
                    <?php
                    $attrname_field_display[$key_index] = 'options[tabs][' . $key_index . '][field_display]';
                    $main_field_display[$key_index] = isset($tabs[$key_index]['field_display']) ? $tabs[$key_index]['field_display'] : '';
                    ?>
                    <?= Html::label(Yii::t('ezform', 'Fields'), 'options[tabs][' . $key_index . '][field_display]', ['class' => 'control-label']) ?>
                    <div id="ref_field_box_<?= $key_index ?>">

                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12 sdbox-col">
                    <?= Html::label(Yii::t('ezform', 'Template Content'), 'options[tabs][' . $key_index . '][template_content]', ['class' => 'control-label']) ?>
                    <?= Html::textarea('options[tabs][' . $key_index . '][template_content]', isset($tabs[$key_index]['template_content']) ? $tabs[$key_index]['template_content'] : '', ['class' => 'form-control', 'row' => 3]) ?>
                </div>
            </div>

            <div class="col-md-12" id="display_widget_content<?= $key_index ?>" style="<?= $type_widget ?>">
                <div class="col-md-12 sdbox-col">
                    <?= Html::label(Yii::t('ezform', 'Widget'), 'options[tabs][' . $key_index . '][widget_id]') ?>
                    <?php
                    $attrname_widget_id = 'options[tabs][' . $key_index . '][widget_id]';
                    $value_widget_id = '';
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
            <div class="col-md-12" id="display_content<?= $key_index ?>" style="<?= $type_content ?>">
                <?= Html::label(Yii::t('ezform', 'Tab Title'), 'options[tabs][' . $key_index . '][tab_content]') ?>
                <?= \appxq\sdii\widgets\FroalaEditorWidget::widget(['name' => 'options[tabs][' . $key_index . '][tab_content]', 'id' => 'tab-type-content' . $key_index]) ?>
            </div>
            <div class="col-md-12" id="display_ajax<?= $key_index ?>" style="<?= $type_ajax ?>">
                <?= Html::label(Yii::t('ezform', 'URL Request <code>Params ที่จะถูกส่งไปด้วย visitid, target</code>'), 'options[tabs][' . $key_index . '][url_request]', ['class' => 'control-label']) ?>
                <?= Html::textarea('options[tabs][' . $key_index . '][url_request]', isset($val['url_request']) ? $val['url_request'] : '', ['class' => 'form-control', 'row' => 3]) ?>
                <?= Html::label(Yii::t('ezform', 'URL Request Popup <code>Params ที่จะถูกส่งไปด้วย visitid, target</code>'), 'options[tabs][' . $key_index . '][url_request_popup]', ['class' => 'control-label']) ?>
                <?= Html::textarea('options[tabs][' . $key_index . '][url_request_popup]', isset($val['url_request_popup']) ? $val['url_request_popup'] : '', ['class' => 'form-control', 'row' => 3]) ?>
            </div>
        </div> 
    </div> 

<?php endif; ?>

<?php
$this->registerJs("
        
    $(function(){
        var tab_keys = " . json_encode($tab_keys) . ";
        if(tab_keys.length > 0){
            for(var i=0;i<tab_keys.length;i++){
                fields($('#config_ezf_id_'+tab_keys[i]).val(),tab_keys[i]);
                field_pic($('#config_ezf_id_'+tab_keys[i]).val(),tab_keys[i]);
                field_bdate($('#config_ezf_id_'+tab_keys[i]).val(),tab_keys[i]);
            }
        }else{
            key_index = '$key_index';
            fields($('#config_ezf_id_'+key_index).val(),key_index);
            field_pic($('#config_ezf_id_'+key_index).val(),key_index);
            field_bdate($('#config_ezf_id_'+key_index).val(),key_index);
        }
        
    });
    
    
    
    $(document).on('change','[id^=config_ezf_id]',function(){
      var ezf_id = $(this).val();
      var key_index = $(this).attr('key_index');
      fields(ezf_id,key_index);
      field_pic(ezf_id,key_index);
      field_bdate(ezf_id,key_index);
    });
    
    function fields(ezf_id,index){
        var value = " . json_encode($main_field_display) . ";
        var name = " . json_encode($attrname_field_display) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: name[index], value: value[index] ,id:'config_fields_'+index}
          ).done(function(result){
             $('#ref_field_box_'+index).html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function field_pic(ezf_id,index){
        var value = " . json_encode($main_field_pic) . ";
        var name = " . json_encode($attrname_field_pic) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: name[index], value: value[index] ,id:'config_field_pic_'+index}
          ).done(function(result){
             $('#ref_field_pic_'+index).html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    } 
    
    function field_bdate(ezf_id,index){
        var value = " . json_encode($main_field_bdate) . ";
        var name = " . json_encode($attrname_field_bdate) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: name[index], value: value[index] ,id:'config_field_bdate_'+index}
          ).done(function(result){
             $('#ref_field_bdate_'+index).html(result);
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
    })

");
?>