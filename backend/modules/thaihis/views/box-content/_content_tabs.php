<?php

use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfHelper;
use kartik\tabs\TabsX;

$custom_css = $this->registerCss("
    
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

$template_h = '<div id="box-{id}" class="row">
                    <div class="col-md-4 sdcol-label">
                      <strong>{label}</strong>
                    </div>
                    <div class="col-md-8 sdbox-col">
                      {value}
                    </div>
               </div>';

$template_v = '<dl id="{id}">
                    <dt>{label}</dt>
                    <dd>{value}</dd>
               </dl>';

$template_item;

if (isset($options['display'])) {
    if ($options['display'] == 'content_v') {
        $template_item = $template_v;
    } elseif ($options['display'] == 'content_h') {
        $template_item = $template_h;
    } else {
        $template_item = '<span id="box-{id}">{value}</span>';
    }
} else {
    $template_item = $template_h;
}

//start content
$column = isset($options['column']) ? $options['column'] : 2;


$js_ajax_content = '';
$content = '';
$firstIndex = null;
if (isset($tabs) && is_array($tabs)) {
    foreach ($tabs as $key => $valTab) {
        if ($firstIndex === null)
            $firstIndex = $key;
        else
            continue;
    }
}

if (isset($tabs) && is_array($tabs)) {
    foreach ($tabs as $keyField => $valTab) {
        $template_content = "";

        $path_items = [
            "{update_date}" => (isset($model_tabs[$keyField]['update_date']) && !empty($model_tabs[$keyField]['update_date']) ? '<i class="glyphicon glyphicon-calendar"></i> ' . appxq\sdii\utils\SDdate::mysql2phpDateTime($model_tabs[$keyField]['update_date']) : ''),
        ];

        // Tab type ajax requeset ======================================
        if ($valTab['tab_type'] == '4') { // Tab type ajax requeset
            if (isset($valTab['url_request']) && $valTab['url_request'] != null) {
                $url = yii\helpers\Url::to([$valTab['url_request'],
                            'visit_id' => $visitid,
                            'reloadDiv' => $reloadDiv,
                ]);
                $template_content .= "<div id='show-content-tab$keyField-$widget_id'>";

                $template_content .= "</div>";
                $js_ajax_content .= "
                var div = $('#show-content-tab$keyField-$widget_id');
                div.html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                $.get('$url',function(result){
                    div.empty();
                    div.html(result);
                });
            ";
            }
        } else if ($valTab['tab_type'] == '2') { //Tab type widget
            $sub_widget_id = $valTab['widget_id'];
            $widget_ops = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($sub_widget_id);
            $template_content .= "<div id='show-content-tab$keyField-$widget_id'>";
            $template_content .= $this->render($widget_ops['widget_render'], ['widget_config' => $widget_ops, 'modal' => $modal]);
            $template_content .= "</div>";
        } else {

            if (isset($valTab['field_pic']) && !empty($valTab['field_pic'])) {
                $image_field = Html::img(Yii::getAlias('@storageUrl/images/nouser.png'), ['class' => 'media-object img-rounded', 'style' => 'width: 100px;']);
                if (isset($model_tabs[$keyField][$valTab['field_pic']]) && !empty($model_tabs[$keyField][$valTab['field_pic']])) {
                    $image_field = Html::img(Yii::getAlias('@storageUrl/ezform/fileinput/') . $model_tabs[$keyField][$valTab['field_pic']], ['class' => 'media-object img-rounded', 'style' => 'width: 100px;']);
                }

                $path_items["{{$options['image_field']}}"] = $image_field;
            }

            if (is_array($modelFields_tabs[$keyField])) {

                foreach ($modelFields_tabs[$keyField] as $key => $value) {

                    $fieldName = $value['ezf_field_name'];
                    $col = 12 / $column;
                    $template_content .= "<div class=\"col-md-$col\">{{$value['ezf_field_name']}}</div>";

                    $var = $value['ezf_field_name'];
                    $label = $value['ezf_field_label'];

                    $dataInput;
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }

                    if (isset($valTab['template_content']) && strlen($valTab['template_content']) > 0) {
                        $template_item = '<span id="box-{id}">{value}</span>';
                    } else {
                        $template_item = $template_h;
                    }

                    $path_data = [
                        '{id}' => Html::getInputId($model_tabs[$keyField], $fieldName),
                        '{label}' => $label,
                        '{value}' => backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model_tabs[$keyField]),
                    ];

                    $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);
                }
            }

            if (isset($valTab['field_bdate']) && $valTab['field_bdate'] != '') {
                $bdate_name = explode('.', $valTab['field_bdate']);
                $path_data = [
                    '{id}' => 'ezf-age',
                    '{label}' => '',
                    '{value}' => \backend\modules\thaihis\classes\ThaiHisQuery::calAge($model_tabs[$keyField][$bdate_name[1]]),
                ];
                $path_items["{age}"] = strtr($template_item, $path_data);
            }

            if (isset($valTab['template_content']) && !empty($valTab['template_content'])) {
                $template_content = $valTab['template_content'];
            }
        }

        if ($firstIndex == $keyField) {
            $tab_content = '<div class="tab-pane fade in active" id="content-tab' . $widget_id . $keyField . '" role="tabpanel" aria-labelledby="nav-tab' . $keyField . '-tab">';
        } else {
            $tab_content = '<div class="tab-pane" id="content-tab' . $widget_id . $keyField . '" role="tabpanel" aria-labelledby="nav-tab' . $keyField . '-tab">';
        }

        if (isset($valTab['field_pic']) && $valTab['field_pic'] != '') {
            $template_content = $tab_content . "<div class=\"media\"> 
                                <div class=\"media-left\"> 
                                      $image_field
                                </div> 
                                <div class=\"media-body\"> 
                                      $template_content
                                </div> 
                        </div>";
        } else {
            $template_content = $tab_content . "<div > 
                                      $template_content
                                </div>";
        }

        $template_content .= '</div>';

        $content .= strtr($template_content, $path_items);
    }
}

//end content
//start box content
$title = isset($options['title']) ? $options['title'] : '';
$readonly = isset($options['readonly']) ? $options['readonly'] : 0;
$disabled = isset($options['disabled']) ? $options['disabled'] : 0;
$disabled_box = isset($options['disabled_box']) ? $options['disabled_box'] : 0;

$template_box = isset($options['template_box']) ? $options['template_box'] : '';
$addon = isset($options['addon']) ? $options['addon'] : 0;
$theme = isset($options['theme']) ? $options['theme'] : 'primary';

$title = '<ul class="nav nav-tabs navtabs-' . $widget_id . '" role="tablist" >';

foreach ($tabs as $key => $val) {

    if ($key == $firstIndex)
        $title .= '<li class="nav-item"><a href="#tab' . $widget_id . $key . '" class=" tab-primary tab-active btn-tabs' . $widget_id . '"  id="btn-tab' . $widget_id . $key . '" key_index="' . $key . '" >' . $val['tab_title'] . '</a></li>';
    else
        $title .= '<li class="nav-item"><a href="#tab' . $widget_id . $key . '" class="tab-primary btn-tabs' . $widget_id . '" id="btn-tab' . $widget_id . $key . '"  key_index="' . $key . '">' . $val['tab_title'] . '</a></li>';
}
$title .= '</ul>';
$js_ajax_popup = '';
if (!$disabled) {
    $template_action = '';
    $path_display = [
        '{custom_css}' => $custom_css,
        '{content}' => $content,
        '{theme}' => $theme,
        '{title}' => $title,
    ];

//    if (isset($model['create_date']) && $model['create_date']) {
//        $path_display['{create_date}'] = '<i class="glyphicon glyphicon-calendar"></i> '
//                . appxq\sdii\utils\SDdate::mysql2phpDateTime($model['create_date']);
//        $template_action .= '{create_date}';
//    }
    
    if (isset($tabs) && is_array($tabs)) {
        foreach ($tabs as $keyField => $valTab) {

            $action = isset($valTab['action']) && !empty($valTab['action']) ? $valTab['action'] : ['create', 'update', 'view', 'delete'];
            if ($keyField == $firstIndex)
                $template_action .= '<div id="action_tab_' . $widget_id . $keyField . '">';
            else
                $template_action .= '<div id="action_tab_' . $widget_id . $keyField . '" style="display:none;">';

            $initdata = [];
            if ($valTab['ezf_id'] == '1503378440057007100') {
                $initdata = ['pt_cid' => $model_tabs[$keyField]['pt_cid']];
            }

            if ($valTab['tab_type'] == '4') {
                foreach ($action as $value) {
                    if ($value == 'create') {
                        $template_action .= ' {create' . $keyField . '}';
                        $path_display['{create' . $keyField . '}'] = Html::button('<i class="fa fa-plus"></i>', ['class' => 'btn btn-success', 'id' => 'btn_popup' . $keyField . '-' . $widget_id]);
                        if (isset($valTab['url_request_popup']) && $valTab['url_request_popup'] != null) {
                            $url = yii\helpers\Url::to([$valTab['url_request_popup'],
                                        'visitid' => $visitid,
                                        'visit_type' => $visit_type,
                                        'target' => $target,
                            ]);

                            $js_ajax_popup .= "
                                $('#btn_popup$keyField-$widget_id').click(function(){
                                    $('#$modal').modal();
                                    $('#$modal').find('.modal-content').load('$url');
                                });
                            ";
                        }
                    }
                }
            } else if ($valTab['tab_type'] == '2') {
                foreach ($action as $value) {
                    if ($value == 'create') {
                        $template_action .= ' {create' . $keyField . '}';
                        $path_display['{create' . $keyField . '}'] = Html::button('<i class="fa fa-plus"></i>', ['class' => 'btn btn-success', 'id' => 'btn_popup' . $keyField . '-' . $widget_id]);
                        if (isset($valTab['custom_action']) && $valTab['custom_action'] != null) {
                            $widgetData = \backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($valTab['widget_id']);
                            $widget_option = \appxq\sdii\utils\SDUtility::string2Array($widgetData['options']);

                            $url = yii\helpers\Url::to([$valTab['custom_action'],
                                        'visitid' => $visitid,
                                        'visit_type' => $visit_type,
                                        'target' => $target,
                                        'modal' => $modal,
                                        'options' => backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($widget_option),
                            ]);

                            $js_ajax_popup .= "
                                $('#btn_popup$keyField-$widget_id').click(function(){
                                    $('#$modal').modal();
                                    $('#$modal').find('.modal-content').html('');
                                    $('#$modal').find('.modal-content').load('$url');
                                });
                            ";
                        }
                    }
                }
            } else if ($valTab['tab_type'] == '1') {
                foreach ($action as $value) {
                    //create, update, delete, view, search
                    if ($value == 'create') {
                        $template_action .= ' {create' . $keyField . '}';

                        $path_display['{create' . $keyField . '}'] = EzfHelper::btn($valTab['ezf_id'])
                                ->reloadDiv($reloadDiv)
                                ->modal('modal-ezform-main')
                                ->initdata($initdata)
                                ->target($dataid)
                                ->label('<i class="glyphicon glyphicon-plus"></i>')
                                ->options(['class' => 'btn btn-success btn-sm'])
                                ->buildBtnAdd();
                    } elseif ($value == 'update') {
                        if (isset($model_tabs[$keyField]['id'])) {
                            $template_action .= ' {update' . $keyField . '}';
                            $path_display['{update' . $keyField . '}'] = '';

                            if (isset($model_tabs[$keyField]['id'])) {
                                $path_display['{update' . $keyField . '}'] = EzfHelper::btn($valTab['ezf_id'])
                                        ->reloadDiv($reloadDiv)
                                        ->target($model_tabs[$keyField]['target'])
                                        ->targetField('target')
                                        ->modal('modal-ezform-main')
                                        ->label('<i class="glyphicon glyphicon-pencil"></i>')
                                        ->options(['class' => 'btn btn-primary btn-sm'])
                                        ->buildBtnEdit($model_tabs[$keyField]['id']);
                            }
                        }
                    } elseif ($value == 'delete') {
                        if (isset($model_tabs[$keyField]['id'])) {
                            $template_action .= ' {delete' . $keyField . '}';
                            $path_display['{delete' . $keyField . '}'] = '';
                            if (isset($model_tabs[$keyField]['id'])) {
                                $path_display['{delete' . $keyField . '}'] = EzfHelper::btn($valTab['ezf_id'])
                                        ->reloadDiv($reloadDiv)
                                        ->target($model_tabs[$keyField]['target'])
                                        ->targetField('target')
                                        ->modal('modal-ezform-main')
                                        ->label('<i class="glyphicon glyphicon-trash"></i>')
                                        ->options(['class' => 'btn btn-danger btn-sm'])
                                        ->buildBtnDelete($model_tabs[$keyField]['id']);
                            }
                        }
                    } elseif ($value == 'view') {
                        if (isset($model_tabs[$keyField]['id'])) {
                            $template_action .= ' {view' . $keyField . '}';
                            $path_display['{view' . $keyField . '}'] = EzfHelper::btn($valTab['ezf_id'])
                                    ->reloadDiv($reloadDiv)
                                    ->dataid($model_tabs[$keyField]['id'])
                                    ->targetField('target')
                                    ->modal('modal-ezform-main')
                                    ->addbtn(0)
                                    ->readonly(1)
                                    ->popup(1)
                                    ->data_column($fields)
                                    ->label('<i class="glyphicon glyphicon-th-list"></i>')
                                    ->options(['class' => 'btn btn-default btn-sm'])
                                    ->buildBtnGrid();
                        }
                    }
                }
            }
            $template_action .= '</div>';
        }
    }
    $template_action_view = '';
    if (isset($tabs) && is_array($tabs)) {
        foreach ($tabs as $keyField => $valTab) {
            $action = isset($valTab['action']) && !empty($valTab['action']) ? $valTab['action'] : ['create', 'update', 'view', 'delete'];
            if ($keyField == $firstIndex) {
                $template_action_view .= '<div id="action_tab_' . $key_gen . $keyField . '">';
            } else {
                $template_action_view .= '<div id="action_tab_' . $key_gen . $keyField . '">';
            }

            $initdata = [];
            if ($valTab['ezf_id'] == '1503378440057007100') {
                $initdata = ['pt_cid' => $model_tabs[$keyField]['pt_cid']];
            }

            if ($valTab['tab_type'] == '1') {

                foreach ($action as $value) {
                    if ($value == 'view') {
                        $template_action_view .= ' {view' . $keyField . '}';
                        $path_display['{view' . $keyField . '}'] = EzfHelper::btn($valTab['main_ezf_id'])
                                ->reloadDiv($reloadDiv)
                                ->dataid($model_tabs[$keyField]['dataid'])
                                ->targetField('target')
                                ->target($model_tabs[$keyField]['target'])
                                ->modal('modal-ezform-main')
                                ->addbtn(0)
                                ->readonly(0)
                                ->popup(1)
                                ->data_column($fields)
                                ->label('<i class="glyphicon glyphicon-th-list"></i>')
                                ->options(['class' => 'btn btn-default btn-sm'])
                                ->buildBtnGrid();
                    }
                }
            }
            
            $template_action_view .= '</div>';
        }
    }

    if ($readonly) {
        $template_action = $template_action_view;
    }

    if (empty($template_box)) {
        $template_box = '<div class="panel panel-{theme}">
            <div class="panel-heading" style="padding-bottom:0">
                <div class="row">
                    <div class="col-md-8">
                        {title}
                    </div>
                    <div class="col-md-4 text-right">
                        
                        ' . $template_action . '
                        
                    </div>
                  </div>
              </div>
              <div class="panel-body" >
                <div class="tab-content">
                    {content}
                </div>
              </div>
          </div>';
    }

    $display_box = strtr($template_box, $path_display);

    if ($disabled_box) {
        echo $content;
    } else {
        echo $display_box;
    }
}
//end box content
?>

<?php

$this->registerJs("
    $js_ajax_content
    $js_ajax_popup
    $('.btn-tabs$widget_id').on('click',function(){
        var widget_id = '$widget_id';
        var navtabs = 'navtabs-$widget_id';
        $(this).parents('.'+navtabs).find('.nav-item').each(function(i,e){
            $('#content-tab'+widget_id+''+$(e).children().attr('key_index')).removeClass('fade in active');
            $('#action_tab_'+widget_id+''+$(e).children().attr('key_index')).css('display','none');
            $(e).children().removeClass('tab-active');
        });
        
        $('#content-tab'+widget_id+''+$(this).attr('key_index')).addClass('fade in active');
        $('#action_tab_'+widget_id+''+$(this).attr('key_index')).css('display','block');
        $(this).addClass('tab-active');
    });
");
?>